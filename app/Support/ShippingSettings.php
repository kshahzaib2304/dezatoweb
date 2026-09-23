<?php

namespace App\Support;

use App\Models\SiteSetting;
use Illuminate\Support\Facades\Config;

/**
 * Pakistan courier fee / ETA — editable from Admin.
 */
final class ShippingSettings
{
    public const KEY = 'shipping_settings';

    /**
     * @return array{fee: float, eta: string, label: string}
     */
    public static function all(): array
    {
        $stored = SiteSetting::getJson(self::KEY, []);
        $stored = is_array($stored) ? $stored : [];

        return [
            'fee' => (float) ($stored['fee'] ?? config('dezato.shipping.fee', 499)),
            'eta' => (string) ($stored['eta'] ?? config('dezato.shipping.eta', '')),
            'label' => (string) ($stored['label'] ?? config('dezato.shipping.label', 'Pakistan Courier')),
        ];
    }

    public static function fee(): float
    {
        return self::all()['fee'];
    }

    public static function eta(): string
    {
        return self::all()['eta'];
    }

    public static function label(): string
    {
        return self::all()['label'];
    }

    /**
     * @param  array{fee?: float|int|string, eta?: string, label?: string}  $data
     */
    public static function save(array $data): void
    {
        SiteSetting::putJson(self::KEY, [
            'fee' => round((float) ($data['fee'] ?? 0), 2),
            'eta' => trim((string) ($data['eta'] ?? '')),
            'label' => trim((string) ($data['label'] ?? 'Pakistan Courier')) ?: 'Pakistan Courier',
        ]);

        self::apply();
    }

    public static function apply(): void
    {
        $all = self::all();
        Config::set('dezato.shipping.fee', $all['fee']);
        Config::set('dezato.shipping.eta', $all['eta']);
        Config::set('dezato.shipping.label', $all['label']);
    }
}
