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
            <p class="admin-muted">Change login email or password anytime under <a href="{{ route('admin.settings.edit') }}">Contact &amp; store → Admin login</a>.</p>
        </section>

        <section class="admin-panel">
            <h2>Everyday tasks</h2>
            <ul class="admin-tips">
                <li><strong>Products</strong> - Add, edit, hide items and upload photos. Use “Needs real photo” to replace homepage placeholders (1200 × 1200).</li>
                <li><strong>Categories</strong> - Menu groups shown in the mobile nav drawer. Add/rename here; they appear on the website automatically.</li>
                <li><strong>Payment options</strong> - Tick COD / bank / JazzCash / Easypaisa and fill account numbers customers should pay to.</li>
                <li><strong>Delivery times</strong> - Edit the pickup/delivery time windows shown at checkout.</li>
                <li><strong>Orders</strong> - Update status, mark transfer payments as paid, and print an invoice / packing slip.</li>
                <li><strong>Messages</strong> - Read contact-form requests; reply by email, call, or WhatsApp.</li>
                <li><strong>Store locations</strong> - Addresses, hours, Maps links, delivery fees. Use <em>Add location</em> for a new shop.</li>
                <li><strong>Homepage &amp; text</strong> - Announcement, hero slides, category shortcuts, and occasion tiles. Use <em>Add tile</em> for new shortcuts.</li>
                <li><strong>Website pages</strong> - About intro, timeline, Services packages, Cake Customization cards, Order choice cards (Pickup / Courier / Catering), Privacy / Terms / FAQ. Use the <em>Add</em> buttons for new rows.</li>
                <li><strong>Storefront chrome</strong> - Inner page heroes, homepage section titles / “How you’ll get it” tiles, product notes, cart &amp; checkout copy, Karachi delivery areas.</li>
                <li><strong>Contact &amp; store</strong> - Admin login email/password, brand name, logos, top menu, bakery phone/WhatsApp, public emails, courier fee.</li>
                <li><strong>Email, logins &amp; links</strong> - SMTP, Google/Facebook login, footer links, online payment API keys.</li>
                <li><strong>Custom cake prices</strong> - Sizes, flavours, bakery rules (fondant / heart / letter cakes), and décor add-ons (flowers, toppers, macarons). Use Add for new rows; Restore bakery defaults if needed.</li>
                <li><strong>Promo codes</strong> - Discount codes customers enter at checkout.</li>
                <li><strong>Reports</strong> - Snapshot plus CSV export (7 / 30 / 90 days).</li>
            </ul>
        </section>

        <section class="admin-panel">
            <h2>Where to change website wording</h2>
            <dl class="admin-detail">
                <div><dt>Admin login email or password</dt><dd>Contact &amp; store → Admin login</dd></div>
                <div><dt>Bakery name, logos, favicon</dt><dd>Contact &amp; store → Brand name / Logos</dd></div>
                <div><dt>Top links (Home / About / Services…)</dt><dd>Contact &amp; store → Top website menu</dd></div>
                <div><dt>Menu / About / Cart page banners</dt><dd>Storefront chrome → Page heroes</dd></div>
                <div><dt>Homepage section titles &amp; pickup tiles</dt><dd>Storefront chrome → Homepage sections</dd></div>
                <div><dt>Product “Good to know” notes</dt><dd>Storefront chrome → Product notes, cart &amp; checkout</dd></div>
                <div><dt>Karachi delivery neighbourhoods</dt><dd>Storefront chrome → Karachi delivery areas</dd></div>
                <div><dt>Cake Customization blurbs</dt><dd>Website pages → Cake Customization cards</dd></div>
                <div><dt>Fondant / heart / flower prices</dt><dd>Custom cake prices → Bakery rules &amp; Add-ons</dd></div>
                <div><dt>Order page Pickup / Courier / Catering</dt><dd>Website pages → Order page choice cards</dd></div>
                <div><dt>Mobile menu cake categories</dt><dd>Categories (left menu) - they sync from your product categories</dd></div>
            </dl>
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
            <h2>Theme-only pieces</h2>
            <p class="admin-muted">Layout, colours, and typography live in the website theme CSS. Almost all customer-facing copy and images are editable in Admin (Homepage &amp; text, Website pages, Storefront chrome, Contact &amp; store).</p>
            <p class="admin-muted">Picture sizes for every upload type are listed below and also on each edit screen.</p>
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
            <p>Ask your web partner only for: connecting real email sending (SMTP), online payment gateways (JazzCash/card), or unique product photography. Day-to-day store management - including brand name, menus, locations, tiles, packages, and page cards - is fully covered in this Admin.</p>
        </section>
    </div>
@endsection
