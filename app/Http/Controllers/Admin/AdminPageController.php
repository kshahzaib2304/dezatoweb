<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Admin dashboard shell (UI-first).
 */
class AdminPageController extends Controller
{
    public function dashboard(): View
    {
        return $this->page('admin.dashboard', 'Dashboard', 'dashboard', [
            'stats' => config('dezato_ui.admin.stats', []),
            'recentOrders' => config('dezato_ui.admin.recent_orders', []),
        ]);
    }

    public function products(): View
    {
        return $this->page('admin.products', 'Products', 'products', [
            'products' => config('dezato.menu.products', []),
            'categories' => config('dezato.menu.categories', []),
        ]);
    }

    public function orders(): View
    {
        return $this->page('admin.orders', 'Orders', 'orders', [
            'orders' => config('dezato_ui.orders', []),
        ]);
    }

    public function customers(): View
    {
        return $this->page('admin.customers', 'Customers', 'customers', [
            'customers' => [
                ['name' => 'Ayesha Khan', 'email' => 'ayesha@example.com', 'orders' => 6, 'segment' => 'Frequent'],
                ['name' => 'Bilal Ahmed', 'email' => 'bilal@example.com', 'orders' => 2, 'segment' => 'New'],
                ['name' => 'Sara Malik', 'email' => 'sara@example.com', 'orders' => 1, 'segment' => 'Lapsed'],
            ],
        ]);
    }

    public function promotions(): View
    {
        return $this->page('admin.promotions', 'Promotions', 'promotions', [
            'coupons' => [
                ['code' => 'DEZATO10', 'type' => '10%', 'uses' => '12 / 100', 'expires' => '2026-12-31'],
                ['code' => 'EID500', 'type' => '₨ 500', 'uses' => '4 / 50', 'expires' => '2026-10-15'],
            ],
        ]);
    }

    public function content(): View
    {
        return $this->page('admin.content', 'Content', 'content', [
            'pages' => [
                ['title' => 'About Us', 'status' => 'Published'],
                ['title' => 'Privacy Policy', 'status' => 'Draft'],
                ['title' => 'FAQs', 'status' => 'Published'],
            ],
            'banners' => [
                ['title' => 'Home hero', 'status' => 'Live'],
                ['title' => 'Ramadan promo', 'status' => 'Scheduled'],
            ],
        ]);
    }

    public function reports(): View
    {
        return $this->page('admin.reports', 'Reports', 'reports', [
            'reports' => [
                'Sales overview',
                'Best-selling products',
                'Revenue by category',
                'Delivery vs pickup',
                'Payment methods',
                'Peak order times',
                'New vs returning customers',
            ],
        ]);
    }

    public function stub(Request $request): RedirectResponse
    {
        return back()->with(
            'status',
            'Admin UI ready — actions will persist when the backend is connected.'
        );
    }

    /**
     * @param  array<string, mixed>  $extra
     */
    private function page(string $view, string $heading, string $active, array $extra = []): View
    {
        return view($view, array_merge([
            'title' => $heading.' | Dezato Admin',
            'metaDescription' => 'Dezato Cake House admin — '.$heading,
            'heading' => $heading,
            'active' => $active,
            'nav' => [
                ['id' => 'dashboard', 'label' => 'Dashboard', 'route' => 'admin.dashboard'],
                ['id' => 'products', 'label' => 'Products', 'route' => 'admin.products'],
                ['id' => 'orders', 'label' => 'Orders', 'route' => 'admin.orders'],
                ['id' => 'customers', 'label' => 'Customers', 'route' => 'admin.customers'],
                ['id' => 'promotions', 'label' => 'Promotions', 'route' => 'admin.promotions'],
                ['id' => 'content', 'label' => 'Content', 'route' => 'admin.content'],
                ['id' => 'reports', 'label' => 'Reports', 'route' => 'admin.reports'],
            ],
        ], $extra));
    }
}
