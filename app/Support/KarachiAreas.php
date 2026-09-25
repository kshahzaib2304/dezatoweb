<?php

namespace App\Support;

use App\Models\SiteSetting;
use Illuminate\Support\Str;

final class KarachiAreas
{
    public const KEY = 'karachi_areas';

    /**
     * @return list<array{id: string, label: string, location_id: string}>
     */
    public static function all(): array
    {
        $stored = SiteSetting::getJson(self::KEY);
        $rows = is_array($stored) && $stored !== []
            ? $stored
            : config('dezato.karachi_areas', []);

        if (! is_array($rows)) {
            return [];
        }

        $out = [];

        foreach ($rows as $row) {
            if (! is_array($row)) {
                continue;
            }

            $id = trim((string) ($row['id'] ?? ''));
            $label = trim((string) ($row['label'] ?? ''));
            $locationId = trim((string) ($row['location_id'] ?? ''));

            if ($label === '' || $locationId === '') {
                continue;
            }

            if ($id === '') {
                $id = Str::slug($label);
            }

            $out[] = [
                'id' => $id,
                'label' => $label,
                'location_id' => $locationId,
            ];
        }

        usort($out, static fn (array $a, array $b): int => strcasecmp($a['label'], $b['label']));

        return $out;
    }

    /**
     * @param  list<array{id?: string, label?: string, location_id?: string}>  $rows
     */
    public static function save(array $rows): void
    {
        $clean = [];

        foreach ($rows as $row) {
            if (! is_array($row)) {
                continue;
            }

            $label = trim((string) ($row['label'] ?? ''));
            $locationId = trim((string) ($row['location_id'] ?? ''));
            $id = trim((string) ($row['id'] ?? ''));

            if ($label === '' || $locationId === '') {
                continue;
            }

            $clean[] = [
                'id' => $id !== '' ? $id : Str::slug($label),
                'label' => $label,
                'location_id' => $locationId,
            ];
        }

        SiteSetting::putJson(self::KEY, $clean);
    }

    public static function append(): void
    {
        $rows = self::all();
        $rows[] = [
            'id' => 'area-'.Str::lower(Str::random(4)),
            'label' => 'New area',
            'location_id' => Catalog::locations()->first()['id'] ?? 'dha-phase-6',
        ];
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
            static fn (array $row): bool => $row['id'] !== $id
        ));

        if (count($filtered) === count($rows)) {
            return false;
        }

        self::save($filtered);

        return true;
    }

    /**
     * @return list<string>
     */
    public static function ids(): array
    {
        return array_column(self::all(), 'id');
    }

    /**
     * @return array{id: string, label: string, location_id: string}|null
     */
    public static function find(string $id): ?array
    {
        foreach (self::all() as $area) {
            if ($area['id'] === $id) {
                return $area;
            }
        }

        return null;
    }

    /**
     * @return list<array{id: string, label: string, location_id: string}>
     */
    public static function forLocation(string $locationId): array
    {
        return array_values(array_filter(
            self::all(),
            static fn (array $area): bool => $area['location_id'] === $locationId
        ));
    }
}
