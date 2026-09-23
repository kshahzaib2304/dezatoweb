<?php

namespace App\Support;

use App\Models\SiteSetting;

/**
 * Editable website pages & section copy for non-technical admins.
 */
final class SiteContent
{
    public const PAGES = [
        'privacy' => [
            'label' => 'Privacy Policy',
            'route' => 'pages.privacy',
            'default_title' => 'Privacy Policy',
            'default_body' => "We collect only the information needed to fulfill your cake orders - name, phone, email, and delivery details.\n\nWe do not sell your personal information. Order details are shared only with our bakery team and delivery partners as needed.\n\nFor questions about your data, contact us using the phone or email listed on this website.",
        ],
        'terms' => [
            'label' => 'Terms & Conditions',
            'route' => 'pages.terms',
            'default_title' => 'Terms & Conditions',
            'default_body' => "Orders are confirmed once we receive your request and (where applicable) payment.\n\nCustom cakes may require advance notice. Same-day availability is not guaranteed.\n\nPlease check flavour, size, and delivery details carefully before placing an order. Cancellations after baking has started may not be refundable.\n\nPrices are in Pakistani Rupees (PKR) and may change without prior notice.",
        ],
        'faq' => [
            'label' => 'FAQ',
            'route' => 'pages.faq',
            'default_title' => 'Frequently Asked Questions',
            'default_body' => "How far in advance should I order?\nFor celebration cakes we recommend 24–48 hours. Custom designs may need more time.\n\nDo you deliver in Karachi?\nYes - pickup, local delivery, and Pakistan courier options are available at checkout.\n\nCan I write a message on the cake?\nYes. Add your message in the notes or custom cake builder.\n\nWhat payment methods do you accept?\nCash on delivery/pickup, pay at bakery, and (when enabled) bank / JazzCash / Easypaisa transfer.",
        ],
    ];

    public const SECTIONS = [
        'about_intro' => [
            'label' => 'About page intro',
            'hint' => 'The main paragraph on the About Us page.',
        ],
        'services_intro' => [
            'label' => 'Services page intro',
            'hint' => 'Short intro shown at the top of Our Services (optional supporting text).',
        ],
    ];

    /**
     * @return array{title: string, body: string}
     */
    public static function page(string $key): array
    {
        $meta = self::PAGES[$key] ?? null;

        if ($meta === null) {
            return ['title' => 'Page', 'body' => ''];
        }

        $stored = SiteSetting::getJson('page_'.$key, []);

        return [
            'title' => trim((string) ($stored['title'] ?? $meta['default_title'])) ?: $meta['default_title'],
            'body' => trim((string) ($stored['body'] ?? $meta['default_body'])) ?: $meta['default_body'],
        ];
    }

    public static function savePage(string $key, string $title, string $body): void
    {
        if (! isset(self::PAGES[$key])) {
            return;
        }

        SiteSetting::putJson('page_'.$key, [
            'title' => trim($title),
            'body' => trim($body),
        ]);
    }

    public static function section(string $key, ?string $default = null): string
    {
        $value = SiteSetting::getValue('section_'.$key);

        if ($value !== null && $value !== '') {
            return $value;
        }

        return (string) ($default ?? '');
    }

    public static function saveSection(string $key, ?string $value): void
    {
        if (! isset(self::SECTIONS[$key])) {
            return;
        }

        SiteSetting::putValue('section_'.$key, trim((string) $value) ?: null);
    }

    public static function statusEmailsEnabled(): bool
    {
        $value = SiteSetting::getValue('notify_status_emails');

        if ($value === null) {
            return true;
        }

        return $value === '1' || $value === 'true';
    }

    public static function setStatusEmailsEnabled(bool $enabled): void
    {
        SiteSetting::putValue('notify_status_emails', $enabled ? '1' : '0');
    }
}
