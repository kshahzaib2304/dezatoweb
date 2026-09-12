@extends('layouts.account')

@section('account')
    @php
        $keys = collect($steps)->pluck('key')->values();
        $activeIndex = $keys->search($order['status']);
        if ($activeIndex === false) {
            $activeIndex = 0;
        }
    @endphp

    <header class="account-head">
        <h1>Track {{ $order['number'] }}</h1>
        <p>{{ $order['status_label'] }} · {{ pkr($order['total']) }}</p>
    </header>

    <ol class="track-steps">
        @foreach ($steps as $i => $step)
            <li class="{{ $i < $activeIndex ? 'is-done' : ($i === $activeIndex ? 'is-current' : '') }}">
                <span class="track-steps__dot"></span>
                <span>{{ $step['label'] }}</span>
            </li>
        @endforeach
    </ol>

    <div class="track-map" aria-label="Delivery map placeholder">
        <p>Live GPS tracking UI</p>
        <p>Map provider will connect when delivery partners are integrated.</p>
    </div>

    <div class="track-actions">
        <a class="btn btn--outline" href="{{ route('account.orders') }}">Back to orders</a>
        @if (! in_array($order['status'], ['out_for_delivery', 'delivered'], true))
            <form method="post" action="{{ route('account.stub') }}">
                @csrf
                <button class="btn btn--ghost" type="submit">Cancel order</button>
            </form>
        @endif
    </div>
@endsection
