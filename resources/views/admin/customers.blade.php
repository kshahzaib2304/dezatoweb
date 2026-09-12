@extends('layouts.admin')

@section('content')
    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Customer</th>
                    <th>Email</th>
                    <th>Orders</th>
                    <th>Segment</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($customers as $customer)
                    <tr>
                        <td>{{ $customer['name'] }}</td>
                        <td>{{ $customer['email'] }}</td>
                        <td>{{ $customer['orders'] }}</td>
                        <td>{{ $customer['segment'] }}</td>
                        <td>
                            <form method="post" action="{{ route('admin.stub') }}" class="admin-inline">
                                @csrf
                                <button class="text-btn" type="submit">View</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
