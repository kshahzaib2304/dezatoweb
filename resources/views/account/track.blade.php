@extends('layouts.account')

@section('account')
    <header class="account-head">
        <h1>Track {{ $order['number'] }}</h1>
        <p>{{ $order['status_label'] }} · {{ pkr($order['total']) }}</p>
    </header>

    @if ($errors->any())
        <div class="flash flash--error" role="alert">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

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
        @if ($order['can_cancel'] ?? false)
            <form method="post" action="{{ route('account.cancel', $order['number']) }}" onsubmit="return confirm('Cancel this order?');">
                @csrf
                <button class="btn btn--ghost" type="submit">Cancel order</button>
            </form>
        @endif
    </div>
@endsection
