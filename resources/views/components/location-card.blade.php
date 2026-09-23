@props([
    'location',
])

<article class="location-card" data-reveal>
    <div class="location-card__media">
        <img
            src="{{ asset($location['image']) }}"
            alt="{{ $location['name'] }} bakery"
            width="900"
            height="675"
            loading="lazy"
        >
    </div>
    <div class="location-card__body">
        <p class="location-card__city">{{ $location['city'] }}, {{ $location['region'] }}</p>
        <h3 class="location-card__title">{{ $location['name'] }}</h3>
        <p class="location-card__address">{{ $location['address'] }}</p>
        <p class="location-card__hours">{{ $location['hours'] }}</p>
        <p class="location-card__phone">
            <a href="tel:{{ preg_replace('/\D+/', '', $location['phone']) }}">{{ $location['phone'] }}</a>
        </p>
        @if (! empty($location['map_url']))
            <p class="location-card__map">
                <a href="{{ $location['map_url'] }}" target="_blank" rel="noopener noreferrer">Open in Google Maps</a>
            </p>
        @endif
        @if (! empty($location['services']))
            <ul class="location-card__tags">
                @foreach ($location['services'] as $service)
                    <li>{{ $service }}</li>
                @endforeach
            </ul>
        @endif
        <a class="btn btn--outline" href="{{ route('order') }}">Order for pickup</a>
    </div>
</article>
