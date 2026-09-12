@extends('layouts.app')

@section('content')
    <x-page-hero
        eyebrow="Menu"
        title="What we’re baking"
        text="Cakes, cupcakes, cheesecakes, eclairs & more — priced in PKR."
        image="images/home/promo-workshop.jpg"
    />

    <div class="menu-toolbar" data-sticky-toolbar>
        <div class="container">
            <nav class="chip-row" aria-label="Menu categories">
                @foreach ($categories as $category)
                    <a
                        class="chip {{ $activeCategory === $category['id'] ? 'is-active' : '' }}"
                        href="{{ route('menu', $category['id'] === 'all' ? [] : ['category' => $category['id']]) }}"
                    >
                        {{ $category['label'] }}
                    </a>
                @endforeach
            </nav>
        </div>
    </div>

    <section class="section-block" aria-label="Products">
        <div class="container">
            @if (count($products) === 0)
                <p class="empty-state">No treats in this category yet. Try another filter.</p>
            @else
                <div class="product-grid">
                    @foreach ($products as $product)
                        <x-product-card :product="$product" />
                    @endforeach
                </div>
            @endif
        </div>
    </section>
@endsection
