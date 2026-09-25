@extends('layouts.app')

@section('content')
    <x-page-hero
        eyebrow="Dezato Cake House"
        title="About Us"
        text="Handcrafted cakes and desserts for Karachi - baked fresh since 2018."
        image="images/home/promo-anniversary.jpg"
    />

    <section class="section-block">
        <div class="container story-intro" data-reveal>
            <img
                class="story-intro__image"
                src="{{ asset('images/products/chocolate-heaven-cake.jpg') }}"
                alt="Dezato chocolate heaven cake"
                width="320"
                height="320"
                loading="lazy"
            >
            <div>
                <h2>Baked for Karachi celebrations</h2>
                <p>{{ $intro }}</p>
            </div>
        </div>
    </section>

    <section class="section-block section-block--tint" aria-label="Timeline">
        <div class="container">
            <div class="section-head">
                <h2>Our journey</h2>
                <p>A few moments that shaped Dezato Cake House.</p>
            </div>

            <ol class="timeline">
                @foreach ($milestones as $milestone)
                    <li class="timeline__item" data-reveal>
                        <p class="timeline__year">{{ $milestone['year'] }}</p>
                        <div>
                            <h3>{{ $milestone['title'] }}</h3>
                            <p>{{ $milestone['text'] }}</p>
                        </div>
                    </li>
                @endforeach
            </ol>
        </div>
    </section>

    <section class="cta-band" data-reveal>
        <div class="container cta-band__inner">
            <h2>Taste what’s baking</h2>
            <p>Order for pickup, Karachi delivery, or plan something sweet for your next gathering.</p>
            <div class="cta-band__actions">
                <a class="btn btn--primary" href="{{ route('menu') }}">Browse menu</a>
                <a class="btn btn--outline" href="{{ route('customization') }}">Custom cake</a>
            </div>
        </div>
    </section>
@endsection
