<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\FulfillmentController;
use App\Http\Controllers\InquiryController;
use App\Http\Controllers\StorefrontController;
use App\Http\Middleware\EnsureCartNotEmpty;
use App\Http\Middleware\EnsureFulfillmentSelected;
use Illuminate\Support\Facades\Route;

Route::get('/', [StorefrontController::class, 'home'])->name('home');
Route::get('/menu', [StorefrontController::class, 'menu'])->name('menu');
Route::get('/menu/{product}', [StorefrontController::class, 'product'])->name('products.show');
Route::get('/locations', [StorefrontController::class, 'locations'])->name('locations');
Route::get('/about-us', [StorefrontController::class, 'about'])->name('about');
Route::get('/our-services', [StorefrontController::class, 'services'])->name('services');
Route::get('/cake-customization', [StorefrontController::class, 'customization'])->name('customization');

Route::redirect('/our-story', '/about-us', 301);
Route::redirect('/catering', '/our-services', 301);

Route::post('/inquiries', [InquiryController::class, 'store'])->name('inquiries.store');

Route::get('/order', [StorefrontController::class, 'order'])->name('order');
Route::get('/order/start', [FulfillmentController::class, 'start'])->name('order.start');
Route::post('/order/fulfillment', [FulfillmentController::class, 'store'])->name('order.fulfillment.store');
Route::post('/order/fulfillment/dismiss', [FulfillmentController::class, 'dismiss'])->name('order.fulfillment.dismiss');

Route::middleware(EnsureFulfillmentSelected::class)->group(function (): void {
    Route::get('/cart', [CartController::class, 'show'])->name('cart.show');
    Route::post('/cart/items', [CartController::class, 'store'])->name('cart.items.store');
    Route::patch('/cart/items/{product}', [CartController::class, 'update'])->name('cart.items.update');
    Route::delete('/cart/items/{product}', [CartController::class, 'destroy'])->name('cart.items.destroy');

    Route::middleware(EnsureCartNotEmpty::class)->group(function (): void {
        Route::get('/checkout', [CheckoutController::class, 'show'])->name('checkout.show');
        Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    });
});

Route::get('/order/confirmation/{order:number}', [CheckoutController::class, 'confirmation'])
    ->name('checkout.confirmation');

Route::get('/sitemap.xml', [StorefrontController::class, 'sitemap'])->name('sitemap');
Route::get('/robots.txt', [StorefrontController::class, 'robots'])->name('robots');
