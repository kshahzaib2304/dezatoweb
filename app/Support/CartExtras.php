<?php

namespace App\Support;

/**
 * Present cake-builder options as priced “extras” for cart UIs.
 */
final class CartExtras
{
    /**
     * @param  array<string, mixed>|null  $options
     * @return list<array{label: string, meta: string|null, amount: int|null}>
     */
    public static function rows(?array $options): array
    {
        if ($options === null || $options === []) {
            return [];
        }

        $rows = [];

        foreach (['size', 'shape', 'base', 'filling', 'frosting'] as $key) {
            $row = $options[$key] ?? null;
            if (! is_array($row) || empty($row['label'])) {
                continue;
            }

            $price = (int) ($row['price'] ?? 0);
            $rows[] = [
                'label' => (string) $row['label'],
                'meta' => match ($key) {
                    'size' => 'Size',
                    'shape' => 'Shape',
                    'base' => 'Base',
                    'filling' => 'Filling',
                    'frosting' => 'Frosting',
                    default => null,
                },
                'amount' => $key === 'size' ? null : ($price > 0 ? $price : null),
            ];
        }

        if (! empty($options['color']['label'])) {
            $rows[] = [
                'label' => (string) $options['color']['label'].' icing',
                'meta' => 'Colour',
                'amount' => null,
            ];
        }

        foreach ($options['diets'] ?? [] as $diet) {
            if (! is_array($diet) || empty($diet['label'])) {
                continue;
            }

            $price = (int) ($diet['price'] ?? 0);
            $rows[] = [
                'label' => (string) $diet['label'],
                'meta' => 'Dietary',
                'amount' => $price > 0 ? $price : null,
            ];
        }

        foreach ($options['addons'] ?? [] as $addon) {
            if (! is_array($addon) || empty($addon['label'])) {
                continue;
            }

            $qty = max(1, (int) ($addon['qty'] ?? 1));
            $label = (string) $addon['label'];
            $meta = $qty > 1 ? '× '.$qty : 'Extra';
            $amount = (int) ($addon['line_total'] ?? ((int) ($addon['price'] ?? 0) * $qty));

            $rows[] = [
                'label' => $label,
                'meta' => $meta,
                'amount' => $amount > 0 ? $amount : null,
            ];
        }

        if (! empty($options['message'])) {
            $rows[] = [
                'label' => '“'.(string) $options['message'].'”',
                'meta' => 'Message',
                'amount' => null,
            ];
        }

        return $rows;
    }

    /**
     * @param  array<string, mixed>|null  $options
     */
    public static function extrasTotal(?array $options): int
    {
        if ($options === null) {
            return 0;
        }

        return (int) ($options['pricing']['extras'] ?? 0);
    }
}
