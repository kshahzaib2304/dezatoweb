@extends('layouts.admin')

@section('content')
    <aside class="admin-media-guide">
        <strong>What you can change here</strong>
        <ol class="admin-steps">
            <li>Bakery phone, WhatsApp, public email, and alert email.</li>
            <li>Live website address (used for social login callbacks and links).</li>
            <li>Pakistan courier fee and delivery time text.</li>
            <li>Your admin login password (optional — only if you fill the password fields).</li>
        </ol>
        <p class="admin-muted">SMTP email sending and Google/Facebook keys live under <a href="{{ route('admin.integrations.edit') }}">Email, logins &amp; links</a>.</p>
    </aside>

    <section class="admin-panel">
        <form class="admin-form" method="post" action="{{ route('admin.settings.update') }}">
            @csrf
            @method('PUT')

            <h2>Contact &amp; alerts</h2>
            <div class="form-grid">
                <div class="form-row form-row--full">
                    <label class="field-label" for="notify_email">Alert email *</label>
                    <input id="notify_email" class="field-input" type="email" name="notify_email" value="{{ old('notify_email', $notifyEmail) }}" required>
                    <p class="field-hint">New orders and contact-form messages are sent here.</p>
                </div>
                <div class="form-row">
                    <label class="field-label" for="public_email">Public email *</label>
                    <input id="public_email" class="field-input" type="email" name="public_email" value="{{ old('public_email', $publicEmail) }}" required>
                    <p class="field-hint">Shown to customers (footer / invoices).</p>
                </div>
                <div class="form-row">
                    <label class="field-label" for="site_url">Website address (APP URL) *</label>
                    <input id="site_url" class="field-input" type="url" name="site_url" value="{{ old('site_url', $siteUrl) }}" required placeholder="https://dezato.pk">
                    <p class="field-hint">After go-live, set this to your real domain (https://…).</p>
                </div>
                <div class="form-row">
                    <label class="field-label" for="public_phone">Bakery phone *</label>
                    <input id="public_phone" class="field-input" type="tel" name="public_phone" value="{{ old('public_phone', $phone) }}" required placeholder="+92 300 1234567">
                </div>
                <div class="form-row">
                    <label class="field-label" for="whatsapp">WhatsApp number</label>
                    <input id="whatsapp" class="field-input" type="tel" name="whatsapp" value="{{ old('whatsapp', $whatsapp) }}" placeholder="+92 300 1234567">
                    <p class="field-hint">Used for “WhatsApp” reply buttons in Admin. Leave blank to use the phone number.</p>
                </div>
                <div class="form-row form-row--full">
                    <label class="check-inline">
                        <input type="checkbox" name="notify_status_emails" value="1" @checked(old('notify_status_emails', $statusEmails))>
                        <span>Email customers when I change an order’s status</span>
                    </label>
                </div>
            </div>

            <h2 class="admin-panel--spaced" style="margin-top:1.5rem">Pakistan courier</h2>
            <div class="form-grid">
                <div class="form-row">
                    <label class="field-label" for="shipping_label">Courier label *</label>
                    <input id="shipping_label" class="field-input" type="text" name="shipping_label" value="{{ old('shipping_label', $shipping['label']) }}" required>
                </div>
                <div class="form-row">
                    <label class="field-label" for="shipping_fee">Courier fee (PKR) *</label>
                    <input id="shipping_fee" class="field-input" type="number" min="0" step="1" name="shipping_fee" value="{{ old('shipping_fee', $shipping['fee']) }}" required>
                </div>
                <div class="form-row form-row--full">
                    <label class="field-label" for="shipping_eta">Delivery time text *</label>
                    <input id="shipping_eta" class="field-input" type="text" name="shipping_eta" value="{{ old('shipping_eta', $shipping['eta']) }}" required placeholder="2–4 business days within Pakistan">
                </div>
            </div>

            <h2 class="admin-panel--spaced" style="margin-top:1.5rem">Change admin password</h2>
            <p class="field-hint">Leave blank to keep your current password. Default seeded password should be changed after first login.</p>
            <div class="form-grid">
                <div class="form-row form-row--full">
                    <label class="field-label" for="current_password">Current password</label>
                    <input id="current_password" class="field-input" type="password" name="current_password" autocomplete="current-password">
                </div>
                <div class="form-row">
                    <label class="field-label" for="password">New password</label>
                    <input id="password" class="field-input" type="password" name="password" autocomplete="new-password">
                </div>
                <div class="form-row">
                    <label class="field-label" for="password_confirmation">Confirm new password</label>
                    <input id="password_confirmation" class="field-input" type="password" name="password_confirmation" autocomplete="new-password">
                </div>
            </div>

            <div class="admin-form__actions admin-form__actions--spaced">
                <button class="btn btn--primary" type="submit">Save contact &amp; store settings</button>
            </div>
        </form>
    </section>
@endsection
