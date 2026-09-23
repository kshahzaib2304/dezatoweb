@extends('layouts.app')

@section('content')
    <section class="section-block">
        <div class="container confirmation" data-reveal>
            <p class="fulfillment-card__eyebrow">Order confirmed</p>
            <h1>Thank you, {{ $order->customer_name }}</h1>
            <p class="confirmation__lead">
                We’ve received order <strong>{{ $order->number }}</strong>.
                A confirmation will go to <strong>{{ $order->email }}</strong>.
            </p>

            <div class="confirmation__card">
                <div class="confirmation__meta">
                    <p><span>Method</span> {{ $order->methodLabel() }}</p>
                    @if ($order->location_name)
                        <p><span>Bakery</span> {{ $order->location_name }}</p>
                    @endif
                    @if ($order->method === 'delivery')
                        <p><span>Deliver to</span> {{ $order->address }}</p>
                    @endif
                    @if ($order->method === 'shipping')
                        <p>
                            <span>Ship to</span>
                            {{ $order->address }}, {{ $order->city }}, {{ $order->region }} {{ $order->postal_code }}
                        </p>
                    @endif
                    <p><span>Payment</span> {{ $paymentLabel ?? \App\Support\PaymentMethods::label($order->payment_method) }} ({{ ucfirst($order->payment_status ?? 'unpaid') }})</p>
                </div>

                @if (! empty($paymentInstructions))
                    <div class="pay-instructions pay-instructions--confirm">
                        <p><strong>How to pay</strong></p>
                        <ul>
                            @foreach ($paymentInstructions as $label => $value)
                                @if ($value !== '')
                                    <li><span>{{ str_replace('_', ' ', ucfirst($label)) }}:</span> {{ $value }}</li>
                                @endif
                            @endforeach
                        </ul>
                        <p class="field-hint">Use order number <strong>{{ $order->number }}</strong> as the payment reference.</p>
                    </div>
                @endif

                <ul class="checkout-lines">
                    @foreach ($order->items as $item)
                        <li>
                            <span>
                                {{ $item->quantity }} × {{ $item->product_name }}
                                @if ($item->isCustom())
                                    <small style="display:block;color:var(--muted)">{{ $item->optionsSummary() }}</small>
                                @endif
                            </span>
                            <span>{{ pkr($item->line_total) }}</span>
                        </li>
                    @endforeach
                </ul>

                <dl class="cart-totals">
                    <div>
                        <dt>Subtotal</dt>
                        <dd>{{ pkr($order->subtotal) }}</dd>
                    </div>
                    @if ((float) $order->fee > 0)
                        <div>
                            <dt>{{ $order->method === 'shipping' ? 'Courier' : 'Delivery' }}</dt>
                            <dd>{{ pkr($order->fee) }}</dd>
                        </div>
                    @endif
                    @if ((float) $order->discount > 0)
                        <div>
                            <dt>Promo ({{ $order->promo_code }})</dt>
                            <dd>−{{ pkr($order->discount) }}</dd>
                        </div>
                    @endif
                    <div class="cart-totals__total">
                        <dt>Total</dt>
                        <dd>{{ pkr($order->total) }}</dd>
                    </div>
                </dl>
            </div>

            <div class="confirmation__actions">
                <a class="btn btn--primary" href="{{ route('menu') }}">Order more</a>
                <a class="btn btn--outline" href="{{ route('home') }}">Back home</a>
            </div>
        </div>
    </section>
@endsection
