<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    public const STATUS_PLACED = 'placed';

    public const STATUS_BAKING = 'baking';

    public const STATUS_QC = 'qc';

    public const STATUS_OUT_FOR_DELIVERY = 'out_for_delivery';

    public const STATUS_DELIVERED = 'delivered';

    public const STATUS_CANCELLED = 'cancelled';

    public const PAYMENT_UNPAID = 'unpaid';

    public const PAYMENT_PENDING = 'pending';

    public const PAYMENT_PAID = 'paid';

    public const STATUSES = [
        self::STATUS_PLACED => 'Placed',
        self::STATUS_BAKING => 'Baking',
        self::STATUS_QC => 'Quality check',
        self::STATUS_OUT_FOR_DELIVERY => 'Out for delivery',
        self::STATUS_DELIVERED => 'Delivered',
        self::STATUS_CANCELLED => 'Cancelled',
    ];

    protected $fillable = [
        'user_id',
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
        'delivery_date',
        'delivery_slot',
        'payment_method',
        'payment_status',
        'subtotal',
        'fee',
        'discount',
        'promo_code',
        'total',
        'placed_at',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'fee' => 'decimal:2',
            'discount' => 'decimal:2',
            'total' => 'decimal:2',
            'placed_at' => 'datetime',
            'delivery_date' => 'date',
        ];
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? ucfirst(str_replace('_', ' ', $this->status));
    }

    public function methodLabel(): string
    {
        return match ($this->method) {
            'delivery' => 'Delivery',
            'shipping' => 'Courier',
            default => 'Pickup',
        };
    }

    public function trackingSteps(): array
    {
        $pipeline = [
            self::STATUS_PLACED,
            self::STATUS_BAKING,
            self::STATUS_QC,
            self::STATUS_OUT_FOR_DELIVERY,
            self::STATUS_DELIVERED,
        ];

        if ($this->status === self::STATUS_CANCELLED) {
            return [
                ['key' => self::STATUS_CANCELLED, 'label' => 'Cancelled', 'state' => 'current'],
            ];
        }

        $current = array_search($this->status, $pipeline, true);
        if ($current === false) {
            $current = 0;
        }

        return collect($pipeline)->map(function (string $key, int $index) use ($current): array {
            return [
                'key' => $key,
                'label' => self::STATUSES[$key],
                'state' => $index < $current ? 'done' : ($index === $current ? 'current' : 'todo'),
            ];
        })->all();
    }
}
