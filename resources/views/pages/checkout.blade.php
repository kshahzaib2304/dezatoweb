@extends('layouts.app')

@section('content')
    <section class="checkout-page">
        <div class="container checkout-layout">
            <header class="checkout-page__head">
                <h1>Checkout</h1>
                <p>Confirm your details and place your order. We bake and deliver across Karachi.</p>
            </header>

            @if ($errors->any())
                <div class="form-errors checkout-page__alert" role="alert">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form class="checkout-form" method="post" action="{{ route('checkout.store') }}">
                @csrf

                <div class="checkout-form__section">
                    <h2>Contact</h2>
                    <div class="form-grid">
                        <div class="form-row form-row--full">
                            <label class="field-label" for="customer_name">Full name</label>
                            <input id="customer_name" class="field-input" type="text" name="customer_name" value="{{ old('customer_name', auth()->user()->name ?? '') }}" autocomplete="name" required>
                        </div>
                        <div class="form-row">
                            <label class="field-label" for="phone">Phone (WhatsApp)</label>
                            <input id="phone" class="field-input" type="tel" name="phone" value="{{ old('phone', auth()->user()->phone ?? '') }}" autocomplete="tel" inputmode="tel" required>
                            <p class="field-hint">We’ll confirm your order on this number.</p>
                        </div>
                        <div class="form-row">
                            <label class="field-label" for="email">Email</label>
                            <input id="email" class="field-input" type="email" name="email" value="{{ old('email', auth()->user()->email ?? '') }}" autocomplete="email" required>
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
                    @if ($scheduleNote)
                        <p class="field-hint">{{ $scheduleNote }}</p>
                    @endif
                    <div class="form-grid">
                        <div class="form-row">
                            <label class="field-label" for="delivery_date">Date</label>
                            <input id="delivery_date" class="field-input" type="date" name="delivery_date" value="{{ old('delivery_date') }}" min="{{ $earliestDate }}">
                        </div>
                        <div class="form-row">
                            <label class="field-label" for="delivery_slot">Time slot</label>
                            <select id="delivery_slot" class="field-input" name="delivery_slot">
                                <option value="">Select a window</option>
                                @foreach ($timeSlots as $slot)
                                    <option value="{{ $slot }}" @selected(old('delivery_slot') === $slot)>{{ $slot }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-row form-row--full">
                            <label class="check-inline">
                                <input type="checkbox" name="express" value="1" @checked(old('express'))>
                                <span>Express / same-day (we’ll confirm availability)</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="checkout-form__section">
                    <h2>Promo code</h2>
                    <div class="form-row">
                        <label class="field-label" for="promo">Have a code?</label>
                        <input id="promo" class="field-input field-input--code" type="text" name="promo" value="{{ old('promo') }}" placeholder="e.g. DEZATO10" autocomplete="off">
                        @if ($promoMessage)
                            <p class="field-hint field-hint--ok">{{ $promoMessage }}</p>
                        @endif
                    </div>
                </div>

                <div class="checkout-form__section" data-checkout-payment>
                    <h2>Payment</h2>
                    <p class="field-hint">Cash on delivery is available for Karachi orders.</p>
                    @forelse ($paymentMethods as $i => $method)
                        <label class="pay-option">
                            <input
                                type="radio"
                                name="payment_method"
                                value="{{ $method['id'] }}"
                                data-pay-type="{{ $method['type'] }}"
                                @checked(old('payment_method', 'cod') === $method['id'] || ($i === 0 && ! old('payment_method')))
                            >
                            <span>
                                <strong>{{ $method['label'] }}</strong>
                                <small>{{ $method['hint'] }}</small>
                            </span>
                        </label>
                        @if ($method['type'] === 'transfer' && is_array($method['instructions']))
                            <div
                                class="pay-instructions"
                                data-pay-panel="{{ $method['id'] }}"
                                hidden
                            >
                                <p><strong>Send payment to:</strong></p>
                                <ul>
                                    @foreach ($method['instructions'] as $label => $value)
                                        @if ($value !== '')
                                            <li><span>{{ str_replace('_', ' ', ucfirst($label)) }}:</span> {{ $value }}</li>
                                        @endif
                                    @endforeach
                                </ul>
                                <p class="field-hint">Use order number as reference after placing the order. We’ll confirm once payment is received.</p>
                            </div>
                        @endif
                    @empty
                        <p class="field-hint">Payment options are being set up. Please contact the bakery.</p>
                    @endforelse
                    <label class="agree-row">
                        <input type="checkbox" name="agree" value="1" @checked(old('agree')) required>
                        <span>I confirm my order details are correct.</span>
                    </label>
                </div>

                <button class="btn btn--primary btn--block" type="submit">Place order · {{ pkr($total) }}</button>
            </form>

            <aside class="cart-summary">
                <h2>Order summary</h2>
                <ul class="checkout-lines">
                    @foreach ($lines as $line)
                        <li>
                            <span>
                                {{ $line['quantity'] }} × {{ $line['product']['name'] }}
                                @if ($line['is_custom'] && ($line['product']['description'] ?? '') !== '')
                                    <small class="checkout-lines__note">{{ $line['product']['description'] }}</small>
                                @endif
                            </span>
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
                    @if ($discount > 0)
                        <div>
                            <dt>Promo discount</dt>
                            <dd>−{{ pkr($discount) }}</dd>
                        </div>
                    @endif
                    <div class="cart-totals__total">
                        <dt>Total</dt>
                        <dd>{{ pkr($total) }}</dd>
                    </div>
                </dl>
                <p class="checkout-trust">Prices in PKR · Karachi pickup &amp; delivery · Confirm on WhatsApp</p>
            </aside>
        </div>
    </section>
@endsection

@push('scripts')
<script src="{{ asset('js/features.js') }}" defer></script>
@endpush
