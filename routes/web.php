<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\Admin\ChromeController as AdminChromeController;
use App\Http\Controllers\Admin\CakeBuilderController as AdminCakeBuilderController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\ContentController as AdminContentController;
use App\Http\Controllers\Admin\CustomerController as AdminCustomerController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\HelpController as AdminHelpController;
use App\Http\Controllers\Admin\InquiryController as AdminInquiryController;
use App\Http\Controllers\Admin\IntegrationsController as AdminIntegrationsController;
use App\Http\Controllers\Admin\LocationController as AdminLocationController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\PagesController as AdminPagesController;
use App\Http\Controllers\Admin\PaymentSettingsController as AdminPaymentSettingsController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\PromoController as AdminPromoController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Admin\ScheduleController as AdminScheduleController;
use App\Http\Controllers\Admin\SettingsController as AdminSettingsController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CakeBuilderController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\FulfillmentController;
use App\Http\Controllers\InquiryController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\SocialAuthController;
use App\Http\Controllers\StorefrontController;
use App\Http\Middleware\EnsureCartNotEmpty;
use App\Http\Middleware\EnsureFulfillmentSelected;
use App\Http\Middleware\EnsureUserIsAdmin;
use Illuminate\Support\Facades\Route;

Route::get('/', [StorefrontController::class, 'home'])->name('home');
Route::get('/menu', [StorefrontController::class, 'menu'])->name('menu');
Route::get('/menu/{product}', [StorefrontController::class, 'product'])->name('products.show');
Route::get('/locations', [StorefrontController::class, 'locations'])->name('locations');
Route::get('/about-us', [StorefrontController::class, 'about'])->name('about');
Route::get('/our-services', [StorefrontController::class, 'services'])->name('services');
Route::get('/cake-customization', [StorefrontController::class, 'customization'])->name('customization');
Route::get('/custom-cake', [CakeBuilderController::class, 'show'])->name('builder.show');
Route::post('/custom-cake', [CakeBuilderController::class, 'store'])->name('builder.store');

Route::redirect('/our-story', '/about-us', 301);
Route::redirect('/catering', '/our-services', 301);

Route::get('/privacy-policy', fn () => app(PageController::class)->show('privacy'))->name('pages.privacy');
Route::get('/terms', fn () => app(PageController::class)->show('terms'))->name('pages.terms');
Route::get('/faq', fn () => app(PageController::class)->show('faq'))->name('pages.faq');

Route::post('/inquiries', [InquiryController::class, 'store'])
    ->middleware('throttle:8,1')
    ->name('inquiries.store');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:6,1');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:6,1');
    Route::get('/forgot-password', [AuthController::class, 'showForgot'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->middleware('throttle:3,1')->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showReset'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'reset'])->middleware('throttle:6,1')->name('password.update');

    Route::get('/auth/{provider}/redirect', [SocialAuthController::class, 'redirect'])
        ->whereIn('provider', ['google', 'facebook'])
        ->middleware('throttle:20,1')
        ->name('auth.social.redirect');
    Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'callback'])
        ->whereIn('provider', ['google', 'facebook'])
        ->middleware('throttle:20,1')
        ->name('auth.social.callback');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware('auth')->prefix('account')->name('account.')->group(function (): void {
    Route::get('/', fn () => redirect()->route('account.profile'));
    Route::get('/profile', [AccountController::class, 'profile'])->name('profile');
    Route::post('/profile', [AccountController::class, 'updateProfile'])->name('profile.update');
    Route::get('/addresses', [AccountController::class, 'addresses'])->name('addresses');
    Route::post('/addresses', [AccountController::class, 'storeAddress'])->name('addresses.store');
    Route::delete('/addresses/{address}', [AccountController::class, 'destroyAddress'])->name('addresses.destroy');
    Route::get('/orders', [AccountController::class, 'orders'])->name('orders');
    Route::post('/orders/{number}/reorder', [AccountController::class, 'reorder'])->name('reorder');
    Route::post('/orders/{number}/cancel', [AccountController::class, 'cancel'])->name('cancel');
    Route::get('/orders/{number}', [AccountController::class, 'track'])->name('track');
});

Route::middleware(['auth', EnsureUserIsAdmin::class])->prefix('admin')->name('admin.')->group(function (): void {
    Route::get('/', AdminDashboardController::class)->name('dashboard');
    Route::get('/help', AdminHelpController::class)->name('help');
    Route::get('/reports', AdminReportController::class)->name('reports');

    Route::get('/products', [AdminProductController::class, 'index'])->name('products.index');
    Route::get('/products/create', [AdminProductController::class, 'create'])->name('products.create');
    Route::post('/products', [AdminProductController::class, 'store'])->name('products.store');
    Route::get('/products/{product}/edit', [AdminProductController::class, 'edit'])->name('products.edit');
    Route::put('/products/{product}', [AdminProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{product}', [AdminProductController::class, 'destroy'])->name('products.destroy');

    Route::get('/categories', [AdminCategoryController::class, 'index'])->name('categories.index');
    Route::get('/categories/create', [AdminCategoryController::class, 'create'])->name('categories.create');
    Route::post('/categories', [AdminCategoryController::class, 'store'])->name('categories.store');
    Route::get('/categories/{category}/edit', [AdminCategoryController::class, 'edit'])->name('categories.edit');
    Route::put('/categories/{category}', [AdminCategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [AdminCategoryController::class, 'destroy'])->name('categories.destroy');

    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::get('/orders/{order}/invoice', [AdminOrderController::class, 'invoice'])->name('orders.invoice');
    Route::patch('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.status');
    Route::patch('/orders/{order}/paid', [AdminOrderController::class, 'markPaid'])->name('orders.paid');

    Route::get('/inquiries', [AdminInquiryController::class, 'index'])->name('inquiries.index');
    Route::get('/inquiries/{inquiry}', [AdminInquiryController::class, 'show'])->name('inquiries.show');
    Route::delete('/inquiries/{inquiry}', [AdminInquiryController::class, 'destroy'])->name('inquiries.destroy');

    Route::get('/customers', [AdminCustomerController::class, 'index'])->name('customers.index');

    Route::get('/content', [AdminContentController::class, 'edit'])->name('content.edit');
    Route::put('/content', [AdminContentController::class, 'update'])->name('content.update');
    Route::put('/content/showcase', [AdminContentController::class, 'updateShowcase'])->name('content.showcase');
    Route::post('/content/categories', [AdminContentController::class, 'storeCategory'])->name('content.categories.store');
    Route::delete('/content/categories/{tile}', [AdminContentController::class, 'destroyCategory'])->name('content.categories.destroy');
    Route::post('/content/occasions', [AdminContentController::class, 'storeOccasion'])->name('content.occasions.store');
    Route::delete('/content/occasions/{tile}', [AdminContentController::class, 'destroyOccasion'])->name('content.occasions.destroy');
    Route::post('/content/slides', [AdminContentController::class, 'storeSlide'])->name('content.slides.store');
    Route::put('/content/slides/{slide}', [AdminContentController::class, 'updateSlide'])->name('content.slides.update');
    Route::delete('/content/slides/{slide}', [AdminContentController::class, 'destroySlide'])->name('content.slides.destroy');

    Route::get('/locations', [AdminLocationController::class, 'edit'])->name('locations.edit');
    Route::put('/locations', [AdminLocationController::class, 'update'])->name('locations.update');
    Route::post('/locations', [AdminLocationController::class, 'store'])->name('locations.store');
    Route::delete('/locations/{location}', [AdminLocationController::class, 'destroy'])->name('locations.destroy');

    Route::get('/pages', [AdminPagesController::class, 'edit'])->name('pages.edit');
    Route::put('/pages', [AdminPagesController::class, 'update'])->name('pages.update');
    Route::post('/pages/milestones', [AdminPagesController::class, 'storeMilestone'])->name('pages.milestones.store');
    Route::delete('/pages/milestones/{item}', [AdminPagesController::class, 'destroyMilestone'])->name('pages.milestones.destroy');
    Route::post('/pages/packages', [AdminPagesController::class, 'storePackage'])->name('pages.packages.store');
    Route::delete('/pages/packages/{item}', [AdminPagesController::class, 'destroyPackage'])->name('pages.packages.destroy');
    Route::post('/pages/customization', [AdminPagesController::class, 'storeCustomization'])->name('pages.customization.store');
    Route::delete('/pages/customization/{item}', [AdminPagesController::class, 'destroyCustomization'])->name('pages.customization.destroy');
    Route::post('/pages/order-cards', [AdminPagesController::class, 'storeOrderCard'])->name('pages.order-cards.store');
    Route::delete('/pages/order-cards/{item}', [AdminPagesController::class, 'destroyOrderCard'])->name('pages.order-cards.destroy');

    Route::get('/chrome', [AdminChromeController::class, 'edit'])->name('chrome.edit');
    Route::put('/chrome', [AdminChromeController::class, 'update'])->name('chrome.update');
    Route::post('/chrome/areas', [AdminChromeController::class, 'storeArea'])->name('chrome.areas.store');
    Route::delete('/chrome/areas/{area}', [AdminChromeController::class, 'destroyArea'])->name('chrome.areas.destroy');

    Route::get('/cake-builder', [AdminCakeBuilderController::class, 'edit'])->name('cake-builder.edit');
    Route::put('/cake-builder', [AdminCakeBuilderController::class, 'update'])->name('cake-builder.update');
    Route::post('/cake-builder/guidelines', [AdminCakeBuilderController::class, 'storeGuideline'])->name('cake-builder.guidelines.store');
    Route::delete('/cake-builder/guidelines/{index}', [AdminCakeBuilderController::class, 'destroyGuideline'])->name('cake-builder.guidelines.destroy');
    Route::post('/cake-builder/addons', [AdminCakeBuilderController::class, 'storeAddon'])->name('cake-builder.addons.store');
    Route::delete('/cake-builder/addons/{item}', [AdminCakeBuilderController::class, 'destroyAddon'])->name('cake-builder.addons.destroy');
    Route::post('/cake-builder/sizes', [AdminCakeBuilderController::class, 'storeSize'])->name('cake-builder.sizes.store');
    Route::delete('/cake-builder/sizes/{item}', [AdminCakeBuilderController::class, 'destroySize'])->name('cake-builder.sizes.destroy');
    Route::post('/cake-builder/reset', [AdminCakeBuilderController::class, 'resetDefaults'])->name('cake-builder.reset');

    Route::get('/promos', [AdminPromoController::class, 'index'])->name('promos.index');
    Route::post('/promos', [AdminPromoController::class, 'store'])->name('promos.store');
    Route::put('/promos/{promo}', [AdminPromoController::class, 'update'])->name('promos.update');
    Route::patch('/promos/{promo}/toggle', [AdminPromoController::class, 'toggle'])->name('promos.toggle');
    Route::delete('/promos/{promo}', [AdminPromoController::class, 'destroy'])->name('promos.destroy');

    Route::get('/payments', [AdminPaymentSettingsController::class, 'edit'])->name('payments.edit');
    Route::put('/payments', [AdminPaymentSettingsController::class, 'update'])->name('payments.update');

    Route::get('/schedule', [AdminScheduleController::class, 'edit'])->name('schedule.edit');
    Route::put('/schedule', [AdminScheduleController::class, 'update'])->name('schedule.update');

    Route::get('/settings', [AdminSettingsController::class, 'edit'])->name('settings.edit');
    Route::put('/settings', [AdminSettingsController::class, 'update'])->name('settings.update');
    Route::post('/settings/nav/{index}/move', [AdminSettingsController::class, 'moveNav'])->name('settings.nav.move');

    Route::get('/integrations', [AdminIntegrationsController::class, 'edit'])->name('integrations.edit');
    Route::put('/integrations', [AdminIntegrationsController::class, 'update'])->name('integrations.update');

    Route::get('/reports/export', [AdminReportController::class, 'export'])->name('reports.export');
});

Route::get('/pay/{order}', [PaymentController::class, 'start'])->name('payments.start');
Route::match(['get', 'post'], '/pay/{order}/callback/{provider}', [PaymentController::class, 'callback'])
    ->whereIn('provider', ['jazzcash', 'easypaisa', 'card'])
    ->name('payments.callback');

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
