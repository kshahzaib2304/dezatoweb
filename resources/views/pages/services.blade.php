@extends('layouts.app')

@section('content')
    <x-page-hero
        eyebrow="Our Services"
        title="Catering, gifting &amp; events"
        text="{{ $intro }}"
        image="images/home/promo-catering.jpg"
    >
        <a class="btn btn--primary" href="#inquiry">Get a quote</a>
    </x-page-hero>

    <section class="section-block" aria-label="Service packages">
        <div class="container">
            <div class="section-head">
                <h2>Popular packages</h2>
                <p>Start with a set menu or tell us your headcount - we’ll shape a sweet selection around your event.</p>
            </div>

            <div class="package-grid">
                @foreach ($packages as $package)
                    <article class="package-card" data-reveal>
                        <div class="package-card__media">
                            <img
                                src="{{ asset($package['image']) }}"
                                alt="{{ $package['title'] }}"
                                width="900"
                                height="675"
                                loading="lazy"
                            >
                        </div>
                        <div class="package-card__body">
                            <p class="package-card__price">{{ $package['price'] }}</p>
                            <h3>{{ $package['title'] }}</h3>
                            <p class="package-card__serves">{{ $package['serves'] }}</p>
                            <p>{{ $package['blurb'] }}</p>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="section-block section-block--tint" id="inquiry">
        <div class="container inquiry">
            <div class="inquiry__copy" data-reveal>
                <h2>Tell us about your event</h2>
                <p>Share a few details and our team will follow up with availability and a custom quote in PKR.</p>
            </div>

            @if (session('status'))
                <p class="flash" role="status">{{ session('status') }}</p>
            @endif

            @if ($errors->any())
                <div class="form-errors" role="alert">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form class="inquiry-form" data-reveal action="{{ route('inquiries.store') }}" method="post">
                @csrf
                <input type="hidden" name="type" value="services">
                <div class="form-row">
                    <label for="inquiry-name">Full name</label>
                    <input id="inquiry-name" name="name" type="text" value="{{ old('name') }}" autocomplete="name" required>
                </div>
                <div class="form-row">
                    <label for="inquiry-email">Email</label>
                    <input id="inquiry-email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required>
                </div>
                <div class="form-row">
                    <label for="inquiry-phone">Phone</label>
                    <input id="inquiry-phone" name="phone" type="tel" value="{{ old('phone') }}" autocomplete="tel">
                </div>
                <div class="form-row">
                    <label for="inquiry-date">Event date</label>
                    <input id="inquiry-date" name="event_date" type="date" value="{{ old('event_date') }}">
                </div>
                <div class="form-row">
                    <label for="inquiry-guests">Guest count</label>
                    <input id="inquiry-guests" name="guests" type="number" min="1" value="{{ old('guests') }}" inputmode="numeric">
                </div>
                <div class="form-row form-row--full">
                    <label for="inquiry-notes">What are you celebrating?</label>
                    <textarea id="inquiry-notes" name="notes" rows="4" placeholder="Office lunch, birthday, mehndi, client gifts…">{{ old('notes') }}</textarea>
                </div>
                <div class="form-row form-row--full">
                    <button class="btn btn--primary" type="submit">Request a quote</button>
                </div>
            </form>
        </div>
    </section>
@endsection
