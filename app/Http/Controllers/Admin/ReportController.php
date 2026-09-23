<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function __invoke(): View
    {
        $todayOrders = Order::query()->whereDate('placed_at', today())->count();
        $todayRevenue = (float) Order::query()->whereDate('placed_at', today())->sum('total');
        $weekOrders = Order::query()->where('placed_at', '>=', now()->subDays(7))->count();
        $weekRevenue = (float) Order::query()->where('placed_at', '>=', now()->subDays(7))->sum('total');
        $monthOrders = Order::query()->where('placed_at', '>=', now()->subDays(30))->count();
        $monthRevenue = (float) Order::query()->where('placed_at', '>=', now()->subDays(30))->sum('total');

        $topItems = OrderItem::query()
            ->select('product_name', DB::raw('SUM(quantity) as qty'), DB::raw('SUM(line_total) as revenue'))
            ->groupBy('product_name')
            ->orderByDesc('qty')
            ->limit(8)
            ->get();

        return view('admin.reports.index', [
            'title' => 'Reports | Dezato Admin',
            'heading' => 'Simple reports',
            'active' => 'reports',
            'nav' => config('dezato_admin.nav'),
            'stats' => [
                ['label' => 'Orders today', 'value' => (string) $todayOrders, 'hint' => pkr($todayRevenue).' revenue'],
                ['label' => 'Last 7 days', 'value' => (string) $weekOrders, 'hint' => pkr($weekRevenue).' revenue'],
                ['label' => 'Last 30 days', 'value' => (string) $monthOrders, 'hint' => pkr($monthRevenue).' revenue'],
                ['label' => 'All-time orders', 'value' => (string) Order::query()->count(), 'hint' => pkr((float) Order::query()->sum('total')).' revenue'],
            ],
            'topItems' => $topItems,
        ]);
    }
}
