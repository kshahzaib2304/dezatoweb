<?php

namespace App\Support;

use App\Models\SiteSetting;
use Illuminate\Support\Str;

/**
 * Homepage category shortcuts + occasion tiles (Admin-managed).
 */
final class HomeShowcase
{
    public const KEY = 'home_showcase';

    /**
     * @return array{categories: list<array<string, mixed>>, occasions: list<array<string, mixed>>}
     */
    public static function config(): array
    {
        $stored = SiteSetting::getJson(self::KEY, []);
        $stored = is_array($stored) ? $stored : [];

        return [
            'categories' => self::normalizeList(
                is_array($stored['categories'] ?? null) ? $stored['categories'] : config('dezato.home.products', []),
                withTone: true
            ),
            'occasions' => self::normalizeList(
                is_array($stored['occasions'] ?? null) ? $stored['occasions'] : config('dezato.home.occasions', []),
                withTone: false
            ),
        ];
    }

    /**
     * @return list<array{label: string, image: string, href: string, tone?: string}>
     */
    public static function categories(): array
    {
        return self::forDisplay(self::config()['categories']);
    }

    /**
     * @return list<array{label: string, image: string, href: string}>
     */
    public static function occasions(): array
    {
        return self::forDisplay(self::config()['occasions']);
    }

    /**
     * @param  array{categories?: list<array<string, mixed>>, occasions?: list<array<string, mixed>>}  $data
     */
    public static function save(array $data): void
    {
        SiteSetting::putJson(self::KEY, [
            'categories' => self::normalizeList($data['categories'] ?? [], withTone: true),
            'occasions' => self::normalizeList($data['occasions'] ?? [], withTone: false),
        ]);
    }

    /**
     * @param  list<array<string, mixed>>  $rows
     * @return list<array<string, mixed>>
     */
    private static function normalizeList(array $rows, bool $withTone): array
    {
        $out = [];

        foreach ($rows as $row) {
            if (! is_array($row)) {
                continue;
            }

            $label = trim((string) ($row['label'] ?? ''));
            if ($label === '') {
                continue;
            }

            $item = [
                'id' => (string) ($row['id'] ?? Str::slug($label).'-'.Str::lower(Str::random(3))),
                'label' => $label,
                'href' => trim((string) ($row['href'] ?? '/menu')),
                'image' => trim((string) ($row['image'] ?? '')),
            ];

            if ($withTone) {
                $item['tone'] = trim((string) ($row['tone'] ?? 'cream')) ?: 'cream';
            }

            $out[] = $item;
        }

        return $out;
    }

    /**
     * @param  list<array<string, mixed>>  $rows
     * @return list<array<string, mixed>>
     */
    private static function forDisplay(array $rows): array
    {
        return array_map(function (array $row): array {
            $row['image'] = MediaPaths::public($row['image'] ?? null, 'images/home/hero.jpg');

            return $row;
        }, $rows);
    }
}
