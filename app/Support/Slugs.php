<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

final class Slugs
{
    /**
     * @param  class-string<Model>  $modelClass
     */
    public static function unique(string $modelClass, string $source, ?int $ignoreId = null, int $maxLength = 180): string
    {
        $base = Str::slug($source);
        $base = $base !== '' ? Str::limit($base, $maxLength, '') : 'item';
        $candidate = $base;
        $suffix = 2;

        while (self::taken($modelClass, $candidate, $ignoreId)) {
            $tail = '-'.$suffix;
            $candidate = Str::limit($base, max(1, $maxLength - strlen($tail)), '').$tail;
            $suffix++;

            if ($suffix > 100) {
                $tail = '-'.Str::lower(Str::random(4));

                return Str::limit($base, max(1, $maxLength - strlen($tail)), '').$tail;
            }
        }

        return $candidate;
    }

    /**
     * @param  class-string<Model>  $modelClass
     */
    private static function taken(string $modelClass, string $slug, ?int $ignoreId): bool
    {
        return $modelClass::query()
            ->where('slug', $slug)
            ->when($ignoreId !== null, fn ($query) => $query->whereKeyNot($ignoreId))
            ->exists();
    }
}
