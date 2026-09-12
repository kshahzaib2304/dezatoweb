<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\Admin\AdminPageController;
use App\Http\Controllers\AuthPageController;
use App\Http\Controllers\CakeBuilderController;
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
Route::get('/custom-cake', [CakeBuilderController::class, 'show'])->name('builder.show');
Route::post('/custom-cake', [CakeBuilderController::class, 'stub'])->name('builder.stub');

Route::redirect('/our-story', '/about-us', 301);
Route::redirect('/catering', '/our-services', 301);

Route::post('/inquiries', [InquiryController::class, 'store'])->name('inquiries.store');

/* Auth UI */
Route::get('/login', [AuthPageController::class, 'login'])->name('login');
Route::get('/register', [AuthPageController::class, 'register'])->name('register');
Route::get('/forgot-password', [AuthPageController::class, 'forgot'])->name('password.request');
Route::get('/reset-password', [AuthPageController::class, 'reset'])->name('password.reset');
Route::post('/auth/ui', [AuthPageController::class, 'stub'])->name('auth.stub');

/* Account UI */
Route::prefix('account')->name('account.')->group(function (): void {
    Route::get('/', fn () => redirect()->route('account.profile'));
    Route::get('/profile', [AccountController::class, 'profile'])->name('profile');
    Route::get('/addresses', [AccountController::class, 'addresses'])->name('addresses');
    Route::get('/orders', [AccountController::class, 'orders'])->name('orders');
    Route::get('/orders/{number}', [AccountController::class, 'track'])->name('track');
    Route::post('/ui', [AccountController::class, 'stub'])->name('stub');
});

/* Admin UI shell */
Route::prefix('admin')->name('admin.')->group(function (): void {
    Route::get('/', [AdminPageController::class, 'dashboard'])->name('dashboard');
    Route::get('/products', [AdminPageController::class, 'products'])->name('products');
    Route::get('/orders', [AdminPageController::class, 'orders'])->name('orders');
    Route::get('/customers', [AdminPageController::class, 'customers'])->name('customers');
    Route::get('/promotions', [AdminPageController::class, 'promotions'])->name('promotions');
    Route::get('/content', [AdminPageController::class, 'content'])->name('content');
    Route::get('/reports', [AdminPageController::class, 'reports'])->name('reports');
    Route::post('/ui', [AdminPageController::class, 'stub'])->name('stub');
});

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
