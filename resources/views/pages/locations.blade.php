@extends('layouts.app')

@section('content')
    <x-page-hero
        eyebrow="Visit us"
        title="Hours &amp; Locations"
        text="Find Dezato Cake House in DHA Phase 6 and Gizri for pickup, delivery, and custom cakes."
        image="images/home/delivery-pickup.png"
    />

    <section class="section-block" aria-label="Bakery locations">
        <div class="container location-grid">
            @foreach ($locations as $location)
                <x-location-card :location="$location" />
            @endforeach
        </div>
    </section>
@endsection
