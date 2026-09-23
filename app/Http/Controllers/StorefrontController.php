<?php

namespace App\Http\Controllers;

use App\Support\Catalog;
use App\Support\Fulfillment;
use App\Support\HeroSlider;
use App\Support\HomeShowcase;
use App\Support\SiteContent;
use App\Support\StoryBlocks;
use App\Support\StoreLocations;
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
            'heroSlides' => HeroSlider::activeSlides(),
            'heroIntervalMs' => HeroSlider::intervalMs(),
            'homeCategories' => HomeShowcase::categories(),
            'homeOccasions' => HomeShowcase::occasions(),
        ]);
    }

    public function menu(Request $request): View
    {
        $category = $request->string('category')->toString() ?: 'all';
        $categories = Catalog::categories()->all();
        $validIds = collect($categories)->pluck('id')->all();

        if (! in_array($category, $validIds, true)) {
            $category = 'all';
        }

        $query = $request->string('q')->trim()->toString();
        $sort = $request->string('sort')->toString() ?: 'featured';
        $weight = $request->string('weight')->toString();

        $products = Catalog::products()
            ->when($category !== 'all', fn ($items) => $items->where('category', $category))
            ->when($query !== '', function ($items) use ($query) {
                $needle = mb_strtolower($query);

                return $items->filter(function (array $product) use ($needle): bool {
                    $haystack = mb_strtolower($product['name'].' '.$product['description'].' '.($product['badge'] ?? ''));

                    return str_contains($haystack, $needle);
                });
            })
            ->when($weight !== '', fn ($items) => $items->filter(fn (array $p): bool => ($p['weight'] ?? '') === $weight))
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
                'weight' => $weight,
            ],
        ]);
    }

    public function locations(): View
    {
        $locations = StoreLocations::forStorefront();

        return view('pages.locations', [
            'title' => 'Locations | Dezato Cake House Karachi',
            'metaDescription' => 'Visit Dezato Cake House in Karachi - hours, addresses, and delivery.',
            'locations' => $locations,
        ]);
    }

    public function services(): View
    {
        return view('pages.services', [
            'title' => 'Our Services | Dezato Cake House',
            'metaDescription' => 'Catering, dessert tables, office sweet boxes, and corporate gifting from Dezato Cake House Karachi.',
            'intro' => SiteContent::section(
                'services_intro',
                'From office boxes to full dessert tables - custom selections of Dezato’s best for every occasion.'
            ),
            'packages' => StoryBlocks::packagesForStorefront(),
        ]);
    }

    public function about(): View
    {
        return view('pages.about', [
            'title' => 'About Us | Dezato Cake House',
            'metaDescription' => 'Learn how Dezato Cake House has baked celebration cakes and desserts for Karachi since 2018.',
            'intro' => SiteContent::section(
                'about_intro',
                (string) config('dezato.about.intro', '')
            ),
            'milestones' => StoryBlocks::milestones(),
        ]);
    }

    public function customization(): View
    {
        return view('pages.customization', [
            'title' => 'Cake Customization | Dezato Cake House',
            'metaDescription' => 'Custom celebration cakes in Karachi - flavours, sizes, inscriptions, and finishes from Dezato Cake House.',
            'options' => \App\Support\StorefrontCards::customizationOptions(),
            'guidelines' => \App\Support\CakeBuilder::guidelines(),
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
            'options' => \App\Support\StorefrontCards::orderOptionsForStorefront(),
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
            ['loc' => route('pages.privacy'), 'changefreq' => 'yearly', 'priority' => '0.3'],
            ['loc' => route('pages.terms'), 'changefreq' => 'yearly', 'priority' => '0.3'],
            ['loc' => route('pages.faq'), 'changefreq' => 'monthly', 'priority' => '0.5'],
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
