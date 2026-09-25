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

        if ($path === '' || str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $fallback;
        }

        if (str_starts_with($path, 'storage/') || str_starts_with($path, 'images/')) {
            return $path;
        }

        return 'storage/'.$path;
    }

    public static function deleteIfOwned(?string $path): void
    {
        $path = trim((string) $path);

        if ($path !== '' && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
