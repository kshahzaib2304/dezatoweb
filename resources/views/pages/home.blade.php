@extends('layouts.app')

@section('content')
@php
    $categories = $homeCategories ?? [];
    $occasions = $homeOccasions ?? [];
    $favorites = $favorites ?? [];
    $chrome = \App\Support\HomeChrome::all();
@endphp

<section
    class="home-hero"
    data-hero-slider
    data-hero-interval="{{ (int) ($heroIntervalMs ?? 4500) }}"
    aria-roledescription="carousel"
    aria-label="Homepage highlights"
>
    <div class="home-hero__slides">
        @foreach ($heroSlides as $index => $slide)
            <div
                class="home-hero__slide{{ $index === 0 ? ' is-active' : '' }}"
                data-hero-slide
                @if ($index !== 0) aria-hidden="true" @endif
            >
                <div class="home-hero__media" aria-hidden="true">
                    <img
                        src="{{ asset($slide['image']) }}"
                        alt=""
                        width="1600"
                        height="1000"
                        @if ($index === 0) fetchpriority="high" @else loading="lazy" @endif
                    >
                </div>
                <div class="home-hero__veil"></div>
                <div class="container home-hero__content">
                    <p class="home-hero__brand">{{ $brandShortName ?? 'Dezato' }}</p>
                    <h1>{{ $slide['headline'] }}</h1>
                    <p class="home-hero__lede">{{ $slide['lede'] }}</p>
                    <div class="home-hero__actions">
                        @if ($hasFulfillment ?? false)
                            <a class="btn btn--primary" href="{{ route('menu') }}">{{ $chrome['hero_cta_primary'] }}</a>
                        @else
                            <button class="btn btn--primary" type="button" data-fulfillment-open>{{ $chrome['hero_cta_start'] }}</button>
                        @endif
                        <a class="btn btn--ghost-light" href="{{ route('locations') }}">{{ $chrome['hero_cta_secondary'] }}</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    @if (count($heroSlides) > 1)
        <div class="home-hero__controls" data-hero-controls>
            <button type="button" class="home-hero__nav" data-hero-prev aria-label="Previous slide">‹</button>
            <div class="home-hero__dots" role="tablist" aria-label="Choose slide">
                @foreach ($heroSlides as $index => $slide)
                    <button
                        type="button"
                        class="home-hero__dot{{ $index === 0 ? ' is-active' : '' }}"
                        data-hero-dot="{{ $index }}"
                        aria-label="Show slide {{ $index + 1 }}"
                        @if ($index === 0) aria-current="true" @endif
                    ></button>
                @endforeach
            </div>
            <button type="button" class="home-hero__nav" data-hero-next aria-label="Next slide">›</button>
        </div>
    @endif
</section>

<section class="home-section" data-reveal>
    <div class="container">
        <div class="home-section__head">
            <h2>{{ $chrome['favorites_title'] }}</h2>
            <a class="home-link" href="{{ route('menu') }}">{{ $chrome['favorites_link'] }}</a>
        </div>
        <div class="product-grid product-grid--home">
            @foreach ($favorites as $product)
                <x-product-card :product="$product" />
            @endforeach
        </div>
    </div>
</section>

<section class="home-section home-section--soft" data-reveal>
    <div class="container">
        <div class="home-section__head home-section__head--stack">
            <h2>{{ $chrome['categories_title'] }}</h2>
            <p>{{ $chrome['categories_text'] }}</p>
        </div>
        <div class="home-cats" data-rail>
            <div class="home-cats__viewport" data-rail-viewport>
                <ul class="home-cats__track">
                    @foreach ($categories as $item)
                        <li>
                            <a class="home-cat" href="{{ url($item['href']) }}">
                                <img src="{{ asset($item['image']) }}" alt="{{ $item['label'] }}" width="400" height="500" loading="lazy">
                                <span>{{ $item['label'] }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</section>

<section class="home-section" data-reveal>
    <div class="container">
        <div class="home-section__head home-section__head--stack">
            <h2>{{ $chrome['ways_title'] }}</h2>
            <p>{{ $chrome['ways_text'] }}</p>
        </div>
        <div class="home-ways">
            @foreach ($chrome['ways'] as $way)
                @if (($way['action'] ?? '') === 'builder')
                    <a class="home-way" href="{{ route('builder.show') }}">
                        <img src="{{ asset($way['image']) }}" alt="" width="400" height="400" loading="lazy">
                        <h3>{{ $way['title'] }}</h3>
                        <p>{{ $way['text'] }}</p>
                    </a>
                @else
                    <button
                        class="home-way"
                        type="button"
                        data-fulfillment-open
                        @if (! empty($way['action'])) data-fulfillment-method="{{ $way['action'] }}" @endif
                    >
                        <img src="{{ asset($way['image']) }}" alt="" width="400" height="400" loading="lazy">
                        <h3>{{ $way['title'] }}</h3>
                        <p>{{ $way['text'] }}</p>
                    </button>
                @endif
            @endforeach
        </div>
    </div>
</section>

<section class="home-section home-section--plum" data-reveal>
    <div class="container">
        <div class="home-section__head home-section__head--light">
            <h2>{{ $chrome['occasions_title'] }}</h2>
            <p>{{ $chrome['occasions_text'] }}</p>
        </div>
        <ul class="home-occasions">
            @foreach ($occasions as $item)
                <li>
                    <a href="{{ url($item['href']) }}">
                        <img src="{{ asset($item['image']) }}" alt="{{ $item['label'] }}" width="360" height="360" loading="lazy">
                        <span>{{ $item['label'] }}</span>
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
</section>

<section class="home-story" data-reveal>
    <div class="container home-story__grid">
        <div class="home-story__copy">
            <p class="home-kicker">{{ $chrome['story_kicker'] }}</p>
            <h2>{{ $chrome['story_title'] }}</h2>
            <p>{{ $chrome['story_text'] }}</p>
            <a class="btn btn--outline" href="{{ route('about') }}">{{ $chrome['story_cta'] }}</a>
        </div>
        <div class="home-story__media">
            <img src="{{ asset($chrome['story_image']) }}" alt="{{ $brandShortName ?? 'Dezato' }} cakes prepared for an order" width="800" height="1000" loading="lazy">
        </div>
    </div>
</section>

<section class="home-cater" data-reveal>
    <div class="container home-cater__inner">
        <div>
            <h2>{{ $chrome['cater_title'] }}</h2>
            <p>{{ $chrome['cater_text'] }}</p>
        </div>
        <a class="btn btn--primary" href="{{ route('services') }}">{{ $chrome['cater_cta'] }}</a>
    </div>
</section>

<section class="home-news" id="newsletter" data-reveal>
    <div class="container home-news__inner">
        <h2>{{ $chrome['news_title'] }}</h2>
        <p>{{ $chrome['news_text'] }}</p>
        @if (session('status'))
            <p class="flash" role="status">{{ session('status') }}</p>
        @endif
        <form class="home-news__form" action="{{ route('inquiries.store') }}" method="post">
            @csrf
            <input type="hidden" name="type" value="newsletter">
            <label class="sr-only" for="news-email">Email</label>
            <input id="news-email" type="email" name="email" value="{{ old('email') }}" placeholder="{{ $chrome['news_placeholder'] }}" autocomplete="email" required>
            <button class="btn btn--primary" type="submit">{{ $chrome['news_cta'] }}</button>
        </form>
    </div>
</section>
@endsection

@push('scripts')
<script src="{{ asset('js/features.js') }}" defer></script>
@endpush
