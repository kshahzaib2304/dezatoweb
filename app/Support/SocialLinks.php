<?php

namespace App\Support;

use App\Models\SiteSetting;

/**
 * Public social profile URLs shown in the website footer (editable in Admin).
 */
final class SocialLinks
{
    public const KEY = 'social_links';

    /**
     * @return array<string, array{label: string, url: string, placeholder: string}>
     */
    public static function catalog(): array
    {
        $stored = SiteSetting::getJson(self::KEY, []);
        $stored = is_array($stored) ? $stored : [];

        $defaults = [
            'facebook' => [
                'label' => 'Facebook',
                'placeholder' => 'https://facebook.com/your-bakery',
            ],
            'instagram' => [
                'label' => 'Instagram',
                'placeholder' => 'https://instagram.com/your-bakery',
            ],
            'tiktok' => [
                'label' => 'TikTok',
                'placeholder' => 'https://tiktok.com/@your-bakery',
            ],
            'youtube' => [
                'label' => 'YouTube',
                'placeholder' => 'https://youtube.com/@your-bakery',
            ],
        ];

        $out = [];

        foreach ($defaults as $id => $meta) {
            $out[$id] = [
                'label' => $meta['label'],
                'placeholder' => $meta['placeholder'],
                'url' => trim((string) ($stored[$id] ?? '')),
            ];
        }

        return $out;
    }

    /**
     * @param  array<string, string|null>  $urls
     */
    public static function save(array $urls): void
    {
        $clean = [];

        foreach (array_keys(self::catalog()) as $id) {
            $url = trim((string) ($urls[$id] ?? ''));
            $clean[$id] = $url;
        }

        SiteSetting::putJson(self::KEY, $clean);
    }

    /**
     * @return list<array{id: string, label: string, url: string}>
     */
    public static function forFooter(): array
    {
        return collect(self::catalog())
            ->filter(fn (array $row): bool => $row['url'] !== '')
            ->map(fn (array $row, string $id): array => [
                'id' => $id,
                'label' => $row['label'],
                'url' => $row['url'],
            ])
            ->values()
            ->all();
    }
}
