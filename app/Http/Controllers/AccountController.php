<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Order;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    public function profile(): View
    {
        $user = Auth::user();

        return view('account.profile', $this->shell([
            'title' => 'Profile | Dezato Cake House',
            'metaDescription' => 'Manage your Dezato Cake House profile.',
            'active' => 'profile',
            'profile' => [
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone ?? '',
            ],
        ]));
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'phone' => ['nullable', 'string', 'max:40'],
        ]);

        $request->user()->update($data);

        return back()->with('status', 'Your profile was updated.');
    }

    public function addresses(): View
    {
        return view('account.addresses', $this->shell([
            'title' => 'Addresses | Dezato Cake House',
            'metaDescription' => 'Saved delivery addresses.',
            'active' => 'addresses',
            'addresses' => Auth::user()->addresses()->latest()->get()->map(fn (Address $address): array => [
                'id' => (string) $address->id,
                'label' => $address->label,
                'line1' => $address->line1,
                'area' => $address->area,
                'city' => $address->city,
                'is_default' => $address->is_default,
            ])->all(),
        ]));
    }

    public function storeAddress(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'label' => ['nullable', 'string', 'max:64'],
            'line1' => ['required', 'string', 'max:255'],
            'area' => ['nullable', 'string', 'max:120'],
            'city' => ['nullable', 'string', 'max:120'],
        ]);

        $user = $request->user();
        $makeDefault = $user->addresses()->count() === 0;

        if ($makeDefault) {
            $user->addresses()->update(['is_default' => false]);
        }

        $user->addresses()->create([
            'label' => $data['label'] ?: 'Home',
            'line1' => $data['line1'],
            'area' => $data['area'] ?? null,
            'city' => $data['city'] ?: 'Karachi',
            'is_default' => $makeDefault,
        ]);

        return back()->with('status', 'Address saved.');
    }

    public function destroyAddress(Address $address): RedirectResponse
    {
        abort_unless($address->user_id === Auth::id(), 403);
        $address->delete();

        return back()->with('status', 'Address removed.');
    }

    public function orders(): View
    {
        $orders = Order::query()
            ->where(function ($query): void {
                $query->where('user_id', Auth::id())
                    ->orWhere('email', Auth::user()->email);
            })
            ->latest('placed_at')
            ->get()
            ->map(fn (Order $order): array => [
                'number' => $order->number,
                'placed_at' => optional($order->placed_at)->format('Y-m-d H:i'),
                'status' => $order->status,
                'status_label' => $order->statusLabel(),
                'total' => (float) $order->total,
                'method' => $order->methodLabel().($order->location_name ? ' · '.$order->location_name : ''),
                'items' => $order->items->map(fn ($item) => $item->product_name.($item->quantity > 1 ? ' × '.$item->quantity : ''))->all(),
            ]);

        return view('account.orders', $this->shell([
            'title' => 'Order history | Dezato Cake House',
            'metaDescription' => 'Your Dezato orders.',
            'active' => 'orders',
            'orders' => $orders,
        ]));
    }

    public function track(string $number): View
    {
        $order = Order::query()
            ->with('items')
            ->where('number', $number)
            ->where(function ($query): void {
                $query->where('user_id', Auth::id())
                    ->orWhere('email', Auth::user()->email);
            })
            ->firstOrFail();

        return view('account.track', $this->shell([
            'title' => 'Track '.$order->number.' | Dezato Cake House',
            'metaDescription' => 'Track your order.',
            'active' => 'orders',
            'order' => [
                'number' => $order->number,
                'status' => $order->status,
                'status_label' => $order->statusLabel(),
                'total' => (float) $order->total,
            ],
            'steps' => collect($order->trackingSteps())->map(fn (array $step): array => [
                'key' => $step['key'],
                'label' => $step['label'],
                'state' => $step['state'],
            ])->all(),
        ]));
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function shell(array $data): array
    {
        return array_merge([
            'nav' => [
                ['id' => 'profile', 'label' => 'Profile', 'route' => 'account.profile'],
                ['id' => 'addresses', 'label' => 'Addresses', 'route' => 'account.addresses'],
                ['id' => 'orders', 'label' => 'Orders', 'route' => 'account.orders'],
            ],
        ], $data);
    }
}
