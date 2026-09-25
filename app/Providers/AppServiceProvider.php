<?php

namespace App\Providers;

use App\Models\SiteSetting;
use App\Support\BakeryProfile;
use App\Support\Cart;
use App\Support\Catalog;
use App\Support\Fulfillment;
use App\Support\MailSettings;
use App\Support\NavigationMenu;
use App\Support\ShippingSettings;
use App\Support\SiteBrand;
use App\Support\SocialAuth;
use App\Support\SocialLinks;
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
            ShippingSettings::apply();
            BakeryProfile::applySiteUrl();
        } catch (Throwable) {
            // Database may be unavailable during early install / migrate.
        }

        View::composer('*', function ($view): void {
            $name = $view->name();

            if (
                $name === 'layouts.admin'
                || str_starts_with($name, 'admin.')
                || str_starts_with($name, 'mail.')
                || str_starts_with($name, 'pagination.')
            ) {
                return;
            }

            $view->with(once(function (): array {
                $cart = app(Cart::class);
                $fulfillment = app(Fulfillment::class);

                return [
                    'navLinks' => NavigationMenu::links(),
                    'brandName' => SiteBrand::name(),
                    'brandShortName' => SiteBrand::shortName(),
                    'brandTagline' => SiteBrand::tagline(),
                    'menuCategories' => Catalog::categories()->all(),
                    'currentRoute' => Route::currentRouteName(),
                    'cartCount' => $cart->count(),
                    'cartLines' => $cart->lines(),
                    'cartSubtotal' => $cart->subtotal(),
                    'fulfillmentSummary' => $fulfillment->summary(),
                    'hasFulfillment' => $fulfillment->has(),
                    'welcomeSeen' => $fulfillment->welcomeSeen(),
                    'currentFulfillment' => $fulfillment->get(),
                    'fulfillmentLocations' => Catalog::locations()->all(),
                    'shippingFee' => ShippingSettings::fee(),
                    'shippingEta' => ShippingSettings::eta(),
                ];
            }));
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
