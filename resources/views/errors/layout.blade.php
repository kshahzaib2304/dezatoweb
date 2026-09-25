<!DOCTYPE html>
<html lang="en">
<head>
    @php
        $brandName = \App\Support\SiteBrand::name();
        $brandLogoMark = \App\Support\SiteBrand::logoMark();
        $brandLogoIcon = \App\Support\SiteBrand::logoIcon();
        $faviconType = str_ends_with(strtolower($brandLogoMark), '.svg') ? 'image/svg+xml' : 'image/png';
    @endphp
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#FAF6F2">
    <meta name="robots" content="noindex">
    <title>{{ $title }} | {{ $brandName }}</title>
    <link rel="icon" type="{{ $faviconType }}" href="{{ asset($brandLogoMark) }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:opsz,wght@9..40,400;9..40,600;9..40,700&family=Fraunces:opsz,wght@9..144,600;9..144,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/dezato.css') }}">
    <style>
        .error-page {
            min-height: 100dvh;
            display: grid;
            place-items: center;
            padding: 2rem 1.25rem calc(2rem + env(safe-area-inset-bottom, 0px));
            background:
                radial-gradient(ellipse 80% 50% at 20% 0%, rgba(110, 69, 111, 0.12), transparent 55%),
                radial-gradient(ellipse 70% 45% at 90% 100%, rgba(232, 201, 154, 0.35), transparent 50%),
                #faf6f2;
        }
        .error-page__card {
            width: min(100%, 34rem);
            text-align: center;
        }
        .error-page__brand {
            display: inline-flex;
            align-items: center;
            gap: 0.65rem;
            margin: 0 0 1.75rem;
            color: var(--plum-700, #3a2438);
            text-decoration: none;
            font-weight: 600;
            letter-spacing: 0.02em;
        }
        .error-page__brand img {
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 0.5rem;
        }
        .error-page__code {
            margin: 0;
            font-family: var(--font-brand, "Fraunces", serif);
            font-size: clamp(3.5rem, 12vw, 5.5rem);
            line-height: 1;
            color: var(--plum-600, #52314f);
        }
        .error-page__title {
            margin: 0.75rem 0 0;
            font-family: var(--font-brand, "Fraunces", serif);
            font-size: clamp(1.55rem, 4vw, 2rem);
            color: var(--ink, #241820);
        }
        .error-page__text {
            margin: 0.85rem auto 0;
            max-width: 34ch;
            color: var(--muted, #6b5a66);
            font-size: 1.02rem;
            line-height: 1.55;
        }
        .error-page__actions {
            margin-top: 1.75rem;
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            justify-content: center;
        }
    </style>
</head>
<body>
    <main class="error-page">
        <div class="error-page__card">
            <a class="error-page__brand" href="{{ url('/') }}">
                <img src="{{ asset($brandLogoIcon) }}" width="40" height="40" alt="">
                <span>{{ $brandName }}</span>
            </a>
            <p class="error-page__code">{{ $code }}</p>
            <h1 class="error-page__title">{{ $heading }}</h1>
            <p class="error-page__text">{{ $message }}</p>
            <div class="error-page__actions">
                @yield('actions')
            </div>
        </div>
    </main>
</body>
</html>
