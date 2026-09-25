@extends('layouts.app')

@section('content')
    <x-page-hero page="locations" />

    <section class="section-block" aria-label="Bakery locations">
        <div class="container location-grid">
            @foreach ($locations as $location)
                <x-location-card :location="$location" />
            @endforeach
        </div>
    </section>
@endsection
