<?php

namespace App\Support;

/**
 * Structured data for SEO (kept out of Blade so @context is not parsed as a directive).
 */
final class SeoSchema
{
    /**
     * Site-wide Bakery + WebSite graph (local SEO + sitelinks search box target).
     */
    public static function siteGraph(): string
    {
        return self::encode([
            '@context' => 'https://schema.org',
            '@graph' => [
                self::bakeryNode(),
                self::websiteNode(),
            ],
        ]);
    }

    /**
     * @deprecated Use siteGraph() - kept for any external callers.
     */
    public static function bakery(string $name = '', string $description = ''): string
    {
        return self::siteGraph();
    }

    /**
     * @param  array<string, mixed>  $product
     * @param  list<array{name: string, url: string}>  $crumbs
     */
    public static function product(array $product, array $crumbs = []): string
    {
        $stock = $product['stock'] ?? null;
        $inStock = $stock === null || $stock === '' || (int) $stock > 0;
        $url = route('products.show', $product['id']);
        $image = asset((string) ($product['image'] ?? SiteBrand::logoIcon()));

        $nodes = [
            [
                '@type' => 'Product',
                '@id' => $url.'#product',
                'name' => (string) $product['name'],
                'description' => SeoMeta::description((string) ($product['description'] ?? ''), 300),
                'image' => [$image],
                'sku' => (string) $product['id'],
                'brand' => [
                    '@type' => 'Brand',
                    'name' => SiteBrand::name(),
                ],
                'offers' => [
                    '@type' => 'Offer',
                    'url' => $url,
                    'priceCurrency' => 'PKR',
                    'price' => number_format((float) $product['price'], 2, '.', ''),
                    'availability' => $inStock
                        ? 'https://schema.org/InStock'
                        : 'https://schema.org/OutOfStock',
                    'itemCondition' => 'https://schema.org/NewCondition',
                    'seller' => [
                        '@type' => 'Bakery',
                        'name' => SiteBrand::name(),
                        'url' => url('/'),
                    ],
                ],
            ],
        ];

        if ($crumbs !== []) {
            $nodes[] = self::breadcrumbNode($crumbs);
        }

        return self::encode([
            '@context' => 'https://schema.org',
            '@graph' => $nodes,
        ]);
    }

    /**
     * @param  list<array{name: string, url: string}>  $crumbs
     * @return array<string, mixed>
     */
    public static function breadcrumbNode(array $crumbs): array
    {
        $items = [];

        foreach (array_values($crumbs) as $index => $crumb) {
            $items[] = [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'name' => $crumb['name'],
                'item' => $crumb['url'],
            ];
        }

        return [
            '@type' => 'BreadcrumbList',
            'itemListElement' => $items,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function bakeryNode(): array
    {
        $name = SiteBrand::name();
        $logo = asset(SiteBrand::logoIcon());
        $phone = BakeryProfile::phone();
        $email = BakeryProfile::publicEmail();
        $locations = StoreLocations::forStorefront();
        $primary = $locations[0] ?? null;

        $node = [
            '@type' => 'Bakery',
            '@id' => url('/').'#bakery',
            'name' => $name,
            'url' => url('/'),
            'image' => $logo,
            'logo' => $logo,
            'description' => SeoMeta::description(SiteBrand::tagline(), 300),
            'currenciesAccepted' => 'PKR',
            'priceRange' => '₨₨',
            'servesCuisine' => 'Bakery',
            'areaServed' => [
                '@type' => 'City',
                'name' => 'Karachi',
            ],
        ];

        if ($phone !== '') {
            $node['telephone'] = $phone;
        }

        if ($email !== '') {
            $node['email'] = $email;
        }

        $sameAs = array_column(SocialLinks::forFooter(), 'url');
        if ($sameAs !== []) {
            $node['sameAs'] = array_values($sameAs);
        }

        if (is_array($primary)) {
            $node['address'] = self::postalAddress($primary);
            if (($primary['hours'] ?? '') !== '') {
                $node['openingHours'] = (string) $primary['hours'];
            }
            if (($primary['map_url'] ?? '') !== '') {
                $node['hasMap'] = (string) $primary['map_url'];
            }
        } else {
            $node['address'] = [
                '@type' => 'PostalAddress',
                'addressLocality' => 'Karachi',
                'addressRegion' => 'Sindh',
                'addressCountry' => 'PK',
            ];
        }

        if (count($locations) > 1) {
            $node['department'] = array_map(static function (array $location) use ($name): array {
                $dept = [
                    '@type' => 'Bakery',
                    'name' => ($location['name'] ?? '') !== '' ? (string) $location['name'] : $name,
                    'address' => self::postalAddress($location),
                ];
                if (($location['phone'] ?? '') !== '') {
                    $dept['telephone'] = (string) $location['phone'];
                }
                if (($location['hours'] ?? '') !== '') {
                    $dept['openingHours'] = (string) $location['hours'];
                }
                if (($location['map_url'] ?? '') !== '') {
                    $dept['hasMap'] = (string) $location['map_url'];
                }

                return $dept;
            }, $locations);
        }

        return $node;
    }

    /**
     * @return array<string, mixed>
     */
    private static function websiteNode(): array
    {
        return [
            '@type' => 'WebSite',
            '@id' => url('/').'#website',
            'url' => url('/'),
            'name' => SiteBrand::name(),
            'description' => SeoMeta::description(SiteBrand::tagline(), 300),
            'publisher' => [
                '@id' => url('/').'#bakery',
            ],
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => [
                    '@type' => 'EntryPoint',
                    'urlTemplate' => route('menu').'?q={search_term_string}',
                ],
                'query-input' => 'required name=search_term_string',
            ],
        ];
    }

    /**
     * @param  array<string, mixed>  $location
     * @return array<string, string>
     */
    private static function postalAddress(array $location): array
    {
        $address = [
            '@type' => 'PostalAddress',
            'addressLocality' => (string) ($location['city'] ?? 'Karachi'),
            'addressRegion' => (string) ($location['region'] ?? 'Sindh'),
            'addressCountry' => 'PK',
        ];

        if (($location['address'] ?? '') !== '') {
            $address['streetAddress'] = (string) $location['address'];
        }

        return $address;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private static function encode(array $data): string
    {
        return json_encode(
            $data,
            JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP
        ) ?: '{}';
    }
}
