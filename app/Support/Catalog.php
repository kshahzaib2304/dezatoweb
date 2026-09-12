<?php

namespace App\Support;

use Illuminate\Support\Collection;

final class Catalog
{
    public static function products(): Collection
    {
        return collect(config('dezato.menu.products', []));
    }

    public static function findProduct(string $id): ?array
    {
        $product = self::products()->firstWhere('id', $id);

        return is_array($product) ? $product : null;
    }

    public static function locations(): Collection
    {
        return collect(config('dezato.locations', []));
    }

    public static function findLocation(string $id): ?array
    {
        $location = self::locations()->firstWhere('id', $id);

        return is_array($location) ? $location : null;
    }

    public static function deliveryLocations(): Collection
    {
        return self::locations()->where('delivers', true)->values();
    }

    public static function categoryLabel(string $categoryId): string
    {
        return collect(config('dezato.menu.categories', []))
            ->firstWhere('id', $categoryId)['label'] ?? ucfirst($categoryId);
    }
}
