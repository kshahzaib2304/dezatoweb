@extends('layouts.app')

@section('content')
    <x-page-hero
        eyebrow="Menu"
        title="What we’re baking"
        text="Cakes, cupcakes, cheesecakes, eclairs & more - priced in PKR."
        image="images/home/promo-workshop.jpg"
    />

    <div class="menu-toolbar" data-sticky-toolbar>
        <div class="container">
            <nav class="chip-row" aria-label="Menu categories">
                @foreach ($categories as $category)
                    <a
                        class="chip {{ $activeCategory === $category['id'] ? 'is-active' : '' }}"
                        href="{{ route('menu', array_filter(['category' => $category['id'] === 'all' ? null : $category['id']])) }}"
                    >
                        {{ $category['label'] }}
                    </a>
                @endforeach
            </nav>
        </div>
    </div>

    <section class="section-block" aria-label="Products">
        <div class="container">
            <form class="menu-tools" method="get" action="{{ route('menu') }}">
                @if ($activeCategory !== 'all')
                    <input type="hidden" name="category" value="{{ $activeCategory }}">
                @endif

                <div class="menu-search">
                    <label class="sr-only" for="menu-q">Search menu</label>
                    <input
                        id="menu-q"
                        type="search"
                        name="q"
                        value="{{ $filters['q'] ?? '' }}"
                        placeholder="Search cakes, flavours, eclairs…"
                    >
                </div>

                <div class="menu-tools__row">
                    <div class="menu-sort">
                        <label class="sr-only" for="menu-sort">Sort</label>
                        <select id="menu-sort" name="sort" onchange="this.form.submit()">
                            <option value="featured" @selected(($filters['sort'] ?? '') === 'featured')>Featured</option>
                            <option value="popular" @selected(($filters['sort'] ?? '') === 'popular')>Popular</option>
                            <option value="newest" @selected(($filters['sort'] ?? '') === 'newest')>Newest</option>
                            <option value="price_asc" @selected(($filters['sort'] ?? '') === 'price_asc')>Price: low to high</option>
                            <option value="price_desc" @selected(($filters['sort'] ?? '') === 'price_desc')>Price: high to low</option>
                        </select>
                    </div>
                    <div class="menu-size">
                        <label class="sr-only" for="weight">Size</label>
                        <select id="weight" name="weight" onchange="this.form.submit()">
                            <option value="">All sizes</option>
                            @foreach ($weights as $size)
                                <option value="{{ $size }}" @selected(($filters['weight'] ?? '') === $size)>{{ $size }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <noscript><button class="btn btn--outline" type="submit">Apply filters</button></noscript>
            </form>

            @if (count($products) === 0)
                <p class="empty-state">No treats match these filters. Try another search or size.</p>
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
