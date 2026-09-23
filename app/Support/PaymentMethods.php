<?php

namespace App\Support;

use App\Models\SiteSetting;

final class PaymentMethods
{
    public const SETTING_KEY = 'payment_methods';

    public const INSTRUCTIONS_KEY = 'payment_instructions';

    /**
     * @return list<array{id: string, label: string, hint: string, enabled_by_default: bool, live: bool, type: string}>
     */
    public static function catalog(): array
    {
        return [
            [
                'id' => 'cod',
                'label' => 'Cash on delivery / pickup',
                'hint' => 'Customer pays in PKR when the order arrives or at pickup',
                'enabled_by_default' => true,
                'live' => true,
                'type' => 'cash',
            ],
            [
                'id' => 'pay_later',
                'label' => 'Pay at bakery',
                'hint' => 'Confirm the order now — payment when they collect',
                'enabled_by_default' => true,
                'live' => true,
                'type' => 'cash',
            ],
            [
                'id' => 'bank_transfer',
                'label' => 'Bank transfer',
                'hint' => 'Customer transfers to your bank account, then you confirm payment',
                'enabled_by_default' => false,
                'live' => true,
                'type' => 'transfer',
            ],
            [
                'id' => 'jazzcash',
                'label' => 'JazzCash',
                'hint' => 'Customer sends payment to your JazzCash number',
                'enabled_by_default' => false,
                'live' => true,
                'type' => 'transfer',
            ],
            [
                'id' => 'easypaisa',
                'label' => 'Easypaisa',
                'hint' => 'Customer sends payment to your Easypaisa number',
                'enabled_by_default' => false,
                'live' => true,
                'type' => 'transfer',
            ],
            [
                'id' => 'card',
                'label' => 'Credit / Debit card',
                'hint' => 'Online card gateway — needs a payment partner to connect later',
                'enabled_by_default' => false,
                'live' => false,
                'type' => 'gateway',
            ],
        ];
    }

    /**
     * @return list<string>
     */
    public static function enabledIds(): array
    {
        $stored = SiteSetting::getJson(self::SETTING_KEY);
        $defaults = collect(self::catalog())
            ->filter(fn (array $method): bool => $method['enabled_by_default'])
            ->pluck('id')
            ->all();

        if (! is_array($stored) || $stored === []) {
            return $defaults;
        }

        $allowed = collect(self::catalog())->pluck('id')->all();

        return collect($stored)
            ->filter(fn ($id) => in_array($id, $allowed, true))
            ->values()
            ->all() ?: $defaults;
    }

    /**
     * @param  list<string>  $ids
     */
    public static function saveEnabled(array $ids): void
    {
        $allowed = collect(self::catalog())->pluck('id')->all();
        $clean = collect($ids)
            ->filter(fn ($id) => in_array($id, $allowed, true))
            ->unique()
            ->values()
            ->all();

        if ($clean === []) {
            $clean = ['cod'];
        }

        SiteSetting::putJson(self::SETTING_KEY, $clean);
    }

    /**
     * @return array<string, array<string, string>>
     */
    public static function instructions(): array
    {
        $defaults = [
            'bank_transfer' => [
                'account_name' => '',
                'bank_name' => '',
                'account_number' => '',
                'iban' => '',
                'notes' => 'Please use your order number as the payment reference.',
            ],
            'jazzcash' => [
                'account_name' => '',
                'account_number' => '',
                'notes' => 'Send the exact order total and share the screenshot with us.',
            ],
            'easypaisa' => [
                'account_name' => '',
                'account_number' => '',
                'notes' => 'Send the exact order total and share the screenshot with us.',
            ],
        ];

        $stored = SiteSetting::getJson(self::INSTRUCTIONS_KEY, []);

        if (! is_array($stored)) {
            return $defaults;
        }

        foreach ($defaults as $key => $fields) {
            $defaults[$key] = array_merge($fields, is_array($stored[$key] ?? null) ? $stored[$key] : []);
        }

        return $defaults;
    }

    /**
     * @param  array<string, array<string, string>>  $instructions
     */
    public static function saveInstructions(array $instructions): void
    {
        $clean = [];

        foreach (self::instructions() as $method => $defaults) {
            $row = is_array($instructions[$method] ?? null) ? $instructions[$method] : [];
            $clean[$method] = [];
            foreach (array_keys($defaults) as $field) {
                $clean[$method][$field] = trim((string) ($row[$field] ?? ''));
            }
        }

        SiteSetting::putJson(self::INSTRUCTIONS_KEY, $clean);
    }

    /**
     * @return array<string, string>|null
     */
    public static function instructionsFor(string $id): ?array
    {
        $all = self::instructions();

        return $all[$id] ?? null;
    }

    public static function isTransfer(string $id): bool
    {
        $method = collect(self::catalog())->firstWhere('id', $id);

        return ($method['type'] ?? null) === 'transfer';
    }

    /**
     * @return list<array{id: string, label: string, hint: string, live: bool, type: string, instructions: ?array}>
     */
    public static function forCheckout(): array
    {
        $enabled = self::enabledIds();

        return collect(self::catalog())
            ->filter(fn (array $method): bool => in_array($method['id'], $enabled, true))
            ->map(fn (array $method): array => [
                'id' => $method['id'],
                'label' => $method['label'],
                'hint' => $method['hint'],
                'live' => $method['live'],
                'type' => $method['type'],
                'instructions' => self::isTransfer($method['id']) ? self::instructionsFor($method['id']) : null,
            ])
            ->values()
            ->all();
    }

    public static function label(string $id): string
    {
        return collect(self::catalog())->firstWhere('id', $id)['label']
            ?? str_replace('_', ' ', ucfirst($id));
    }

    public static function isUnpaidMethod(string $id): bool
    {
        return in_array($id, ['cod', 'pay_later'], true);
    }

    public static function paymentStatusFor(string $id): string
    {
        if (self::isTransfer($id)) {
            return \App\Models\Order::PAYMENT_PENDING;
        }

        if (self::isUnpaidMethod($id)) {
            return \App\Models\Order::PAYMENT_UNPAID;
        }

        return \App\Models\Order::PAYMENT_PENDING;
    }
}
