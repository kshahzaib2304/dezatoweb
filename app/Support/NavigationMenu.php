<?php

namespace App\Support;

use App\Models\SiteSetting;
use Illuminate\Support\Facades\Route;

/**
 * Primary website navigation - labels & order editable from Admin.
 */
final class NavigationMenu
{
    public const KEY = 'main_nav';

    /**
     * Safe route names the bakery can link to (prevents broken menus).
     *
     * @return array<string, string>
     */
    public static function allowedRoutes(): array
    {
        return [
            'home' => 'Home page',
            'about' => 'About Us',
            'services' => 'Our Services',
            'customization' => 'Cake Customization (info page)',
            'builder.show' => 'Custom cake builder',
            'menu' => 'Menu',
            'locations' => 'Locations',
            'order' => 'Order options page',
        ];
    }

    /**
     * @return list<array{label: string, route: string}>
     */
    public static function links(): array
    {
        $stored = SiteSetting::getJson(self::KEY);
        $source = is_array($stored) && $stored !== []
            ? $stored
            : config('dezato.nav', []);

        $allowed = array_keys(self::allowedRoutes());
        $out = [];

        foreach ($source as $row) {
            if (! is_array($row)) {
                continue;
            }

            $route = (string) ($row['route'] ?? '');
            $label = trim((string) ($row['label'] ?? ''));

            if ($label === '' || ! in_array($route, $allowed, true) || ! Route::has($route)) {
                continue;
            }

            $out[] = [
                'label' => $label,
                'route' => $route,
            ];
        }

        return $out !== [] ? $out : self::defaults();
    }

    /**
     * @param  list<array{label?: string, route?: string}>  $rows
     */
    public static function save(array $rows): void
    {
        $allowed = array_keys(self::allowedRoutes());
        $clean = [];

        foreach ($rows as $row) {
            if (! is_array($row)) {
                continue;
            }

            $label = trim((string) ($row['label'] ?? ''));
            $route = (string) ($row['route'] ?? '');

            if ($label === '' || ! in_array($route, $allowed, true)) {
                continue;
            }

            $clean[] = [
                'label' => $label,
                'route' => $route,
            ];
        }

        SiteSetting::putJson(self::KEY, $clean !== [] ? $clean : self::defaults());
    }

    /**
     * Swap a nav item one step up or down. Returns false if the move is not possible.
     */
    public static function move(int $index, string $direction): bool
    {
        $links = self::links();
        $target = $direction === 'up' ? $index - 1 : $index + 1;

        if ($index < 0 || $index >= count($links) || $target < 0 || $target >= count($links)) {
            return false;
        }

        $swap = $links[$index];
        $links[$index] = $links[$target];
        $links[$target] = $swap;

        self::save($links);

        return true;
    }

    /**
     * @return list<array{label: string, route: string}>
     */
    private static function defaults(): array
    {
        return [
            ['label' => 'Home', 'route' => 'home'],
            ['label' => 'About Us', 'route' => 'about'],
            ['label' => 'Our Services', 'route' => 'services'],
            ['label' => 'Cake Customization', 'route' => 'builder.show'],
        ];
    }
}
