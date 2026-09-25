<?php

namespace App\Support;

use App\Models\SiteSetting;

final class FulfillmentSchedule
{
    public const SETTING_KEY = 'fulfillment_schedule';

    /**
     * @return array{slots: list<string>, min_hours: int, note: string}
     */
    public static function config(): array
    {
        $defaults = [
            'slots' => config('dezato_ui.checkout.time_slots', [
                '10:00 AM  -  12:00 PM',
                '12:00 PM  -  2:00 PM',
                '2:00 PM  -  4:00 PM',
                '4:00 PM  -  6:00 PM',
                '6:00 PM  -  8:00 PM',
                '8:00 PM  -  10:00 PM',
            ]),
            'min_hours' => 4,
            'note' => 'Same-day slots may not always be available - we will confirm by phone if needed.',
        ];

        $stored = SiteSetting::getJson(self::SETTING_KEY);

        if (! is_array($stored)) {
            return $defaults;
        }

        $slots = collect($stored['slots'] ?? [])
            ->map(fn ($slot) => trim((string) $slot))
            ->filter()
            ->values()
            ->all();

        return [
            'slots' => $slots !== [] ? $slots : $defaults['slots'],
            'min_hours' => max(0, (int) ($stored['min_hours'] ?? $defaults['min_hours'])),
            'note' => trim((string) ($stored['note'] ?? $defaults['note'])),
        ];
    }

    /**
     * @param  array{slots?: list<string>, min_hours?: int, note?: string}  $data
     */
    public static function save(array $data): void
    {
        $slots = collect($data['slots'] ?? [])
            ->map(fn ($slot) => trim((string) $slot))
            ->filter()
            ->unique()
            ->values()
            ->all();

        SiteSetting::putJson(self::SETTING_KEY, [
            'slots' => $slots,
            'min_hours' => max(0, (int) ($data['min_hours'] ?? 0)),
            'note' => trim((string) ($data['note'] ?? '')),
        ]);
    }

    /**
     * @return list<string>
     */
    public static function slots(): array
    {
        return self::config()['slots'];
    }

    public static function earliestDate(): string
    {
        $hours = self::config()['min_hours'];
        $from = now()->addHours($hours);

        return $from->toDateString();
    }

    public static function note(): string
    {
        return self::config()['note'];
    }
}
