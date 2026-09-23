<?php

namespace App\Support;

use App\Models\SiteSetting;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

/**
 * Customization blurbs + Order page choice cards (Admin-managed).
 */
final class StorefrontCards
{
    public const CUSTOMIZATION_KEY = 'customization_options';

    public const ORDER_KEY = 'order_options';

    /**
     * @return array<string, string>
     */
    public static function orderRoutes(): array
    {
        return [
            'order.start' => 'Start order (pickup / delivery / courier chooser)',
            'services' => 'Our Services page',
            'menu' => 'Menu',
            'builder.show' => 'Custom cake builder',
            'locations' => 'Locations',
        ];
    }

    /**
     * @return list<array{id: string, title: string, text: string}>
     */
    public static function customizationOptions(): array
    {
        $stored = SiteSetting::getJson(self::CUSTOMIZATION_KEY);
        $source = is_array($stored) && $stored !== []
            ? $stored
            : config('dezato.customization.options', []);

        return array_values(array_map(function (array $row): array {
            return [
                'id' => (string) ($row['id'] ?? Str::slug((string) ($row['title'] ?? 'option'))),
                'title' => trim((string) ($row['title'] ?? '')),
                'text' => trim((string) ($row['text'] ?? '')),
            ];
        }, $source));
    }

    /**
     * @param  list<array<string, mixed>>  $rows
     */
    public static function saveCustomization(array $rows): void
    {
        $clean = [];

        foreach ($rows as $row) {
            if (! is_array($row)) {
                continue;
            }

            $title = trim((string) ($row['title'] ?? ''));
            if ($title === '') {
                continue;
            }

            $clean[] = [
                'id' => (string) ($row['id'] ?? Str::slug($title)),
                'title' => $title,
                'text' => trim((string) ($row['text'] ?? '')),
            ];
        }

        SiteSetting::putJson(self::CUSTOMIZATION_KEY, $clean);
    }

    public static function appendCustomization(): void
    {
        $rows = self::customizationOptions();
        $rows[] = [
            'id' => 'opt-'.Str::lower(Str::random(4)),
            'title' => 'New option',
            'text' => 'Describe this customization choice.',
        ];
        self::saveCustomization($rows);
    }

    public static function removeCustomization(string $id): bool
    {
        $rows = self::customizationOptions();
        if (count($rows) <= 1) {
            return false;
        }

        $filtered = array_values(array_filter($rows, fn (array $row): bool => ($row['id'] ?? '') !== $id));
        if (count($filtered) === count($rows)) {
            return false;
        }

        self::saveCustomization($filtered);

        return true;
    }

    /**
     * @return list<array{id: string, title: string, text: string, cta: string, route: string, image: string}>
     */
    public static function orderOptions(): array
    {
        $stored = SiteSetting::getJson(self::ORDER_KEY);
        $source = is_array($stored) && $stored !== []
            ? $stored
            : config('dezato.order.options', []);

        $allowed = array_keys(self::orderRoutes());

        return array_values(array_map(function (array $row) use ($allowed): array {
            $route = (string) ($row['route'] ?? 'order.start');
            if (! in_array($route, $allowed, true) || ! Route::has($route)) {
                $route = 'order.start';
            }

            return [
                'id' => (string) ($row['id'] ?? Str::slug((string) ($row['title'] ?? 'order'))),
                'title' => trim((string) ($row['title'] ?? '')),
                'text' => trim((string) ($row['text'] ?? '')),
                'cta' => trim((string) ($row['cta'] ?? 'Continue')) ?: 'Continue',
                'route' => $route,
                'image' => trim((string) ($row['image'] ?? '')),
            ];
        }, $source));
    }

    /**
     * @return list<array{id: string, title: string, text: string, cta: string, route: string, image: string}>
     */
    public static function orderOptionsForStorefront(): array
    {
        return array_map(function (array $row): array {
            $row['image'] = MediaPaths::public($row['image'] ?? null, 'images/home/delivery-pickup.png');

            return $row;
        }, self::orderOptions());
    }

    /**
     * @param  list<array<string, mixed>>  $rows
     */
    public static function saveOrderOptions(array $rows): void
    {
        $allowed = array_keys(self::orderRoutes());
        $clean = [];

        foreach ($rows as $row) {
            if (! is_array($row)) {
                continue;
            }

            $title = trim((string) ($row['title'] ?? ''));
            if ($title === '') {
                continue;
            }

            $route = (string) ($row['route'] ?? 'order.start');
            if (! in_array($route, $allowed, true)) {
                $route = 'order.start';
            }

            $clean[] = [
                'id' => (string) ($row['id'] ?? Str::slug($title)),
                'title' => $title,
                'text' => trim((string) ($row['text'] ?? '')),
                'cta' => trim((string) ($row['cta'] ?? 'Continue')) ?: 'Continue',
                'route' => $route,
                'image' => trim((string) ($row['image'] ?? '')),
            ];
        }

        SiteSetting::putJson(self::ORDER_KEY, $clean);
    }

    public static function appendOrderOption(): void
    {
        $rows = self::orderOptions();
        $rows[] = [
            'id' => 'ord-'.Str::lower(Str::random(4)),
            'title' => 'New order option',
            'text' => 'Describe how customers can get their order.',
            'cta' => 'Continue',
            'route' => 'order.start',
            'image' => '',
        ];
        self::saveOrderOptions($rows);
    }

    public static function removeOrderOption(string $id): bool
    {
        $rows = self::orderOptions();
        if (count($rows) <= 1) {
            return false;
        }

        $removed = collect($rows)->firstWhere('id', $id);
        $filtered = array_values(array_filter($rows, fn (array $row): bool => ($row['id'] ?? '') !== $id));
        if (count($filtered) === count($rows)) {
            return false;
        }

        if (is_array($removed)) {
            MediaPaths::deleteIfOwned($removed['image'] ?? null);
        }

        self::saveOrderOptions($filtered);

        return true;
    }
}
