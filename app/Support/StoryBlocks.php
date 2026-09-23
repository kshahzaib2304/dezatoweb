<?php

namespace App\Support;

use App\Models\SiteSetting;
use Illuminate\Support\Str;

/**
 * About milestones + Services packages (Admin-managed).
 */
final class StoryBlocks
{
    public const MILESTONES_KEY = 'about_milestones';

    public const PACKAGES_KEY = 'service_packages';

    /**
     * @return list<array{id: string, year: string, title: string, text: string}>
     */
    public static function milestones(): array
    {
        $stored = SiteSetting::getJson(self::MILESTONES_KEY);
        $source = is_array($stored) && $stored !== []
            ? $stored
            : config('dezato.about.milestones', []);

        return array_values(array_map(function (array $row): array {
            return [
                'id' => (string) ($row['id'] ?? Str::slug(($row['year'] ?? '').'-'.($row['title'] ?? 'm'))),
                'year' => trim((string) ($row['year'] ?? '')),
                'title' => trim((string) ($row['title'] ?? '')),
                'text' => trim((string) ($row['text'] ?? '')),
            ];
        }, $source));
    }

    /**
     * @param  list<array<string, mixed>>  $rows
     */
    public static function saveMilestones(array $rows): void
    {
        $clean = [];

        foreach ($rows as $row) {
            if (! is_array($row)) {
                continue;
            }

            $year = trim((string) ($row['year'] ?? ''));
            $title = trim((string) ($row['title'] ?? ''));
            if ($year === '' || $title === '') {
                continue;
            }

            $clean[] = [
                'id' => (string) ($row['id'] ?? Str::slug($year.'-'.$title)),
                'year' => $year,
                'title' => $title,
                'text' => trim((string) ($row['text'] ?? '')),
            ];
        }

        SiteSetting::putJson(self::MILESTONES_KEY, $clean);
    }

    /**
     * @return list<array{id: string, title: string, serves: string, price: string, blurb: string, image: string}>
     */
    public static function packages(): array
    {
        $stored = SiteSetting::getJson(self::PACKAGES_KEY);
        $source = is_array($stored) && $stored !== []
            ? $stored
            : config('dezato.services.packages', []);

        return array_values(array_map(function (array $row): array {
            return [
                'id' => (string) ($row['id'] ?? Str::slug((string) ($row['title'] ?? 'package'))),
                'title' => trim((string) ($row['title'] ?? '')),
                'serves' => trim((string) ($row['serves'] ?? '')),
                'price' => trim((string) ($row['price'] ?? '')),
                'blurb' => trim((string) ($row['blurb'] ?? '')),
                'image' => trim((string) ($row['image'] ?? '')),
            ];
        }, $source));
    }

    /**
     * @return list<array{id: string, title: string, serves: string, price: string, blurb: string, image: string}>
     */
    public static function packagesForStorefront(): array
    {
        return array_map(function (array $row): array {
            $row['image'] = MediaPaths::public($row['image'] ?? null, 'images/home/promo-catering.png');

            return $row;
        }, self::packages());
    }

    /**
     * @param  list<array<string, mixed>>  $rows
     */
    public static function savePackages(array $rows): void
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
                'serves' => trim((string) ($row['serves'] ?? '')),
                'price' => trim((string) ($row['price'] ?? '')),
                'blurb' => trim((string) ($row['blurb'] ?? '')),
                'image' => trim((string) ($row['image'] ?? '')),
            ];
        }

        SiteSetting::putJson(self::PACKAGES_KEY, $clean);
    }
}
