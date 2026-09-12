@extends('layouts.account')

@section('account')
    <header class="account-head">
        <h1>Order history</h1>
        <p>Track, reorder, or review past celebrations.</p>
    </header>

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
                        <form method="post" action="{{ route('account.stub') }}">@csrf<button type="submit">Reorder</button></form>
                        @if ($order['status'] === 'delivered')
                            <form method="post" action="{{ route('account.stub') }}">@csrf<button type="submit">Review</button></form>
                        @endif
                    </div>
                </div>
            </li>
        @endforeach
    </ul>
@endsection
