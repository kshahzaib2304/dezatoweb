@php
    $menuCategories = config('dezato.menu.categories', []);
    $currentRoute = $currentRoute ?? null;
    $hasFulfillment = $hasFulfillment ?? false;
@endphp

<div class="nav-overlay" id="nav-overlay" data-nav-close></div>

<aside
    class="nav-drawer"
    id="nav-drawer"
    role="dialog"
    aria-modal="true"
    aria-label="Site menu"
    aria-hidden="true"
>
    <div class="nav-drawer__top">
        <div class="nav-drawer__brand">
            <img src="{{ asset('images/brand/logo-icon.jpg') }}" width="40" height="40" alt="">
            <span>DEZATO</span>
        </div>
        <button class="icon-btn" type="button" data-nav-close aria-label="Close menu">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path d="M6 6l12 12M18 6 6 18"/>
            </svg>
        </button>
    </div>

    <div class="nav-drawer__scroll">
        @foreach ($navLinks as $link)
            <a class="nav-link" href="{{ route($link['route']) }}" @if ($currentRoute === $link['route']) aria-current="page" @endif>
                {{ $link['label'] }}
            </a>
        @endforeach

        <div>
            <button class="nav-acc__btn" type="button" data-accordion aria-expanded="false">
                Menu
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path d="m6 9 6 6 6-6"/>
                </svg>
            </button>
            <div class="nav-acc__panel">
                @foreach ($menuCategories as $category)
                    @continue($category['id'] === 'all')
                    <a href="{{ route('menu', ['category' => $category['id']]) }}">{{ $category['label'] }}</a>
                @endforeach
            </div>
        </div>

        <a class="nav-link" href="{{ route('locations') }}">Locations</a>
        <a class="nav-link" href="{{ route('account.profile') }}">My account</a>
        <a class="nav-link" href="{{ route('login') }}">Sign in</a>
        <button class="nav-link" type="button" data-fulfillment-open data-fulfillment-method="shipping" data-nav-close>Pakistan Courier</button>
    </div>

    <div class="nav-drawer__cta">
        @if ($hasFulfillment)
            <a class="btn btn--primary btn--block" href="{{ route('menu') }}">Order now</a>
        @else
            <button class="btn btn--primary btn--block" type="button" data-fulfillment-open data-nav-close>Order now</button>
        @endif
    </div>
</aside>
