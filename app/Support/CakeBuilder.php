<?php

namespace App\Support;

use App\Models\SiteSetting;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use InvalidArgumentException;

/**
 * Custom cake builder config + live quotes (Admin-managed).
 */
final class CakeBuilder
{
    public const SETTING_KEY = 'cake_builder';

    /** Bump when default bakery pricing/rules change so existing installs hydrate once. */
    public const VERSION = 2;

    /**
     * @return array<string, mixed>
     */
    public static function config(): array
    {
        $defaults = self::defaults();
        $stored = SiteSetting::getJson(self::SETTING_KEY);

        if (! is_array($stored) || $stored === []) {
            return self::normalize($defaults);
        }

        if ((int) ($stored['version'] ?? 1) < self::VERSION) {
            $upgraded = self::upgradeFromLegacy($stored, $defaults);
            self::saveConfig($upgraded);

            return self::normalize($upgraded);
        }

        return self::normalize(array_merge($defaults, $stored));
    }

    /**
     * @param  array<string, mixed>  $config
     */
    public static function saveConfig(array $config): void
    {
        $config['version'] = self::VERSION;
        SiteSetting::putJson(self::SETTING_KEY, self::normalize($config));
    }

    /**
     * @return array<string, mixed>
     */
    public static function defaults(): array
    {
        return (array) config('dezato_ui.builder', []);
    }

    /**
     * Plain-language bakery rules shown on the builder & customization page.
     *
     * @return list<string>
     */
    public static function guidelines(): array
    {
        $rows = self::config()['guidelines'] ?? [];

        return array_values(array_filter(array_map(
            static fn ($row): string => trim((string) $row),
            is_array($rows) ? $rows : []
        )));
    }

    /**
     * @param  array<string, mixed>  $input
     * @return array{
     *     key: string,
     *     name: string,
     *     unit_price: int,
     *     summary: string,
     *     options: array<string, mixed>,
     *     image: string|null
     * }
     */
    public static function quote(array $input, ?string $imagePath = null): array
    {
        $config = self::config();

        $size = self::findOption($config['sizes'] ?? [], (string) ($input['size'] ?? ''));
        $shape = self::findOption($config['shapes'] ?? [], (string) ($input['shape'] ?? ''));
        $base = self::findOption($config['bases'] ?? [], (string) ($input['base'] ?? ''));
        $filling = self::findOption($config['fillings'] ?? [], (string) ($input['filling'] ?? ''));
        $frosting = self::findOption($config['frostings'] ?? [], (string) ($input['frosting'] ?? ''));

        if ($size === null || $shape === null || $base === null || $filling === null || $frosting === null) {
            throw new InvalidArgumentException('Please complete size, shape, and flavour choices.');
        }

        $diets = self::findOptions($config['diets'] ?? [], Arr::wrap($input['diets'] ?? []));
        $addons = self::resolveAddons($config['addons'] ?? [], $input);

        $colorId = (string) ($input['color'] ?? '');
        $colorHex = (string) ($input['color_hex'] ?? '');
        $colorLabel = 'Custom';

        if ($colorId !== 'custom') {
            $color = self::findOption($config['colors'] ?? [], $colorId);
            if ($color === null) {
                throw new InvalidArgumentException('Please choose an icing colour.');
            }
            $colorLabel = (string) $color['label'];
            $colorHex = (string) ($color['hex'] ?? $colorHex);
        } elseif ($colorHex === '') {
            $colorHex = (string) ($input['color_custom'] ?? '#c45c6a');
        }

        $basePrice = (int) ($size['price'] ?? 0);
        $extras = (int) ($base['price'] ?? 0)
            + (int) ($filling['price'] ?? 0)
            + (int) ($frosting['price'] ?? 0)
            + collect($diets)->sum(fn (array $row): int => (int) ($row['price'] ?? 0))
            + collect($addons)->sum(fn (array $row): int => (int) ($row['line_total'] ?? 0));

        $unitPrice = $basePrice + $extras;
        $message = trim((string) ($input['message'] ?? ''));
        $notes = trim((string) ($input['notes'] ?? ''));

        $addonSummary = collect($addons)->map(function (array $row): string {
            $label = (string) ($row['label'] ?? 'Add-on');
            $qty = (int) ($row['qty'] ?? 1);

            return $qty > 1 ? $label.' × '.$qty : $label;
        })->all();

        $summaryParts = array_filter([
            $size['label'] ?? null,
            $shape['label'] ?? null,
            $base['label'] ?? null,
            $filling['label'] ?? null,
            $frosting['label'] ?? null,
            $colorLabel.' icing',
            $message !== '' ? '“'.$message.'”' : null,
            ...$addonSummary,
        ]);

        $options = [
            'size' => $size,
            'shape' => $shape,
            'base' => $base,
            'filling' => $filling,
            'frosting' => $frosting,
            'diets' => $diets,
            'addons' => $addons,
            'color' => [
                'id' => $colorId,
                'label' => $colorLabel,
                'hex' => $colorHex,
            ],
            'message' => $message !== '' ? $message : null,
            'notes' => $notes !== '' ? $notes : null,
            'pricing' => [
                'base' => $basePrice,
                'extras' => $extras,
                'total' => $unitPrice,
            ],
        ];

        return [
            'key' => 'custom-'.Str::lower(Str::random(10)),
            'name' => 'Custom Cake ('.$size['label'].')',
            'unit_price' => $unitPrice,
            'summary' => implode(' · ', $summaryParts),
            'options' => $options,
            'image' => $imagePath,
        ];
    }

    /**
     * @param  array<string, mixed>  $stored
     * @param  array<string, mixed>  $defaults
     * @return array<string, mixed>
     */
    private static function upgradeFromLegacy(array $stored, array $defaults): array
    {
        $merged = $defaults;

        foreach (['bases', 'fillings', 'frostings', 'diets', 'colors'] as $key) {
            if (! empty($stored[$key]) && is_array($stored[$key])) {
                $merged[$key] = $stored[$key];
            }
        }

        $merged['version'] = self::VERSION;

        return $merged;
    }

    /**
     * @param  array<string, mixed>  $config
     * @return array<string, mixed>
     */
    private static function normalize(array $config): array
    {
        $config['version'] = self::VERSION;
        $config['guidelines'] = array_values(array_filter(array_map(
            static fn ($row): string => trim((string) $row),
            is_array($config['guidelines'] ?? null) ? $config['guidelines'] : []
        )));

        foreach (['sizes', 'shapes', 'bases', 'fillings', 'frostings', 'diets', 'colors', 'addons'] as $key) {
            if (! isset($config[$key]) || ! is_array($config[$key])) {
                $config[$key] = [];
            }
        }

        $config['addons'] = array_values(array_map(static function (array $row): array {
            $billing = ($row['billing'] ?? 'flat') === 'per_unit' ? 'per_unit' : 'flat';

            return [
                'id' => (string) ($row['id'] ?? Str::slug((string) ($row['label'] ?? 'addon'))),
                'label' => trim((string) ($row['label'] ?? '')),
                'price' => (int) ($row['price'] ?? 0),
                'billing' => $billing,
                'unit_label' => trim((string) ($row['unit_label'] ?? '')),
                'hint' => trim((string) ($row['hint'] ?? '')),
                'max_qty' => max(1, min(50, (int) ($row['max_qty'] ?? 12))),
            ];
        }, $config['addons']));

        return $config;
    }

    /**
     * @param  list<array<string, mixed>>  $addons
     * @param  array<string, mixed>  $input
     * @return list<array<string, mixed>>
     */
    private static function resolveAddons(array $addons, array $input): array
    {
        $flatSelected = collect(Arr::wrap($input['addons'] ?? []))
            ->filter()
            ->map(static fn ($id): string => (string) $id)
            ->unique()
            ->all();

        $qtyMap = is_array($input['addon_qty'] ?? null) ? $input['addon_qty'] : [];
        $selected = [];

        foreach ($addons as $addon) {
            if (! is_array($addon) || ($addon['label'] ?? '') === '') {
                continue;
            }

            $id = (string) ($addon['id'] ?? '');
            $price = (int) ($addon['price'] ?? 0);
            $billing = ($addon['billing'] ?? 'flat') === 'per_unit' ? 'per_unit' : 'flat';

            if ($billing === 'per_unit') {
                $qty = max(0, min((int) ($addon['max_qty'] ?? 12), (int) ($qtyMap[$id] ?? 0)));
                if ($qty < 1) {
                    continue;
                }

                $selected[] = array_merge($addon, [
                    'qty' => $qty,
                    'line_total' => $price * $qty,
                ]);

                continue;
            }

            if (! in_array($id, $flatSelected, true)) {
                continue;
            }

            $selected[] = array_merge($addon, [
                'qty' => 1,
                'line_total' => $price,
            ]);
        }

        return $selected;
    }

    /**
     * @param  list<array<string, mixed>>  $items
     * @return array<string, mixed>|null
     */
    private static function findOption(array $items, string $id): ?array
    {
        if ($id === '') {
            return null;
        }

        foreach ($items as $item) {
            if (($item['id'] ?? null) === $id) {
                return $item;
            }
        }

        return null;
    }

    /**
     * @param  list<array<string, mixed>>  $items
     * @param  list<string>  $ids
     * @return list<array<string, mixed>>
     */
    private static function findOptions(array $items, array $ids): array
    {
        $wanted = collect($ids)->filter()->unique()->values()->all();
        $matched = [];

        foreach ($wanted as $id) {
            $row = self::findOption($items, (string) $id);
            if ($row !== null) {
                $matched[] = $row;
            }
        }

        return $matched;
    }
}
