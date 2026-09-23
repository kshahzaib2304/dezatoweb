<?php

namespace App\Support;

use App\Models\Promo;

final class PromoCodes
{
    /**
     * @return array{ok: bool, message: string, discount: float, promo: ?Promo}
     */
    public static function apply(?string $code, float $subtotal): array
    {
        $code = strtoupper(trim((string) $code));

        if ($code === '') {
            return ['ok' => true, 'message' => '', 'discount' => 0.0, 'promo' => null];
        }

        $promo = Promo::query()->where('code', $code)->first();

        if ($promo === null) {
            return ['ok' => false, 'message' => 'That promo code was not found.', 'discount' => 0.0, 'promo' => null];
        }

        $result = $promo->evaluate($subtotal);

        return [
            'ok' => $result['ok'],
            'message' => $result['message'],
            'discount' => $result['discount'],
            'promo' => $result['ok'] ? $promo : null,
        ];
    }
}
