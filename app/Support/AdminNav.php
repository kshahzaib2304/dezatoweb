<?php

namespace App\Support;

use Illuminate\Support\Facades\Route;

/**
 * Admin sidebar navigation. Always returns grouped shape for layouts.admin.
 * Accepts legacy flat config (list of links) so partial deploys / stale config
 * caches cannot break the admin shell.
 */
final class AdminNav
{
    /**
     * @return list<array{label: string|null, items: list<array{id: string, label: string, route: string}>}>
     */
    public static function groups(): array
    {
        $raw = config('dezato_admin.nav', []);

        if (! is_array($raw) || $raw === []) {
            return [];
        }

        $first = $raw[0] ?? null;

        if (! is_array($first)) {
            return [];
        }

        if (array_key_exists('items', $first)) {
            $groups = [];

            foreach ($raw as $group) {
                if (! is_array($group)) {
                    continue;
                }

                $items = self::normalizeItems($group['items'] ?? []);

                if ($items === []) {
                    continue;
                }

                $label = $group['label'] ?? null;
                $groups[] = [
                    'label' => is_string($label) && $label !== '' ? $label : null,
                    'items' => $items,
                ];
            }

            return $groups;
        }

        // Legacy flat list: [{id, label, route}, ...]
        $items = self::normalizeItems($raw);

        return $items === []
            ? []
            : [['label' => null, 'items' => $items]];
    }

    /**
     * @param  mixed  $items
     * @return list<array{id: string, label: string, route: string}>
     */
    private static function normalizeItems(mixed $items): array
    {
        if (! is_array($items)) {
            return [];
        }

        $out = [];

        foreach ($items as $item) {
            $normalized = self::normalizeItem($item);

            if ($normalized !== null) {
                $out[] = $normalized;
            }
        }

        return $out;
    }

    /**
     * @return array{id: string, label: string, route: string}|null
     */
    private static function normalizeItem(mixed $item): ?array
    {
        if (! is_array($item)) {
            return null;
        }

        $id = trim((string) ($item['id'] ?? ''));
        $label = trim((string) ($item['label'] ?? ''));
        $route = trim((string) ($item['route'] ?? ''));

        if ($id === '' || $label === '' || $route === '') {
            return null;
        }

        if (! Route::has($route)) {
            return null;
        }

        return [
            'id' => $id,
            'label' => $label,
            'route' => $route,
        ];
    }
}
