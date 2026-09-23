@extends('layouts.admin')

@section('content')
    <p class="admin-back"><a href="{{ route('admin.orders.index') }}">← Back to all orders</a></p>

    <div class="admin-toolbar" style="margin-bottom:1rem">
        <a class="btn btn--outline" href="{{ route('admin.orders.invoice', $order) }}" target="_blank" rel="noopener">Print invoice / packing slip</a>
        @if (($order->payment_status ?? '') !== 'paid')
            <form method="post" action="{{ route('admin.orders.paid', $order) }}">
                @csrf
                @method('PATCH')
                <button class="btn btn--primary" type="submit">Mark as paid</button>
            </form>
        @else
            <span class="admin-badge admin-badge--ok">Payment received</span>
        @endif
    </div>

    <div class="admin-split">
        <section class="admin-panel">
            <h2>Update status</h2>
            <p class="admin-lead">
                Suggested flow:
                <strong>Placed → Baking → Quality check → Out for delivery → Delivered</strong>.
            </p>
            <form method="post" action="{{ route('admin.orders.status', $order) }}" class="admin-form">
                @csrf
                @method('PATCH')
                <div class="form-row">
                    <label class="field-label" for="status">Current status</label>
                    <select id="status" class="field-input" name="status" required>
                        @foreach ($statuses as $key => $label)
                            <option value="{{ $key }}" @selected($order->status === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <button class="btn btn--primary" type="submit">Update status</button>
            </form>

            <h2 class="admin-section-gap">Items</h2>
            <ul class="admin-list">
                @foreach ($order->items as $item)
                    <li>
                        <strong>{{ $item->product_name }}</strong>
                        × {{ $item->quantity }}
                        - {{ pkr($item->line_total) }}
                        @if ($item->isCustom())
                            <div class="admin-muted">{{ $item->optionsSummary() }}</div>
                            @if (! empty($item->options['notes']))
                                <div class="admin-muted">Baker notes: {{ $item->options['notes'] }}</div>
                            @endif
                            @if ($item->image)
                                <div style="margin-top:0.4rem">
                                    <img src="{{ asset('storage/'.$item->image) }}" alt="Reference" width="96" height="96" style="object-fit:cover;border-radius:6px">
                                </div>
                            @endif
                        @endif
                    </li>
                @endforeach
            </ul>
            <dl class="admin-totals">
                <div><dt>Subtotal</dt><dd>{{ pkr($order->subtotal) }}</dd></div>
                <div><dt>Fee</dt><dd>{{ pkr($order->fee) }}</dd></div>
                @if ((float) $order->discount > 0)
                    <div><dt>Promo ({{ $order->promo_code }})</dt><dd>−{{ pkr($order->discount) }}</dd></div>
                @endif
                <div><dt>Total</dt><dd><strong>{{ pkr($order->total) }}</strong></dd></div>
            </dl>
        </section>

        <section class="admin-panel">
            <h2>Customer &amp; delivery</h2>
            <dl class="admin-detail">
                <div><dt>Name</dt><dd>{{ $order->customer_name }}</dd></div>
                <div><dt>Phone</dt><dd><a href="tel:{{ $order->phone }}">{{ $order->phone }}</a></dd></div>
                <div><dt>Email</dt><dd><a href="mailto:{{ $order->email }}">{{ $order->email }}</a></dd></div>
                <div><dt>Method</dt><dd>{{ $order->methodLabel() }}@if($order->location_name) · {{ $order->location_name }}@endif</dd></div>
                @if ($order->address)
                    <div><dt>Address</dt><dd>{{ $order->address }}@if($order->city), {{ $order->city }}@endif</dd></div>
                @endif
                @if ($order->delivery_date)
                    <div><dt>Date / slot</dt><dd>{{ $order->delivery_date->format('d M Y') }}@if($order->delivery_slot) · {{ $order->delivery_slot }}@endif</dd></div>
                @endif
                <div>
                    <dt>Payment</dt>
                    <dd>
                        {{ \App\Support\PaymentMethods::label($order->payment_method) }}
                        · {{ ucfirst($order->payment_status ?? 'unpaid') }}
                    </dd>
                </div>
                @if ($paymentInstructions)
                    <div>
                        <dt>Pay to</dt>
                        <dd>
                            @foreach ($paymentInstructions as $label => $value)
                                @if ($value !== '')
                                    <div>{{ str_replace('_', ' ', ucfirst($label)) }}: {{ $value }}</div>
                                @endif
                            @endforeach
                        </dd>
                    </div>
                @endif
                @if ($order->notes)
                    <div><dt>Notes</dt><dd>{{ $order->notes }}</dd></div>
                @endif
                <div><dt>Placed</dt><dd>{{ optional($order->placed_at)->format('d M Y, h:i A') }}</dd></div>
            </dl>
        </section>
    </div>
@endsection
