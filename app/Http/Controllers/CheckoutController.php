<?php

namespace App\Http\Controllers;

use App\Http\Requests\PlaceOrderRequest;
use App\Models\Order;
use App\Support\Cart;
use App\Support\Fulfillment;
use App\Support\PlaceOrder;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use RuntimeException;

class CheckoutController extends Controller
{
    public function __construct(
        private readonly Cart $cart,
        private readonly Fulfillment $fulfillment,
        private readonly PlaceOrder $placeOrder,
    ) {}

    public function show(): View
    {
        $subtotal = $this->cart->subtotal();
        $fee = $this->fulfillment->fee();

        return view('pages.checkout', [
            'title' => 'Checkout | Dezato Cake House',
            'metaDescription' => 'Complete your Dezato order for pickup, Karachi delivery, or Pakistan courier.',
            'lines' => $this->cart->lines(),
            'subtotal' => $subtotal,
            'fee' => $fee,
            'total' => round($subtotal + $fee, 2),
            'fulfillment' => $this->fulfillment->get(),
            'fulfillmentSummary' => $this->fulfillment->summary(),
            'feeLabel' => $this->fulfillment->feeLabel(),
            'paymentHint' => $this->fulfillment->paymentHint(),
        ]);
    }

    public function store(PlaceOrderRequest $request): RedirectResponse
    {
        try {
            $order = $this->placeOrder->handle([
                'customer_name' => $request->string('customer_name')->trim()->toString(),
                'email' => $request->string('email')->trim()->toString(),
                'phone' => $request->string('phone')->trim()->toString(),
                'notes' => $request->string('notes')->trim()->toString() ?: null,
                'payment_method' => $request->string('payment_method')->toString(),
            ]);
        } catch (RuntimeException $exception) {
            return redirect()
                ->route('cart.show')
                ->withErrors(['cart' => $exception->getMessage()]);
        }

        return redirect()
            ->route('checkout.confirmation', $order)
            ->with('status', 'Order placed successfully.');
    }

    public function confirmation(Order $order): View
    {
        $order->loadMissing('items');

        return view('pages.confirmation', [
            'title' => 'Order '.$order->number.' | Dezato Cake House',
            'metaDescription' => 'Your Dezato Cake House order confirmation.',
            'order' => $order,
        ]);
    }
}
