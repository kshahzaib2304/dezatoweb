<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Invoice {{ $order->number }}</title>
    <style>
        :root { color-scheme: light; }
        body { font-family: Georgia, "Times New Roman", serif; color: #2a1f28; margin: 0; padding: 1.5rem; background: #fff; }
        h1 { font-size: 1.5rem; margin: 0 0 0.25rem; }
        .muted { color: #6b5b66; font-size: 0.92rem; }
        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; margin: 1.25rem 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
        th, td { text-align: left; padding: 0.55rem 0.35rem; border-bottom: 1px solid #e6dce2; font-size: 0.95rem; vertical-align: top; }
        th { font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.04em; color: #6b5b66; }
        .totals { margin-top: 1rem; width: 280px; margin-left: auto; }
        .totals div { display: flex; justify-content: space-between; padding: 0.3rem 0; }
        .totals .grand { font-weight: 700; font-size: 1.1rem; border-top: 1px solid #2a1f28; margin-top: 0.35rem; padding-top: 0.5rem; }
        .actions { margin-bottom: 1.25rem; display: flex; gap: 0.75rem; }
        .actions a, .actions button { font-family: system-ui, sans-serif; font-size: 0.9rem; padding: 0.45rem 0.85rem; border: 1px solid #cbb7c4; background: #fff; border-radius: 6px; cursor: pointer; text-decoration: none; color: inherit; }
        .small { font-size: 0.85rem; color: #6b5b66; }
        @media print {
            .actions { display: none; }
            body { padding: 0; }
        }
    </style>
</head>
<body>
    <div class="actions">
        <button type="button" onclick="window.print()">Print</button>
        <a href="{{ route('admin.orders.show', $order) }}">Back to order</a>
    </div>

    <header>
        <h1>{{ $bakery['name'] }}</h1>
        <p class="muted">{{ $bakery['phone'] }} · {{ $bakery['email'] }}</p>
        <p><strong>Invoice / packing slip</strong> · {{ $order->number }}</p>
        <p class="muted">Placed {{ optional($order->placed_at)->format('d M Y, h:i A') }}</p>
    </header>

    <div class="grid">
        <div>
            <strong>Customer</strong>
            <p>{{ $order->customer_name }}<br>{{ $order->phone }}<br>{{ $order->email }}</p>
        </div>
        <div>
            <strong>Fulfillment</strong>
            <p>
                {{ $order->methodLabel() }}
                @if ($order->location_name)<br>{{ $order->location_name }}@endif
                @if ($order->address)<br>{{ $order->address }}@if($order->city), {{ $order->city }}@endif@endif
                @if ($order->delivery_date)<br>{{ $order->delivery_date->format('d M Y') }}@if($order->delivery_slot) · {{ $order->delivery_slot }}@endif@endif
            </p>
            <p class="small">Payment: {{ \App\Support\PaymentMethods::label($order->payment_method) }} ({{ ucfirst($order->payment_status ?? 'unpaid') }})</p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Item</th>
                <th>Qty</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($order->items as $item)
                <tr>
                    <td>
                        {{ $item->product_name }}
                        @if ($item->isCustom())
                            <div class="small">{{ $item->optionsSummary() }}</div>
                        @endif
                    </td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ pkr($item->line_total) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <div><span>Subtotal</span><span>{{ pkr($order->subtotal) }}</span></div>
        <div><span>Fee</span><span>{{ pkr($order->fee) }}</span></div>
        @if ((float) $order->discount > 0)
            <div><span>Discount</span><span>−{{ pkr($order->discount) }}</span></div>
        @endif
        <div class="grand"><span>Total</span><span>{{ pkr($order->total) }}</span></div>
    </div>

    @if ($order->notes)
        <p style="margin-top:1.5rem"><strong>Notes:</strong> {{ $order->notes }}</p>
    @endif
</body>
</html>
