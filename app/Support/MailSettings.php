<?php

namespace App\Support;

use App\Models\SiteSetting;
use Illuminate\Support\Facades\Config;
use Throwable;

/**
 * Outgoing email (SMTP) settings managed from the Admin panel.
 */
final class MailSettings
{
    public const KEY = 'mail_settings';

    /**
     * @return array{
     *     enabled: bool,
     *     host: string,
     *     port: int,
     *     encryption: string,
     *     username: string,
     *     password: string,
     *     from_address: string,
     *     from_name: string
     * }
     */
    public static function all(): array
    {
        $stored = SiteSetting::getJson(self::KEY, []);
        $stored = is_array($stored) ? $stored : [];

        return [
            'enabled' => (bool) ($stored['enabled'] ?? false),
            'host' => (string) ($stored['host'] ?? ''),
            'port' => (int) ($stored['port'] ?? 587),
            'encryption' => (($stored['encryption'] ?? 'tls') === '' ? 'none' : (string) ($stored['encryption'] ?? 'tls')),
            'username' => (string) ($stored['username'] ?? ''),
            'password' => (string) (SecureSettings::get('mail_smtp_password') ?? ''),
            'from_address' => (string) ($stored['from_address'] ?? config('mail.from.address', '')),
            'from_name' => (string) ($stored['from_name'] ?? config('mail.from.name', 'Dezato Cake House')),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function save(array $data, ?string $passwordInput = null): void
    {
        SiteSetting::putJson(self::KEY, [
            'enabled' => (bool) ($data['enabled'] ?? false),
            'host' => trim((string) ($data['host'] ?? '')),
            'port' => (int) ($data['port'] ?? 587),
            'encryption' => in_array($data['encryption'] ?? 'tls', ['tls', 'ssl', 'none'], true)
                ? (($data['encryption'] ?? 'tls') === 'none' ? '' : (string) $data['encryption'])
                : 'tls',
            'username' => trim((string) ($data['username'] ?? '')),
            'from_address' => trim((string) ($data['from_address'] ?? '')),
            'from_name' => trim((string) ($data['from_name'] ?? '')),
        ]);

        SecureSettings::putIfFilled('mail_smtp_password', $passwordInput);
    }

    /**
     * Apply Admin SMTP settings over .env defaults at runtime.
     */
    public static function apply(): void
    {
        try {
            $settings = self::all();
        } catch (Throwable) {
            return;
        }

        if (! $settings['enabled'] || $settings['host'] === '' || $settings['from_address'] === '') {
            return;
        }

        Config::set('mail.default', 'smtp');
        Config::set('mail.mailers.smtp.host', $settings['host']);
        Config::set('mail.mailers.smtp.port', $settings['port']);
        Config::set('mail.mailers.smtp.username', $settings['username'] ?: null);
        Config::set('mail.mailers.smtp.password', $settings['password'] !== '' ? $settings['password'] : null);
        Config::set('mail.mailers.smtp.scheme', $settings['encryption'] !== '' ? $settings['encryption'] : null);
        Config::set('mail.from.address', $settings['from_address']);
        Config::set('mail.from.name', $settings['from_name'] !== '' ? $settings['from_name'] : 'Dezato Cake House');
    }
}
