@extends('layouts.admin')

@section('content')
    <p class="admin-back"><a href="{{ route('admin.orders.index') }}">← Back to all orders</a></p>

    <div class="admin-split">
        <section class="admin-panel">
            <h2>Update status</h2>
            <p class="admin-lead">
                Change this as the order moves along. Suggested flow:
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
                        — {{ pkr($item->line_total) }}
                    </li>
                @endforeach
            </ul>
            <dl class="admin-totals">
                <div><dt>Subtotal</dt><dd>{{ pkr($order->subtotal) }}</dd></div>
                <div><dt>Fee</dt><dd>{{ pkr($order->fee) }}</dd></div>
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
                    <div><dt>Delivery date</dt><dd>{{ $order->delivery_date->format('d M Y') }}@if($order->delivery_slot) · {{ $order->delivery_slot }}@endif</dd></div>
                @endif
                <div><dt>Payment</dt><dd>{{ str_replace('_', ' ', ucfirst($order->payment_method)) }}</dd></div>
                @if ($order->notes)
                    <div><dt>Notes</dt><dd>{{ $order->notes }}</dd></div>
                @endif
                <div><dt>Placed</dt><dd>{{ optional($order->placed_at)->format('d M Y, h:i A') }}</dd></div>
            </dl>
        </section>
    </div>
@endsection
