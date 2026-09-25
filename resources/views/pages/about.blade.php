@extends('layouts.app')

@section('content')
@php
    $copy = $storefrontCopy ?? \App\Support\StorefrontCopy::all();
    $chrome = \App\Support\HomeChrome::all();
@endphp

    <x-page-hero page="about" />

    <section class="section-block">
        <div class="container story-intro" data-reveal>
            <img
                class="story-intro__image"
                src="{{ asset($chrome['about_intro_image']) }}"
                alt="{{ $brandShortName ?? 'Dezato' }} cake"
                width="320"
                height="320"
                loading="lazy"
            >
            <div>
                <h2>{{ $copy['about_story_title'] }}</h2>
                <p>{{ $intro }}</p>
            </div>
        </div>
    </section>

    <section class="section-block section-block--tint" aria-label="Timeline">
        <div class="container">
            <div class="section-head">
                <h2>{{ $copy['about_journey_title'] }}</h2>
                <p>{{ $copy['about_journey_text'] }}</p>
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
            <h2>{{ $copy['about_cta_title'] }}</h2>
            <p>{{ $copy['about_cta_text'] }}</p>
            <div class="cta-band__actions">
                <a class="btn btn--primary" href="{{ route('menu') }}">{{ $copy['about_cta_primary'] }}</a>
                <a class="btn btn--outline" href="{{ route('customization') }}">{{ $copy['about_cta_secondary'] }}</a>
            </div>
        </div>
    </section>
@endsection
