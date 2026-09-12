<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Customer account screens (UI-first with mock data).
 */
class AccountController extends Controller
{
    public function profile(): View
    {
        return view('account.profile', $this->shell([
            'title' => 'Profile | Dezato Cake House',
            'metaDescription' => 'Manage your Dezato Cake House profile.',
            'active' => 'profile',
            'profile' => config('dezato_ui.profile'),
        ]));
    }

    public function addresses(): View
    {
        return view('account.addresses', $this->shell([
            'title' => 'Addresses | Dezato Cake House',
            'metaDescription' => 'Saved delivery addresses for faster checkout.',
            'active' => 'addresses',
            'addresses' => config('dezato_ui.addresses', []),
        ]));
    }

    public function orders(): View
    {
        return view('account.orders', $this->shell([
            'title' => 'Order history | Dezato Cake House',
            'metaDescription' => 'View your past Dezato orders.',
            'active' => 'orders',
            'orders' => config('dezato_ui.orders', []),
        ]));
    }

    public function track(string $number): View
    {
        $order = collect(config('dezato_ui.orders', []))
            ->firstWhere('number', $number);

        abort_if($order === null, 404);

        return view('account.track', $this->shell([
            'title' => 'Track '.$number.' | Dezato Cake House',
            'metaDescription' => 'Track your Dezato order status.',
            'active' => 'orders',
            'order' => $order,
            'steps' => config('dezato_ui.tracking_steps', []),
        ]));
    }

    public function stub(Request $request): RedirectResponse
    {
        return back()->with(
            'status',
            'UI ready — account updates will save when backend is connected.'
        );
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
