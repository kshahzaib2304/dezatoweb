@extends('layouts.app')

@section('content')
@php
    $categories = $homeCategories ?? [];
    $occasions = $homeOccasions ?? [];
    $favorites = $favorites ?? [];
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
                    <p class="home-hero__brand">Dezato</p>
                    <h1>{{ $slide['headline'] }}</h1>
                    <p class="home-hero__lede">{{ $slide['lede'] }}</p>
                    <div class="home-hero__actions">
                        @if ($hasFulfillment ?? false)
                            <a class="btn btn--primary" href="{{ route('menu') }}">Shop the menu</a>
                        @else
                            <button class="btn btn--primary" type="button" data-fulfillment-open>Start an order</button>
                        @endif
                        <a class="btn btn--ghost-light" href="{{ route('locations') }}">Visit us</a>
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

<div class="home-marquee" aria-hidden="true">
    <div class="home-marquee__track">
        @foreach (['Baked in Karachi', 'Since 2018', 'Cakes from ₨ 1,500', 'Pickup & delivery', 'Custom inscriptions'] as $item)
            <span>{{ $item }}</span>
            <span class="home-marquee__dot" aria-hidden="true">·</span>
        @endforeach
        @foreach (['Baked in Karachi', 'Since 2018', 'Cakes from ₨ 1,500', 'Pickup & delivery', 'Custom inscriptions'] as $item)
            <span>{{ $item }}</span>
            <span class="home-marquee__dot" aria-hidden="true">·</span>
        @endforeach
    </div>
</div>

<section class="home-section" data-reveal>
    <div class="container">
        <div class="home-section__head">
            <h2>Favourites</h2>
            <a class="home-link" href="{{ route('menu') }}">Shop all</a>
        </div>
        <ul class="home-products">
            @foreach ($favorites as $product)
                <li>
                    <a class="home-product" href="{{ route('products.show', $product['id']) }}">
                        <span class="home-product__media">
                            <img src="{{ asset($product['image']) }}" alt="{{ $product['name'] }}" width="480" height="480" loading="lazy">
                        </span>
                        <span class="home-product__meta">
                            <span class="home-product__name">{{ $product['name'] }}</span>
                            <span class="home-product__price">{{ pkr($product['price']) }}</span>
                        </span>
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
</section>

<section class="home-section home-section--soft" data-reveal>
    <div class="container">
        <div class="home-section__head home-section__head--stack">
            <h2>Shop by category</h2>
            <p>Cakes, cupcakes, cheesecakes, eclairs, brownies, sundaes, tarts &amp; mini pies.</p>
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
            <h2>How you’ll get it</h2>
            <p>Pickup in DHA or Gizri, delivery across Karachi, or courier across Pakistan.</p>
        </div>
        <div class="home-ways">
            <button class="home-way" type="button" data-fulfillment-open data-fulfillment-method="pickup">
                <img src="{{ asset('images/home/delivery-pickup.png') }}" alt="" width="400" height="400" loading="lazy">
                <h3>Store pickup</h3>
                <p>Order ahead and collect fresh from our counters.</p>
            </button>
            <button class="home-way" type="button" data-fulfillment-open data-fulfillment-method="delivery">
                <img src="{{ asset('images/home/delivery-catering.png') }}" alt="" width="400" height="400" loading="lazy">
                <h3>Karachi delivery</h3>
                <p>Same-day delivery where we serve your neighbourhood.</p>
            </button>
            <button class="home-way" type="button" data-fulfillment-open data-fulfillment-method="shipping">
                <img src="{{ asset('images/home/delivery-ship.png') }}" alt="" width="400" height="400" loading="lazy">
                <h3>Pakistan courier</h3>
                <p>Packed carefully for delivery across the country.</p>
            </button>
        </div>
    </div>
</section>

<section class="home-section home-section--plum" data-reveal>
    <div class="container">
        <div class="home-section__head home-section__head--light">
            <h2>For every occasion</h2>
            <p>Birthdays, office treats, custom cakes, and gifts.</p>
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
            <p class="home-kicker">About Dezato</p>
            <h2>Karachi-baked. Celebration-ready.</h2>
            <p>Since 2018 we’ve baked cakes and desserts for Karachi - from Lotus and Ferrero classics to custom birthday finishes.</p>
            <a class="btn btn--outline" href="{{ route('about') }}">About us</a>
        </div>
        <div class="home-story__media">
            <img src="{{ asset('images/home/promo-workshop.jpg') }}" alt="Dezato cakes prepared for an order" width="800" height="1000" loading="lazy">
        </div>
    </div>
</section>

<section class="home-cater" data-reveal>
    <div class="container home-cater__inner">
        <div>
            <h2>Services &amp; catering</h2>
            <p>Office boxes, dessert tables, and corporate gifting - built around your guest list.</p>
        </div>
        <a class="btn btn--primary" href="{{ route('services') }}">Our services</a>
    </div>
</section>

<section class="home-news" id="newsletter" data-reveal>
    <div class="container home-news__inner">
        <h2>Stay in the know</h2>
        <p>Seasonal flavours and bakery news - no spam.</p>
        @if (session('status'))
            <p class="flash" role="status">{{ session('status') }}</p>
        @endif
        <form class="home-news__form" action="{{ route('inquiries.store') }}" method="post">
            @csrf
            <input type="hidden" name="type" value="newsletter">
            <label class="sr-only" for="news-email">Email</label>
            <input id="news-email" type="email" name="email" value="{{ old('email') }}" placeholder="Email address" autocomplete="email" required>
            <button class="btn btn--primary" type="submit">Subscribe</button>
        </form>
    </div>
</section>
@endsection

@push('scripts')
<script src="{{ asset('js/features.js') }}" defer></script>
@endpush
