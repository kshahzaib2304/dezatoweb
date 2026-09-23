<?php

namespace App\Support;

use App\Models\SiteSetting;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Homepage hero slider (admin-managed slides + interval).
 */
final class HeroSlider
{
    public const SETTING_KEY = 'hero_slider';

    public const MAX_SLIDES = 6;

    public const DEFAULT_INTERVAL_MS = 4500;

    /**
     * @return array{interval_ms: int, slides: list<array<string, mixed>>}
     */
    public static function config(): array
    {
        $stored = SiteSetting::getJson(self::SETTING_KEY);

        if (is_array($stored) && isset($stored['slides']) && is_array($stored['slides']) && $stored['slides'] !== []) {
            return [
                'interval_ms' => self::normalizeInterval((int) ($stored['interval_ms'] ?? self::DEFAULT_INTERVAL_MS)),
                'slides' => array_values($stored['slides']),
            ];
        }

        return [
            'interval_ms' => self::DEFAULT_INTERVAL_MS,
            'slides' => [self::legacySlide()],
        ];
    }

    /**
     * Active slides ready for the storefront (public image paths).
     *
     * @return list<array{id: string, image: string, headline: string, lede: string}>
     */
    public static function activeSlides(): array
    {
        return collect(self::config()['slides'])
            ->filter(fn (array $slide): bool => (bool) ($slide['active'] ?? true))
            ->map(fn (array $slide): array => [
                'id' => (string) ($slide['id'] ?? Str::random(8)),
                'image' => self::publicImagePath($slide['image'] ?? null),
                'headline' => (string) ($slide['headline'] ?? ''),
                'lede' => (string) ($slide['lede'] ?? ''),
            ])
            ->filter(fn (array $slide): bool => $slide['headline'] !== '')
            ->values()
            ->all() ?: [[
                'id' => 'default',
                'image' => 'images/home/hero.jpg',
                'headline' => 'Cakes for Karachi celebrations.',
                'lede' => 'Handcrafted desserts, baked fresh for pickup or delivery.',
            ]];
    }

    public static function intervalMs(): int
    {
        return self::config()['interval_ms'];
    }

    /**
     * @param  array{interval_ms?: int, slides: list<array<string, mixed>>}  $config
     */
    public static function save(array $config): void
    {
        SiteSetting::putJson(self::SETTING_KEY, [
            'interval_ms' => self::normalizeInterval((int) ($config['interval_ms'] ?? self::DEFAULT_INTERVAL_MS)),
            'slides' => array_values($config['slides']),
        ]);
    }

    /**
     * @return array{id: string, image: ?string, headline: string, lede: string, active: bool}
     */
    public static function makeSlide(string $headline, string $lede, ?string $image = null, bool $active = true): array
    {
        return [
            'id' => Str::lower(Str::random(10)),
            'image' => $image,
            'headline' => $headline,
            'lede' => $lede,
            'active' => $active,
        ];
    }

    public static function deleteImage(?string $path): void
    {
        if ($path && str_starts_with($path, 'banners/') && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    public static function publicImagePath(?string $path): string
    {
        if ($path && Storage::disk('public')->exists($path)) {
            return 'storage/'.$path;
        }

        if ($path && ! str_starts_with($path, 'http') && ! str_starts_with($path, 'storage/')) {
            if (str_starts_with($path, 'images/')) {
                return $path;
            }
        }

        return 'images/home/hero.jpg';
    }

    /**
     * @return array{id: string, image: ?string, headline: string, lede: string, active: bool}
     */
    private static function legacySlide(): array
    {
        $image = SiteSetting::getValue('hero_image');

        return self::makeSlide(
            (string) (SiteSetting::getValue('hero_headline') ?: 'Cakes for Karachi celebrations.'),
            (string) (SiteSetting::getValue('hero_lede') ?: 'Handcrafted desserts, baked fresh for pickup or delivery.'),
            $image,
            true
        );
    }

    private static function normalizeInterval(int $ms): int
    {
        $allowed = [4000, 4500, 5000];

        return in_array($ms, $allowed, true) ? $ms : self::DEFAULT_INTERVAL_MS;
    }
}
