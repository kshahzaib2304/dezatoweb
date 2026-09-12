@extends('layouts.admin')

@section('content')
    <div class="admin-split">
        <section class="admin-panel">
            <div class="admin-panel__head">
                <h2>Pages</h2>
                <form method="post" action="{{ route('admin.stub') }}">@csrf<button class="btn btn--outline" type="submit">New page</button></form>
            </div>
            <ul class="admin-list">
                @foreach ($pages as $page)
                    <li><strong>{{ $page['title'] }}</strong> · {{ $page['status'] }}</li>
                @endforeach
            </ul>
        </section>
        <section class="admin-panel">
            <div class="admin-panel__head">
                <h2>Banners</h2>
                <form method="post" action="{{ route('admin.stub') }}">@csrf<button class="btn btn--outline" type="submit">New banner</button></form>
            </div>
            <ul class="admin-list">
                @foreach ($banners as $banner)
                    <li><strong>{{ $banner['title'] }}</strong> · {{ $banner['status'] }}</li>
                @endforeach
            </ul>
        </section>
    </div>
    <section class="admin-panel">
        <h2>Also in CMS</h2>
        <ul class="admin-list">
            <li>Blog posts</li>
            <li>Testimonials moderation</li>
            <li>SEO meta tags</li>
            <li>Email / SMS templates</li>
            <li>Push &amp; newsletter campaigns</li>
        </ul>
    </section>
@endsection
