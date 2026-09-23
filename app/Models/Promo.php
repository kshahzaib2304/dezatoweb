<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Promo extends Model
{
    public const TYPE_PERCENT = 'percent';

    public const TYPE_FIXED = 'fixed';

    protected $fillable = [
        'code',
        'label',
        'type',
        'value',
        'min_subtotal',
        'max_uses',
        'used_count',
        'starts_at',
        'ends_at',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'value' => 'integer',
            'min_subtotal' => 'integer',
            'max_uses' => 'integer',
            'used_count' => 'integer',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function typeLabel(): string
    {
        return $this->type === self::TYPE_PERCENT ? 'Percent off' : 'Fixed PKR off';
    }

    public function discountDescription(): string
    {
        return $this->type === self::TYPE_PERCENT
            ? $this->value.'% off'
            : pkr($this->value).' off';
    }

    /**
     * @return array{ok: bool, message: string, discount: float}
     */
    public function evaluate(float $subtotal): array
    {
        if (! $this->is_active) {
            return ['ok' => false, 'message' => 'This promo code is turned off.', 'discount' => 0.0];
        }

        if ($this->starts_at && $this->starts_at->isFuture()) {
            return ['ok' => false, 'message' => 'This promo code is not active yet.', 'discount' => 0.0];
        }

        if ($this->ends_at && $this->ends_at->isPast()) {
            return ['ok' => false, 'message' => 'This promo code has expired.', 'discount' => 0.0];
        }

        if ($this->max_uses !== null && $this->used_count >= $this->max_uses) {
            return ['ok' => false, 'message' => 'This promo code has reached its usage limit.', 'discount' => 0.0];
        }

        if ($this->min_subtotal !== null && $subtotal < $this->min_subtotal) {
            return [
                'ok' => false,
                'message' => 'Add more items - this code needs a subtotal of at least '.pkr($this->min_subtotal).'.',
                'discount' => 0.0,
            ];
        }

        $discount = $this->type === self::TYPE_PERCENT
            ? round($subtotal * ($this->value / 100), 2)
            : (float) $this->value;

        $discount = min($discount, $subtotal);

        if ($discount <= 0) {
            return ['ok' => false, 'message' => 'This promo does not change the total.', 'discount' => 0.0];
        }

        return [
            'ok' => true,
            'message' => 'Promo applied: '.$this->discountDescription(),
            'discount' => $discount,
        ];
    }

    public function markUsed(): void
    {
        $this->increment('used_count');
    }
}
