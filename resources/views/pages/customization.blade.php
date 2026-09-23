@extends('layouts.app')

@section('content')
    <x-page-hero
        eyebrow="Made for you"
        title="Cake Customization"
        text="Tell us the flavour, size, and message - we’ll bake a celebration cake for your Karachi occasion."
        image="images/home/promo-anniversary.jpg"
    >
        <a class="btn btn--primary" href="{{ route('builder.show') }}">Open cake builder</a>
    </x-page-hero>

    <section class="section-block">
        <div class="container">
            <div class="section-head">
                <h2>How customization works</h2>
                <p>Use the full builder for uploads and live pricing - or leave a quick request below.</p>
            </div>
            <div class="package-grid">
                @foreach ($options as $option)
                    <article class="package-card" data-reveal>
                        <div class="package-card__body">
                            <h3>{{ $option['title'] }}</h3>
                            <p>{{ $option['text'] }}</p>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    @if (! empty($guidelines))
        <section class="section-block section-block--tint">
            <div class="container">
                <div class="section-head">
                    <h2>Bakery guidelines</h2>
                    <p>Size and décor rules from Dezato - also shown in the cake builder.</p>
                </div>
                <ul class="builder-guidelines builder-guidelines--page">
                    @foreach ($guidelines as $line)
                        <li>{{ $line }}</li>
                    @endforeach
                </ul>
                <p style="margin-top:1.25rem">
                    <a class="btn btn--primary" href="{{ route('builder.show') }}">Open cake builder</a>
                </p>
            </div>
        </section>
    @endif

    <section class="section-block section-block--tint" id="customize">
        <div class="container inquiry">
            <div class="inquiry__copy" data-reveal>
                <h2>Request a custom cake</h2>
                <p>Share your details and we’ll reply with timing and a quote.</p>
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
                <input type="hidden" name="type" value="customization">
                <div class="form-row">
                    <label for="custom-name">Full name</label>
                    <input id="custom-name" name="name" type="text" value="{{ old('name') }}" autocomplete="name" required>
                </div>
                <div class="form-row">
                    <label for="custom-email">Email</label>
                    <input id="custom-email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required>
                </div>
                <div class="form-row">
                    <label for="custom-phone">Phone</label>
                    <input id="custom-phone" name="phone" type="tel" value="{{ old('phone') }}" autocomplete="tel" required>
                </div>
                <div class="form-row">
                    <label for="custom-flavour">Preferred flavour</label>
                    <input id="custom-flavour" name="flavour" type="text" value="{{ old('flavour') }}" placeholder="e.g. Lotus, Red Velvet, Three Milk" list="flavour-suggestions">
                    <datalist id="flavour-suggestions">
                        <option value="Chocolate Heaven"></option>
                        <option value="Lotus"></option>
                        <option value="Ferrero"></option>
                        <option value="Red Velvet"></option>
                        <option value="Three Milk"></option>
                        <option value="Nutella"></option>
                        <option value="New York Cheesecake"></option>
                    </datalist>
                </div>
                <div class="form-row">
                    <label for="custom-size">Size</label>
                    <select id="custom-size" name="size">
                        <option value="">Select size</option>
                        <option value="2.5 lbs" @selected(old('size') === '2.5 lbs')>2.5 lbs</option>
                        <option value="3 lbs" @selected(old('size') === '3 lbs')>3 lbs</option>
                        <option value="4 lbs" @selected(old('size') === '4 lbs')>4 lbs</option>
                        <option value="Cupcake box" @selected(old('size') === 'Cupcake box')>Cupcake box</option>
                    </select>
                </div>
                <div class="form-row">
                    <label for="custom-date">Needed by</label>
                    <input id="custom-date" name="event_date" type="date" value="{{ old('event_date') }}">
                </div>
                <div class="form-row form-row--full">
                    <label for="custom-message">Message on cake</label>
                    <input id="custom-message" name="message_on_cake" type="text" value="{{ old('message_on_cake') }}" maxlength="120" placeholder="Happy Birthday Sara">
                </div>
                <div class="form-row form-row--full">
                    <label for="custom-notes">Extra notes</label>
                    <textarea id="custom-notes" name="notes" rows="4" placeholder="Colour theme, allergies, delivery area…">{{ old('notes') }}</textarea>
                </div>
                <div class="form-row form-row--full">
                    <button class="btn btn--primary" type="submit">Submit request</button>
                </div>
            </form>
        </div>
    </section>
@endsection
