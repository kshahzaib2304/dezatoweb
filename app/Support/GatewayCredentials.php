<?php

namespace App\Support;

use App\Models\SiteSetting;

/**
 * Online payment gateway keys (JazzCash / Easypaisa / card) - Admin-managed.
 * Transfer account numbers stay under Payment options.
 */
final class GatewayCredentials
{
    public const KEY = 'gateway_credentials';

    public const FLAGS_KEY = 'gateway_flags';

    /**
     * @return array<string, array{label: string, hint: string, online_enabled: bool, sandbox: bool, fields: array<string, array{label: string, secret?: bool, value: string, set?: bool}>}>
     */
    public static function groups(): array
    {
        $stored = SiteSetting::getJson(self::KEY, []);
        $stored = is_array($stored) ? $stored : [];
        $flags = SiteSetting::getJson(self::FLAGS_KEY, []);
        $flags = is_array($flags) ? $flags : [];

        $definitions = [
            'jazzcash' => [
                'label' => 'JazzCash (online checkout)',
                'hint' => 'Turn on only when Merchant ID + Password + Integrity salt are filled. Customers will be sent to JazzCash to pay.',
                'fields' => [
                    'merchant_id' => ['label' => 'Merchant ID'],
                    'password' => ['label' => 'Password', 'secret' => true],
                    'integrity_salt' => ['label' => 'Integrity salt', 'secret' => true],
                ],
            ],
            'easypaisa' => [
                'label' => 'Easypaisa (online checkout)',
                'hint' => 'Turn on when Store ID + Hash key are ready. Until then, customers can still use the manual Easypaisa number under Payment options.',
                'fields' => [
                    'store_id' => ['label' => 'Store ID'],
                    'account_number' => ['label' => 'Merchant account number'],
                    'hash_key' => ['label' => 'Hash key', 'secret' => true],
                ],
            ],
            'card' => [
                'label' => 'Card (Stripe Checkout)',
                'hint' => 'Set Provider to “Stripe”, then paste publishable + secret keys. Other providers can be stored for a developer later.',
                'fields' => [
                    'provider' => ['label' => 'Provider name (e.g. Stripe)'],
                    'public_key' => ['label' => 'Public / publishable key'],
                    'secret_key' => ['label' => 'Secret key', 'secret' => true],
                    'webhook_secret' => ['label' => 'Webhook secret (optional)', 'secret' => true],
                ],
            ],
        ];

        $out = [];

        foreach ($definitions as $group => $meta) {
            $row = is_array($stored[$group] ?? null) ? $stored[$group] : [];
            $groupFlags = is_array($flags[$group] ?? null) ? $flags[$group] : [];
            $fields = [];

            foreach ($meta['fields'] as $field => $fieldMeta) {
                $isSecret = (bool) ($fieldMeta['secret'] ?? false);
                $secretKey = "gateway_{$group}_{$field}";

                $fields[$field] = [
                    'label' => $fieldMeta['label'],
                    'secret' => $isSecret,
                    'value' => $isSecret ? '' : (string) ($row[$field] ?? ''),
                    'set' => $isSecret ? SecureSettings::isSet($secretKey) : (($row[$field] ?? '') !== ''),
                ];
            }

            $out[$group] = [
                'label' => $meta['label'],
                'hint' => $meta['hint'],
                'online_enabled' => (bool) ($groupFlags['online_enabled'] ?? false),
                'sandbox' => (bool) ($groupFlags['sandbox'] ?? true),
                'fields' => $fields,
            ];
        }

        return $out;
    }

    /**
     * @param  array<string, array<string, string|null>>  $input
     * @param  array<string, array<string, mixed>>  $flagsInput
     */
    public static function save(array $input, array $flagsInput = []): void
    {
        $groups = self::groups();
        $clean = [];
        $flags = [];

        foreach ($groups as $group => $meta) {
            $row = is_array($input[$group] ?? null) ? $input[$group] : [];
            $flagRow = is_array($flagsInput[$group] ?? null) ? $flagsInput[$group] : [];
            $clean[$group] = [];

            foreach ($meta['fields'] as $field => $fieldMeta) {
                $value = isset($row[$field]) ? trim((string) $row[$field]) : '';

                if ($fieldMeta['secret']) {
                    SecureSettings::putIfFilled("gateway_{$group}_{$field}", $value !== '' ? $value : null);
                } else {
                    $clean[$group][$field] = $value;
                }
            }

            $flags[$group] = [
                'online_enabled' => (bool) ($flagRow['online_enabled'] ?? false),
                'sandbox' => (bool) ($flagRow['sandbox'] ?? true),
            ];
        }

        SiteSetting::putJson(self::KEY, $clean);
        SiteSetting::putJson(self::FLAGS_KEY, $flags);
    }

    public static function field(string $group, string $field): string
    {
        $groups = self::groups();
        $meta = $groups[$group]['fields'][$field] ?? null;

        if ($meta === null) {
            return '';
        }

        if ($meta['secret']) {
            return (string) (SecureSettings::get("gateway_{$group}_{$field}") ?? '');
        }

        return (string) $meta['value'];
    }

    public static function onlineEnabled(string $group): bool
    {
        return (bool) (self::groups()[$group]['online_enabled'] ?? false);
    }

    public static function sandbox(string $group): bool
    {
        return (bool) (self::groups()[$group]['sandbox'] ?? true);
    }

    public static function isReady(string $group): bool
    {
        if (! self::onlineEnabled($group)) {
            return false;
        }

        return match ($group) {
            'jazzcash' => self::field('jazzcash', 'merchant_id') !== ''
                && self::field('jazzcash', 'password') !== ''
                && self::field('jazzcash', 'integrity_salt') !== '',
            'easypaisa' => self::field('easypaisa', 'store_id') !== ''
                && self::field('easypaisa', 'hash_key') !== '',
            'card' => str_contains(strtolower(self::field('card', 'provider')), 'stripe')
                && self::field('card', 'public_key') !== ''
                && self::field('card', 'secret_key') !== '',
            default => false,
        };
    }
}
