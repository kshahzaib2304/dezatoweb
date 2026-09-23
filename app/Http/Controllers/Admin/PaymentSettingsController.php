<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\PaymentMethods;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PaymentSettingsController extends Controller
{
    public function edit(): View
    {
        return view('admin.payments.edit', [
            'title' => 'Payment options | Dezato Admin',
            'heading' => 'Payment options',
            'active' => 'payments',
            'nav' => config('dezato_admin.nav'),
            'catalog' => PaymentMethods::catalog(),
            'enabled' => PaymentMethods::enabledIds(),
            'instructions' => PaymentMethods::instructions(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'methods' => ['nullable', 'array'],
            'methods.*' => ['string'],
            'instructions' => ['nullable', 'array'],
            'instructions.bank_transfer.account_name' => ['nullable', 'string', 'max:120'],
            'instructions.bank_transfer.bank_name' => ['nullable', 'string', 'max:120'],
            'instructions.bank_transfer.account_number' => ['nullable', 'string', 'max:80'],
            'instructions.bank_transfer.iban' => ['nullable', 'string', 'max:80'],
            'instructions.bank_transfer.notes' => ['nullable', 'string', 'max:500'],
            'instructions.jazzcash.account_name' => ['nullable', 'string', 'max:120'],
            'instructions.jazzcash.account_number' => ['nullable', 'string', 'max:40'],
            'instructions.jazzcash.notes' => ['nullable', 'string', 'max:500'],
            'instructions.easypaisa.account_name' => ['nullable', 'string', 'max:120'],
            'instructions.easypaisa.account_number' => ['nullable', 'string', 'max:40'],
            'instructions.easypaisa.notes' => ['nullable', 'string', 'max:500'],
        ]);

        PaymentMethods::saveEnabled($data['methods'] ?? []);
        PaymentMethods::saveInstructions($data['instructions'] ?? []);

        return back()->with(
            'status',
            'Payment options and account details saved. Customers will see transfer instructions at checkout.'
        );
    }
}
