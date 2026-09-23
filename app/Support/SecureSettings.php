<?php

namespace App\Support;

use App\Models\SiteSetting;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;
use Throwable;

/**
 * Encrypted SiteSetting helpers for API secrets editable in Admin.
 */
final class SecureSettings
{
    public static function get(string $key, ?string $default = null): ?string
    {
        $raw = SiteSetting::getValue($key);

        if ($raw === null || $raw === '') {
            return $default;
        }

        try {
            return Crypt::decryptString($raw);
        } catch (DecryptException|Throwable) {
            // Legacy plain-text fallback (pre-encryption values).
            return $raw;
        }
    }

    public static function put(string $key, ?string $value): void
    {
        $trimmed = trim((string) $value);

        if ($trimmed === '') {
            SiteSetting::putValue($key, null);

            return;
        }

        SiteSetting::putValue($key, Crypt::encryptString($trimmed));
    }

    /**
     * Keep the existing secret when the admin leaves the field blank.
     */
    public static function putIfFilled(string $key, ?string $value): void
    {
        if ($value === null || trim($value) === '') {
            return;
        }

        self::put($key, $value);
    }

    public static function isSet(string $key): bool
    {
        $value = self::get($key);

        return $value !== null && $value !== '';
    }

    public static function maskedHint(string $key): string
    {
        return self::isSet($key)
            ? 'Saved - leave blank to keep the current value.'
            : 'Not set yet.';
    }
}
