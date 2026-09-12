@props([
    'product',
])

@php
    $url = route('products.show', $product['id']);
@endphp

<article class="product-card" data-reveal>
    <a class="product-card__media" href="{{ $url }}">
        <img
            src="{{ asset($product['image']) }}"
            alt="{{ $product['name'] }}"
            width="800"
            height="800"
            loading="lazy"
        >
    </a>
    <div class="product-card__body">
        <div class="product-card__meta">
            <h3 class="product-card__title">
                <a href="{{ $url }}">{{ $product['name'] }}</a>
            </h3>
            <p class="product-card__price">{{ pkr($product['price']) }}</p>
        </div>
        @if (! empty($product['weight']))
            <p class="product-card__text">{{ $product['weight'] }}</p>
        @endif
        <a class="product-card__link" href="{{ $url }}">View details</a>
    </div>
</article>
