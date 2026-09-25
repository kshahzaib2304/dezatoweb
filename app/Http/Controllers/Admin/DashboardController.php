<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $todayOrders = Order::query()->whereDate('placed_at', today())->count();
        $todayRevenue = (int) Order::query()->whereDate('placed_at', today())->sum('total');
        $pending = Order::query()->whereIn('status', [
            Order::STATUS_PLACED,
            Order::STATUS_BAKING,
            Order::STATUS_QC,
            Order::STATUS_OUT_FOR_DELIVERY,
        ])->count();
        $lowStock = Product::query()
            ->whereNotNull('stock')
            ->where('stock', '<=', 5)
            ->count();

        return view('admin.dashboard', [
            'title' => 'Dashboard | Dezato Admin',
            'heading' => 'Dashboard',
            'active' => 'dashboard',
            'stats' => [
                ['label' => 'Orders today', 'value' => (string) $todayOrders, 'hint' => 'Placed since midnight'],
                ['label' => 'Revenue today', 'value' => pkr($todayRevenue), 'hint' => 'Sum of those orders'],
                ['label' => 'Still open', 'value' => (string) $pending, 'hint' => 'Not delivered yet'],
                ['label' => 'Low stock', 'value' => (string) $lowStock, 'hint' => 'Five or fewer left'],
            ],
            'recentOrders' => Order::query()->latest('placed_at')->limit(8)->get(),
        ]);
    }
}
