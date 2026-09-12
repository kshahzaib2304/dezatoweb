@extends('layouts.app')

@section('content')
    <x-page-hero
        eyebrow="Almost there"
        title="Checkout"
        text="Confirm your details and place your Dezato order."
        image="images/home/delivery-ship.png"
        :compact="true"
    />

    <section class="section-block">
        <div class="container checkout-layout">
            @if ($errors->any())
                <div class="form-errors" role="alert">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form class="checkout-form" method="post" action="{{ route('checkout.store') }}" data-reveal>
                @csrf

                <div class="checkout-form__section">
                    <h2>Contact</h2>
                    <div class="form-grid">
                        <div class="form-row form-row--full">
                            <label class="field-label" for="customer_name">Full name</label>
                            <input id="customer_name" class="field-input" type="text" name="customer_name" value="{{ old('customer_name') }}" autocomplete="name" required>
                        </div>
                        <div class="form-row">
                            <label class="field-label" for="email">Email</label>
                            <input id="email" class="field-input" type="email" name="email" value="{{ old('email') }}" autocomplete="email" required>
                        </div>
                        <div class="form-row">
                            <label class="field-label" for="phone">Phone</label>
                            <input id="phone" class="field-input" type="tel" name="phone" value="{{ old('phone') }}" autocomplete="tel" required>
                        </div>
                        <div class="form-row form-row--full">
                            <label class="field-label" for="notes">Order notes <span class="field-optional">(optional)</span></label>
                            <textarea id="notes" class="field-input field-textarea" name="notes" rows="3" placeholder="Allergies, inscription, gate code…">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="checkout-form__section">
                    <h2>Fulfillment</h2>
                    <p class="checkout-fulfillment">
                        {{ $fulfillmentSummary }}
                        <a href="{{ route('order.start', ['change' => 1]) }}">Change</a>
                    </p>
                    @if (($fulfillment['method'] ?? null) === 'shipping')
                        <p class="field-hint">
                            {{ $fulfillment['address'] ?? '' }}<br>
                            {{ $fulfillment['city'] ?? '' }}, {{ $fulfillment['region'] ?? '' }} {{ $fulfillment['postal_code'] ?? '' }}
                        </p>
                    @elseif (($fulfillment['method'] ?? null) === 'delivery')
                        <p class="field-hint">Deliver to: {{ $fulfillment['address'] ?? '' }} · From {{ $fulfillment['location_name'] ?? 'bakery' }}</p>
                    @else
                        <p class="field-hint">Pickup at {{ $fulfillment['location_name'] ?? 'your bakery' }}</p>
                    @endif
                </div>

                <div class="checkout-form__section">
                    <h2>Delivery timing</h2>
                    <div class="checkout-extras">
                        <div class="form-grid">
                            <div class="form-row">
                                <label class="field-label" for="delivery_date">Date</label>
                                <input id="delivery_date" class="field-input" type="date" name="delivery_date" value="{{ old('delivery_date') }}">
                            </div>
                            <div class="form-row">
                                <label class="field-label" for="delivery_slot">Time slot</label>
                                <select id="delivery_slot" class="field-input" name="delivery_slot">
                                    <option value="">Select a window</option>
                                    @foreach (config('dezato_ui.checkout.time_slots', []) as $slot)
                                        <option value="{{ $slot }}" @selected(old('delivery_slot') === $slot)>{{ $slot }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-row form-row--full">
                                <label class="check-inline">
                                    <input type="checkbox" name="express" value="1" @checked(old('express'))>
                                    <span>Express / same-day delivery (availability confirmed at checkout)</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="checkout-form__section">
                    <h2>Promo &amp; rewards</h2>
                    <div class="form-grid">
                        <div class="form-row">
                            <label class="field-label" for="promo">Promo code</label>
                            <input id="promo" class="field-input" type="text" name="promo" value="{{ old('promo') }}" placeholder="DEZATO10">
                        </div>
                        <div class="form-row">
                            <label class="field-label" for="gift_card">Gift card</label>
                            <input id="gift_card" class="field-input" type="text" name="gift_card" value="{{ old('gift_card') }}" placeholder="Optional">
                        </div>
                        <div class="form-row form-row--full">
                            <label class="field-label" for="points">Reward points</label>
                            <input id="points" class="field-input" type="number" name="points" min="0" value="{{ old('points', 0) }}" placeholder="0">
                        </div>
                    </div>
                </div>

                <div class="checkout-form__section">
                    <h2>Payment</h2>
                    @foreach (config('dezato_ui.checkout.payment_methods', []) as $i => $method)
                        <label class="pay-option">
                            <input type="radio" name="payment_method" value="{{ $method['id'] }}" @checked(old('payment_method', 'cod') === $method['id'] || ($i === 0 && ! old('payment_method')))>
                            <span>
                                <strong>{{ $method['label'] }}</strong>
                                <small>{{ $method['hint'] }}</small>
                            </span>
                        </label>
                    @endforeach
                    <label class="check-inline" style="margin-top:0.75rem">
                        <input type="checkbox" name="save_card" value="1">
                        <span>Save card for 1-click checkout (UI — connects with gateway later)</span>
                    </label>
                    <label class="agree-row">
                        <input type="checkbox" name="agree" value="1" @checked(old('agree')) required>
                        <span>I confirm my order details are correct.</span>
                    </label>
                </div>

                <button class="btn btn--primary btn--block" type="submit">Place order</button>
            </form>

            <aside class="cart-summary" data-reveal>
                <h2>Order summary</h2>
                <ul class="checkout-lines">
                    @foreach ($lines as $line)
                        <li>
                            <span>{{ $line['quantity'] }} × {{ $line['product']['name'] }}</span>
                            <span>{{ pkr($line['line_total']) }}</span>
                        </li>
                    @endforeach
                </ul>
                <dl class="cart-totals">
                    <div>
                        <dt>Subtotal</dt>
                        <dd>{{ pkr($subtotal) }}</dd>
                    </div>
                    @if ($feeLabel)
                        <div>
                            <dt>{{ $feeLabel }}</dt>
                            <dd>{{ pkr($fee) }}</dd>
                        </div>
                    @endif
                    <div class="cart-totals__total">
                        <dt>Total</dt>
                        <dd>{{ pkr($total) }}</dd>
                    </div>
                </dl>
                <a class="btn btn--outline btn--block" href="{{ route('cart.show') }}">Back to cart</a>
            </aside>
        </div>
    </section>
@endsection
