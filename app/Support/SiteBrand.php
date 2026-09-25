<?php

namespace App\Support;

use App\Models\SiteSetting;
use Illuminate\Http\UploadedFile;

/**
 * Public brand name / short label / logos - editable from Admin.
 */
final class SiteBrand
{
    public const KEY = 'site_brand';

    /**
     * @return array{
     *     name: string,
     *     short_name: string,
     *     tagline: string,
     *     header_tag: string,
     *     logo_mark: string,
     *     logo_icon: string
     * }
     */
    public static function all(): array
    {
        $stored = SiteSetting::getJson(self::KEY, []);
        $stored = is_array($stored) ? $stored : [];

        $logoMark = MediaPaths::public(
            (string) ($stored['logo_mark'] ?? 'images/brand/logo-mark.svg'),
            'images/brand/logo-mark.svg'
        );
        $logoIcon = MediaPaths::public(
            (string) ($stored['logo_icon'] ?? 'images/brand/logo-icon.jpg'),
            'images/brand/logo-icon.jpg'
        );

        return [
            'name' => trim((string) ($stored['name'] ?? config('dezato.brand.name', 'Dezato Cake House')))
                ?: 'Dezato Cake House',
            'short_name' => trim((string) ($stored['short_name'] ?? 'Dezato')) ?: 'Dezato',
            'tagline' => trim((string) ($stored['tagline'] ?? config('dezato.brand.tagline', '')))
                ?: 'Handcrafted cakes & desserts in Karachi',
            'header_tag' => trim((string) ($stored['header_tag'] ?? 'cake house')) ?: 'cake house',
            'logo_mark' => $logoMark,
            'logo_icon' => $logoIcon,
        ];
    }

    public static function name(): string
    {
        return self::all()['name'];
    }

    public static function shortName(): string
    {
        return self::all()['short_name'];
    }

    public static function tagline(): string
    {
        return self::all()['tagline'];
    }

    public static function headerTag(): string
    {
        return self::all()['header_tag'];
    }

    public static function logoMark(): string
    {
        return self::all()['logo_mark'];
    }

    public static function logoIcon(): string
    {
        return self::all()['logo_icon'];
    }

    /**
     * @param  array{name?: string, short_name?: string, tagline?: string, header_tag?: string}  $data
     */
    public static function save(array $data, ?UploadedFile $logoMark = null, ?UploadedFile $logoIcon = null): void
    {
        $current = self::all();

        $markPath = $current['logo_mark'];
        if ($logoMark instanceof UploadedFile) {
            MediaPaths::deleteIfOwned($markPath);
            $markPath = $logoMark->store('brand', 'public');
        }

        $iconPath = $current['logo_icon'];
        if ($logoIcon instanceof UploadedFile) {
            MediaPaths::deleteIfOwned($iconPath);
            $iconPath = $logoIcon->store('brand', 'public');
        }

        SiteSetting::putJson(self::KEY, [
            'name' => trim((string) ($data['name'] ?? '')) ?: 'Dezato Cake House',
            'short_name' => trim((string) ($data['short_name'] ?? '')) ?: 'Dezato',
            'tagline' => trim((string) ($data['tagline'] ?? '')),
            'header_tag' => trim((string) ($data['header_tag'] ?? '')) ?: 'cake house',
            'logo_mark' => self::persistPath($markPath),
            'logo_icon' => self::persistPath($iconPath),
        ]);
    }

    private static function persistPath(string $path): string
    {
        $path = trim($path);

        return str_starts_with($path, 'storage/')
            ? substr($path, strlen('storage/'))
            : $path;
    }
}
