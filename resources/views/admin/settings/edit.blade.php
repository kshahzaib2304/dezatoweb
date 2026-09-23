@extends('layouts.admin')

@section('content')
    <aside class="admin-media-guide">
        <strong>How alerts work</strong>
        <ol class="admin-steps">
            <li>Enter the email that should receive <strong>new order</strong> and <strong>website message</strong> alerts.</li>
            <li>Enter the phone / WhatsApp customers should use to reach you.</li>
            <li>Ask your web partner to connect real email sending (SMTP) on the server so messages leave the website. Until then, alerts are still saved under Messages, and mail may only appear in the server log.</li>
        </ol>
    </aside>

    <section class="admin-panel">
        <form class="admin-form" method="post" action="{{ route('admin.settings.update') }}">
            @csrf
            @method('PUT')
            <div class="form-grid">
                <div class="form-row form-row--full">
                    <label class="field-label" for="notify_email">Alert email *</label>
                    <input id="notify_email" class="field-input" type="email" name="notify_email" value="{{ old('notify_email', $notifyEmail) }}" required>
                    <p class="field-hint">New orders and contact-form messages are sent here.</p>
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
                        <span>Email customers when I change an order’s status (Baking, Out for delivery, etc.)</span>
                    </label>
                    <p class="field-hint">Turn this off if you prefer to call/WhatsApp customers yourself.</p>
                </div>
            </div>
            <div class="admin-form__actions">
                <button class="btn btn--primary" type="submit">Save contact &amp; alerts</button>
            </div>
        </form>
    </section>
@endsection
