<?php

namespace App\Support;

use App\Models\SiteSetting;

/**
 * Online payment gateway keys (JazzCash / Easypaisa / card) - stored for go-live.
 * Transfer account numbers stay under Payment options; these are API merchant keys.
 */
final class GatewayCredentials
{
    public const KEY = 'gateway_credentials';

    /**
     * @return array<string, array{label: string, hint: string, fields: array<string, array{label: string, secret?: bool, value: string, set?: bool}>}>
     */
    public static function groups(): array
    {
        $stored = SiteSetting::getJson(self::KEY, []);
        $stored = is_array($stored) ? $stored : [];

        $definitions = [
            'jazzcash' => [
                'label' => 'JazzCash (online / merchant API)',
                'hint' => 'Fill these when your JazzCash merchant account is ready. Leave blank until then.',
                'fields' => [
                    'merchant_id' => ['label' => 'Merchant ID'],
                    'password' => ['label' => 'Password', 'secret' => true],
                    'integrity_salt' => ['label' => 'Integrity salt', 'secret' => true],
                    'return_url' => ['label' => 'Return URL (optional)'],
                ],
            ],
            'easypaisa' => [
                'label' => 'Easypaisa (online / merchant API)',
                'hint' => 'Store ID and credentials from Easypaisa when you enable online payments.',
                'fields' => [
                    'store_id' => ['label' => 'Store ID'],
                    'account_number' => ['label' => 'Account number'],
                    'hash_key' => ['label' => 'Hash key', 'secret' => true],
                ],
            ],
            'card' => [
                'label' => 'Card gateway (Stripe / local partner)',
                'hint' => 'Public and secret keys from your card payment partner.',
                'fields' => [
                    'provider' => ['label' => 'Provider name (e.g. Stripe)'],
                    'public_key' => ['label' => 'Public / publishable key'],
                    'secret_key' => ['label' => 'Secret key', 'secret' => true],
                    'webhook_secret' => ['label' => 'Webhook secret', 'secret' => true],
                ],
            ],
        ];

        $out = [];

        foreach ($definitions as $group => $meta) {
            $row = is_array($stored[$group] ?? null) ? $stored[$group] : [];
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
                'fields' => $fields,
            ];
        }

        return $out;
    }

    /**
     * @param  array<string, array<string, string|null>>  $input
     */
    public static function save(array $input): void
    {
        $groups = self::groups();
        $clean = [];

        foreach ($groups as $group => $meta) {
            $row = is_array($input[$group] ?? null) ? $input[$group] : [];
            $clean[$group] = [];

            foreach ($meta['fields'] as $field => $fieldMeta) {
                $value = isset($row[$field]) ? trim((string) $row[$field]) : '';

                if ($fieldMeta['secret']) {
                    SecureSettings::putIfFilled("gateway_{$group}_{$field}", $value !== '' ? $value : null);
                } else {
                    $clean[$group][$field] = $value;
                }
            }
        }

        SiteSetting::putJson(self::KEY, $clean);
    }
}
