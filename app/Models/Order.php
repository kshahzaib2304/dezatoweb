<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    public const STATUS_PLACED = 'placed';

    protected $fillable = [
        'number',
        'status',
        'method',
        'location_id',
        'location_name',
        'customer_name',
        'email',
        'phone',
        'address',
        'city',
        'region',
        'postal_code',
        'notes',
        'payment_method',
        'subtotal',
        'fee',
        'total',
        'placed_at',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'fee' => 'decimal:2',
            'total' => 'decimal:2',
            'placed_at' => 'datetime',
        ];
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function methodLabel(): string
    {
        return match ($this->method) {
            'delivery' => 'Delivery',
            'shipping' => 'Courier',
            default => 'Pickup',
        };
    }
}
