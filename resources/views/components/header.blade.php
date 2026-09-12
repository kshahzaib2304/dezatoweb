@php
    $currentRoute = $currentRoute ?? null;
    $cartCount = $cartCount ?? 0;
    $hasFulfillment = $hasFulfillment ?? false;
    $announcement = config('dezato.home.announcement');
@endphp

@if ($announcement)
    <div class="topbar" role="region" aria-label="Announcement">
        <p>{{ $announcement }}</p>
    </div>
@endif

<header class="site-header site-header--brand" role="banner">
    <div class="container header-bar">
        <button
            class="icon-btn menu-toggle"
            type="button"
            data-nav-open
            aria-controls="nav-drawer"
            aria-expanded="false"
            aria-label="Open menu"
        >
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path d="M4 7h16M4 12h16M4 17h16"/>
            </svg>
        </button>

        <a class="brand" href="{{ route('home') }}" aria-label="Dezato Cake House home">
            <img class="brand__mark" src="{{ asset('images/brand/logo-mark.svg') }}" width="40" height="40" alt="">
            <span class="brand__text">
                <span class="brand__name">Dezato</span>
                <span class="brand__tag">cake house</span>
            </span>
        </a>

        <nav class="desktop-nav" aria-label="Primary">
            @foreach ($navLinks as $link)
                <a href="{{ route($link['route']) }}" @if ($currentRoute === $link['route']) aria-current="page" @endif>
                    {{ $link['label'] }}
                </a>
            @endforeach
        </nav>

        <div class="header-tools">
            @if ($hasFulfillment)
                <a class="btn btn--primary header-cta" href="{{ route('menu') }}">Order</a>
            @else
                <button class="btn btn--primary header-cta" type="button" data-fulfillment-open>Order</button>
            @endif

            <a class="icon-btn cart-btn" href="{{ route('cart.show') }}" aria-label="Cart, {{ $cartCount }} items">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path d="M6 6h15l-1.5 9h-12z"/><path d="M6 6 5 3H2"/>
                    <circle cx="9" cy="20" r="1.5"/><circle cx="17" cy="20" r="1.5"/>
                </svg>
                @if ($cartCount > 0)
                    <span class="cart-btn__badge">{{ $cartCount > 99 ? '99+' : $cartCount }}</span>
                @endif
            </a>
        </div>
    </div>
</header>
