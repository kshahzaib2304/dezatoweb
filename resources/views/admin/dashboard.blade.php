@extends('layouts.admin')

@section('content')
    <div class="admin-stats">
        @foreach ($stats as $stat)
            <article>
                <p>{{ $stat['label'] }}</p>
                <strong>{{ $stat['value'] }}</strong>
            </article>
        @endforeach
    </div>

    <section class="admin-panel">
        <div class="admin-panel__head">
            <h2>Recent orders</h2>
            <a href="{{ route('admin.orders') }}">View all</a>
        </div>
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Order</th>
                        <th>Customer</th>
                        <th>Total</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($recentOrders as $order)
                        <tr>
                            <td>{{ $order['number'] }}</td>
                            <td>{{ $order['customer'] }}</td>
                            <td>{{ pkr($order['total']) }}</td>
                            <td>{{ $order['status'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
@endsection
