<?php

namespace App\Support;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Titles, descriptions, canonicals, and robots directives for the storefront.
 */
final class SeoMeta
{
    public static function description(?string $text, int $max = 160): string
    {
        $text = trim(preg_replace('/\s+/u', ' ', (string) $text) ?? '');

        if ($text === '') {
            return '';
        }

        if (mb_strlen($text) <= $max) {
            return $text;
        }

        return rtrim(mb_substr($text, 0, $max - 1), " \t.,;:-").'…';
    }

    /**
     * Stable menu canonical: category + page only (search/sort/weight stay out to limit duplicates).
     *
     * @param  array{category: string, filters: array{q: string, sort: string, weight: string}}  $listing
     */
    public static function menuCanonical(array $listing, LengthAwarePaginator $paginator): string
    {
        $params = [];

        if (($listing['category'] ?? 'all') !== 'all') {
            $params['category'] = $listing['category'];
        }

        if ($paginator->currentPage() > 1) {
            $params['page'] = $paginator->currentPage();
        }

        return route('menu', $params);
    }

    /**
     * @param  array{filters: array{q: string}}  $listing
     */
    public static function menuRobots(array $listing): string
    {
        $query = trim((string) ($listing['filters']['q'] ?? ''));

        return $query !== '' ? 'noindex, follow' : 'index, follow';
    }
}
