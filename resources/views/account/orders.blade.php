@extends('layouts.account')

@section('account')
    <header class="account-head">
        <h1>Order history</h1>
        <p>Track past and current orders.</p>
    </header>

    @if (count($orders) === 0)
        <p class="admin-empty">You have no orders yet. <a href="{{ route('menu') }}">Browse the menu</a>.</p>
    @else
        <ul class="order-list">
            @foreach ($orders as $order)
                <li class="order-row">
                    <div>
                        <p class="order-row__id">{{ $order['number'] }}</p>
                        <p class="order-row__meta">{{ $order['placed_at'] }} · {{ $order['method'] }}</p>
                        <p class="order-row__items">{{ implode(' · ', $order['items']) }}</p>
                    </div>
                    <div class="order-row__side">
                        <p class="order-row__status">{{ $order['status_label'] }}</p>
                        <p class="order-row__total">{{ pkr($order['total']) }}</p>
                        <div class="order-row__actions">
                            <a href="{{ route('account.track', $order['number']) }}">Track</a>
                        </div>
                    </div>
                </li>
            @endforeach
        </ul>
    @endif
@endsection
