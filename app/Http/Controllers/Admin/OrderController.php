<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->string('status')->toString();

        $orders = Order::query()
            ->withCount('items')
            ->when($status !== '' && isset(Order::STATUSES[$status]), fn ($q) => $q->where('status', $status))
            ->latest('placed_at')
            ->paginate(20)
            ->withQueryString();

        return view('admin.orders.index', [
            'title' => 'Orders | Dezato Admin',
            'heading' => 'Orders',
            'active' => 'orders',
            'nav' => config('dezato_admin.nav'),
            'orders' => $orders,
            'statuses' => Order::STATUSES,
            'status' => $status,
        ]);
    }

    public function show(Order $order): View
    {
        $order->load('items');

        return view('admin.orders.show', [
            'title' => 'Order '.$order->number.' | Dezato Admin',
            'heading' => 'Order '.$order->number,
            'active' => 'orders',
            'nav' => config('dezato_admin.nav'),
            'order' => $order,
            'statuses' => Order::STATUSES,
        ]);
    }

    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(array_keys(Order::STATUSES))],
        ]);

        $order->update(['status' => $data['status']]);

        return back()->with(
            'status',
            'Order status updated to “'.$order->fresh()->statusLabel().'”. The customer can see this on Track order.'
        );
    }
}
