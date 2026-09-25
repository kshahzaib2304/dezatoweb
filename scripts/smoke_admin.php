<?php

/**
 * Full admin GET smoke test (authenticated).
 * Run: php scripts/smoke_admin.php
 */

use App\Models\User;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$admin = User::query()->where('role', User::ROLE_ADMIN)->first()
    ?? User::query()->first();

if ($admin === null) {
    fwrite(STDERR, "No user to authenticate.\n");
    exit(1);
}

Auth::login($admin);

$failures = [];
$ok = 0;

$skip = [
    'admin.products.edit',
    'admin.products.update',
    'admin.products.destroy',
    'admin.categories.edit',
    'admin.categories.update',
    'admin.categories.destroy',
    'admin.orders.show',
    'admin.orders.update',
    'admin.orders.invoice',
    'admin.inquiries.show',
    'admin.inquiries.update',
    'admin.settings.nav.move',
    'admin.promos.destroy',
];

foreach (Route::getRoutes() as $route) {
    $name = $route->getName();

    if ($name === null || ! str_starts_with($name, 'admin.')) {
        continue;
    }

    if (! in_array('GET', $route->methods(), true)) {
        continue;
    }

    if (in_array($name, $skip, true)) {
        continue;
    }

    // Skip parameterized routes without defaults
    if (preg_match('/\{[^}]+\}/', $route->uri())) {
        continue;
    }

    try {
        $response = $app->handle(Request::create(route($name, absolute: false), 'GET'));
        $status = $response->getStatusCode();

        if ($status >= 500) {
            $failures[] = "{$name} => HTTP {$status}";
            continue;
        }

        $ok++;
        echo "OK {$status} {$name}\n";
    } catch (Throwable $e) {
        $failures[] = "{$name}: {$e->getMessage()}";
    }
}

echo "\nPassed: {$ok}\n";

if ($failures !== []) {
    fwrite(STDERR, "FAILURES:\n- ".implode("\n- ", $failures)."\n");
    exit(1);
}

echo "ALL ADMIN GET ROUTES OK\n";
exit(0);
