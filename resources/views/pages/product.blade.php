@extends('layouts.app')

@push('head')
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'Product',
    'name' => $product['name'],
    'description' => $product['description'],
    'image' => asset($product['image']),
    'sku' => $product['id'],
    'brand' => [
        '@type' => 'Brand',
        'name' => 'Dezato Cake House',
    ],
    'offers' => [
        '@type' => 'Offer',
        'priceCurrency' => 'PKR',
        'price' => (string) (int) $product['price'],
        'availability' => 'https://schema.org/InStock',
        'url' => route('products.show', $product['id']),
    ],
], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE|JSON_HEX_TAG|JSON_HEX_AMP) !!}
</script>
@endpush

@section('content')
    <section class="section-block product-page">
        <div class="container product-layout">
            <div class="product-layout__media" data-reveal>
                <img
                    src="{{ asset($product['image']) }}"
                    alt="{{ $product['name'] }}"
                    width="1000"
                    height="1000"
                >
                @if (! empty($product['badge']))
                    <span class="product-card__badge">{{ $product['badge'] }}</span>
                @endif
            </div>

            <div class="product-layout__copy" data-reveal>
                <p class="product-layout__category">
                    <a href="{{ route('menu', ['category' => $product['category']]) }}">{{ $categoryLabel }}</a>
                </p>
                <h1>{{ $product['name'] }}</h1>
                <p class="product-layout__price">{{ pkr($product['price']) }}</p>
                @if (! empty($product['weight']))
                    <p class="product-layout__weight">{{ $product['weight'] }}</p>
                @endif
                <p class="product-layout__text">{{ $product['description'] }}</p>

                @if (! empty($fulfillmentSummary))
                    <p class="product-layout__fulfillment">
                        Ordering for <strong>{{ $fulfillmentSummary }}</strong>
                        · <a href="{{ route('order.start', ['change' => 1]) }}">Change</a>
                    </p>
                @endif

                <form class="product-add" method="post" action="{{ route('cart.items.store') }}">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product['id'] }}">
                    <label class="field-label" for="qty">Quantity</label>
                    <div class="qty-row">
                        <input id="qty" class="field-input qty-input" type="number" name="quantity" value="1" min="1" max="99" inputmode="numeric">
                        <button class="btn btn--primary" type="submit">Add to cart</button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    @if (count($related) > 0)
        <section class="section-block section-block--tint">
            <div class="container">
                <div class="section-head">
                    <h2>You may also like</h2>
                </div>
                <div class="product-grid">
                    @foreach ($related as $item)
                        <x-product-card :product="$item" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection
