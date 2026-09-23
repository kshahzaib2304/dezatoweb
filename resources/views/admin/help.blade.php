@extends('layouts.admin')

@section('content')
    <div class="admin-help">
        <section class="admin-panel">
            <h2>How to sign in</h2>
            <ol class="admin-steps">
                <li>Go to the website and open <strong>Sign in</strong>.</li>
                <li>Use your admin email: <code>{{ $loginEmail }}</code></li>
                <li>After login you will land on this Admin dashboard.</li>
            </ol>
            <p class="admin-muted">Change the admin password after first login (ask your developer if you need help).</p>
        </section>

        <section class="admin-panel">
            <h2>Everyday tasks</h2>
            <ul class="admin-tips">
                <li><strong>Products</strong> — Add, edit, hide items and upload photos. Use “Needs real photo” to replace homepage placeholders (1200 × 1200).</li>
                <li><strong>Payment options</strong> — Tick COD / bank / JazzCash / Easypaisa and fill account numbers customers should pay to.</li>
                <li><strong>Delivery times</strong> — Edit the pickup/delivery time windows shown at checkout.</li>
                <li><strong>Orders</strong> — Update status, mark transfer payments as paid, and print an invoice / packing slip.</li>
                <li><strong>Messages</strong> — Read contact-form requests; reply by email, call, or WhatsApp.</li>
                <li><strong>Store locations</strong> — Addresses, hours, Maps links, delivery fees, and shop photos.</li>
                <li><strong>Homepage &amp; text</strong> — Announcement, hero slides, category shortcuts, and occasion tiles.</li>
                <li><strong>Website pages</strong> — About intro, milestones, Services packages, Privacy / Terms / FAQ.</li>
                <li><strong>Contact &amp; store</strong> — Phone, WhatsApp, emails, live website URL, courier fee/ETA, and change admin password.</li>
                <li><strong>Email, logins &amp; links</strong> — SMTP, Google/Facebook login, footer links, online payment API keys.</li>
                <li><strong>Custom cake prices / Promo codes</strong> — All editable without a developer.</li>
                <li><strong>Reports</strong> — Snapshot plus CSV export (7 / 30 / 90 days).</li>
            </ul>
        </section>

        <section class="admin-panel">
            <h2>Social sign-in (Google &amp; Facebook)</h2>
            <ol class="admin-steps">
                <li>Open <strong>Email, logins &amp; links</strong> in the Admin menu.</li>
                <li>Copy the Callback URL shown for Google / Facebook into that provider’s free developer console.</li>
                <li>Paste Client/App ID + Secret, tick Enable, Save.</li>
                <li>Customers will see “Continue with Google / Facebook” on Sign in and Create account.</li>
            </ol>
            <p class="admin-muted">Instagram is not used for login (Meta does not offer a reliable free website login with email). Put your Instagram profile under Footer links so customers can follow you.</p>
        </section>

        <section class="admin-panel">
            <h2>Picture sizes (important)</h2>
            <div class="admin-media-cards">
                @foreach ($media as $guide)
                    <article class="admin-media-card">
                        <h3>{{ $guide['label'] }}</h3>
                        <dl>
                            <div><dt>Recommended size</dt><dd><strong>{{ $guide['size'] }}</strong></dd></div>
                            <div><dt>Shape</dt><dd>{{ $guide['ratio'] }}</dd></div>
                            <div><dt>File type</dt><dd>{{ $guide['formats'] }}</dd></div>
                            <div><dt>Max file size</dt><dd>{{ $guide['max'] }}</dd></div>
                        </dl>
                        <p>{{ $guide['tip'] }}</p>
                    </article>
                @endforeach
            </div>
            <div class="admin-media-guide">
                <strong>How to resize a photo</strong>
                <ol class="admin-steps">
                    <li>Open the photo in Photos, Canva, or your phone crop tool.</li>
                    <li>Products: crop <strong>square</strong> ≈ <strong>1200 × 1200</strong>.</li>
                    <li>Homepage hero: wide crop ≈ <strong>1600 × 1000</strong>.</li>
                    <li>Export as JPG/WebP under the size limit, then upload in Admin.</li>
                </ol>
            </div>
        </section>

        <section class="admin-panel">
            <h2>Order status meanings</h2>
            <dl class="admin-detail">
                <div><dt>Placed</dt><dd>Customer just ordered - confirm and start prep.</dd></div>
                <div><dt>Baking</dt><dd>Cake is being prepared.</dd></div>
                <div><dt>Quality check</dt><dd>Finishing / packing.</dd></div>
                <div><dt>Out for delivery</dt><dd>On the way (or ready for pickup).</dd></div>
                <div><dt>Delivered</dt><dd>Complete.</dd></div>
                <div><dt>Cancelled</dt><dd>Will not be fulfilled.</dd></div>
            </dl>
        </section>

        <section class="admin-panel">
            <h2>Need a developer?</h2>
            <p>Ask your web partner only for: connecting real email sending (SMTP), online payment gateways (JazzCash/card), or unique product photography. Day-to-day store management is fully covered in this Admin.</p>
        </section>
    </div>
@endsection
