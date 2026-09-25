@extends('layouts.app')

@section('content')
@php
    $copy = $storefrontCopy ?? \App\Support\StorefrontCopy::all();
@endphp

    <x-page-hero page="cart" />

    <section class="section-block">
        <div class="container">
            @if (session('status') && ! session('open_cart'))
                <p class="flash flash--cart" role="status">{{ session('status') }}</p>
            @endif

            <div class="cart-layout">
                @if ($lines->isEmpty())
                    <div class="empty-state cart-empty">
                        <h2>{{ $copy['cart_empty_title'] }}</h2>
                        <p>{{ $copy['cart_empty_text'] }}</p>
                        <a class="btn btn--primary" href="{{ route('menu') }}">{{ $copy['cart_empty_cta'] }}</a>
                    </div>
                @else
                    <div class="cart-lines">
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

                                    @if ($line['is_custom'])
                                        <x-cart-line-extras :options="$line['options']" />
                                        @php
                                            $extrasTotal = \App\Support\CartExtras::extrasTotal($line['options']);
                                        @endphp
                                        @if ($extrasTotal > 0)
                                            <p class="cart-line__extras-total">Extras {{ pkr($extrasTotal) }}</p>
                                        @endif
                                    @elseif (! empty($line['product']['weight']))
                                        <p>{{ $line['product']['weight'] }}</p>
                                    @endif

                                    <div class="cart-line__actions">
                                        <form method="post" action="{{ route('cart.items.update', $line['product_id']) }}">
                                            @csrf
                                            @method('PATCH')
                                            <label class="sr-only" for="qty-{{ $line['product_id'] }}">Quantity</label>
                                            <x-qty-stepper
                                                :id="'qty-'.$line['product_id']"
                                                name="quantity"
                                                :value="$line['quantity']"
                                                :min="0"
                                                :max="99"
                                                data-auto-submit="1"
                                            />
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

                    <aside class="cart-summary">
                        <h2>{{ $copy['cart_summary_title'] }}</h2>
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
                            {{ $copy['cart_checkout_cta'] }}
                        </a>
                        <a class="btn btn--outline btn--block" href="{{ route('menu') }}">{{ $copy['cart_continue_cta'] }}</a>
                    </aside>
                @endif
            </div>
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
                    <h2>{{ $copy['cart_upsell_title'] }}</h2>
                    <p>{{ $copy['cart_upsell_text'] }}</p>
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
