<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Support\BakeryProfile;
use App\Support\PaymentMethods;
use App\Support\SiteContent;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    public function __construct(private readonly \App\Support\Notifier $notifier) {}

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
            'paymentInstructions' => PaymentMethods::isTransfer($order->payment_method)
                ? PaymentMethods::instructionsFor($order->payment_method)
                : null,
        ]);
    }

    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(array_keys(Order::STATUSES))],
        ]);

        $previous = $order->status;
        $order->update(['status' => $data['status']]);
        $order = $order->fresh();

        $this->notifier->orderStatusUpdated($order, $previous);

        return back()->with(
            'status',
            'Order status updated to “'.$order->statusLabel().'”. Customer can see this under Track order'
            .(SiteContent::statusEmailsEnabled() ? ', and an update email was queued.' : '.')
        );
    }

    public function markPaid(Order $order): RedirectResponse
    {
        $order->update(['payment_status' => Order::PAYMENT_PAID]);

        return back()->with('status', 'Marked as paid. Great - you can start preparing the order.');
    }

    public function invoice(Order $order): View
    {
        $order->load('items');

        return view('admin.orders.invoice', [
            'order' => $order,
            'bakery' => [
                'name' => config('dezato.brand.name', 'Dezato Cake House'),
                'phone' => BakeryProfile::phone(),
                'email' => BakeryProfile::notifyEmail(),
            ],
        ]);
    }
}
