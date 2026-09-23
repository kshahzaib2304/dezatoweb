<?php

namespace App\Support;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Collection;

final class Catalog
{
    public static function products(): Collection
    {
        if (Product::query()->exists()) {
            return Product::query()
                ->with('category')
                ->active()
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get()
                ->map(fn (Product $product): array => $product->toCatalogArray())
                ->values();
        }

        return collect(config('dezato.menu.products', []));
    }

    public static function findProduct(string $id): ?array
    {
        $product = Product::query()->with('category')->where('slug', $id)->active()->first();

        if ($product) {
            return $product->toCatalogArray();
        }

        $fallback = collect(config('dezato.menu.products', []))->firstWhere('id', $id);

        return is_array($fallback) ? $fallback : null;
    }

    public static function categories(): Collection
    {
        if (Category::query()->exists()) {
            $items = Category::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get()
                ->map(fn (Category $category): array => [
                    'id' => $category->slug,
                    'label' => $category->label,
                ])
                ->values();

            return collect([['id' => 'all', 'label' => 'All']])->merge($items);
        }

        return collect(config('dezato.menu.categories', []));
    }

    public static function locations(): Collection
    {
        return collect(StoreLocations::forStorefront());
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
        return self::categories()->firstWhere('id', $categoryId)['label']
            ?? ucfirst($categoryId);
    }
}
