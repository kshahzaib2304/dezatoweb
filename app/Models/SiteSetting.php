<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SiteSetting extends Model
{
    protected $primaryKey = 'key';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'key',
        'value',
    ];

    public static function getValue(string $key, ?string $default = null): ?string
    {
        return Cache::remember("site_setting.{$key}", 300, function () use ($key, $default): ?string {
            $row = static::query()->find($key);

            return $row?->value ?? $default;
        });
    }

    public static function putValue(string $key, ?string $value): void
    {
        static::query()->updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );

        Cache::forget("site_setting.{$key}");
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
