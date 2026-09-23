<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\GatewayCredentials;
use App\Support\MailSettings;
use App\Support\SecureSettings;
use App\Support\SocialAuth;
use App\Support\SocialLinks;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class IntegrationsController extends Controller
{
    public function edit(): View
    {
        $mail = MailSettings::all();
        unset($mail['password']);

        return view('admin.integrations.edit', [
            'title' => 'Email, logins & links | Dezato Admin',
            'heading' => 'Email, logins & links',
            'active' => 'integrations',
            'nav' => config('dezato_admin.nav'),
            'mail' => $mail,
            'mailPasswordHint' => SecureSettings::maskedHint('mail_smtp_password'),
            'socialProviders' => SocialAuth::providers(),
            'socialLinks' => SocialLinks::catalog(),
            'gateways' => GatewayCredentials::groups(),
            'appUrl' => rtrim((string) config('app.url'), '/'),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $links = collect($request->input('links', []))
            ->map(fn ($value) => trim((string) $value) ?: null)
            ->all();

        $request->merge(['links' => $links]);

        $data = $request->validate([
            'mail_enabled' => ['nullable', 'boolean'],
            'mail_host' => ['nullable', 'string', 'max:180'],
            'mail_port' => ['nullable', 'integer', 'min:1', 'max:65535'],
            'mail_encryption' => ['nullable', Rule::in(['tls', 'ssl', 'none'])],
            'mail_username' => ['nullable', 'string', 'max:180'],
            'mail_password' => ['nullable', 'string', 'max:255'],
            'mail_from_address' => ['nullable', 'email', 'max:180'],
            'mail_from_name' => ['nullable', 'string', 'max:120'],

            'social' => ['nullable', 'array'],
            'social.google.enabled' => ['nullable', 'boolean'],
            'social.google.client_id' => ['nullable', 'string', 'max:255'],
            'social.google.client_secret' => ['nullable', 'string', 'max:255'],
            'social.facebook.enabled' => ['nullable', 'boolean'],
            'social.facebook.client_id' => ['nullable', 'string', 'max:255'],
            'social.facebook.client_secret' => ['nullable', 'string', 'max:255'],

            'links' => ['nullable', 'array'],
            'links.facebook' => ['nullable', 'url', 'max:255'],
            'links.instagram' => ['nullable', 'url', 'max:255'],
            'links.tiktok' => ['nullable', 'url', 'max:255'],
            'links.youtube' => ['nullable', 'url', 'max:255'],

            'gateway' => ['nullable', 'array'],
        ], [
            'links.*.url' => 'Please enter a full link starting with https://',
        ]);

        MailSettings::save([
            'enabled' => $request->boolean('mail_enabled'),
            'host' => $data['mail_host'] ?? '',
            'port' => $data['mail_port'] ?? 587,
            'encryption' => $data['mail_encryption'] ?? 'tls',
            'username' => $data['mail_username'] ?? '',
            'from_address' => $data['mail_from_address'] ?? '',
            'from_name' => $data['mail_from_name'] ?? '',
        ], $data['mail_password'] ?? null);

        $socialInput = is_array($data['social'] ?? null) ? $data['social'] : [];
        SocialAuth::save($socialInput, [
            'google' => $socialInput['google']['client_secret'] ?? null,
            'facebook' => $socialInput['facebook']['client_secret'] ?? null,
        ]);

        SocialLinks::save(is_array($data['links'] ?? null) ? $data['links'] : []);

        GatewayCredentials::save(
            is_array($request->input('gateway')) ? $request->input('gateway') : [],
            is_array($request->input('gateway_flags')) ? $request->input('gateway_flags') : []
        );

        MailSettings::apply();

        return back()->with(
            'status',
            'Saved. Email sending, social sign-in, footer links, and payment keys update immediately - no code deploy needed.'
        );
    }
}
