@extends('layouts.admin')

@section('content')
    <div class="admin-toolbar">
        <form method="post" action="{{ route('admin.stub') }}">@csrf<button class="btn btn--primary" type="submit">Create coupon</button></form>
    </div>
    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Type</th>
                    <th>Uses</th>
                    <th>Expires</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($coupons as $coupon)
                    <tr>
                        <td>{{ $coupon['code'] }}</td>
                        <td>{{ $coupon['type'] }}</td>
                        <td>{{ $coupon['uses'] }}</td>
                        <td>{{ $coupon['expires'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <section class="admin-panel">
        <h2>Marketing automations</h2>
        <ul class="admin-list">
            <li>Flash sale scheduler</li>
            <li>Loyalty points configuration</li>
            <li>Referral program</li>
            <li>Abandoned cart recovery</li>
        </ul>
    </section>
@endsection
