<?php

namespace App\Support;

/**
 * Structured data for SEO (kept out of Blade so @context is not parsed as a directive).
 */
final class SeoSchema
{
    /**
     * @return string JSON-LD for the bakery organization
     */
    public static function bakery(string $name, string $description): string
    {
        return self::encode([
            '@context' => 'https://schema.org',
            '@type' => 'Bakery',
            'name' => $name,
            'url' => url('/'),
            'image' => asset('images/brand/logo-icon.jpg'),
            'description' => $description,
            'address' => [
                '@type' => 'PostalAddress',
                'addressLocality' => 'Karachi',
                'addressRegion' => 'Sindh',
                'addressCountry' => 'PK',
            ],
            'areaServed' => 'Karachi',
            'currenciesAccepted' => 'PKR',
            'priceRange' => '₨₨',
            'servesCuisine' => 'Bakery',
        ]);
    }

    /**
     * @param  array<string, mixed>  $product
     */
    public static function product(array $product): string
    {
        return self::encode([
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => $product['name'],
            'description' => $product['description'],
            'image' => asset($product['image']),
            'sku' => $product['id'],
            'brand' => [
                '@type' => 'Brand',
                'name' => SiteBrand::name(),
            ],
            'offers' => [
                '@type' => 'Offer',
                'priceCurrency' => 'PKR',
                'price' => (string) (int) $product['price'],
                'availability' => 'https://schema.org/InStock',
                'url' => route('products.show', $product['id']),
            ],
        ]);
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
