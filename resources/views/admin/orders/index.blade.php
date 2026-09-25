@extends('layouts.admin')

@section('content')
    <div class="admin-toolbar">
        <form method="get" action="{{ route('admin.orders.index') }}" class="admin-filter">
            <label class="field-label" for="status">Status</label>
            <select id="status" class="field-input" name="status" onchange="this.form.submit()">
                <option value="">All orders</option>
                @foreach ($statuses as $key => $label)
                    <option value="{{ $key }}" @selected($status === $key)>{{ $label }}</option>
                @endforeach
            </select>
        </form>
    </div>

    <section class="admin-panel">
        <p class="admin-lead">
            Open an order to see details. Update the status as you bake and deliver - the customer sees the same status on Track order.
        </p>

        @if ($orders->isEmpty())
            <p class="admin-empty">No orders match this filter.</p>
        @else
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Order #</th>
                            <th>When</th>
                            <th>Customer</th>
                            <th>Method</th>
                            <th>Status</th>
                            <th>Total</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($orders as $order)
                            <tr>
                                <td><strong>{{ $order->number }}</strong></td>
                                <td>{{ optional($order->placed_at)->format('d M Y, h:i A') }}</td>
                                <td>
                                    {{ $order->customer_name }}
                                    <div class="admin-muted">{{ $order->phone }}</div>
                                </td>
                                <td>{{ $order->methodLabel() }}</td>
                                <td><span class="admin-badge">{{ $order->statusLabel() }}</span></td>
                                <td>{{ pkr($order->total) }}</td>
                                <td><a href="{{ route('admin.orders.show', $order) }}">Manage</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="admin-pagination">{{ $orders->links() }}</div>
        @endif
    </section>
@endsection
