<?php

namespace App\Support;

final class KarachiAreas
{
    /**
     * @return list<array{id: string, label: string, location_id: string}>
     */
    public static function all(): array
    {
        $rows = config('dezato.karachi_areas', []);

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

            if ($id === '' || $label === '' || $locationId === '') {
                continue;
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
     * Prefer areas that map to the given bakery counter.
     *
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
