<?php

namespace App\Support;

use App\Models\SiteSetting;
use Illuminate\Http\UploadedFile;

/**
 * Inner-page heroes (eyebrow / title / text / image) for the storefront PLPs.
 */
final class PageHeroes
{
    public const KEY = 'page_heroes';

    /**
     * @return array<string, array{label: string, eyebrow: string, title: string, text: string, image: string, compact: bool}>
     */
    public static function defaults(): array
    {
        return [
            'menu' => [
                'label' => 'Menu',
                'eyebrow' => 'Menu',
                'title' => 'What we’re baking',
                'text' => 'Cakes, cupcakes, cheesecakes, eclairs & more - priced in PKR.',
                'image' => 'images/home/promo-workshop.jpg',
                'compact' => false,
            ],
            'about' => [
                'label' => 'About Us',
                'eyebrow' => 'Dezato Cake House',
                'title' => 'About Us',
                'text' => 'Handcrafted cakes and desserts for Karachi - baked fresh since 2018.',
                'image' => 'images/home/promo-anniversary.jpg',
                'compact' => false,
            ],
            'services' => [
                'label' => 'Our Services',
                'eyebrow' => 'Services',
                'title' => 'Our Services',
                'text' => 'Catering, dessert tables, and corporate gifting.',
                'image' => 'images/home/promo-catering.jpg',
                'compact' => false,
            ],
            'locations' => [
                'label' => 'Locations',
                'eyebrow' => 'Visit us',
                'title' => 'Locations',
                'text' => 'Find Dezato Cake House across Karachi.',
                'image' => 'images/home/delivery-pickup.jpg',
                'compact' => false,
            ],
            'customization' => [
                'label' => 'Cake Customization',
                'eyebrow' => 'Custom cakes',
                'title' => 'Cake Customization',
                'text' => 'Design a cake with flavours, size, and finish.',
                'image' => 'images/home/promo-anniversary.jpg',
                'compact' => false,
            ],
            'cart' => [
                'label' => 'Cart',
                'eyebrow' => 'Your order',
                'title' => 'Cart',
                'text' => 'Review your treats, then we’ll confirm pickup or delivery details at checkout.',
                'image' => 'images/home/delivery-ship.jpg',
                'compact' => true,
            ],
            'order' => [
                'label' => 'Order options',
                'eyebrow' => 'Order',
                'title' => 'How would you like it?',
                'text' => 'Pickup, delivery, or courier - choose what works for you.',
                'image' => 'images/home/delivery-ship.jpg',
                'compact' => false,
            ],
            'legal' => [
                'label' => 'Legal / FAQ pages',
                'eyebrow' => 'Dezato Cake House',
                'title' => 'Information',
                'text' => 'Policies and answers for Dezato guests.',
                'image' => 'images/home/promo-workshop.jpg',
                'compact' => true,
            ],
        ];
    }

    /**
     * @return array{eyebrow: string, title: string, text: string, image: string, compact: bool}
     */
    public static function get(string $key): array
    {
        $defaults = self::defaults();
        $base = $defaults[$key] ?? [
            'label' => $key,
            'eyebrow' => '',
            'title' => '',
            'text' => '',
            'image' => 'images/home/hero.jpg',
            'compact' => false,
        ];

        $stored = SiteSetting::getJson(self::KEY, []);
        $row = is_array($stored[$key] ?? null) ? $stored[$key] : [];

        return [
            'eyebrow' => trim((string) ($row['eyebrow'] ?? $base['eyebrow'])),
            'title' => trim((string) ($row['title'] ?? $base['title'])) ?: $base['title'],
            'text' => trim((string) ($row['text'] ?? $base['text'])),
            'image' => MediaPaths::public(
                (string) ($row['image'] ?? $base['image']),
                $base['image']
            ),
            'compact' => (bool) ($row['compact'] ?? $base['compact']),
        ];
    }

    /**
     * @return list<array{key: string, label: string, eyebrow: string, title: string, text: string, image: string, image_url: string, compact: bool}>
     */
    public static function forAdmin(): array
    {
        $out = [];

        foreach (self::defaults() as $key => $meta) {
            $hero = self::get($key);
            $out[] = [
                'key' => $key,
                'label' => $meta['label'],
                'eyebrow' => $hero['eyebrow'],
                'title' => $hero['title'],
                'text' => $hero['text'],
                'image' => $hero['image'],
                'image_url' => asset($hero['image']),
                'compact' => $hero['compact'],
            ];
        }

        return $out;
    }

    /**
     * @param  array<string, array<string, mixed>>  $rows
     * @param  array<string, UploadedFile|null>  $files
     */
    public static function save(array $rows, array $files = []): void
    {
        $stored = [];

        foreach (self::defaults() as $key => $meta) {
            $row = is_array($rows[$key] ?? null) ? $rows[$key] : [];
            $image = trim((string) ($row['existing_image'] ?? $meta['image']));

            if (isset($files[$key]) && $files[$key] instanceof UploadedFile) {
                MediaPaths::deleteIfOwned($image);
                $image = $files[$key]->store('heroes', 'public');
            }

            $stored[$key] = [
                'eyebrow' => trim((string) ($row['eyebrow'] ?? $meta['eyebrow'])),
                'title' => trim((string) ($row['title'] ?? $meta['title'])) ?: $meta['title'],
                'text' => trim((string) ($row['text'] ?? $meta['text'])),
                'image' => $image !== '' ? $image : $meta['image'],
                'compact' => filter_var($row['compact'] ?? $meta['compact'], FILTER_VALIDATE_BOOLEAN),
            ];
        }

        SiteSetting::putJson(self::KEY, $stored);
    }
}
