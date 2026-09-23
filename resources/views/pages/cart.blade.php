@extends('layouts.app')

@section('content')
    <x-page-hero
        eyebrow="Your order"
        title="Cart"
        text="Review your treats, then we’ll confirm pickup or delivery details at checkout."
        image="images/home/delivery-ship.png"
        :compact="true"
    />

    <section class="section-block">
        <div class="container cart-layout">
            @if (session('status'))
                <p class="flash" role="status">{{ session('status') }}</p>
            @endif

            @if ($lines->isEmpty())
                <div class="empty-state cart-empty" data-reveal>
                    <h2>Your cart is empty</h2>
                    <p>Browse the menu and add something sweet.</p>
                    <a class="btn btn--primary" href="{{ route('menu') }}">Browse menu</a>
                </div>
            @else
                <div class="cart-lines" data-reveal>
                    @foreach ($lines as $line)
                        <article class="cart-line">
                            @if ($line['is_custom'])
                                <div class="cart-line__media">
                                    <img
                                        src="{{ asset($line['product']['image']) }}"
                                        alt="{{ $line['product']['name'] }}"
                                        width="160"
                                        height="160"
                                        loading="lazy"
                                    >
                                </div>
                            @else
                                <a class="cart-line__media" href="{{ route('products.show', $line['product_id']) }}">
                                    <img
                                        src="{{ asset($line['product']['image']) }}"
                                        alt="{{ $line['product']['name'] }}"
                                        width="160"
                                        height="160"
                                        loading="lazy"
                                    >
                                </a>
                            @endif
                            <div class="cart-line__body">
                                <div class="cart-line__top">
                                    <h2>
                                        @if ($line['is_custom'])
                                            {{ $line['product']['name'] }}
                                        @else
                                            <a href="{{ route('products.show', $line['product_id']) }}">{{ $line['product']['name'] }}</a>
                                        @endif
                                    </h2>
                                    <p class="cart-line__price">{{ pkr($line['line_total']) }}</p>
                                </div>
                                <p>{{ pkr($line['product']['price']) }} each</p>
                                @if ($line['is_custom'] && ($line['product']['description'] ?? '') !== '')
                                    <p class="field-hint">{{ $line['product']['description'] }}</p>
                                @endif

                                <div class="cart-line__actions">
                                    <form method="post" action="{{ route('cart.items.update', $line['product_id']) }}">
                                        @csrf
                                        @method('PATCH')
                                        <label class="sr-only" for="qty-{{ $line['product_id'] }}">Quantity</label>
                                        <input
                                            id="qty-{{ $line['product_id'] }}"
                                            class="field-input qty-input"
                                            type="number"
                                            name="quantity"
                                            value="{{ $line['quantity'] }}"
                                            min="0"
                                            max="99"
                                            onchange="this.form.submit()"
                                        >
                                    </form>
                                    <form method="post" action="{{ route('cart.items.destroy', $line['product_id']) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button class="text-btn" type="submit">Remove</button>
                                    </form>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                <aside class="cart-summary" data-reveal>
                    <h2>Order summary</h2>
                    @if ($fulfillmentSummary)
                        <p class="cart-summary__fulfillment">
                            {{ $fulfillmentSummary }}
                            <a href="{{ route('order.start', ['change' => 1]) }}">Change</a>
                        </p>
                    @endif
                    <dl class="cart-totals">
                        <div>
                            <dt>Subtotal</dt>
                            <dd>{{ pkr($subtotal) }}</dd>
                        </div>
                        @if ($feeLabel)
                            <div>
                                <dt>{{ $feeLabel }}</dt>
                                <dd>{{ pkr($deliveryFee) }}</dd>
                            </div>
                        @endif
                        <div>
                            <dt>Tax</dt>
                            <dd>{{ pkr(0) }}</dd>
                        </div>
                        <div class="cart-totals__total">
                            <dt>Total</dt>
                            <dd>{{ pkr($total) }}</dd>
                        </div>
                    </dl>
                    <a class="btn btn--primary btn--block" href="{{ route('checkout.show') }}">
                        Checkout
                    </a>
                    <a class="btn btn--outline btn--block" href="{{ route('menu') }}">Keep shopping</a>
                </aside>
            @endif
        </div>
    </section>

    @php
        $upsells = \App\Support\Catalog::products()
            ->filter(fn (array $p): bool => ($p['badge'] ?? null) !== null)
            ->take(3)
            ->values();
    @endphp
    @if ($upsells->isNotEmpty())
        <section class="section-block section-block--tint">
            <div class="container">
                <div class="section-head">
                    <h2>You might also like</h2>
                    <p>Add a little extra to your order.</p>
                </div>
                <div class="upsell-row">
                    @foreach ($upsells as $product)
                        <x-product-card :product="$product" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection
