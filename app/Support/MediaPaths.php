<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

/**
 * Resolve stored / public image paths for storefront and admin previews.
 */
final class MediaPaths
{
    public static function public(?string $path, string $fallback = 'images/home/hero.jpg'): string
    {
        $path = trim((string) $path);

        if ($path === '') {
            return $fallback;
        }

        if (Storage::disk('public')->exists($path)) {
            return 'storage/'.$path;
        }

        if (str_starts_with($path, 'storage/') || str_starts_with($path, 'images/')) {
            return $path;
        }

        if (! str_starts_with($path, 'http')) {
            return $path;
        }

        return $fallback;
    }

    public static function deleteIfOwned(?string $path): void
    {
        $path = trim((string) $path);

        if ($path !== '' && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
