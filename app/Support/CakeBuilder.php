<?php

namespace App\Support;

use App\Models\SiteSetting;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use InvalidArgumentException;

final class CakeBuilder
{
    public const SETTING_KEY = 'cake_builder';

    /**
     * @return array<string, mixed>
     */
    public static function config(): array
    {
        $stored = SiteSetting::getJson(self::SETTING_KEY);

        return is_array($stored) && $stored !== []
            ? $stored
            : (array) config('dezato_ui.builder', []);
    }

    /**
     * @param  array<string, mixed>  $config
     */
    public static function saveConfig(array $config): void
    {
        SiteSetting::putJson(self::SETTING_KEY, $config);
    }

    /**
     * Build a priced custom cake line from validated request input.
     *
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
        $addons = self::findOptions($config['addons'] ?? [], Arr::wrap($input['addons'] ?? []));

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
            + collect($addons)->sum(fn (array $row): int => (int) ($row['price'] ?? 0));

        $unitPrice = $basePrice + $extras;
        $message = trim((string) ($input['message'] ?? ''));
        $notes = trim((string) ($input['notes'] ?? ''));

        $summaryParts = array_filter([
            $size['label'] ?? null,
            $shape['label'] ?? null,
            $base['label'] ?? null,
            $filling['label'] ?? null,
            $frosting['label'] ?? null,
            $colorLabel.' icing',
            $message !== '' ? '“'.$message.'”' : null,
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
