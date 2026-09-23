<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use App\Models\User;
use App\Support\BakeryProfile;
use App\Support\ShippingSettings;
use App\Support\SiteContent;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\Validation\ValidationException;

class SettingsController extends Controller
{
    public function edit(): View
    {
        $shipping = ShippingSettings::all();

        return view('admin.settings.edit', [
            'title' => 'Contact & store | Dezato Admin',
            'heading' => 'Contact, shipping & account',
            'active' => 'settings',
            'nav' => config('dezato_admin.nav'),
            'notifyEmail' => BakeryProfile::notifyEmail(),
            'publicEmail' => BakeryProfile::publicEmail(),
            'phone' => BakeryProfile::phone(),
            'whatsapp' => SiteSetting::getValue('whatsapp') ?: BakeryProfile::phone(),
            'siteUrl' => BakeryProfile::siteUrl(),
            'statusEmails' => SiteContent::statusEmailsEnabled(),
            'shipping' => $shipping,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'notify_email' => ['required', 'email', 'max:180'],
            'public_email' => ['required', 'email', 'max:180'],
            'public_phone' => ['required', 'string', 'max:40'],
            'whatsapp' => ['nullable', 'string', 'max:40'],
            'site_url' => ['required', 'url', 'max:255'],
            'notify_status_emails' => ['nullable', 'boolean'],
            'shipping_fee' => ['required', 'numeric', 'min:0', 'max:99999'],
            'shipping_eta' => ['required', 'string', 'max:160'],
            'shipping_label' => ['required', 'string', 'max:80'],
            'current_password' => ['nullable', 'string'],
            'password' => ['nullable', 'confirmed', PasswordRule::defaults()],
        ], [
            'notify_email.required' => 'Enter the email where new orders and messages should arrive.',
            'public_phone.required' => 'Enter the bakery phone number customers can call.',
            'site_url.required' => 'Enter your live website address (https://…).',
        ]);

        SiteSetting::putValue('notify_email', $data['notify_email']);
        SiteSetting::putValue('public_email', $data['public_email']);
        SiteSetting::putValue('public_phone', $data['public_phone']);
        SiteSetting::putValue('whatsapp', trim((string) ($data['whatsapp'] ?? '')) ?: null);
        SiteSetting::putValue('site_url', rtrim($data['site_url'], '/'));
        SiteContent::setStatusEmailsEnabled($request->boolean('notify_status_emails'));

        ShippingSettings::save([
            'fee' => $data['shipping_fee'],
            'eta' => $data['shipping_eta'],
            'label' => $data['shipping_label'],
        ]);

        BakeryProfile::applySiteUrl();

        if (filled($data['password'] ?? null)) {
            /** @var User $user */
            $user = $request->user();

            if (! Hash::check((string) ($data['current_password'] ?? ''), $user->password)) {
                throw ValidationException::withMessages([
                    'current_password' => 'Enter your current password to set a new one.',
                ]);
            }

            $user->forceFill(['password' => $data['password']])->save();
        }

        return back()->with(
            'status',
            'Contact, shipping, and account settings saved.'
        );
    }
}
