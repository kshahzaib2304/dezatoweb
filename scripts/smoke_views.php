<?php

/**
 * Smoke-check admin + storefront critical views for render errors.
 * Run: php scripts/smoke_views.php
 */

use App\Models\User;
use App\Support\AdminNav;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$failures = [];

$flat = [
    ['id' => 'dashboard', 'label' => 'Dashboard', 'route' => 'admin.dashboard'],
    ['id' => 'products', 'label' => 'Products', 'route' => 'admin.products.index'],
];
config(['dezato_admin.nav' => $flat]);
$legacy = AdminNav::groups();
if (! isset($legacy[0]['items'][0]['id'])) {
    $failures[] = 'AdminNav failed to normalize legacy flat nav';
}

$app['config']->set('dezato_admin', require __DIR__.'/../config/dezato_admin.php');
$groups = AdminNav::groups();
if ($groups === [] || ! isset($groups[0]['items'])) {
    $failures[] = 'AdminNav groups empty or missing items';
}

$admin = User::query()->where('role', User::ROLE_ADMIN)->first()
    ?? User::query()->first();

if ($admin === null) {
    $failures[] = 'No user available to authenticate for admin views';
} else {
    Auth::login($admin);
}

$adminViews = [
    'admin.dashboard' => [
        'title' => 't',
        'heading' => 'Dashboard',
        'active' => 'dashboard',
        'stats' => [],
        'recentOrders' => collect(),
    ],
    'admin.help' => [
        'title' => 't',
        'heading' => 'Help',
        'active' => 'help',
        'loginEmail' => $admin?->email ?? 'a@b.c',
        'media' => config('dezato_admin.media'),
    ],
];

foreach ($adminViews as $view => $data) {
    try {
        view($view, $data)->render();
    } catch (Throwable $e) {
        $failures[] = "{$view}: {$e->getMessage()}";
    }
}

$storefrontRoutes = ['home', 'menu', 'about', 'services', 'locations', 'cart.show', 'checkout.show'];

foreach ($storefrontRoutes as $routeName) {
    try {
        $response = $app->handle(Request::create(route($routeName, absolute: false), 'GET'));
        $status = $response->getStatusCode();
        if ($status >= 500) {
            $failures[] = "GET {$routeName} => HTTP {$status}";
        }
    } catch (Throwable $e) {
        $failures[] = "GET {$routeName}: {$e->getMessage()}";
    }
}

try {
    $response = $app->handle(Request::create(route('admin.dashboard', absolute: false), 'GET'));
    $status = $response->getStatusCode();
    if ($status >= 500) {
        $failures[] = "GET admin.dashboard => HTTP {$status}";
    } else {
        echo "admin.dashboard HTTP {$status}\n";
    }
} catch (Throwable $e) {
    $failures[] = 'GET admin.dashboard: '.$e->getMessage();
}

if ($failures !== []) {
    fwrite(STDERR, "SMOKE FAILURES:\n- ".implode("\n- ", $failures)."\n");
    exit(1);
}

echo "SMOKE OK (AdminNav + key views/routes)\n";
exit(0);
