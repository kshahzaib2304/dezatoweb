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
                <li><strong>Add / edit products</strong> — Products → Add product. Fill name, category, price, and photo.</li>
                <li><strong>Hide a product</strong> — Edit it and uncheck “Show on website”.</li>
                <li><strong>Update an order</strong> — Orders → Manage → choose the new status → Update status.</li>
                <li><strong>Call the customer</strong> — Open the order; tap the phone number on mobile.</li>
            </ul>
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
                <strong>How to resize a photo (phone or computer)</strong>
                <ol class="admin-steps">
                    <li>Open the photo in any editor (Photos, Canva, Photoshop, or your phone crop tool).</li>
                    <li>For products, crop to a <strong>square</strong>, then resize to about <strong>1200 × 1200</strong>.</li>
                    <li>For banners, use a wide crop around <strong>1600 × 1000</strong>.</li>
                    <li>Export as JPG or WebP under the size limit, then upload in Admin.</li>
                </ol>
            </div>
        </section>

        <section class="admin-panel">
            <h2>Order status meanings</h2>
            <dl class="admin-detail">
                <div><dt>Placed</dt><dd>Customer just ordered — confirm and start prep.</dd></div>
                <div><dt>Baking</dt><dd>Cake is being prepared.</dd></div>
                <div><dt>Quality check</dt><dd>Finishing / packing.</dd></div>
                <div><dt>Out for delivery</dt><dd>On the way (or ready for pickup if pickup order).</dd></div>
                <div><dt>Delivered</dt><dd>Complete — customer received it.</dd></div>
                <div><dt>Cancelled</dt><dd>Order will not be fulfilled.</dd></div>
            </dl>
        </section>

        <section class="admin-panel">
            <h2>Need more help?</h2>
            <p>Ask your web partner to change homepage banners, promotions, or payment settings. Products, categories, and orders are fully manageable here without technical knowledge.</p>
        </section>
    </div>
@endsection
