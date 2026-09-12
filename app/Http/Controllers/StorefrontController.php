<?php

namespace App\Http\Controllers;

use App\Support\Catalog;
use App\Support\Fulfillment;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class StorefrontController extends Controller
{
    public function home(): View
    {
        $favorites = Catalog::products()
            ->sortBy('price')
            ->filter(fn (array $product): bool => ($product['badge'] ?? null) !== null)
            ->take(4)
            ->values();

        if ($favorites->count() < 4) {
            $favorites = Catalog::products()->take(4)->values();
        }

        return view('pages.home', [
            'title' => 'Dezato Cake House | Cakes & Desserts in Karachi',
            'metaDescription' => 'Order cakes, cupcakes, eclairs, brownies, cheesecakes, tarts, mini pies and sundaes from Dezato Cake House in Karachi. Pickup & delivery in PKR.',
            'favorites' => $favorites->all(),
        ]);
    }

    public function menu(Request $request): View
    {
        $category = $request->string('category')->toString() ?: 'all';
        $categories = config('dezato.menu.categories', []);
        $validIds = collect($categories)->pluck('id')->all();

        if (! in_array($category, $validIds, true)) {
            $category = 'all';
        }

        $query = $request->string('q')->trim()->toString();
        $sort = $request->string('sort')->toString() ?: 'featured';
        $minPrice = $request->integer('min_price', 0);
        $maxPrice = $request->integer('max_price', 0);
        $weight = $request->string('weight')->toString();
        $occasion = $request->string('occasion')->toString();

        $products = Catalog::products()
            ->when($category !== 'all', fn ($items) => $items->where('category', $category))
            ->when($query !== '', function ($items) use ($query) {
                $needle = mb_strtolower($query);

                return $items->filter(function (array $product) use ($needle): bool {
                    $haystack = mb_strtolower($product['name'].' '.$product['description'].' '.($product['badge'] ?? ''));

                    return str_contains($haystack, $needle);
                });
            })
            ->when($minPrice > 0, fn ($items) => $items->filter(fn (array $p): bool => (float) $p['price'] >= $minPrice))
            ->when($maxPrice > 0, fn ($items) => $items->filter(fn (array $p): bool => (float) $p['price'] <= $maxPrice))
            ->when($weight !== '', fn ($items) => $items->filter(fn (array $p): bool => ($p['weight'] ?? '') === $weight))
            ->when($occasion === 'bestsellers', fn ($items) => $items->filter(fn (array $p): bool => in_array($p['badge'] ?? null, ['Bestseller', 'Popular', 'Guest favorite', 'Signature'], true)))
            ->when($occasion === 'new', fn ($items) => $items->filter(fn (array $p): bool => ($p['badge'] ?? null) === 'New'))
            ->values();

        $products = match ($sort) {
            'price_asc' => $products->sortBy('price')->values(),
            'price_desc' => $products->sortByDesc('price')->values(),
            'newest' => $products->sortByDesc(fn (array $p): int => ($p['badge'] ?? null) === 'New' ? 1 : 0)->values(),
            'popular' => $products->sortByDesc(fn (array $p): int => ($p['badge'] ?? null) !== null ? 1 : 0)->values(),
            default => $products,
        };

        $categoryLabel = $category === 'all'
            ? 'All desserts'
            : Catalog::categoryLabel($category);

        return view('pages.menu', [
            'title' => $categoryLabel.' | Dezato Cake House',
            'metaDescription' => 'Browse '.$categoryLabel.' from Dezato Cake House, Karachi. Prices in PKR (₨).',
            'categories' => $categories,
            'products' => $products->all(),
            'activeCategory' => $category,
            'filters' => [
                'q' => $query,
                'sort' => $sort,
                'min_price' => $minPrice ?: '',
                'max_price' => $maxPrice ?: '',
                'weight' => $weight,
                'occasion' => $occasion,
            ],
        ]);
    }

    public function locations(): View
    {
        return view('pages.locations', [
            'title' => 'Locations | Dezato Cake House Karachi',
            'metaDescription' => 'Visit Dezato Cake House in DHA Phase 6 and Gizri, Karachi - hours, addresses, and delivery.',
            'locations' => config('dezato.locations', []),
        ]);
    }

    public function services(): View
    {
        return view('pages.services', [
            'title' => 'Our Services | Dezato Cake House',
            'metaDescription' => 'Catering, dessert tables, office sweet boxes, and corporate gifting from Dezato Cake House Karachi.',
            'packages' => config('dezato.services.packages', []),
        ]);
    }

    public function about(): View
    {
        return view('pages.about', [
            'title' => 'About Us | Dezato Cake House',
            'metaDescription' => 'Learn how Dezato Cake House has baked celebration cakes and desserts for Karachi since 2018.',
            'intro' => (string) config('dezato.about.intro', ''),
            'milestones' => config('dezato.about.milestones', []),
        ]);
    }

    public function customization(): View
    {
        return view('pages.customization', [
            'title' => 'Cake Customization | Dezato Cake House',
            'metaDescription' => 'Custom celebration cakes in Karachi - flavours, sizes, inscriptions, and finishes from Dezato Cake House.',
            'options' => config('dezato.customization.options', []),
        ]);
    }

    public function order(Fulfillment $fulfillment): View|RedirectResponse
    {
        if (! $fulfillment->has()) {
            return redirect()
                ->route('home', ['fulfillment' => 1])
                ->with('open_fulfillment', true);
        }

        return view('pages.order', [
            'title' => 'Order | Dezato Cake House',
            'metaDescription' => 'Order Dezato for Karachi pickup, local delivery, or Pakistan courier shipping.',
            'options' => config('dezato.order.options', []),
            'fulfillmentSummary' => $fulfillment->summary(),
        ]);
    }

    public function product(string $product): View
    {
        $item = Catalog::findProduct($product);

        abort_if($item === null, 404);

        $related = Catalog::products()
            ->where('category', $item['category'])
            ->where('id', '!=', $item['id'])
            ->take(3)
            ->values()
            ->all();

        $gallery = collect([$item['image']])
            ->merge(collect($related)->pluck('image'))
            ->filter()
            ->unique()
            ->take(4)
            ->values()
            ->all();

        return view('pages.product', [
            'title' => $item['name'].' | Dezato Cake House',
            'metaDescription' => $item['description'],
            'product' => $item,
            'categoryLabel' => Catalog::categoryLabel($item['category']),
            'related' => $related,
            'gallery' => $gallery,
            'fulfillmentSummary' => app(Fulfillment::class)->summary(),
        ]);
    }

    public function sitemap(): Response
    {
        $urls = [
            ['loc' => route('home'), 'changefreq' => 'weekly', 'priority' => '1.0'],
            ['loc' => route('menu'), 'changefreq' => 'daily', 'priority' => '0.9'],
            ['loc' => route('about'), 'changefreq' => 'monthly', 'priority' => '0.7'],
            ['loc' => route('services'), 'changefreq' => 'monthly', 'priority' => '0.7'],
            ['loc' => route('customization'), 'changefreq' => 'monthly', 'priority' => '0.8'],
            ['loc' => route('locations'), 'changefreq' => 'monthly', 'priority' => '0.8'],
            ['loc' => route('order'), 'changefreq' => 'weekly', 'priority' => '0.6'],
        ];

        foreach (Catalog::products() as $product) {
            $urls[] = [
                'loc' => route('products.show', $product['id']),
                'changefreq' => 'weekly',
                'priority' => '0.7',
            ];
        }

        $xml = view('seo.sitemap', ['urls' => $urls])->render();

        return response($xml, 200)->header('Content-Type', 'application/xml');
    }

    public function robots(): Response
    {
        $body = "User-agent: *\nAllow: /\n\nSitemap: ".url('/sitemap.xml')."\n";

        return response($body, 200)->header('Content-Type', 'text/plain');
    }
}
