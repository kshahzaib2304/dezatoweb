<!DOCTYPE html>
<html lang="en">
<head>
    @php
        $brandName = $brandName ?? \App\Support\SiteBrand::name();
        $brandShortName = $brandShortName ?? \App\Support\SiteBrand::shortName();
        $brandLogoMark = $brandLogoMark ?? \App\Support\SiteBrand::logoMark();
        $faviconType = str_ends_with(strtolower($brandLogoMark), '.svg') ? 'image/svg+xml' : 'image/png';
    @endphp
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#FAF6F2">
    <title>{{ $title ?? $brandName }}</title>
    <meta name="description" content="{{ $metaDescription ?? $brandName }}">
    <meta name="robots" content="noindex, follow">
    <link rel="canonical" href="{{ url()->current() }}">
    <link rel="icon" type="{{ $faviconType }}" href="{{ asset($brandLogoMark) }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:opsz,wght@9..40,400;9..40,500;9..40,600;9..40,700&family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/dezato.css') }}">
    <link rel="stylesheet" href="{{ asset('css/theme-home.css') }}">
    <link rel="stylesheet" href="{{ asset('css/features.css') }}">
</head>
<body class="auth-body">
    <a class="sr-only" href="#main">Skip to content</a>
    <main id="main" class="auth-shell">
        <a class="auth-brand" href="{{ route('home') }}">
            <img src="{{ asset($brandLogoMark) }}" width="40" height="40" alt="">
            <span>{{ $brandShortName }}</span>
        </a>
        <div class="auth-card">
            @yield('content')
        </div>
        <p class="auth-foot">
            <a href="{{ route('home') }}">← Back to store</a>
            <span>·</span>
            <a href="{{ route('menu') }}">Continue as guest</a>
        </p>
    </main>
</body>
</html>
