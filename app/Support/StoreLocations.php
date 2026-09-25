<?php

namespace App\Support;

use App\Models\SiteSetting;
use Illuminate\Support\Str;

/**
 * Bakery store locations - editable from Admin (addresses, hours, map link, photo).
 */
final class StoreLocations
{
    public const KEY = 'store_locations';

    /**
     * @return list<array<string, mixed>>
     */
    public static function all(): array
    {
        $stored = SiteSetting::getJson(self::KEY);

        if (is_array($stored) && $stored !== []) {
            return array_values(array_map([self::class, 'normalize'], $stored));
        }

        return array_values(array_map(
            [self::class, 'normalize'],
            config('dezato.locations', [])
        ));
    }

    /**
     * @return list<array<string, mixed>>
     */
    public static function forStorefront(): array
    {
        return collect(self::all())
            ->filter(fn (array $row): bool => (bool) ($row['active'] ?? true))
            ->map(function (array $row): array {
                $row['image'] = MediaPaths::public($row['image'] ?? null, 'images/home/delivery-pickup.jpg');
                $row['phone'] = $row['phone'] !== '' ? $row['phone'] : BakeryProfile::phone();
                $row['services'] = is_array($row['services'] ?? null)
                    ? array_values(array_filter($row['services']))
                    : [];

                return $row;
            })
            ->values()
            ->all();
    }

    /**
     * @param  list<array<string, mixed>>  $rows
     */
    public static function save(array $rows): void
    {
        $clean = [];

        foreach ($rows as $row) {
            if (! is_array($row)) {
                continue;
            }

            $clean[] = self::normalize($row);
        }

        SiteSetting::putJson(self::KEY, array_values($clean));
    }

    public static function appendBlank(): void
    {
        $rows = self::all();
        $rows[] = self::normalize([
            'name' => 'New bakery location',
            'city' => 'Karachi',
            'region' => 'Sindh',
            'address' => '',
            'hours' => 'Daily 10:00 AM – 10:00 PM',
            'phone' => '',
            'map_url' => '',
            'services' => ['Pickup', 'Delivery'],
            'delivers' => true,
            'delivery_fee' => 199,
            'image' => '',
            'active' => true,
        ]);
        self::save($rows);
    }

    public static function remove(string $id): bool
    {
        $rows = self::all();

        if (count($rows) <= 1) {
            return false;
        }

        $filtered = array_values(array_filter(
            $rows,
            fn (array $row): bool => ($row['id'] ?? '') !== $id
        ));

        if (count($filtered) === count($rows)) {
            return false;
        }

        $removed = collect($rows)->firstWhere('id', $id);
        if (is_array($removed)) {
            MediaPaths::deleteIfOwned($removed['image'] ?? null);
        }

        self::save($filtered);

        return true;
    }

    /**
     * @param  array<string, mixed>  $row
     * @return array<string, mixed>
     */
    public static function normalize(array $row): array
    {
        $services = $row['services'] ?? [];

        if (is_string($services)) {
            $services = preg_split('/\s*,\s*/', $services) ?: [];
        }

        $id = trim((string) ($row['id'] ?? ''));
        if ($id === '') {
            $id = Str::slug((string) ($row['name'] ?? 'location')).'-'.Str::lower(Str::random(4));
        }

        return [
            'id' => $id,
            'name' => trim((string) ($row['name'] ?? '')),
            'city' => trim((string) ($row['city'] ?? 'Karachi')),
            'region' => trim((string) ($row['region'] ?? 'Sindh')),
            'address' => trim((string) ($row['address'] ?? '')),
            'hours' => trim((string) ($row['hours'] ?? '')),
            'phone' => trim((string) ($row['phone'] ?? '')),
            'map_url' => trim((string) ($row['map_url'] ?? '')),
            'services' => array_values(array_filter(array_map('trim', (array) $services))),
            'delivers' => (bool) ($row['delivers'] ?? true),
            'delivery_fee' => (float) ($row['delivery_fee'] ?? 0),
            'image' => trim((string) ($row['image'] ?? '')),
            'active' => (bool) ($row['active'] ?? true),
        ];
    }
}
