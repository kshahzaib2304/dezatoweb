@php
    $currentRoute = $currentRoute ?? null;
    $hasFulfillment = $hasFulfillment ?? false;
@endphp

<nav class="mobile-appbar" aria-label="Mobile quick actions">
    <a href="{{ route('home') }}" @class(['is-active' => $currentRoute === 'home'])>
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
            <path d="M4 10.5 12 4l8 6.5V20a1 1 0 0 1-1 1h-5v-6H10v6H5a1 1 0 0 1-1-1v-9.5z"/>
        </svg>
        <span>Home</span>
    </a>
    <a href="{{ route('menu') }}" @class(['is-active' => $currentRoute === 'menu' || $currentRoute === 'products.show'])>
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
            <path d="M4 7h16M4 12h16M4 17h10"/>
        </svg>
        <span>Menu</span>
    </a>
    @if ($hasFulfillment)
        <button
            class="mobile-appbar__order"
            type="button"
            data-cart-open
            aria-controls="cart-drawer"
            aria-expanded="false"
            @class(['is-active' => in_array($currentRoute, ['order', 'cart.show', 'checkout.show'], true)])
        >
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                <path d="M6 6h15l-1.5 9h-12z"/>
                <path d="M6 6 5 3H2"/>
            </svg>
            <span>Cart</span>
            @if (($cartCount ?? 0) > 0)
                <span class="mobile-appbar__badge" data-cart-badge>{{ ($cartCount ?? 0) > 99 ? '99+' : $cartCount }}</span>
            @else
                <span class="mobile-appbar__badge" data-cart-badge hidden></span>
            @endif
        </button>
    @else
        <button class="mobile-appbar__order" type="button" data-fulfillment-open>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                <path d="M6 6h15l-1.5 9h-12z"/>
                <path d="M6 6 5 3H2"/>
            </svg>
            <span>Order</span>
        </button>
    @endif
    <a href="{{ route('locations') }}" @class(['is-active' => $currentRoute === 'locations'])>
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
            <path d="M12 21s7-5.2 7-11a7 7 0 1 0-14 0c0 5.8 7 11 7 11z"/>
            <circle cx="12" cy="10" r="2.5"/>
        </svg>
        <span>Stores</span>
    </a>
</nav>
