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
    public const VERSION = 5;

    public const LEAD_TIME_GUIDELINE = 'Please order at least one day before your delivery or pickup date. Same-day custom cakes are not available.';

    /**
     * Where the customer wants written text.
     *
     * @return array<string, string>
     */
    public static function messagePlacements(): array
    {
        return [
            'cake' => 'Text on the cake',
            'plate' => 'Text on the plate',
            'both' => 'Both cake & plate',
            'none' => 'No text',
        ];
    }

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

    public const MAX_REFERENCE_IMAGES = 5;

    /**
     * @param  array<string, mixed>  $input
     * @param  list<string>  $imagePaths  Relative paths on the public disk (e.g. custom-cakes/…)
     * @return array{
     *     key: string,
     *     name: string,
     *     unit_price: int,
     *     summary: string,
     *     options: array<string, mixed>,
     *     image: string|null,
     *     images: list<string>
     * }
     */
    public static function quote(array $input, array $imagePaths = []): array
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

        $diets = [];
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
        // Base / filling / frosting are included in the size price - no flavour surcharges.
        $extras = collect($addons)->sum(fn (array $row): int => (int) ($row['line_total'] ?? 0));

        $unitPrice = $basePrice + $extras;
        $placement = self::resolveMessagePlacement((string) ($input['message_placement'] ?? 'cake'));
        $message = $placement === 'none'
            ? ''
            : trim((string) ($input['message'] ?? ''));
        $notes = trim((string) ($input['notes'] ?? ''));
        $placementLabel = self::messagePlacements()[$placement];
        $images = array_values(array_filter(array_map(
            static fn ($path): string => trim((string) $path),
            $imagePaths
        )));

        $addonSummary = collect($addons)->map(function (array $row): string {
            $label = (string) ($row['label'] ?? 'Add-on');
            $qty = (int) ($row['qty'] ?? 1);

            return $qty > 1 ? $label.' × '.$qty : $label;
        })->all();

        $messageSummary = null;
        if ($placement === 'none') {
            $messageSummary = 'No text';
        } elseif ($message !== '') {
            $messageSummary = '“'.$message.'” · '.$placementLabel;
        }

        $summaryParts = array_filter([
            $size['label'] ?? null,
            $shape['label'] ?? null,
            $base['label'] ?? null,
            $filling['label'] ?? null,
            $frosting['label'] ?? null,
            $colorLabel.' icing',
            $messageSummary,
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
            'message_placement' => $placement,
            'message_placement_label' => $placementLabel,
            'message' => $message !== '' ? $message : null,
            'notes' => $notes !== '' ? $notes : null,
            'reference_images' => $images,
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
            'image' => $images[0] ?? null,
            'images' => $images,
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

        foreach (['bases', 'fillings', 'frostings', 'colors'] as $key) {
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
        $config['guidelines'] = self::ensureLeadTimeGuideline(array_values(array_filter(array_map(
            static fn ($row): string => trim((string) $row),
            is_array($config['guidelines'] ?? null) ? $config['guidelines'] : []
        ))));

        foreach (['sizes', 'shapes', 'bases', 'fillings', 'frostings', 'colors', 'addons'] as $key) {
            if (! isset($config[$key]) || ! is_array($config[$key])) {
                $config[$key] = [];
            }
        }

        // Dietary extras are not offered - keep key empty for a stable config shape.
        $config['diets'] = [];

        foreach (['bases', 'fillings', 'frostings'] as $key) {
            $config[$key] = self::normalizeFlavourOptions($config[$key]);
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
     * Flavour choices (base / filling / frosting) have no surcharge and coffee is not offered.
     *
     * @param  list<mixed>  $rows
     * @return list<array<string, mixed>>
     */
    private static function normalizeFlavourOptions(array $rows): array
    {
        $out = [];

        foreach ($rows as $row) {
            if (! is_array($row)) {
                continue;
            }

            $id = Str::lower(trim((string) ($row['id'] ?? '')));
            $label = trim((string) ($row['label'] ?? ''));

            if ($id === 'coffee' || Str::lower($label) === 'coffee') {
                continue;
            }

            if ($label === '') {
                continue;
            }

            $out[] = [
                'id' => $id !== '' ? $id : Str::slug($label),
                'label' => $label,
                'price' => 0,
            ];
        }

        return array_values($out);
    }

    private static function resolveMessagePlacement(string $value): string
    {
        $value = Str::lower(trim($value));

        return array_key_exists($value, self::messagePlacements()) ? $value : 'cake';
    }

    /**
     * @param  list<string>  $guidelines
     * @return list<string>
     */
    private static function ensureLeadTimeGuideline(array $guidelines): array
    {
        foreach ($guidelines as $line) {
            if (preg_match('/\b(one day|1 day|a day|day prior|at least .+ day)\b/i', $line) === 1) {
                return array_values($guidelines);
            }
        }

        return array_values([self::LEAD_TIME_GUIDELINE, ...$guidelines]);
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
