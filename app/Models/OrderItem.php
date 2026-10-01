<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'product_name',
        'unit_price',
        'quantity',
        'line_total',
        'options',
        'image',
    ];

    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:2',
            'line_total' => 'decimal:2',
            'quantity' => 'integer',
            'options' => 'array',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function isCustom(): bool
    {
        return is_array($this->options) && $this->options !== [];
    }

    public function optionsSummary(): string
    {
        if (! $this->isCustom()) {
            return '';
        }

        $parts = [];

        foreach (['size', 'shape', 'base', 'filling', 'frosting'] as $key) {
            if (! empty($this->options[$key]['label'])) {
                $parts[] = $this->options[$key]['label'];
            }
        }

        if (! empty($this->options['color']['label'])) {
            $parts[] = $this->options['color']['label'].' icing';
        }

        if (! empty($this->options['message'])) {
            $placement = $this->options['message_placement_label'] ?? null;
            $parts[] = $placement
                ? '“'.$this->options['message'].'” ('.$placement.')'
                : '“'.$this->options['message'].'”';
        } elseif (($this->options['message_placement'] ?? null) === 'none') {
            $parts[] = 'No text';
        }

        foreach ($this->options['diets'] ?? [] as $diet) {
            if (! empty($diet['label'])) {
                $parts[] = $diet['label'];
            }
        }

        foreach ($this->options['addons'] ?? [] as $addon) {
            if (! empty($addon['label'])) {
                $qty = max(1, (int) ($addon['qty'] ?? 1));
                $parts[] = $qty > 1
                    ? $addon['label'].' × '.$qty
                    : $addon['label'];
            }
        }

        return implode(' · ', $parts);
    }
}
