@extends('layouts.admin')

@section('content')
    <section class="admin-stats" aria-label="Today at a glance">
        @foreach ($stats as $stat)
            <article>
                <p>{{ $stat['label'] }}</p>
                <strong>{{ $stat['value'] }}</strong>
                <span class="admin-stat-hint">{{ $stat['hint'] }}</span>
            </article>
        @endforeach
    </section>

    <div class="admin-split admin-split--board">
        <section class="admin-panel">
            <div class="admin-panel__head">
                <h2>Recent orders</h2>
                <a class="btn btn--outline btn--sm" href="{{ route('admin.orders.index') }}">See all</a>
            </div>
            @if ($recentOrders->isEmpty())
                <p class="admin-empty">No orders yet.</p>
            @else
                <div class="admin-table-wrap">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Order</th>
                                <th>Customer</th>
                                <th>Status</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($recentOrders as $order)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.orders.show', $order) }}">{{ $order->number }}</a>
                                        <div class="admin-muted">{{ optional($order->placed_at)->format('d M, h:i A') }}</div>
                                    </td>
                                    <td>{{ $order->customer_name }}</td>
                                    <td><span class="admin-badge">{{ $order->statusLabel() }}</span></td>
                                    <td>{{ pkr($order->total) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>

        <section class="admin-panel">
            <h2>Shortcuts</h2>
            <ul class="admin-jumps">
                <li><a href="{{ route('admin.orders.index') }}">Orders</a></li>
                <li><a href="{{ route('admin.products.index') }}">Products</a></li>
                <li><a href="{{ route('admin.inquiries.index') }}">Messages</a></li>
                <li><a href="{{ route('admin.content.edit') }}">Homepage</a></li>
                <li><a href="{{ route('admin.help') }}">Photo sizes and setup</a></li>
            </ul>
        </section>
    </div>
@endsection
