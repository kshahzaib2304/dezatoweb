<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Throwable;

class SiteSetting extends Model
{
    protected $primaryKey = 'key';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'key',
        'value',
    ];

    /**
     * One map for the request, backed by a single cache entry.
     * Per-key cache lookups were a database query each, and admin saves
     * read the same settings many times.
     *
     * @var array<string, string|null>|null
     */
    private static ?array $values = null;

    public static function getValue(string $key, ?string $default = null): ?string
    {
        $values = self::values();

        if (! array_key_exists($key, $values) || $values[$key] === null) {
            return $default;
        }

        return $values[$key];
    }

    public static function putValue(string $key, ?string $value): void
    {
        static::query()->updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );

        if (self::$values !== null) {
            self::$values[$key] = $value;
        }

        Cache::forget('site_settings.all');
        Cache::forget("site_setting.{$key}");
    }

    /**
     * @return array<string, string|null>
     */
    private static function values(): array
    {
        if (self::$values !== null) {
            return self::$values;
        }

        try {
            $cached = Cache::remember('site_settings.all', 300, function (): array {
                return static::query()->pluck('value', 'key')->all();
            });
        } catch (Throwable) {
            try {
                $cached = static::query()->pluck('value', 'key')->all();
            } catch (Throwable) {
                $cached = [];
            }
        }

        self::$values = is_array($cached) ? $cached : [];

        return self::$values;
    }

    public static function getJson(string $key, mixed $default = null): mixed
    {
        $raw = static::getValue($key);

        if ($raw === null || $raw === '') {
            return $default;
        }

        $decoded = json_decode($raw, true);

        return is_array($decoded) ? $decoded : $default;
    }

    public static function putJson(string $key, array $value): void
    {
        static::putValue($key, json_encode($value, JSON_UNESCAPED_UNICODE));
    }
}
