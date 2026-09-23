<?php

namespace App\Support;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

/**
 * Builds hosted checkout payloads / redirects for JazzCash, Easypaisa, and Stripe.
 */
final class OnlineCheckout
{
    public static function usesOnlineGateway(string $paymentMethod): bool
    {
        return GatewayCredentials::isReady($paymentMethod);
    }

    /**
     * @return array{type: string, action?: string, fields?: array<string, string>, url?: string}|null
     */
    public static function begin(Order $order): ?array
    {
        if (! self::usesOnlineGateway($order->payment_method)) {
            return null;
        }

        return match ($order->payment_method) {
            'jazzcash' => self::jazzCashForm($order),
            'easypaisa' => self::easyPaisaForm($order),
            'card' => self::stripeCheckout($order),
            default => null,
        };
    }

    public static function markPaidIfValid(Order $order, array $payload): bool
    {
        $method = $order->payment_method;

        $ok = match ($method) {
            'jazzcash' => self::verifyJazzCash($payload),
            'easypaisa' => self::verifyEasyPaisa($payload),
            'card' => self::verifyStripeSession($payload),
            default => false,
        };

        if ($ok && $order->payment_status !== Order::PAYMENT_PAID) {
            $order->update(['payment_status' => Order::PAYMENT_PAID]);
        }

        return $ok;
    }

    /**
     * @return array{type: string, action: string, fields: array<string, string>}
     */
    private static function jazzCashForm(Order $order): array
    {
        $merchantId = GatewayCredentials::field('jazzcash', 'merchant_id');
        $password = GatewayCredentials::field('jazzcash', 'password');
        $salt = GatewayCredentials::field('jazzcash', 'integrity_salt');
        $sandbox = GatewayCredentials::sandbox('jazzcash');

        $amount = (string) ((int) round(((float) $order->total) * 100));
        $txnDateTime = now()->format('YmdHis');
        $txnRef = 'T'.$txnDateTime.Str::upper(Str::random(4));
        $expiry = now()->addHour()->format('YmdHis');

        $fields = [
            'pp_Version' => '1.1',
            'pp_TxnType' => 'MWALLET',
            'pp_Language' => 'EN',
            'pp_MerchantID' => $merchantId,
            'pp_SubMerchantID' => '',
            'pp_Password' => $password,
            'pp_BankID' => '',
            'pp_ProductID' => 'RETL',
            'pp_TxnRefNo' => $txnRef,
            'pp_Amount' => $amount,
            'pp_TxnCurrency' => 'PKR',
            'pp_TxnDateTime' => $txnDateTime,
            'pp_BillReference' => $order->number,
            'pp_Description' => 'Dezato order '.$order->number,
            'pp_TxnExpiryDateTime' => $expiry,
            'pp_ReturnURL' => route('payments.callback', ['provider' => 'jazzcash', 'order' => $order->number]),
            'ppmpf_1' => $order->number,
        ];

        $fields['pp_SecureHash'] = self::jazzCashHash($fields, $salt);

        $action = $sandbox
            ? 'https://sandbox.jazzcash.com.pk/CustomerPortal/transactionmanagement/merchantform/'
            : 'https://payments.jazzcash.com.pk/CustomerPortal/transactionmanagement/merchantform/';

        return [
            'type' => 'form',
            'action' => $action,
            'fields' => $fields,
        ];
    }

    /**
     * @param  array<string, string>  $fields
     */
    private static function jazzCashHash(array $fields, string $salt): string
    {
        ksort($fields);
        $hashString = $salt;

        foreach ($fields as $key => $value) {
            if ($key === 'pp_SecureHash' || $value === '' || $value === null) {
                continue;
            }
            $hashString .= '&'.$value;
        }

        return strtoupper(hash_hmac('sha256', $hashString, $salt));
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private static function verifyJazzCash(array $payload): bool
    {
        $code = (string) ($payload['pp_ResponseCode'] ?? $payload['pp_responseCode'] ?? '');

        return in_array($code, ['000', '00', '121'], true);
    }

    /**
     * Easypaisa OTC / merchant form style handoff.
     *
     * @return array{type: string, action: string, fields: array<string, string>}
     */
    private static function easyPaisaForm(Order $order): array
    {
        $storeId = GatewayCredentials::field('easypaisa', 'store_id');
        $hashKey = GatewayCredentials::field('easypaisa', 'hash_key');
        $account = GatewayCredentials::field('easypaisa', 'account_number');
        $sandbox = GatewayCredentials::sandbox('easypaisa');

        $amount = number_format((float) $order->total, 0, '.', '');
        $orderRef = $order->number;
        $token = strtoupper(hash_hmac('sha256', $storeId.$amount.$orderRef, $hashKey));

        $fields = [
            'storeId' => $storeId,
            'amount' => $amount,
            'postBackURL' => route('payments.callback', ['provider' => 'easypaisa', 'order' => $order->number]),
            'orderRefNum' => $orderRef,
            'merchantAccountNumber' => $account,
            'autoRedirect' => '1',
            'paymentMethod' => 'INITIAL_REQUEST',
            'token' => $token,
        ];

        $action = $sandbox
            ? 'https://easypaystg.easypaisa.com.pk/easypay/Index.jsf'
            : 'https://easypay.easypaisa.com.pk/easypay/Index.jsf';

        return [
            'type' => 'form',
            'action' => $action,
            'fields' => $fields,
        ];
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private static function verifyEasyPaisa(array $payload): bool
    {
        $status = strtolower((string) ($payload['status'] ?? $payload['paymentStatus'] ?? $payload['responseCode'] ?? ''));

        return in_array($status, ['success', 'paid', '0000', '00', 'completed'], true);
    }

    /**
     * @return array{type: string, url: string}|null
     */
    private static function stripeCheckout(Order $order): ?array
    {
        $secret = GatewayCredentials::field('card', 'secret_key');

        if ($secret === '') {
            return null;
        }

        $amount = (int) round(((float) $order->total) * 100);

        try {
            $response = Http::withToken($secret)
                ->asForm()
                ->post('https://api.stripe.com/v1/checkout/sessions', [
                    'mode' => 'payment',
                    'success_url' => route('payments.callback', ['provider' => 'card', 'order' => $order->number]).'?session_id={CHECKOUT_SESSION_ID}',
                    'cancel_url' => route('checkout.confirmation', $order),
                    'client_reference_id' => $order->number,
                    'customer_email' => $order->email,
                    'line_items[0][price_data][currency]' => 'pkr',
                    'line_items[0][price_data][product_data][name]' => 'Dezato order '.$order->number,
                    'line_items[0][price_data][unit_amount]' => (string) $amount,
                    'line_items[0][quantity]' => '1',
                ]);

            if (! $response->successful()) {
                Log::warning('Stripe Checkout session failed', [
                    'order' => $order->number,
                    'body' => $response->body(),
                ]);

                return null;
            }

            $url = (string) $response->json('url');

            if ($url === '') {
                return null;
            }

            return [
                'type' => 'redirect',
                'url' => $url,
            ];
        } catch (Throwable $exception) {
            Log::warning('Stripe Checkout exception', [
                'order' => $order->number,
                'message' => $exception->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private static function verifyStripeSession(array $payload): bool
    {
        $sessionId = (string) ($payload['session_id'] ?? '');
        $secret = GatewayCredentials::field('card', 'secret_key');

        if ($sessionId === '' || $secret === '') {
            return false;
        }

        try {
            $response = Http::withToken($secret)
                ->get('https://api.stripe.com/v1/checkout/sessions/'.$sessionId);

            if (! $response->successful()) {
                return false;
            }

            return ($response->json('payment_status') === 'paid')
                || ($response->json('status') === 'complete');
        } catch (Throwable) {
            return false;
        }
    }
}
