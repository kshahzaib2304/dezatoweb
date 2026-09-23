<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use App\Support\BakeryProfile;
use App\Support\SiteContent;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function edit(): View
    {
        return view('admin.settings.edit', [
            'title' => 'Contact & alerts | Dezato Admin',
            'heading' => 'Contact & order alerts',
            'active' => 'settings',
            'nav' => config('dezato_admin.nav'),
            'notifyEmail' => BakeryProfile::notifyEmail(),
            'phone' => BakeryProfile::phone(),
            'whatsapp' => SiteSetting::getValue('whatsapp') ?: BakeryProfile::phone(),
            'statusEmails' => SiteContent::statusEmailsEnabled(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'notify_email' => ['required', 'email', 'max:180'],
            'public_phone' => ['required', 'string', 'max:40'],
            'whatsapp' => ['nullable', 'string', 'max:40'],
            'notify_status_emails' => ['nullable', 'boolean'],
        ], [
            'notify_email.required' => 'Enter the email where new orders and messages should arrive.',
            'public_phone.required' => 'Enter the bakery phone number customers can call.',
        ]);

        SiteSetting::putValue('notify_email', $data['notify_email']);
        SiteSetting::putValue('public_phone', $data['public_phone']);
        SiteSetting::putValue('whatsapp', trim((string) ($data['whatsapp'] ?? '')) ?: null);
        SiteContent::setStatusEmailsEnabled($request->boolean('notify_status_emails'));

        return back()->with(
            'status',
            'Contact details saved. New orders and website messages will alert '.$data['notify_email'].'.'
        );
    }
}
