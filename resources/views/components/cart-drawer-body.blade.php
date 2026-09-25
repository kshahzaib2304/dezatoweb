@php
    $lines = collect($lines ?? []);
    $count = (int) ($count ?? 0);
    $subtotal = (float) ($subtotal ?? 0);
@endphp

@if ($lines->isEmpty())
    <div class="cart-drawer__empty">
        <p>{{ $storefrontCopy['cart_empty_title'] ?? 'Your cart is empty' }}</p>
        <a class="btn btn--primary" href="{{ route('menu') }}" data-cart-close>{{ $storefrontCopy['cart_empty_cta'] ?? 'Browse menu' }}</a>
    </div>
@else
    <p class="cart-drawer__status" data-cart-status role="status" hidden></p>

    <ul class="cart-drawer__lines">
        @foreach ($lines as $line)
            <li class="cart-drawer__line">
                <div class="cart-drawer__media">
                    <img
                        src="{{ asset($line['product']['image']) }}"
                        alt=""
                        width="88"
                        height="88"
                        loading="lazy"
                    >
                </div>
                <div class="cart-drawer__copy">
                    <div class="cart-drawer__top">
                        <h3>
                            @if ($line['is_custom'])
                                {{ $line['product']['name'] }}
                            @else
                                <a href="{{ route('products.show', $line['product_id']) }}">{{ $line['product']['name'] }}</a>
                            @endif
                        </h3>
                        <p class="cart-drawer__price">{{ pkr($line['line_total']) }}</p>
                    </div>

                    <p class="cart-drawer__unit">{{ pkr($line['product']['price']) }} each</p>

                    @if ($line['is_custom'])
                        <x-cart-line-extras :options="$line['options']" class="cart-extras--drawer" />
                    @elseif (! empty($line['product']['weight']))
                        <p class="cart-drawer__meta">{{ $line['product']['weight'] }}</p>
                    @endif

                    <div class="cart-drawer__actions">
                        <form
                            method="post"
                            action="{{ route('cart.items.update', $line['product_id']) }}"
                            data-cart-update
                        >
                            @csrf
                            @method('PATCH')
                            <label class="sr-only" for="drawer-qty-{{ $line['product_id'] }}">Quantity</label>
                            <x-qty-stepper
                                :id="'drawer-qty-'.$line['product_id']"
                                name="quantity"
                                :value="$line['quantity']"
                                :min="1"
                                :max="99"
                                data-auto-submit="1"
                            />
                        </form>
                        <form
                            method="post"
                            action="{{ route('cart.items.destroy', $line['product_id']) }}"
                            data-cart-remove
                        >
                            @csrf
                            @method('DELETE')
                            <button class="text-btn" type="submit">Remove</button>
                        </form>
                    </div>
                </div>
            </li>
        @endforeach
    </ul>

    <footer class="cart-drawer__foot">
        <div class="cart-drawer__subtotal">
            <span>Subtotal</span>
            <strong data-cart-subtotal>{{ pkr($subtotal) }}</strong>
        </div>
        @if (! empty($feeLabel) && isset($deliveryFee))
            <div class="cart-drawer__fee">
                <span>{{ $feeLabel }}</span>
                <strong>{{ pkr($deliveryFee) }}</strong>
            </div>
            @if (! empty($paymentHint))
                <p class="cart-drawer__hint">{{ $paymentHint }}</p>
            @endif
        @else
            <p class="cart-drawer__hint">Pickup is free. Delivery fees show after you choose an area.</p>
        @endif
        <a class="btn btn--primary btn--block" href="{{ route('checkout.show') }}">Checkout</a>
        <a class="btn btn--outline btn--block" href="{{ route('cart.show') }}">View full cart</a>
        <button class="text-btn cart-drawer__continue" type="button" data-cart-close>Continue shopping</button>
    </footer>
@endif

<span class="sr-only" data-cart-count-value>{{ $count }}</span>
