@extends('layouts.account')

@section('account')
    <header class="account-head">
        <h1>Track {{ $order['number'] }}</h1>
        <p>{{ $order['status_label'] }} · {{ pkr($order['total']) }}</p>
    </header>

    <ol class="track-steps">
        @foreach ($steps as $step)
            <li class="{{ $step['state'] === 'done' ? 'is-done' : ($step['state'] === 'current' ? 'is-current' : '') }}">
                <span class="track-steps__dot"></span>
                <span>{{ $step['label'] }}</span>
            </li>
        @endforeach
    </ol>

    <div class="track-actions">
        <a class="btn btn--outline" href="{{ route('account.orders') }}">Back to orders</a>
    </div>
@endsection
