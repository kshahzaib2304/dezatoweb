@extends('layouts.app')

@section('content')
    <x-page-hero page="order" />

    @if (! empty($fulfillmentSummary))
        <div class="container order-status">
            <p class="flash" role="status">
                Current selection: <strong>{{ $fulfillmentSummary }}</strong>
                · <a href="{{ route('order.start', ['change' => 1]) }}">Change pickup/delivery</a>
            </p>
        </div>
    @endif

    <section class="section-block" aria-label="Order options">
        <div class="container order-grid">
            @foreach ($options as $option)
                <article class="order-card" data-reveal>
                    <div class="order-card__media">
                        <img
                            src="{{ asset($option['image']) }}"
                            alt=""
                            width="900"
                            height="675"
                            loading="lazy"
                        >
                    </div>
                    <div class="order-card__body">
                        <h2>{{ $option['title'] }}</h2>
                        <p>{{ $option['text'] }}</p>
                        <a class="btn btn--primary" href="{{ route($option['route']) }}">{{ $option['cta'] }}</a>
                    </div>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section-block section-block--tint">
        <div class="container dual-cta" data-reveal>
            <div>
                <h2>Browse the menu</h2>
                <p>Explore cakes, cupcakes, eclairs, and more before you check out.</p>
            </div>
            <a class="btn btn--outline" href="{{ route('menu') }}">View full menu</a>
        </div>
    </section>
@endsection
