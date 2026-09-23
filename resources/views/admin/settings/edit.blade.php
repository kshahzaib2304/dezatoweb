@extends('layouts.admin')

@section('content')
    <aside class="admin-media-guide">
        <strong>What you can change here</strong>
        <ol class="admin-steps">
            <li><strong>Brand name</strong> shown in the header, footer, and emails.</li>
            <li><strong>Top menu</strong> labels and order (Home, About, etc.).</li>
            <li>Phone, WhatsApp, emails, live website URL, courier fee.</li>
            <li>Your admin password (optional).</li>
        </ol>
        <p class="admin-muted">SMTP / Google / Facebook keys: <a href="{{ route('admin.integrations.edit') }}">Email, logins &amp; links</a>.</p>
    </aside>

    <section class="admin-panel">
        <form class="admin-form" method="post" action="{{ route('admin.settings.update') }}">
            @csrf
            @method('PUT')

            <h2>Brand name</h2>
            <div class="form-grid">
                <div class="form-row">
                    <label class="field-label" for="brand_name">Full bakery name *</label>
                    <input id="brand_name" class="field-input" type="text" name="brand_name" value="{{ old('brand_name', $brand['name']) }}" required>
                </div>
                <div class="form-row">
                    <label class="field-label" for="brand_short_name">Short name (logo text) *</label>
                    <input id="brand_short_name" class="field-input" type="text" name="brand_short_name" value="{{ old('brand_short_name', $brand['short_name']) }}" required>
                    <p class="field-hint">Shown next to the logo (e.g. Dezato).</p>
                </div>
                <div class="form-row form-row--full">
                    <label class="field-label" for="brand_tagline">Tagline *</label>
                    <input id="brand_tagline" class="field-input" type="text" name="brand_tagline" value="{{ old('brand_tagline', $brand['tagline']) }}" required>
                </div>
            </div>

            <h2 class="admin-section-title">Top website menu</h2>
            <p class="field-hint">Change labels or which page each link opens. Use <strong>Move up / Move down</strong> to change the order (left-to-right on desktop).</p>
            @foreach ($navLinks as $index => $link)
                <article class="admin-slide-card">
                    <div class="admin-panel__head">
                        <strong>{{ $link['label'] }}</strong>
                        <div class="admin-toolbar__actions">
                            @if ($index > 0)
                                <button class="btn btn--ghost btn--sm" type="submit" form="nav-move-{{ $index }}-up">Move up</button>
                            @endif
                            @if ($index < count($navLinks) - 1)
                                <button class="btn btn--ghost btn--sm" type="submit" form="nav-move-{{ $index }}-down">Move down</button>
                            @endif
                        </div>
                    </div>
                    <div class="form-grid">
                        <div class="form-row">
                            <label class="field-label">Menu label *</label>
                            <input class="field-input" type="text" name="nav[{{ $index }}][label]" value="{{ old('nav.'.$index.'.label', $link['label']) }}" required>
                        </div>
                        <div class="form-row">
                            <label class="field-label">Goes to *</label>
                            <select class="field-input" name="nav[{{ $index }}][route]" required>
                                @foreach ($navRouteOptions as $route => $routeLabel)
                                    <option value="{{ $route }}" @selected(old('nav.'.$index.'.route', $link['route']) === $route)>{{ $routeLabel }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </article>
            @endforeach

            <h2 class="admin-section-title">Contact &amp; alerts</h2>
            <div class="form-grid">
                <div class="form-row form-row--full">
                    <label class="field-label" for="notify_email">Alert email *</label>
                    <input id="notify_email" class="field-input" type="email" name="notify_email" value="{{ old('notify_email', $notifyEmail) }}" required>
                </div>
                <div class="form-row">
                    <label class="field-label" for="public_email">Public email *</label>
                    <input id="public_email" class="field-input" type="email" name="public_email" value="{{ old('public_email', $publicEmail) }}" required>
                </div>
                <div class="form-row">
                    <label class="field-label" for="site_url">Website address *</label>
                    <input id="site_url" class="field-input" type="url" name="site_url" value="{{ old('site_url', $siteUrl) }}" required placeholder="https://dezato.pk">
                    <p class="field-hint">Live site URL for emails and sharing. Locally, match how you open the site (e.g. <code>http://127.0.0.1:8000</code>). Wrong value used to break CSS.</p>
                </div>
                <div class="form-row">
                    <label class="field-label" for="public_phone">Bakery phone *</label>
                    <input id="public_phone" class="field-input" type="tel" name="public_phone" value="{{ old('public_phone', $phone) }}" required>
                </div>
                <div class="form-row">
                    <label class="field-label" for="whatsapp">WhatsApp number</label>
                    <input id="whatsapp" class="field-input" type="tel" name="whatsapp" value="{{ old('whatsapp', $whatsapp) }}">
                </div>
                <div class="form-row form-row--full">
                    <label class="check-inline">
                        <input type="checkbox" name="notify_status_emails" value="1" @checked(old('notify_status_emails', $statusEmails))>
                        <span>Email customers when I change an order’s status</span>
                    </label>
                </div>
            </div>

            <h2 class="admin-section-title">Pakistan courier</h2>
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
                    <input id="shipping_eta" class="field-input" type="text" name="shipping_eta" value="{{ old('shipping_eta', $shipping['eta']) }}" required>
                </div>
            </div>

            <h2 class="admin-section-title">Change admin password</h2>
            <p class="field-hint">Leave blank to keep your current password.</p>
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
                <button class="btn btn--primary" type="submit">Save brand, menu &amp; store settings</button>
            </div>
        </form>
    </section>

    @foreach ($navLinks as $index => $link)
        @if ($index > 0)
            <form id="nav-move-{{ $index }}-up" method="post" action="{{ route('admin.settings.nav.move', $index) }}" class="sr-only">
                @csrf
                <input type="hidden" name="direction" value="up">
            </form>
        @endif
        @if ($index < count($navLinks) - 1)
            <form id="nav-move-{{ $index }}-down" method="post" action="{{ route('admin.settings.nav.move', $index) }}" class="sr-only">
                @csrf
                <input type="hidden" name="direction" value="down">
            </form>
        @endif
    @endforeach
@endsection
