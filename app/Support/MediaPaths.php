<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

/**
 * Resolve stored / public image paths for storefront and admin previews.
 */
final class MediaPaths
{
    private const EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg'];

    public static function public(?string $path, string $fallback = 'images/home/hero.jpg'): string
    {
        $path = trim((string) $path);

        if ($path === '' || str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $fallback;
        }

        if (! str_starts_with($path, 'storage/') && ! str_starts_with($path, 'images/')) {
            $path = 'storage/'.$path;
        }

        if (self::exists($path)) {
            return $path;
        }

        $alternate = self::alternateExisting($path);
        if ($alternate !== null) {
            return $alternate;
        }

        return self::exists($fallback) ? $fallback : 'images/home/hero.jpg';
    }

    public static function deleteIfOwned(?string $path): void
    {
        $path = trim((string) $path);

        if ($path === '') {
            return;
        }

        if (str_starts_with($path, 'storage/')) {
            $path = substr($path, strlen('storage/'));
        }

        // Only remove files owned by the public disk (uploaded media), never theme assets.
        if (! str_starts_with($path, 'images/') && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    private static function exists(string $path): bool
    {
        if (str_starts_with($path, 'storage/')) {
            return Storage::disk('public')->exists(substr($path, strlen('storage/')));
        }

        return is_file(public_path($path));
    }

    private static function alternateExisting(string $path): ?string
    {
        $dot = strrpos($path, '.');
        if ($dot === false) {
            return null;
        }

        $base = substr($path, 0, $dot);
        $current = strtolower(substr($path, $dot + 1));

        foreach (self::EXTENSIONS as $ext) {
            if ($ext === $current) {
                continue;
            }

            $candidate = $base.'.'.$ext;
            if (self::exists($candidate)) {
                return $candidate;
            }
        }

        return null;
    }
}
