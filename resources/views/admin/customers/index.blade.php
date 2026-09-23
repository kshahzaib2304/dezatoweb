@extends('layouts.admin')

@section('content')
    <section class="admin-panel">
        <p class="admin-lead">
            Customers who created an account. Guest checkout orders still appear under Orders (by name/phone).
        </p>

        @if ($customers->isEmpty())
            <p class="admin-empty">No customer accounts yet.</p>
        @else
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Orders</th>
                            <th>Joined</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($customers as $customer)
                            <tr>
                                <td>{{ $customer->name }}</td>
                                <td>{{ $customer->email }}</td>
                                <td>{{ $customer->phone ?: '-' }}</td>
                                <td>{{ $customer->orders_count }}</td>
                                <td>{{ optional($customer->created_at)->format('d M Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="admin-pagination">{{ $customers->links() }}</div>
        @endif
    </section>
@endsection
