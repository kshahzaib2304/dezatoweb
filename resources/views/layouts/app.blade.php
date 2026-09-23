<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#FAF6F2">
    @php
        $brandName = $brandName ?? \App\Support\SiteBrand::name();
        $pageTitle = $title ?? $brandName;
        $pageDescription = $metaDescription ?? $brandName.' — cakes, cupcakes, eclairs, brownies, cheesecakes, tarts, mini pies and sundaes in Karachi. Order in PKR.';
        $canonical = $canonical ?? url()->current();
        $ogImage = asset($ogImage ?? 'images/brand/logo-icon.jpg');
    @endphp
    <title>{{ $pageTitle }}</title>
    <meta name="description" content="{{ $pageDescription }}">
    <link rel="canonical" href="{{ $canonical }}">

    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ $brandName }}">
    <meta property="og:locale" content="en_PK">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $pageDescription }}">
    <meta property="og:url" content="{{ $canonical }}">
    <meta property="og:image" content="{{ $ogImage }}">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $pageTitle }}">
    <meta name="twitter:description" content="{{ $pageDescription }}">
    <meta name="twitter:image" content="{{ $ogImage }}">

    <link rel="icon" type="image/svg+xml" href="{{ asset('images/brand/logo-mark.svg') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/brand/logo-icon.jpg') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/dezato.css') }}">
    <link rel="stylesheet" href="{{ asset('css/theme-home.css') }}">
    <link rel="stylesheet" href="{{ asset('css/features.css') }}">
    @stack('head')

    <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'Bakery',
            'name' => $brandName,
            'url' => url('/'),
            'image' => asset('images/brand/logo-icon.jpg'),
            'description' => $pageDescription,
            'address' => [
                '@type' => 'PostalAddress',
                'addressLocality' => 'Karachi',
                'addressRegion' => 'Sindh',
                'addressCountry' => 'PK',
            ],
            'areaServed' => 'Karachi',
            'currenciesAccepted' => 'PKR',
            'priceRange' => '₨₨',
            'servesCuisine' => 'Bakery',
        ], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE|JSON_HEX_TAG|JSON_HEX_AMP) !!}
    </script>
</head>
<body class="{{ ($currentRoute ?? null) === 'home' ? 'is-home' : '' }}">
    @include('components.bake-loader')

    <a class="sr-only" href="#main">Skip to content</a>

    @include('components.header')
    @include('components.fulfillment-bar')
    @include('components.nav-drawer')

    <main id="main">
        @yield('content')
    </main>

    @include('components.footer')
    @include('components.mobile-appbar')
    @include('components.fulfillment-modal')

    <script src="{{ asset('js/dezato.js') }}" defer></script>
    @stack('scripts')
</body>
</html>
