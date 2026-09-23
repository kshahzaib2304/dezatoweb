<?php

namespace App\Support;

use App\Models\SiteSetting;

/**
 * Public brand name / short label — editable from Admin.
 */
final class SiteBrand
{
    public const KEY = 'site_brand';

    /**
     * @return array{name: string, short_name: string, tagline: string}
     */
    public static function all(): array
    {
        $stored = SiteSetting::getJson(self::KEY, []);
        $stored = is_array($stored) ? $stored : [];

        return [
            'name' => trim((string) ($stored['name'] ?? config('dezato.brand.name', 'Dezato Cake House')))
                ?: 'Dezato Cake House',
            'short_name' => trim((string) ($stored['short_name'] ?? 'Dezato')) ?: 'Dezato',
            'tagline' => trim((string) ($stored['tagline'] ?? config('dezato.brand.tagline', '')))
                ?: 'Handcrafted cakes & desserts in Karachi',
        ];
    }

    public static function name(): string
    {
        return self::all()['name'];
    }

    public static function shortName(): string
    {
        return self::all()['short_name'];
    }

    public static function tagline(): string
    {
        return self::all()['tagline'];
    }

    /**
     * @param  array{name?: string, short_name?: string, tagline?: string}  $data
     */
    public static function save(array $data): void
    {
        SiteSetting::putJson(self::KEY, [
            'name' => trim((string) ($data['name'] ?? '')) ?: 'Dezato Cake House',
            'short_name' => trim((string) ($data['short_name'] ?? '')) ?: 'Dezato',
            'tagline' => trim((string) ($data['tagline'] ?? '')),
        ]);
    }
}
