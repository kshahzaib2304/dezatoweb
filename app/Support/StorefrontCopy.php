<?php

namespace App\Support;

use App\Models\SiteSetting;

/**
 * Shared storefront copy: product notes, cart empty state, checkout intro.
 */
final class StorefrontCopy
{
    public const KEY = 'storefront_copy';

    /**
     * @return array<string, string>
     */
    public static function defaults(): array
    {
        return [
            'product_notes_title' => 'Good to know',
            'product_notes_fresh' => 'Cakes are baked fresh and are best the day you receive them. Keep chilled and bring to room temperature before serving.',
            'product_notes_allergy' => 'Our kitchen handles wheat, milk, eggs, soy, and nuts. Tell us about allergies in the order notes.',
            'product_notes_custom_cta' => 'Design a custom cake',
            'product_notes_custom_text' => 'if you need a message, colour, or different size.',
            'cart_empty_title' => 'Your cart is empty',
            'cart_empty_text' => 'Browse the menu and add something sweet.',
            'cart_empty_cta' => 'Browse menu',
            'cart_summary_title' => 'Order summary',
            'cart_checkout_cta' => 'Checkout',
            'cart_continue_cta' => 'Keep shopping',
            'cart_upsell_title' => 'You might also like',
            'cart_upsell_text' => 'Add a little extra to your order.',
            'checkout_title' => 'Checkout',
            'checkout_lede' => 'Confirm your details and place your order. We bake and deliver across Karachi.',
            'checkout_contact_title' => 'Contact',
            'checkout_phone_hint' => 'We’ll confirm your order on this number.',
            'header_order_cta' => 'Order',
            'about_journey_title' => 'Our journey',
            'about_journey_text' => 'A few moments that shaped Dezato Cake House.',
            'about_cta_title' => 'Taste what’s baking',
            'about_cta_text' => 'Order for pickup, Karachi delivery, or plan something sweet for your next gathering.',
            'about_cta_primary' => 'Browse menu',
            'about_cta_secondary' => 'Custom cake',
            'about_story_title' => 'Baked for Karachi celebrations',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function all(): array
    {
        $defaults = self::defaults();
        $stored = SiteSetting::getJson(self::KEY, []);
        $stored = is_array($stored) ? $stored : [];

        $out = [];
        foreach ($defaults as $key => $value) {
            $candidate = trim((string) ($stored[$key] ?? ''));
            $out[$key] = $candidate !== '' ? $candidate : $value;
        }

        return $out;
    }

    public static function get(string $key): string
    {
        return self::all()[$key] ?? '';
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function save(array $data): void
    {
        $defaults = self::defaults();
        $payload = [];

        foreach ($defaults as $key => $value) {
            $payload[$key] = trim((string) ($data[$key] ?? $value)) ?: $value;
        }

        SiteSetting::putJson(self::KEY, $payload);
    }
}
