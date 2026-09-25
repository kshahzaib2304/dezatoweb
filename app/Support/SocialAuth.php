<?php

namespace App\Support;

use App\Models\SiteSetting;
use Illuminate\Support\Facades\Config;

/**
 * Free social sign-in (Google + Facebook) - credentials managed in Admin.
 *
 * Instagram is not offered for login: Meta’s free Instagram APIs do not reliably
 * return an email address, which is required to create bakery accounts safely.
 * Use SocialLinks for the Instagram profile URL instead.
 */
final class SocialAuth
{
    public const KEY = 'social_auth';

    /**
     * @return array<string, array{label: string, enabled: bool, client_id: string, client_secret_set: bool, redirect: string, help: string}>
     */
    public static function providers(): array
    {
        $stored = SiteSetting::getJson(self::KEY, []);
        $stored = is_array($stored) ? $stored : [];

        $out = [];

        foreach (self::catalog() as $id => $meta) {
            $row = is_array($stored[$id] ?? null) ? $stored[$id] : [];
            $out[$id] = [
                'label' => $meta['label'],
                'help' => $meta['help'],
                'enabled' => (bool) ($row['enabled'] ?? false),
                'client_id' => (string) ($row['client_id'] ?? ''),
                'client_secret_set' => SecureSettings::isSet("social_{$id}_secret"),
                'redirect' => url('/auth/'.$id.'/callback'),
            ];
        }

        return $out;
    }

    /**
     * @param  array<string, array<string, mixed>>  $input
     * @param  array<string, string|null>  $secrets
     */
    public static function save(array $input, array $secrets = []): void
    {
        $clean = [];

        foreach (array_keys(self::providers()) as $id) {
            $row = is_array($input[$id] ?? null) ? $input[$id] : [];
            $clean[$id] = [
                'enabled' => (bool) ($row['enabled'] ?? false),
                'client_id' => trim((string) ($row['client_id'] ?? '')),
            ];

            SecureSettings::putIfFilled("social_{$id}_secret", $secrets[$id] ?? null);
        }

        SiteSetting::putJson(self::KEY, $clean);
        self::apply();
    }

    /**
     * @return list<array{id: string, label: string}>
     */
    public static function enabledForLogin(): array
    {
        $list = [];

        foreach (self::providers() as $id => $provider) {
            if ($provider['enabled'] && $provider['client_id'] !== '' && $provider['client_secret_set']) {
                $list[] = ['id' => $id, 'label' => $provider['label']];
            }
        }

        return $list;
    }

    public static function isEnabled(string $provider): bool
    {
        return collect(self::enabledForLogin())->contains(fn (array $row): bool => $row['id'] === $provider);
    }

    public static function apply(): void
    {
        $stored = SiteSetting::getJson(self::KEY, []);
        $stored = is_array($stored) ? $stored : [];

        foreach (array_keys(self::catalog()) as $id) {
            $row = is_array($stored[$id] ?? null) ? $stored[$id] : [];

            Config::set("services.{$id}", [
                'client_id' => (string) ($row['client_id'] ?? ''),
                'client_secret' => SecureSettings::get("social_{$id}_secret") ?? '',
                'redirect' => url('/auth/'.$id.'/callback'),
            ]);
        }
    }

    /**
     * @return array<string, array{label: string, help: string}>
     */
    private static function catalog(): array
    {
        return [
            'google' => [
                'label' => 'Google',
                'help' => 'Free Google Cloud OAuth client. Customers tap “Continue with Google”.',
            ],
            'facebook' => [
                'label' => 'Facebook',
                'help' => 'Free Meta / Facebook Login app. Customers tap “Continue with Facebook”.',
            ],
        ];
    }
}
