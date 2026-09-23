@extends('layouts.app')

@section('content')
    <section class="section-block">
        <div class="container" style="max-width:28rem;margin-inline:auto;text-align:center">
            <h1>Redirecting to payment…</h1>
            <p>Please wait while we open the secure payment page for order <strong>{{ $order->number }}</strong>.</p>
            <form id="dezato-pay-form" method="post" action="{{ $action }}">
                @foreach ($fields as $name => $value)
                    <input type="hidden" name="{{ $name }}" value="{{ $value }}">
                @endforeach
                <noscript>
                    <button class="btn btn--primary" type="submit">Continue to payment</button>
                </noscript>
            </form>
        </div>
    </section>
    <script>
        document.getElementById('dezato-pay-form')?.submit();
    </script>
@endsection
