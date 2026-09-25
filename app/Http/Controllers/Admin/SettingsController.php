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
        return view('admin.settings.edit', [
            'title' => 'Contact & store | Dezato Admin',
            'heading' => 'Contact, brand & store',
            'active' => 'settings',
            'nav' => config('dezato_admin.nav'),
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
        $allowedRoutes = array_keys(NavigationMenu::allowedRoutes());

        $data = $request->validate([
            'brand_name' => ['required', 'string', 'max:120'],
            'brand_short_name' => ['required', 'string', 'max:40'],
            'brand_tagline' => ['required', 'string', 'max:160'],
            'brand_header_tag' => ['required', 'string', 'max:40'],
            'logo_mark' => ['nullable', 'file', 'max:1024', 'mimes:jpeg,jpg,png,webp,gif,svg'],
            'logo_icon' => ['nullable', 'file', 'max:1024', 'mimes:jpeg,jpg,png,webp,gif'],
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
            'nav.min' => 'Keep at least one menu link.',
            'site_url.required' => 'Enter your live website address (https://…).',
        ]);

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

        return back()->with('status', 'Brand, menu, contact, and store settings saved.');
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
