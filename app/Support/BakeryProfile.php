<?php

namespace App\Support;

use App\Models\SiteSetting;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;

/**
 * Bakery contact + site URL + homepage settings (editable from Admin).
 */
final class BakeryProfile
{
    public static function notifyEmail(): string
    {
        return (string) (SiteSetting::getValue('notify_email')
            ?: config('dezato.brand.email', 'hello@dezato.pk'));
    }

    public static function publicEmail(): string
    {
        return (string) (SiteSetting::getValue('public_email')
            ?: config('dezato.brand.email', 'hello@dezato.pk'));
    }

    public static function phone(): string
    {
        return (string) (SiteSetting::getValue('public_phone')
            ?: config('dezato.brand.phone', ''));
    }

    public static function whatsapp(): string
    {
        return (string) (SiteSetting::getValue('whatsapp') ?: self::phone());
    }

    public static function siteUrl(): string
    {
        $stored = SiteSetting::getValue('site_url');

        if ($stored) {
            return rtrim($stored, '/');
        }

        return rtrim((string) config('app.url'), '/');
    }

    /**
     * Digits-only WhatsApp number for wa.me links.
     */
    public static function whatsappDigits(): string
    {
        return preg_replace('/\D+/', '', self::whatsapp()) ?? '';
    }

    public static function whatsappUrl(?string $prefill = null): ?string
    {
        $digits = self::whatsappDigits();

        if ($digits === '') {
            return null;
        }

        $url = 'https://wa.me/'.$digits;

        if ($prefill) {
            $url .= '?text='.rawurlencode($prefill);
        }

        return $url;
    }

    public static function applySiteUrl(): void
    {
        $url = self::siteUrl();

        if ($url === '') {
            return;
        }

        Config::set('app.url', $url);
        URL::forceRootUrl($url);

        if (str_starts_with($url, 'https://')) {
            URL::forceScheme('https');
        }
    }

    /**
     * First active hero slide (legacy helpers / fallbacks).
     *
     * @return array{image: string, headline: string, lede: string}
     */
    public static function hero(): array
    {
        $slide = HeroSlider::activeSlides()[0];

        return [
            'image' => $slide['image'],
            'headline' => $slide['headline'],
            'lede' => $slide['lede'],
        ];
    }
}
