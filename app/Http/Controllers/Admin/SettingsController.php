<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use App\Models\User;
use App\Support\BakeryProfile;
use App\Support\NavigationMenu;
use App\Support\ShippingSettings;
use App\Support\SiteBrand;
use App\Support\SiteContent;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\Validation\ValidationException;

class SettingsController extends Controller
{
    public function edit(): View
    {
        /** @var User $admin */
        $admin = request()->user();

        return view('admin.settings.edit', [
            'title' => 'Contact & store | Dezato Admin',
            'heading' => 'Contact, brand & store',
            'active' => 'settings',
            'nav' => config('dezato_admin.nav'),
            'adminUser' => [
                'name' => $admin->name,
                'email' => $admin->email,
            ],
            'notifyEmail' => BakeryProfile::notifyEmail(),
            'publicEmail' => BakeryProfile::publicEmail(),
            'phone' => BakeryProfile::phone(),
            'whatsapp' => SiteSetting::getValue('whatsapp') ?: BakeryProfile::phone(),
            'siteUrl' => BakeryProfile::siteUrl(),
            'statusEmails' => SiteContent::statusEmailsEnabled(),
            'shipping' => ShippingSettings::all(),
            'brand' => SiteBrand::all(),
            'navLinks' => NavigationMenu::links(),
            'navRouteOptions' => NavigationMenu::allowedRoutes(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        /** @var User $admin */
        $admin = $request->user();
        $allowedRoutes = array_keys(NavigationMenu::allowedRoutes());

        $data = $request->validate([
            'brand_name' => ['required', 'string', 'max:120'],
            'brand_short_name' => ['required', 'string', 'max:40'],
            'brand_tagline' => ['required', 'string', 'max:160'],
            'brand_header_tag' => ['required', 'string', 'max:40'],
            'logo_mark' => ['nullable', 'file', 'max:1024', 'mimes:jpeg,jpg,png,webp,gif,svg'],
            'logo_icon' => ['nullable', 'file', 'max:1024', 'mimes:jpeg,jpg,png,webp,gif'],
            'admin_name' => ['required', 'string', 'max:120'],
            'admin_email' => [
                'required',
                'email',
                'max:180',
                Rule::unique('users', 'email')->ignore($admin->id),
            ],
            'notify_email' => ['required', 'email', 'max:180'],
            'public_email' => ['required', 'email', 'max:180'],
            'public_phone' => ['required', 'string', 'max:40'],
            'whatsapp' => ['nullable', 'string', 'max:40'],
            'site_url' => ['required', 'url', 'max:255'],
            'notify_status_emails' => ['nullable', 'boolean'],
            'shipping_fee' => ['required', 'numeric', 'min:0', 'max:99999'],
            'shipping_eta' => ['required', 'string', 'max:160'],
            'shipping_label' => ['required', 'string', 'max:80'],
            'nav' => ['required', 'array', 'min:1'],
            'nav.*.label' => ['required', 'string', 'max:60'],
            'nav.*.route' => ['required', 'string', Rule::in($allowedRoutes)],
            'current_password' => ['nullable', 'string'],
            'password' => ['nullable', 'confirmed', PasswordRule::defaults()],
        ], [
            'brand_name.required' => 'Enter the bakery name customers should see.',
            'admin_email.unique' => 'That login email is already used by another account.',
            'nav.min' => 'Keep at least one menu link.',
            'site_url.required' => 'Enter your live website address (https://…).',
        ]);

        $emailChanged = strcasecmp($admin->email, $data['admin_email']) !== 0;
        $passwordChanged = filled($data['password'] ?? null);
        $nameChanged = $admin->name !== $data['admin_name'];

        if ($emailChanged || $passwordChanged) {
            if (! Hash::check((string) ($data['current_password'] ?? ''), $admin->password)) {
                throw ValidationException::withMessages([
                    'current_password' => $passwordChanged
                        ? 'Enter your current password to set a new one.'
                        : 'Enter your current password to change the login email.',
                ]);
            }
        }

        SiteBrand::save([
            'name' => $data['brand_name'],
            'short_name' => $data['brand_short_name'],
            'tagline' => $data['brand_tagline'],
            'header_tag' => $data['brand_header_tag'],
        ], $request->file('logo_mark'), $request->file('logo_icon'));

        NavigationMenu::save($data['nav']);

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

        if ($nameChanged || $emailChanged || $passwordChanged) {
            $payload = [
                'name' => $data['admin_name'],
                'email' => $data['admin_email'],
            ];

            if ($passwordChanged) {
                $payload['password'] = $data['password'];
            }

            $admin->forceFill($payload)->save();
        }

        $status = 'Brand, menu, contact, and store settings saved.';
        if ($emailChanged || $passwordChanged) {
            $status = 'Settings saved. Your admin login details were updated.';
        }

        return back()->with('status', $status);
    }

    public function moveNav(Request $request, int $index): RedirectResponse
    {
        $direction = $request->validate([
            'direction' => ['required', 'in:up,down'],
        ])['direction'];

        return NavigationMenu::move($index, $direction)
            ? back()->with('status', 'Menu order updated.')
            : back()->withErrors(['nav' => 'That menu item cannot move further.']);
    }
}
