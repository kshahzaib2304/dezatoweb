<?php

namespace App\Http\Controllers;

use App\Http\Requests\PlaceOrderRequest;
use App\Models\Order;
use App\Support\Cart;
use App\Support\Fulfillment;
use App\Support\FulfillmentSchedule;
use App\Support\OnlineCheckout;
use App\Support\PaymentMethods;
use App\Support\PlaceOrder;
use App\Support\PromoCodes;
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
        $promoCode = old('promo');
        $promoResult = PromoCodes::apply(is_string($promoCode) ? $promoCode : null, $subtotal);
        $discount = $promoResult['ok'] ? (float) $promoResult['discount'] : 0.0;

        return view('pages.checkout', [
            'title' => 'Checkout | Dezato Cake House',
            'metaDescription' => 'Complete your Dezato order for pickup, Karachi delivery, or Pakistan courier.',
            'lines' => $this->cart->lines(),
            'subtotal' => $subtotal,
            'fee' => $fee,
            'discount' => $discount,
            'total' => round(max(0, $subtotal + $fee - $discount), 2),
            'fulfillment' => $this->fulfillment->get(),
            'fulfillmentSummary' => $this->fulfillment->summary(),
            'feeLabel' => $this->fulfillment->feeLabel(),
            'paymentHint' => $this->fulfillment->paymentHint(),
            'paymentMethods' => PaymentMethods::forCheckout(),
            'timeSlots' => FulfillmentSchedule::slots(),
            'earliestDate' => FulfillmentSchedule::earliestDate(),
            'scheduleNote' => FulfillmentSchedule::note(),
            'promoMessage' => $promoResult['ok'] && $discount > 0 ? $promoResult['message'] : null,
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
                'delivery_date' => $request->input('delivery_date'),
                'delivery_slot' => $request->string('delivery_slot')->trim()->toString() ?: null,
                'promo' => $request->string('promo')->trim()->toString() ?: null,
            ]);
        } catch (RuntimeException $exception) {
            return redirect()
                ->route('checkout.show')
                ->withInput()
                ->withErrors(['promo' => $exception->getMessage()]);
        }

        if (OnlineCheckout::usesOnlineGateway($order->payment_method)) {
            return redirect()->route('payments.start', $order);
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
            'paymentInstructions' => PaymentMethods::isTransfer($order->payment_method)
                ? PaymentMethods::instructionsFor($order->payment_method)
                : null,
            'paymentLabel' => PaymentMethods::label($order->payment_method),
        ]);
    }
}
