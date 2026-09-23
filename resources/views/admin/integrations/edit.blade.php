@extends('layouts.admin')

@section('content')
    <aside class="admin-media-guide">
        <strong>What this screen is for</strong>
        <ol class="admin-steps">
            <li><strong>Email (SMTP)</strong> - so order emails leave the website after you go live.</li>
            <li><strong>Google / Facebook sign-in</strong> - free social login for customers (paste App IDs here).</li>
            <li><strong>Footer links</strong> - Instagram, Facebook, etc. shown on the website.</li>
            <li><strong>Payment gateway keys</strong> - paste merchant API keys later; no developer needed to update .env.</li>
        </ol>
        <p class="admin-muted">Secrets stay hidden. Leave a password/secret field blank to keep what is already saved.</p>
    </aside>

    <form class="admin-form" method="post" action="{{ route('admin.integrations.update') }}">
        @csrf
        @method('PUT')

        <section class="admin-panel">
            <h2>1. Outgoing email (SMTP)</h2>
            <p class="admin-lead">Ask your hosting for SMTP details, or use Gmail / Outlook app passwords. Until this is on, emails are only saved in the server log.</p>
            <div class="form-grid">
                <div class="form-row form-row--full">
                    <label class="check-inline">
                        <input type="checkbox" name="mail_enabled" value="1" @checked(old('mail_enabled', $mail['enabled']))>
                        <span>Send real emails using the settings below</span>
                    </label>
                </div>
                <div class="form-row">
                    <label class="field-label" for="mail_host">SMTP host</label>
                    <input id="mail_host" class="field-input" type="text" name="mail_host" value="{{ old('mail_host', $mail['host']) }}" placeholder="smtp.gmail.com">
                </div>
                <div class="form-row">
                    <label class="field-label" for="mail_port">Port</label>
                    <input id="mail_port" class="field-input" type="number" name="mail_port" value="{{ old('mail_port', $mail['port']) }}" min="1" max="65535">
                </div>
                <div class="form-row">
                    <label class="field-label" for="mail_encryption">Encryption</label>
                    <select id="mail_encryption" class="field-input" name="mail_encryption">
                        @foreach (['tls' => 'TLS (recommended)', 'ssl' => 'SSL', 'none' => 'None'] as $value => $label)
                            <option value="{{ $value }}" @selected(old('mail_encryption', $mail['encryption'] ?: 'none') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-row">
                    <label class="field-label" for="mail_username">SMTP username</label>
                    <input id="mail_username" class="field-input" type="text" name="mail_username" value="{{ old('mail_username', $mail['username']) }}" autocomplete="off">
                </div>
                <div class="form-row">
                    <label class="field-label" for="mail_password">SMTP password</label>
                    <input id="mail_password" class="field-input" type="password" name="mail_password" value="" autocomplete="new-password" placeholder="••••••••">
                    <p class="field-hint">{{ $mailPasswordHint }}</p>
                </div>
                <div class="form-row">
                    <label class="field-label" for="mail_from_address">From email</label>
                    <input id="mail_from_address" class="field-input" type="email" name="mail_from_address" value="{{ old('mail_from_address', $mail['from_address']) }}" placeholder="orders@dezato.pk">
                </div>
                <div class="form-row">
                    <label class="field-label" for="mail_from_name">From name</label>
                    <input id="mail_from_name" class="field-input" type="text" name="mail_from_name" value="{{ old('mail_from_name', $mail['from_name']) }}">
                </div>
            </div>
        </section>

        <section class="admin-panel admin-panel--spaced">
            <h2>2. Customer social sign-in (free)</h2>
            <p class="admin-lead">
                <strong>Google</strong> and <strong>Facebook</strong> are free and work well for websites.
                Instagram is not used for sign-in (Meta does not give a reliable free login with email) - add your Instagram page under Footer links below instead.
            </p>

            @foreach ($socialProviders as $id => $provider)
                <article class="admin-slide-card">
                    <label class="check-inline">
                        <input type="checkbox" name="social[{{ $id }}][enabled]" value="1" @checked(old('social.'.$id.'.enabled', $provider['enabled']))>
                        <span><strong>Enable {{ $provider['label'] }} sign-in</strong></span>
                    </label>
                    <p class="field-hint">{{ $provider['help'] }}</p>
                    <div class="form-grid form-grid--spaced">
                        <div class="form-row form-row--full">
                            <label class="field-label">Callback URL (copy into {{ $provider['label'] }} console)</label>
                            <input class="field-input" type="text" readonly value="{{ $provider['redirect'] }}" onclick="this.select()">
                            <p class="field-hint">Paste this exact link as the Authorized redirect URI.</p>
                        </div>
                        <div class="form-row">
                            <label class="field-label" for="social-{{ $id }}-id">Client / App ID</label>
                            <input id="social-{{ $id }}-id" class="field-input" type="text" name="social[{{ $id }}][client_id]" value="{{ old('social.'.$id.'.client_id', $provider['client_id']) }}" autocomplete="off">
                        </div>
                        <div class="form-row">
                            <label class="field-label" for="social-{{ $id }}-secret">Client / App secret</label>
                            <input id="social-{{ $id }}-secret" class="field-input" type="password" name="social[{{ $id }}][client_secret]" value="" autocomplete="new-password" placeholder="••••••••">
                            <p class="field-hint">{{ $provider['client_secret_set'] ? 'Saved - leave blank to keep.' : 'Not set yet.' }}</p>
                        </div>
                    </div>
                    <details class="admin-details">
                        <summary>How to get {{ $provider['label'] }} keys (free)</summary>
                        @if ($id === 'google')
                            <ol class="admin-steps">
                                <li>Open <a href="https://console.cloud.google.com/apis/credentials" target="_blank" rel="noopener">Google Cloud Console → Credentials</a>.</li>
                                <li>Create an <strong>OAuth client ID</strong> (Application type: Web application).</li>
                                <li>Add the Callback URL above under Authorized redirect URIs.</li>
                                <li>Copy Client ID and Client secret into the fields here, tick Enable, Save.</li>
                            </ol>
                        @else
                            <ol class="admin-steps">
                                <li>Open <a href="https://developers.facebook.com/apps/" target="_blank" rel="noopener">Meta for Developers</a> and create an app (type: Consumer).</li>
                                <li>Add the <strong>Facebook Login</strong> product.</li>
                                <li>Under Facebook Login → Settings, paste the Callback URL as a Valid OAuth Redirect URI.</li>
                                <li>Copy App ID and App Secret here, tick Enable, Save.</li>
                            </ol>
                        @endif
                    </details>
                </article>
            @endforeach
            <p class="field-hint">Site URL for providers (if asked): <code>{{ $appUrl }}</code></p>
        </section>

        <section class="admin-panel admin-panel--spaced">
            <h2>3. Social media links (footer)</h2>
            <p class="admin-lead">These appear as icons/links on the website. Leave blank to hide.</p>
            <div class="form-grid">
                @foreach ($socialLinks as $id => $link)
                    <div class="form-row form-row--full">
                        <label class="field-label" for="link-{{ $id }}">{{ $link['label'] }}</label>
                        <input
                            id="link-{{ $id }}"
                            class="field-input"
                            type="url"
                            name="links[{{ $id }}]"
                            value="{{ old('links.'.$id, $link['url']) }}"
                            placeholder="{{ $link['placeholder'] }}"
                        >
                    </div>
                @endforeach
            </div>
        </section>

        <section class="admin-panel admin-panel--spaced">
            <h2>4. Online payment gateway keys</h2>
            <p class="admin-lead">
                Everyday JazzCash / Easypaisa <em>account numbers</em> (for customers to transfer to) stay under
                <a href="{{ route('admin.payments.edit') }}">Payment options</a>.
                Use this section only for merchant API keys when online checkout goes live.
            </p>

            @foreach ($gateways as $group => $gateway)
                <article class="admin-slide-card">
                    <h3>{{ $gateway['label'] }}</h3>
                    <p class="field-hint">{{ $gateway['hint'] }}</p>
                    <div class="form-grid">
                        <div class="form-row form-row--full">
                            <label class="check-inline">
                                <input type="checkbox" name="gateway_flags[{{ $group }}][online_enabled]" value="1" @checked(old('gateway_flags.'.$group.'.online_enabled', $gateway['online_enabled']))>
                                <span>Turn on online checkout for this method</span>
                            </label>
                        </div>
                        <div class="form-row form-row--full">
                            <label class="check-inline">
                                <input type="checkbox" name="gateway_flags[{{ $group }}][sandbox]" value="1" @checked(old('gateway_flags.'.$group.'.sandbox', $gateway['sandbox']))>
                                <span>Use sandbox / test mode (recommended until go-live)</span>
                            </label>
                        </div>
                        @foreach ($gateway['fields'] as $field => $meta)
                            <div class="form-row">
                                <label class="field-label" for="gw-{{ $group }}-{{ $field }}">{{ $meta['label'] }}</label>
                                <input
                                    id="gw-{{ $group }}-{{ $field }}"
                                    class="field-input"
                                    type="{{ $meta['secret'] ? 'password' : 'text' }}"
                                    name="gateway[{{ $group }}][{{ $field }}]"
                                    value="{{ $meta['secret'] ? '' : old('gateway.'.$group.'.'.$field, $meta['value']) }}"
                                    autocomplete="off"
                                    @if ($meta['secret']) placeholder="••••••••" @endif
                                >
                                @if ($meta['secret'])
                                    <p class="field-hint">{{ $meta['set'] ? 'Saved — leave blank to keep.' : 'Not set yet.' }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </article>
            @endforeach
        </section>

        <div class="admin-form__actions admin-form__actions--spaced">
            <button class="btn btn--primary" type="submit">Save email, logins &amp; links</button>
        </div>
    </form>
@endsection
