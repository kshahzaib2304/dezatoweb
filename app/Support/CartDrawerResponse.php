<?php

namespace App\Support;

/**
 * Shared JSON payload for the AJAX cart drawer.
 */
final class CartDrawerResponse
{
    /**
     * @return array{message: string, count: int, subtotal: float, subtotal_label: string, html: string}
     */
    public static function payload(Cart $cart, string $message, ?Fulfillment $fulfillment = null): array
    {
        $lines = $cart->lines();
        $subtotal = $cart->subtotal();
        $fulfillment ??= app(Fulfillment::class);
        $fee = $fulfillment->fee();
        $feeLabel = $fulfillment->feeLabel();

        return [
            'message' => $message,
            'count' => $cart->count(),
            'subtotal' => $subtotal,
            'subtotal_label' => pkr($subtotal),
            'html' => view('components.cart-drawer-body', [
                'lines' => $lines,
                'count' => $cart->count(),
                'subtotal' => $subtotal,
                'deliveryFee' => $fee,
                'feeLabel' => $feeLabel,
                'paymentHint' => $fulfillment->paymentHint(),
            ])->render(),
        ];
    }
}
