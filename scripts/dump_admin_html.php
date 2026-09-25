<?php

use App\Models\User;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Auth;

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$admin = User::query()->where('role', 'admin')->first() ?? User::query()->first();
Auth::login($admin);

$html = view('admin.dashboard', [
    'title' => 'Dashboard | Dezato Admin',
    'heading' => 'Dashboard',
    'active' => 'dashboard',
    'stats' => [
        ['label' => 'Orders today', 'value' => '0', 'hint' => 'Placed since midnight'],
        ['label' => 'Revenue today', 'value' => 'Rs 0', 'hint' => 'Sum of those orders'],
        ['label' => 'Still open', 'value' => '0', 'hint' => 'Not delivered yet'],
        ['label' => 'Low stock', 'value' => '0', 'hint' => 'Five or fewer left'],
    ],
    'recentOrders' => collect(),
])->render();

file_put_contents(__DIR__.'/../storage/app/dash-smoke.html', $html);

// Structure checks
$checks = [
    'has admin-shell' => str_contains($html, 'class="admin-shell"'),
    'has admin-main' => str_contains($html, 'class="admin-main"'),
    'sidebar before main' => strpos($html, 'admin-sidebar') < strpos($html, 'admin-main'),
    'admin.css linked' => str_contains($html, 'css/admin.css'),
    'closed aside' => substr_count($html, '<aside') === substr_count($html, '</aside>'),
    'closed div count ok' => substr_count($html, '<div') <= substr_count($html, '</div>') + 5,
];

foreach ($checks as $label => $ok) {
    echo ($ok ? 'OK' : 'FAIL')." {$label}\n";
}

// Extract shell fragment
if (preg_match('/<div class="admin-shell".*?<\/body>/s', $html, $m)) {
    $frag = $m[0];
    // Show first 2500 chars of structure with tags only
    $tags = preg_replace('/>([^<]{20,})</', '>…<', $frag);
    echo "\n--- structure ---\n";
    echo substr($tags, 0, 3500);
}
