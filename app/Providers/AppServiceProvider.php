<?php

namespace App\Providers;

use App\Support\Cart;
use App\Support\Catalog;
use App\Support\Fulfillment;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        View::composer('*', function ($view): void {
            $cart = app(Cart::class);
            $fulfillment = app(Fulfillment::class);

            $view->with([
                'navLinks' => config('dezato.nav', []),
                'currentRoute' => Route::currentRouteName(),
                'cartCount' => $cart->count(),
                'fulfillmentSummary' => $fulfillment->summary(),
                'hasFulfillment' => $fulfillment->has(),
                'welcomeSeen' => $fulfillment->welcomeSeen(),
                'currentFulfillment' => $fulfillment->get(),
                'fulfillmentLocations' => Catalog::locations()->all(),
                'shippingFee' => (float) config('dezato.shipping.fee', 0),
                'shippingEta' => (string) config('dezato.shipping.eta', ''),
            ]);
        });
    }
}
