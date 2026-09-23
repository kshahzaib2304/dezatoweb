<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Support\OnlineCheckout;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function start(Order $order): View|RedirectResponse
    {
        if ($order->payment_status === Order::PAYMENT_PAID) {
            return redirect()
                ->route('checkout.confirmation', $order)
                ->with('status', 'This order is already marked paid.');
        }

        $payload = OnlineCheckout::begin($order);

        if ($payload === null) {
            return redirect()
                ->route('checkout.confirmation', $order)
                ->with('status', 'Online payment is not available for this order. Please use the payment instructions on the confirmation page.');
        }

        if (($payload['type'] ?? null) === 'redirect' && ! empty($payload['url'])) {
            return redirect()->away($payload['url']);
        }

        return view('pages.payment-redirect', [
            'title' => 'Pay for order '.$order->number.' | Dezato',
            'order' => $order,
            'action' => $payload['action'] ?? '',
            'fields' => $payload['fields'] ?? [],
        ]);
    }

    public function callback(Request $request, string $provider, Order $order): RedirectResponse
    {
        $ok = OnlineCheckout::markPaidIfValid($order->fresh(), array_merge(
            $request->query(),
            $request->request->all()
        ));

        if ($ok) {
            return redirect()
                ->route('checkout.confirmation', $order)
                ->with('status', 'Payment received — thank you! We are preparing your order.');
        }

        return redirect()
            ->route('checkout.confirmation', $order)
            ->with('status', 'We could not confirm the online payment yet. If you paid, message us with your order number '.$order->number.'.');
    }
}
