<?php

namespace App\Support;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Collection;

final class Catalog
{
    /** @var array<string, Collection<int, mixed>> */
    private static array $memo = [];

    public static function products(): Collection
    {
        return self::$memo['products'] ??= self::loadProducts();
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    private static function loadProducts(): Collection
    {
        $products = Product::query()
            ->with('category')
            ->active()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        if ($products->isEmpty() && ! Product::query()->exists()) {
            return collect(config('dezato.menu.products', []));
        }

        return $products
            ->map(fn (Product $product): array => $product->toCatalogArray())
            ->values();
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

    /**
     * Badge-highlighted products for cart / checkout upsells.
     *
     * @return list<array<string, mixed>>
     */
    public static function upsells(int $limit = 3): array
    {
        $limit = max(1, $limit);

        $featured = self::products()
            ->filter(fn (array $product): bool => ($product['badge'] ?? null) !== null)
            ->take($limit)
            ->values();

        if ($featured->count() >= $limit) {
            return $featured->all();
        }

        return self::products()
            ->take($limit)
            ->values()
            ->all();
    }

    public static function categories(): Collection
    {
        return self::$memo['categories'] ??= self::loadCategories();
    }

    /**
     * @return Collection<int, array{id: string, label: string}>
     */
    private static function loadCategories(): Collection
    {
        $rows = Category::query()->orderBy('sort_order')->get(['slug', 'label', 'is_active']);

        if ($rows->isEmpty()) {
            return collect(config('dezato.menu.categories', []));
        }

        $items = $rows
            ->where('is_active', true)
            ->map(fn (Category $category): array => [
                'id' => $category->slug,
                'label' => $category->label,
            ])
            ->values();

        return collect([['id' => 'all', 'label' => 'All']])->merge($items);
    }

    public static function locations(): Collection
    {
        return self::$memo['locations'] ??= collect(StoreLocations::forStorefront());
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
