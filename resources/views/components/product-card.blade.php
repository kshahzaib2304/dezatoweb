@props([
    'product',
])

@php
    $url = route('products.show', $product['id']);
    $stock = $product['stock'] ?? null;
    $stockCount = ($stock === null || $stock === '') ? null : (int) $stock;
    $available = $stockCount === null || $stockCount > 0;
    $lowStock = $stockCount !== null && $stockCount > 0 && $stockCount <= 5;
    $canOrder = app(\App\Support\Fulfillment::class)->has();
@endphp

<article class="product-card" data-reveal>
    <div class="product-card__media-wrap">
        <a class="product-card__media" href="{{ $url }}">
            <img
                src="{{ asset($product['image']) }}"
                alt="{{ $product['name'] }}"
                width="800"
                height="800"
                loading="lazy"
            >
            @if (! empty($product['badge']))
                <span class="product-card__badge">{{ $product['badge'] }}</span>
            @endif
            @if ($lowStock)
                <span class="product-card__urgency">Only {{ $stockCount }} left</span>
            @endif
        </a>

        @if ($available)
            <form
                class="product-card__quick"
                method="post"
                action="{{ route('cart.items.store') }}"
                data-cart-add
                data-quick-add
            >
                @csrf
                <input type="hidden" name="product_id" value="{{ $product['id'] }}">
                <input type="hidden" name="quantity" value="1">
                @if ($canOrder)
                    <button
                        class="product-card__add"
                        type="submit"
                        aria-label="Add {{ $product['name'] }} to cart"
                    >
                        <span class="product-card__add-label">Add to cart</span>
                        <span class="product-card__add-icon" aria-hidden="true">+</span>
                    </button>
                @else
                    <button
                        class="product-card__add"
                        type="button"
                        data-fulfillment-open
                        aria-label="Choose pickup or delivery, then add {{ $product['name'] }}"
                    >
                        <span class="product-card__add-label">Add to cart</span>
                        <span class="product-card__add-icon" aria-hidden="true">+</span>
                    </button>
                @endif
            </form>
        @else
            <span class="product-card__soldout">Sold out</span>
        @endif
    </div>

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
