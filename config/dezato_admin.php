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
        ['id' => 'customers', 'label' => 'Customers', 'route' => 'admin.customers.index'],
        ['id' => 'content', 'label' => 'Website text', 'route' => 'admin.content.edit'],
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
            'label' => 'Homepage / banner photo',
            'size' => '1600 × 1000 px',
            'ratio' => 'Landscape (16:10)',
            'formats' => 'JPG or WebP',
            'max' => '3 MB',
            'tip' => 'Wide photo works best. Keep important parts of the cake away from the bottom edge (text sits there).',
        ],
    ],
];
