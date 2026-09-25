<?php

namespace App\Support;

use App\Models\Promo;
use Illuminate\Database\Eloquent\Builder;

final class PromoCodes
{
    /**
     * Short public hint for an active promo (announcement / checkout), or null.
     */
    public static function storefrontHint(): ?string
    {
        $promo = Promo::query()
            ->active()
            ->where(function (Builder $query): void {
                $query->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->where(function (Builder $query): void {
                $query->whereNull('ends_at')->orWhere('ends_at', '>=', now());
            })
            ->orderByDesc('id')
            ->first();

        if ($promo === null) {
            return null;
        }

        if ($promo->max_uses !== null && $promo->used_count >= $promo->max_uses) {
            return null;
        }

        $hint = $promo->code.': '.$promo->discountDescription();

        if ($promo->min_subtotal !== null && $promo->min_subtotal > 0) {
            $hint .= ' on orders from '.pkr($promo->min_subtotal);
        }

        return $hint;
    }

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
