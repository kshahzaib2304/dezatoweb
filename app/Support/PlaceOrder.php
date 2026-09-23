<?php

namespace App\Support;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Promo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

final class PlaceOrder
{
    public function __construct(
        private readonly Cart $cart,
        private readonly Fulfillment $fulfillment,
        private readonly Notifier $notifier,
    ) {}

    /**
     * @param  array{
     *     customer_name: string,
     *     email: string,
     *     phone: string,
     *     notes?: string|null,
     *     payment_method: string,
     *     delivery_date?: string|null,
     *     delivery_slot?: string|null,
     *     promo?: string|null
     * }  $customer
     */
    public function handle(array $customer): Order
    {
        if (! $this->fulfillment->has()) {
            throw new RuntimeException('Fulfillment preferences are required.');
        }

        $lines = $this->cart->lines();

        if ($lines->isEmpty()) {
            throw new RuntimeException('Your cart is empty.');
        }

        $enabledPayments = PaymentMethods::enabledIds();
        if (! in_array($customer['payment_method'], $enabledPayments, true)) {
            throw new RuntimeException('That payment method is not available right now.');
        }

        $fulfillment = $this->fulfillment->get();
        $subtotal = $this->cart->subtotal();
        $fee = $this->fulfillment->fee();

        $promoResult = PromoCodes::apply($customer['promo'] ?? null, $subtotal);
        if (! $promoResult['ok']) {
            throw new RuntimeException($promoResult['message']);
        }

        $discount = (float) $promoResult['discount'];
        /** @var Promo|null $promo */
        $promo = $promoResult['promo'];
        $total = round(max(0, $subtotal + $fee - $discount), 2);
        $paymentStatus = PaymentMethods::paymentStatusFor($customer['payment_method']);

        return DB::transaction(function () use ($customer, $fulfillment, $lines, $subtotal, $fee, $discount, $promo, $total, $paymentStatus): Order {
            $order = Order::query()->create([
                'user_id' => auth()->id(),
                'number' => $this->generateNumber(),
                'status' => Order::STATUS_PLACED,
                'method' => $fulfillment['method'],
                'location_id' => $fulfillment['location_id'] ?? null,
                'location_name' => $fulfillment['location_name'] ?? null,
                'customer_name' => $customer['customer_name'],
                'email' => $customer['email'],
                'phone' => $customer['phone'],
                'address' => $fulfillment['address'] ?? null,
                'city' => $fulfillment['city'] ?? null,
                'region' => $fulfillment['region'] ?? null,
                'postal_code' => $fulfillment['postal_code'] ?? null,
                'notes' => $customer['notes'] ?? null,
                'delivery_date' => $customer['delivery_date'] ?? null,
                'delivery_slot' => $customer['delivery_slot'] ?? null,
                'payment_method' => $customer['payment_method'],
                'payment_status' => $paymentStatus,
                'subtotal' => $subtotal,
                'fee' => $fee,
                'discount' => $discount,
                'promo_code' => $promo?->code,
                'total' => $total,
                'placed_at' => now(),
            ]);

            foreach ($lines as $line) {
                OrderItem::query()->create([
                    'order_id' => $order->id,
                    'product_id' => $line['product_id'],
                    'product_name' => $line['product']['name'],
                    'unit_price' => (float) $line['product']['price'],
                    'quantity' => $line['quantity'],
                    'line_total' => $line['line_total'],
                    'options' => $line['options'] ?? null,
                    'image' => $line['image_path'] ?? null,
                ]);
            }

            $promo?->markUsed();
            $this->cart->clear();

            $order = $order->load('items');
            $this->notifier->orderPlaced($order);

            return $order;
        });
    }

    private function generateNumber(): string
    {
        do {
            $number = 'DZ-'.now()->format('ymd').'-'.Str::upper(Str::random(4));
        } while (Order::query()->where('number', $number)->exists());

        return $number;
    }
}
