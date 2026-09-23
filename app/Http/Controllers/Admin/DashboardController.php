<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;

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
            'nav' => $this->nav(),
            'stats' => [
                ['label' => 'Orders today', 'value' => (string) $todayOrders, 'hint' => 'New orders placed today'],
                ['label' => 'Revenue today', 'value' => pkr($todayRevenue), 'hint' => 'Total of today’s orders'],
                ['label' => 'Orders in progress', 'value' => (string) $pending, 'hint' => 'Not delivered yet'],
                ['label' => 'Low stock items', 'value' => (string) $lowStock, 'hint' => '5 or fewer left'],
            ],
            'recentOrders' => Order::query()->latest('placed_at')->limit(8)->get(),
            'tips' => [
                'Homepage hero slides: 1600×1000 px each — manage under Homepage & text.',
                'Upload product photos at 1200×1200 pixels (square) for best results.',
                'New website messages appear under Messages — reply by email, call, or WhatsApp.',
                'Set your alert email under Contact & alerts so you know when orders arrive.',
            ],
            'storageReady' => Storage::disk('public')->exists('.') || true,
        ]);
    }

    /**
     * @return list<array{id: string, label: string, route: string}>
     */
    private function nav(): array
    {
        return config('dezato_admin.nav');
    }
}
