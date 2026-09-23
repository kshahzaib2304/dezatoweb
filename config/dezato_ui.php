<?php

/**
 * Frontend UI preview data (mock).
 * Replace with DB / services when backend is wired.
 */
return [
    'profile' => [
        'name' => 'Ayesha Khan',
        'email' => 'ayesha@example.com',
        'phone' => '+92 300 1234567',
    ],

    'addresses' => [
        [
            'id' => 'addr-1',
            'label' => 'Home',
            'line1' => 'House 12, Street 4, Khayaban-e-Seher',
            'area' => 'DHA Phase 6',
            'city' => 'Karachi',
            'is_default' => true,
        ],
        [
            'id' => 'addr-2',
            'label' => 'Office',
            'line1' => 'Suite 3, Gizri Boulevard',
            'area' => 'DHA Phase 4',
            'city' => 'Karachi',
            'is_default' => false,
        ],
    ],

    'orders' => [
        [
            'number' => 'DZ-10428',
            'placed_at' => '2026-09-10 14:22',
            'status' => 'out_for_delivery',
            'status_label' => 'Out for delivery',
            'total' => 4250,
            'method' => 'Delivery',
            'items' => ['Chocolate Heaven Cake', 'Lotus Cupcake × 4'],
        ],
        [
            'number' => 'DZ-10391',
            'placed_at' => '2026-08-22 11:05',
            'status' => 'delivered',
            'status_label' => 'Delivered',
            'total' => 2500,
            'method' => 'Pickup · DHA Phase 6',
            'items' => ['Lotus Cake'],
        ],
        [
            'number' => 'DZ-10355',
            'placed_at' => '2026-07-18 16:40',
            'status' => 'delivered',
            'status_label' => 'Delivered',
            'total' => 1800,
            'method' => 'Delivery',
            'items' => ['Red Velvet Cupcake × 6'],
        ],
    ],

    'tracking_steps' => [
        ['key' => 'placed', 'label' => 'Placed'],
        ['key' => 'baking', 'label' => 'Baking'],
        ['key' => 'qc', 'label' => 'Quality check'],
        ['key' => 'out_for_delivery', 'label' => 'Out for delivery'],
        ['key' => 'delivered', 'label' => 'Delivered'],
    ],

    'builder' => [
        'sizes' => [
            ['id' => '1lb', 'label' => '1 lb', 'serves' => '4–6', 'price' => 1200],
            ['id' => '2.5lb', 'label' => '2.5 lb', 'serves' => '8–10', 'price' => 1850],
            ['id' => '3lb', 'label' => '3 lb', 'serves' => '12–14', 'price' => 2400],
            ['id' => '4lb', 'label' => '4 lb', 'serves' => '16–18', 'price' => 3200],
        ],
        'shapes' => [
            ['id' => 'round', 'label' => 'Round'],
            ['id' => 'square', 'label' => 'Square'],
            ['id' => 'heart', 'label' => 'Heart'],
            ['id' => 'number', 'label' => 'Number'],
            ['id' => 'alphabet', 'label' => 'Alphabet'],
        ],
        'bases' => [
            ['id' => 'vanilla', 'label' => 'Vanilla', 'price' => 0],
            ['id' => 'chocolate', 'label' => 'Chocolate', 'price' => 100],
            ['id' => 'red-velvet', 'label' => 'Red Velvet', 'price' => 200],
            ['id' => 'coffee', 'label' => 'Coffee', 'price' => 150],
        ],
        'fillings' => [
            ['id' => 'buttercream', 'label' => 'Buttercream', 'price' => 0],
            ['id' => 'chocolate-ganache', 'label' => 'Chocolate ganache', 'price' => 150],
            ['id' => 'lotus', 'label' => 'Lotus cream', 'price' => 250],
            ['id' => 'nutella', 'label' => 'Nutella', 'price' => 250],
            ['id' => 'three-milk', 'label' => 'Three milk soak', 'price' => 200],
        ],
        'frostings' => [
            ['id' => 'vanilla-frosting', 'label' => 'Vanilla frosting', 'price' => 0],
            ['id' => 'chocolate-frosting', 'label' => 'Chocolate frosting', 'price' => 50],
            ['id' => 'cream-cheese', 'label' => 'Cream cheese', 'price' => 150],
            ['id' => 'lotus-frosting', 'label' => 'Lotus frosting', 'price' => 200],
        ],
        'diets' => [
            ['id' => 'eggless', 'label' => 'Eggless', 'price' => 200],
            ['id' => 'vegan', 'label' => 'Vegan', 'price' => 400],
            ['id' => 'gluten-free', 'label' => 'Gluten-free', 'price' => 350],
            ['id' => 'sugar-free', 'label' => 'Sugar-free', 'price' => 300],
        ],
        'colors' => [
            ['id' => 'ivory', 'label' => 'Ivory', 'hex' => '#f7f1e8'],
            ['id' => 'blush', 'label' => 'Blush', 'hex' => '#e8d0dc'],
            ['id' => 'plum', 'label' => 'Plum', 'hex' => '#52314f'],
            ['id' => 'butter', 'label' => 'Butter', 'hex' => '#e8c99a'],
            ['id' => 'chocolate', 'label' => 'Chocolate', 'hex' => '#4a3428'],
            ['id' => 'sky', 'label' => 'Sky', 'hex' => '#b7d0e8'],
        ],
        'addons' => [
            ['id' => 'candles', 'label' => 'Candles', 'price' => 150],
            ['id' => 'knife', 'label' => 'Cake knife', 'price' => 200],
            ['id' => 'cupcakes', 'label' => '6 cupcakes', 'price' => 1800],
            ['id' => 'card', 'label' => 'Greeting card', 'price' => 100],
        ],
    ],

    'checkout' => [
        'time_slots' => [
            '10:00 AM – 12:00 PM',
            '12:00 PM – 2:00 PM',
            '2:00 PM – 4:00 PM',
            '4:00 PM – 6:00 PM',
            '6:00 PM – 8:00 PM',
            '8:00 PM – 10:00 PM',
        ],
        'payment_methods' => [
            ['id' => 'cod', 'label' => 'Cash on delivery / pickup', 'hint' => 'Pay in PKR when your order arrives'],
            ['id' => 'card', 'label' => 'Credit / Debit card', 'hint' => 'Visa, Mastercard - gateway coming next'],
            ['id' => 'jazzcash', 'label' => 'JazzCash', 'hint' => 'Mobile wallet'],
            ['id' => 'easypaisa', 'label' => 'Easypaisa', 'hint' => 'Mobile wallet'],
            ['id' => 'bnpl', 'label' => 'Buy now, pay later', 'hint' => 'Split payments - coming next'],
        ],
    ],

    'admin' => [
        'stats' => [
            ['label' => 'Orders today', 'value' => '18'],
            ['label' => 'Revenue (PKR)', 'value' => '₨ 86,400'],
            ['label' => 'Pending', 'value' => '7'],
            ['label' => 'Low stock', 'value' => '3'],
        ],
        'recent_orders' => [
            ['number' => 'DZ-10428', 'customer' => 'Ayesha Khan', 'total' => 4250, 'status' => 'Out for delivery'],
            ['number' => 'DZ-10427', 'customer' => 'Bilal Ahmed', 'total' => 2100, 'status' => 'Baking'],
            ['number' => 'DZ-10426', 'customer' => 'Sara Malik', 'total' => 3000, 'status' => 'Placed'],
        ],
    ],
];
