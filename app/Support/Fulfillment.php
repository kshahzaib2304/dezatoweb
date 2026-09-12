<?php

namespace App\Support;

use Illuminate\Contracts\Session\Session;

final class Fulfillment
{
    public const SESSION_KEY = 'dezato.fulfillment';

    public const WELCOME_KEY = 'dezato.welcome_seen';

    public const METHOD_PICKUP = 'pickup';

    public const METHOD_DELIVERY = 'delivery';

    public const METHOD_SHIPPING = 'shipping';

    public const METHODS = [
        self::METHOD_PICKUP,
        self::METHOD_DELIVERY,
        self::METHOD_SHIPPING,
    ];

    public function __construct(private readonly Session $session) {}

    public function has(): bool
    {
        $data = $this->get();

        if (! is_array($data) || ! in_array($data['method'] ?? null, self::METHODS, true)) {
            return false;
        }

        return match ($data['method']) {
            self::METHOD_SHIPPING => ! empty($data['address'])
                && ! empty($data['city'])
                && ! empty($data['region'])
                && ! empty($data['postal_code']),
            default => ! empty($data['location_id']),
        };
    }

    /**
     * @return array<string, mixed>|null
     */
    public function get(): ?array
    {
        $data = $this->session->get(self::SESSION_KEY);

        return is_array($data) ? $data : null;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function put(array $data): void
    {
        $this->session->put(self::SESSION_KEY, [
            ...$data,
            'saved_at' => now()->toIso8601String(),
        ]);
        $this->markWelcomeSeen();
    }

    public function clear(): void
    {
        $this->session->forget(self::SESSION_KEY);
    }

    public function welcomeSeen(): bool
    {
        return (bool) $this->session->get(self::WELCOME_KEY, false);
    }

    public function markWelcomeSeen(): void
    {
        $this->session->put(self::WELCOME_KEY, true);
    }

    public function fee(): float
    {
        $data = $this->get();

        if ($data === null) {
            return 0.0;
        }

        return round((float) ($data['fee'] ?? $data['delivery_fee'] ?? 0), 2);
    }

    public function methodLabel(): ?string
    {
        return match ($this->get()['method'] ?? null) {
            self::METHOD_PICKUP => 'Pickup',
            self::METHOD_DELIVERY => 'Delivery',
            self::METHOD_SHIPPING => 'Courier',
            default => null,
        };
    }

    public function feeLabel(): ?string
    {
        return match ($this->get()['method'] ?? null) {
            self::METHOD_DELIVERY => 'Delivery',
            self::METHOD_SHIPPING => 'Courier',
            default => null,
        };
    }

    public function summary(): ?string
    {
        $data = $this->get();

        if ($data === null) {
            return null;
        }

        return match ($data['method']) {
            self::METHOD_DELIVERY => 'Delivery · '.($data['address'] ?? $data['location_name'] ?? 'Karachi'),
            self::METHOD_SHIPPING => 'Courier · '.trim(($data['city'] ?? '').', '.($data['region'] ?? ''), ' ,'),
            default => 'Pickup · '.($data['location_name'] ?? 'Store'),
        };
    }

    public function paymentHint(): string
    {
        return match ($this->get()['method'] ?? null) {
            self::METHOD_DELIVERY => 'Pay on delivery (PKR)',
            self::METHOD_SHIPPING => 'Pay with courier fulfillment (PKR)',
            default => 'Pay at pickup (PKR)',
        };
    }
}
