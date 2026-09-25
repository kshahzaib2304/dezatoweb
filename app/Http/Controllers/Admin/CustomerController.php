<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Contracts\View\View;

class CustomerController extends Controller
{
    public function index(): View
    {
        $customers = User::query()
            ->where('role', User::ROLE_CUSTOMER)
            ->withCount('orders')
            ->latest()
            ->paginate(25);

        return view('admin.customers.index', [
            'title' => 'Customers | Dezato Admin',
            'heading' => 'Customers',
            'active' => 'customers',
            'customers' => $customers,
        ]);
    }
}
