<?php

namespace App\Support;

use App\Models\SiteSetting;
use Illuminate\Http\UploadedFile;

/**
 * Homepage section copy + “How you’ll get it” tiles.
 */
final class HomeChrome
{
    public const KEY = 'home_chrome';

    /**
     * @return array<string, mixed>
     */
    public static function defaults(): array
    {
        return [
            'hero_cta_primary' => 'Shop the menu',
            'hero_cta_start' => 'Start an order',
            'hero_cta_secondary' => 'Visit us',
            'favorites_title' => 'Favourites',
            'favorites_link' => 'Shop all',
            'categories_title' => 'Shop by category',
            'categories_text' => 'Cakes, cupcakes, cheesecakes, eclairs, brownies, sundaes, tarts & mini pies.',
            'ways_title' => 'How you’ll get it',
            'ways_text' => 'Pickup in DHA or Gizri, or delivery across Karachi.',
            'occasions_title' => 'For every occasion',
            'occasions_text' => 'Birthdays, office treats, custom cakes, and gifts.',
            'story_kicker' => 'About Dezato',
            'story_title' => 'Karachi-baked. Celebration-ready.',
            'story_text' => 'Since 2018 we’ve baked cakes and desserts for Karachi - from Lotus and Ferrero classics to custom birthday finishes.',
            'story_cta' => 'About us',
            'story_image' => 'images/home/promo-workshop.jpg',
            'about_intro_image' => 'images/products/chocolate-heaven-cake.jpg',
            'cater_title' => 'Services & catering',
            'cater_text' => 'Office boxes, dessert tables, and corporate gifting - built around your guest list.',
            'cater_cta' => 'Our services',
            'news_title' => 'Stay in the know',
            'news_text' => 'Seasonal flavours and bakery news - no spam.',
            'news_placeholder' => 'Email address',
            'news_cta' => 'Subscribe',
            'ways' => [
                [
                    'id' => 'pickup',
                    'title' => 'Store pickup',
                    'text' => 'Order ahead and collect fresh from our counters.',
                    'image' => 'images/home/delivery-pickup.jpg',
                    'action' => 'pickup',
                ],
                [
                    'id' => 'delivery',
                    'title' => 'Karachi delivery',
                    'text' => 'Same-day delivery where we serve your neighbourhood.',
                    'image' => 'images/home/delivery-catering.jpg',
                    'action' => 'delivery',
                ],
                [
                    'id' => 'custom',
                    'title' => 'Custom cakes',
                    'text' => 'Build a celebration cake with flavours, size, and finish.',
                    'image' => 'images/home/delivery-ship.jpg',
                    'action' => 'builder',
                ],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function all(): array
    {
        $defaults = self::defaults();
        $stored = SiteSetting::getJson(self::KEY, []);
        $stored = is_array($stored) ? $stored : [];

        $out = $defaults;

        foreach ($defaults as $key => $value) {
            if ($key === 'ways') {
                continue;
            }
            if (array_key_exists($key, $stored) && is_string($stored[$key])) {
                $trimmed = trim($stored[$key]);
                if ($trimmed !== '') {
                    $out[$key] = $trimmed;
                }
            }
        }

        $out['story_image'] = MediaPaths::public(
            (string) ($stored['story_image'] ?? $defaults['story_image']),
            $defaults['story_image']
        );
        $out['about_intro_image'] = MediaPaths::public(
            (string) ($stored['about_intro_image'] ?? $defaults['about_intro_image']),
            $defaults['about_intro_image']
        );

        $ways = [];
        $storedWays = is_array($stored['ways'] ?? null) ? $stored['ways'] : [];
        foreach ($defaults['ways'] as $index => $way) {
            $row = is_array($storedWays[$index] ?? null) ? $storedWays[$index] : [];
            $ways[] = [
                'id' => (string) ($row['id'] ?? $way['id']),
                'title' => trim((string) ($row['title'] ?? $way['title'])) ?: $way['title'],
                'text' => trim((string) ($row['text'] ?? $way['text'])) ?: $way['text'],
                'image' => MediaPaths::public(
                    (string) ($row['image'] ?? $way['image']),
                    $way['image']
                ),
                'action' => (string) ($row['action'] ?? $way['action']),
            ];
        }
        $out['ways'] = $ways;

        return $out;
    }

    /**
     * @param  array<string, mixed>  $data
     * @param  array<string, UploadedFile|null>  $wayFiles
     */
    public static function save(array $data, ?UploadedFile $storyImage = null, ?UploadedFile $aboutImage = null, array $wayFiles = []): void
    {
        $defaults = self::defaults();
        $current = self::all();

        $payload = [];
        foreach ($defaults as $key => $value) {
            if ($key === 'ways') {
                continue;
            }
            if (in_array($key, ['story_image', 'about_intro_image'], true)) {
                continue;
            }
            $payload[$key] = trim((string) ($data[$key] ?? $value)) ?: $value;
        }

        $storyPath = (string) ($data['existing_story_image'] ?? $current['story_image']);
        if ($storyImage instanceof UploadedFile) {
            MediaPaths::deleteIfOwned($storyPath);
            $storyPath = $storyImage->store('home', 'public');
        }
        $payload['story_image'] = $storyPath;

        $aboutPath = (string) ($data['existing_about_intro_image'] ?? $current['about_intro_image']);
        if ($aboutImage instanceof UploadedFile) {
            MediaPaths::deleteIfOwned($aboutPath);
            $aboutPath = $aboutImage->store('home', 'public');
        }
        $payload['about_intro_image'] = $aboutPath;

        $ways = [];
        $inputWays = is_array($data['ways'] ?? null) ? $data['ways'] : [];
        foreach ($defaults['ways'] as $index => $way) {
            $row = is_array($inputWays[$index] ?? null) ? $inputWays[$index] : [];
            $image = trim((string) ($row['existing_image'] ?? $way['image']));
            if (isset($wayFiles[$index]) && $wayFiles[$index] instanceof UploadedFile) {
                MediaPaths::deleteIfOwned($image);
                $image = $wayFiles[$index]->store('home', 'public');
            }
            $ways[] = [
                'id' => $way['id'],
                'title' => trim((string) ($row['title'] ?? $way['title'])) ?: $way['title'],
                'text' => trim((string) ($row['text'] ?? $way['text'])) ?: $way['text'],
                'image' => $image !== '' ? $image : $way['image'],
                'action' => $way['action'],
            ];
        }
        $payload['ways'] = $ways;

        SiteSetting::putJson(self::KEY, $payload);
    }
}
