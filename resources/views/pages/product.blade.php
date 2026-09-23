@extends('layouts.app')

@push('head')
<script type="application/ld+json">
{!! \App\Support\SeoSchema::product($product) !!}
</script>
@endpush

@section('content')
    <section class="section-block product-page">
        <div class="container product-layout">
            <div class="product-layout__media" data-reveal>
                <div class="product-gallery" data-gallery>
                    <div class="product-gallery__main">
                        <img
                            data-gallery-main
                            src="{{ asset($gallery[0] ?? $product['image']) }}"
                            alt="{{ $product['name'] }}"
                            width="1000"
                            height="1000"
                        >
                        @if (! empty($product['badge']))
                            <span class="product-card__badge">{{ $product['badge'] }}</span>
                        @endif
                    </div>
                    @if (count($gallery) > 1)
                        <div class="product-gallery__thumbs">
                            @foreach ($gallery as $i => $image)
                                <button
                                    type="button"
                                    class="{{ $i === 0 ? 'is-active' : '' }}"
                                    data-gallery-thumb
                                    data-src="{{ asset($image) }}"
                                    aria-label="View image {{ $i + 1 }}"
                                >
                                    <img src="{{ asset($image) }}" alt="" width="160" height="160" loading="lazy">
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>
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

@push('scripts')
<script src="{{ asset('js/features.js') }}" defer></script>
@endpush
