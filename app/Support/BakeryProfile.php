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

    /**
     * Publish the bakery's public URL into config for mail / queues.
     *
     * Never force that URL onto browser requests when the host or port differs
     * (e.g. Admin has http://localhost but you open http://127.0.0.1:8000) -
     * that makes asset() point at the wrong origin and the site looks like
     * unstyled plain HTML.
     */
    public static function applySiteUrl(): void
    {
        $url = self::siteUrl();

        if ($url === '') {
            return;
        }

        Config::set('app.url', $url);

        if (app()->runningInConsole()) {
            self::forceRoot($url);

            return;
        }

        if (! self::matchesCurrentRequest($url)) {
            return;
        }

        self::forceRoot($url);
    }

    private static function forceRoot(string $url): void
    {
        URL::forceRootUrl($url);

        if (str_starts_with($url, 'https://')) {
            URL::forceScheme('https');
        }
    }

    private static function matchesCurrentRequest(string $url): bool
    {
        $parts = parse_url($url);

        if ($parts === false || empty($parts['host'])) {
            return false;
        }

        $request = request();
        $scheme = $parts['scheme'] ?? 'http';
        $host = strtolower((string) $parts['host']);
        $port = isset($parts['port'])
            ? (int) $parts['port']
            : ($scheme === 'https' ? 443 : 80);

        return strtolower($request->getHost()) === $host
            && (int) $request->getPort() === $port
            && $request->getScheme() === $scheme;
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
