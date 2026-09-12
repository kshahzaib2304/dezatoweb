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
                    <h2>Payment</h2>
                    <label class="pay-option">
                        <input type="radio" name="payment_method" value="pay_later" checked>
                        <span>
                            <strong>{{ $paymentHint }}</strong>
                            <small>We confirm your order in PKR (₨) and collect payment with pickup, delivery, or courier.</small>
                        </span>
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
