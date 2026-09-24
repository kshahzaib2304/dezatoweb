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
                <nav class="breadcrumb" aria-label="Breadcrumb">
                    <a href="{{ route('home') }}">Home</a>
                    <span aria-hidden="true">/</span>
                    <a href="{{ route('menu') }}">Menu</a>
                    <span aria-hidden="true">/</span>
                    <a href="{{ route('menu', ['category' => $product['category']]) }}">{{ $categoryLabel }}</a>
                </nav>
                <h1>{{ $product['name'] }}</h1>
                <p class="product-layout__price">{{ pkr($product['price']) }}</p>
                @if (! empty($product['description']))
                    <p class="product-layout__text">{{ $product['description'] }}</p>
                @endif

                <dl class="product-facts">
                    @if (! empty($product['weight']))
                        <div>
                            <dt>Size</dt>
                            <dd>{{ $product['weight'] }}</dd>
                        </div>
                    @endif
                    <div>
                        <dt>Availability</dt>
                        <dd>{{ $stockLabel }}</dd>
                    </div>
                    <div>
                        <dt>Notice</dt>
                        <dd>{{ $leadTime }}</dd>
                    </div>
                    <div>
                        <dt>Category</dt>
                        <dd>{{ $categoryLabel }}</dd>
                    </div>
                </dl>

                @if (! empty($fulfillmentSummary))
                    <p class="product-layout__fulfillment">
                        Ordering for <strong>{{ $fulfillmentSummary }}</strong>
                        · <a href="{{ route('order.start', ['change' => 1]) }}">Change</a>
                    </p>
                @endif

                <form id="product-order" class="product-add" method="post" action="{{ route('cart.items.store') }}">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product['id'] }}">
                    <label class="field-label" for="qty">Quantity</label>
                    <div class="qty-row">
                        <input id="qty" class="field-input qty-input" type="number" name="quantity" value="1" min="1" max="99" inputmode="numeric" @disabled(! $available)>
                        <button class="btn btn--primary" type="submit" @disabled(! $available)>
                            {{ $available ? 'Add to cart' : 'Sold out' }}
                        </button>
                    </div>
                </form>

                <section class="product-notes" aria-label="Good to know">
                    <h2>Good to know</h2>
                    @if ($scheduleNote !== '')
                        <p>{{ $scheduleNote }}</p>
                    @endif
                    <p>Cakes are baked fresh and are best the day you receive them. Keep chilled and bring to room temperature before serving.</p>
                    <p>Our kitchen handles wheat, milk, eggs, soy, and nuts. Tell us about allergies in the order notes, or <a href="{{ route('builder.show') }}">design a custom cake</a> if you need a message, colour, or different size.</p>
                </section>
            </div>
        </div>
    </section>

    <div class="product-buybar" data-product-buybar>
        <strong>{{ pkr($product['price']) }}</strong>
        <button class="btn btn--primary" type="submit" form="product-order" @disabled(! $available)>
            {{ $available ? 'Add to cart' : 'Sold out' }}
        </button>
    </div>
    <div class="product-buybar-spacer" aria-hidden="true"></div>

    @if (count($related) > 0)
        <section class="section-block section-block--tint product-related">
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
