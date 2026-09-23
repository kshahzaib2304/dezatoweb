<?php

/**
 * Admin navigation + media guidelines for non-technical store managers.
 */
return [
    'nav' => [
        ['id' => 'dashboard', 'label' => 'Dashboard', 'route' => 'admin.dashboard'],
        ['id' => 'products', 'label' => 'Products', 'route' => 'admin.products.index'],
        ['id' => 'categories', 'label' => 'Categories', 'route' => 'admin.categories.index'],
        ['id' => 'orders', 'label' => 'Orders', 'route' => 'admin.orders.index'],
        ['id' => 'inquiries', 'label' => 'Messages', 'route' => 'admin.inquiries.index'],
        ['id' => 'customers', 'label' => 'Customers', 'route' => 'admin.customers.index'],
        ['id' => 'cake-builder', 'label' => 'Custom cake prices', 'route' => 'admin.cake-builder.edit'],
        ['id' => 'promos', 'label' => 'Promo codes', 'route' => 'admin.promos.index'],
        ['id' => 'payments', 'label' => 'Payment options', 'route' => 'admin.payments.edit'],
        ['id' => 'schedule', 'label' => 'Delivery times', 'route' => 'admin.schedule.edit'],
        ['id' => 'locations', 'label' => 'Store locations', 'route' => 'admin.locations.edit'],
        ['id' => 'content', 'label' => 'Homepage & text', 'route' => 'admin.content.edit'],
        ['id' => 'pages', 'label' => 'Website pages', 'route' => 'admin.pages.edit'],
        ['id' => 'settings', 'label' => 'Contact & store', 'route' => 'admin.settings.edit'],
        ['id' => 'integrations', 'label' => 'Email, logins & links', 'route' => 'admin.integrations.edit'],
        ['id' => 'reports', 'label' => 'Reports', 'route' => 'admin.reports'],
        ['id' => 'help', 'label' => 'Help & guide', 'route' => 'admin.help'],
    ],

    'media' => [
        'product' => [
            'label' => 'Product photo',
            'size' => '1200 × 1200 px',
            'ratio' => 'Square (1:1)',
            'formats' => 'JPG or WebP',
            'max' => '2 MB',
            'tip' => 'Use a bright photo of the cake on a plain background. Crop tightly so the cake fills most of the frame.',
        ],
        'hero' => [
            'label' => 'Homepage hero slide photo',
            'size' => '1600 × 1000 px',
            'ratio' => 'Landscape (16:10)',
            'formats' => 'JPG or WebP',
            'max' => '3 MB',
            'tip' => 'Wide photo for each slider slide. Keep important parts of the cake away from the bottom (text sits there).',
        ],
        'tile' => [
            'label' => 'Homepage category / occasion tile',
            'size' => '900 × 900 px',
            'ratio' => 'Square (1:1)',
            'formats' => 'JPG or WebP',
            'max' => '2 MB',
            'tip' => 'Clear cake photo that still looks good when cropped square.',
        ],
        'location' => [
            'label' => 'Store location photo',
            'size' => '1200 × 900 px',
            'ratio' => 'Landscape (4:3)',
            'formats' => 'JPG or WebP',
            'max' => '3 MB',
            'tip' => 'Front of shop or inviting interior — well lit.',
        ],
        'package' => [
            'label' => 'Services package photo',
            'size' => '1200 × 900 px',
            'ratio' => 'Landscape (4:3)',
            'formats' => 'JPG or WebP',
            'max' => '3 MB',
            'tip' => 'Show a styled dessert table or boxed treats.',
        ],
        'order_card' => [
            'label' => 'Order page choice card photo',
            'size' => '1200 × 900 px',
            'ratio' => 'Landscape (4:3)',
            'formats' => 'JPG or WebP',
            'max' => '3 MB',
            'tip' => 'Pickup counter, delivery box, or catering table — one clear photo per card.',
        ],
        'reference' => [
            'label' => 'Custom cake reference photo',
            'size' => '1200 × 1200 px (or clear phone photo)',
            'ratio' => 'Square preferred',
            'formats' => 'JPG, PNG, or WebP',
            'max' => '3 MB',
            'tip' => 'Customers upload inspiration photos. Clear, well-lit pictures help the bakery match the design.',
        ],
    ],
];
