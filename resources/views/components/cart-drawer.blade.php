@php
    $lines = $cartLines ?? collect();
    $count = (int) ($cartCount ?? 0);
    $subtotal = (float) ($cartSubtotal ?? 0);
    $openCart = (bool) session('open_cart', false);
@endphp

<div
    class="cart-drawer-root"
    id="cart-drawer-root"
    data-cart-drawer-root
    data-open-on-load="{{ $openCart ? '1' : '0' }}"
    data-status-message="{{ $openCart ? session('status') : '' }}"
    hidden
>
    <div class="cart-drawer__backdrop" data-cart-close tabindex="-1" aria-hidden="true"></div>

    <aside
        id="cart-drawer"
        class="cart-drawer"
        data-cart-drawer
        role="dialog"
        aria-modal="true"
        aria-labelledby="cart-drawer-title"
        tabindex="-1"
    >
        <header class="cart-drawer__head">
            <div>
                <p class="cart-drawer__eyebrow">Dezato</p>
                <h2 id="cart-drawer-title">Your cart</h2>
            </div>
            <button class="icon-btn cart-drawer__close" type="button" data-cart-close aria-label="Close cart">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path d="M6 6l12 12M18 6 6 18"/>
                </svg>
            </button>
        </header>

        <div class="cart-drawer__body" data-cart-drawer-body>
            @include('components.cart-drawer-body', [
                'lines' => $lines,
                'count' => $count,
                'subtotal' => $subtotal,
            ])
        </div>
    </aside>
</div>
