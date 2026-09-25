<?php

namespace App\Support;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;
use Illuminate\Support\Collection;

/**
 * Filtered, sorted, paginated menu listings for the storefront PLP.
 */
final class MenuListing
{
    public const PER_PAGE = 12;

    /**
     * @return array{
     *     category: string,
     *     categories: list<array{id: string, label: string}>,
     *     filters: array{q: string, sort: string, weight: string},
     *     weights: list<string>,
     *     products: LengthAwarePaginator,
     *     category_label: string
     * }
     */
    public static function fromRequest(Request $request): array
    {
        $categories = Catalog::categories()->all();
        $validIds = collect($categories)->pluck('id')->all();

        $category = $request->string('category')->toString() ?: 'all';
        if (! in_array($category, $validIds, true)) {
            $category = 'all';
        }

        $query = $request->string('q')->trim()->toString();
        $sort = $request->string('sort')->toString() ?: 'featured';
        $weight = $request->string('weight')->toString();
        $page = max(1, (int) $request->integer('page', 1));

        $filtered = self::filteredProducts($category, $query, $weight, $sort);
        $paginator = self::paginate($filtered, $page, $request);

        $categoryLabel = $category === 'all'
            ? 'All desserts'
            : Catalog::categoryLabel($category);

        return [
            'category' => $category,
            'categories' => $categories,
            'filters' => [
                'q' => $query,
                'sort' => $sort,
                'weight' => $weight,
            ],
            'weights' => self::availableWeights(),
            'products' => $paginator,
            'category_label' => $categoryLabel,
        ];
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    public static function filteredProducts(string $category, string $query, string $weight, string $sort): Collection
    {
        $products = Catalog::products()
            ->when($category !== 'all', fn (Collection $items) => $items->where('category', $category))
            ->when($query !== '', function (Collection $items) use ($query) {
                $needle = mb_strtolower($query);

                return $items->filter(function (array $product) use ($needle): bool {
                    $haystack = mb_strtolower(
                        $product['name'].' '.$product['description'].' '.($product['badge'] ?? '')
                    );

                    return str_contains($haystack, $needle);
                });
            })
            ->when($weight !== '', fn (Collection $items) => $items->filter(
                fn (array $product): bool => ($product['weight'] ?? '') === $weight
            ))
            ->values();

        return match ($sort) {
            'price_asc' => $products->sortBy('price')->values(),
            'price_desc' => $products->sortByDesc('price')->values(),
            'newest' => $products->sortByDesc(
                fn (array $product): int => ($product['badge'] ?? null) === 'New' ? 1 : 0
            )->values(),
            'popular' => $products->sortByDesc(
                fn (array $product): int => ($product['badge'] ?? null) !== null ? 1 : 0
            )->values(),
            default => $products,
        };
    }

    /**
     * @param  Collection<int, array<string, mixed>>  $products
     */
    public static function paginate(Collection $products, int $page, Request $request): LengthAwarePaginator
    {
        $perPage = self::PER_PAGE;
        $total = $products->count();
        $lastPage = max(1, (int) ceil($total / $perPage));
        $page = min(max(1, $page), $lastPage);

        $paginator = new Paginator(
            $products->forPage($page, $perPage)->values(),
            $total,
            $perPage,
            $page,
            [
                'path' => $request->url(),
                'pageName' => 'page',
            ]
        );

        return $paginator->appends(self::queryWithoutPage($request));
    }

    /**
     * Stable query string for filters (no empty noise, no page).
     *
     * @return array<string, string>
     */
    public static function queryWithoutPage(Request $request): array
    {
        $query = [];

        $category = $request->string('category')->toString();
        if ($category !== '' && $category !== 'all') {
            $query['category'] = $category;
        }

        $q = $request->string('q')->trim()->toString();
        if ($q !== '') {
            $query['q'] = $q;
        }

        $sort = $request->string('sort')->toString();
        if ($sort !== '' && $sort !== 'featured') {
            $query['sort'] = $sort;
        }

        $weight = $request->string('weight')->toString();
        if ($weight !== '') {
            $query['weight'] = $weight;
        }

        return $query;
    }

    /**
     * @return list<string>
     */
    private static function availableWeights(): array
    {
        return Catalog::products()
            ->pluck('weight')
            ->filter(fn (mixed $value): bool => is_string($value) && $value !== '')
            ->unique()
            ->sort()
            ->values()
            ->all();
    }
}
