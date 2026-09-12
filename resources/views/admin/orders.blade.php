@extends('layouts.admin')

@section('content')
    <div class="admin-toolbar">
        <select class="field-input" aria-label="Filter status">
            <option>All statuses</option>
            <option>Placed</option>
            <option>Baking</option>
            <option>Out for delivery</option>
            <option>Delivered</option>
        </select>
        <form method="post" action="{{ route('admin.stub') }}">@csrf<button class="btn btn--outline" type="submit">Export CSV</button></form>
    </div>

    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Order</th>
                    <th>Placed</th>
                    <th>Method</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($orders as $order)
                    <tr>
                        <td>{{ $order['number'] }}</td>
                        <td>{{ $order['placed_at'] }}</td>
                        <td>{{ $order['method'] }}</td>
                        <td>{{ pkr($order['total']) }}</td>
                        <td>{{ $order['status_label'] }}</td>
                        <td>
                            <form method="post" action="{{ route('admin.stub') }}" class="admin-inline">
                                @csrf
                                <button class="text-btn" type="submit">Update</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
