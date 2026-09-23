<?php

namespace App\Providers;

use App\Support\Cart;
use App\Support\Catalog;
use App\Support\Fulfillment;
use App\Support\MailSettings;
use App\Support\SocialAuth;
use App\Support\SocialLinks;
use App\Models\SiteSetting;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Throwable;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Paginator::defaultView('pagination.simple');
        Paginator::defaultSimpleView('pagination.simple');

        try {
            MailSettings::apply();
            SocialAuth::apply();
        } catch (Throwable) {
            // Database may be unavailable during early install / migrate.
        }

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

        View::composer('components.header', function ($view): void {
            $default = (string) config('dezato.home.announcement', '');
            $view->with(
                'announcement',
                SiteSetting::getValue('announcement', $default) ?: null
            );
        });

        View::composer('components.footer', function ($view): void {
            try {
                $view->with('socialLinks', SocialLinks::forFooter());
            } catch (Throwable) {
                $view->with('socialLinks', []);
            }
        });
    }
}
