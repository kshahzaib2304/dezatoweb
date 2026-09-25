@extends('layouts.app')

@php
    /** @var \Illuminate\Contracts\Pagination\LengthAwarePaginator $products */
    $total = $products->total();
    $from = $products->firstItem();
    $to = $products->lastItem();
    $hasMore = $products->hasMorePages();
@endphp

@push('head')
    @if ($products->previousPageUrl())
        <link rel="prev" href="{{ $products->previousPageUrl() }}">
    @endif
    @if ($products->nextPageUrl())
        <link rel="next" href="{{ $products->nextPageUrl() }}">
    @endif
@endpush

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
                    @php
                        $chipQuery = array_filter([
                            'category' => $category['id'] === 'all' ? null : $category['id'],
                            'q' => ($filters['q'] ?? '') !== '' ? $filters['q'] : null,
                            'sort' => (($filters['sort'] ?? 'featured') !== 'featured') ? $filters['sort'] : null,
                            'weight' => ($filters['weight'] ?? '') !== '' ? $filters['weight'] : null,
                        ]);
                    @endphp
                    <a
                        class="chip {{ $activeCategory === $category['id'] ? 'is-active' : '' }}"
                        href="{{ route('menu', $chipQuery) }}"
                    >
                        {{ $category['label'] }}
                    </a>
                @endforeach
            </nav>
        </div>
    </div>

    <section class="section-block" aria-label="Products">
        <div
            class="container"
            data-menu-listing
            data-current-page="{{ $products->currentPage() }}"
            data-last-page="{{ $products->lastPage() }}"
            data-per-page="{{ $products->perPage() }}"
            data-total="{{ $total }}"
        >
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

            <p
                class="menu-count"
                data-menu-status
                aria-live="polite"
            >
                @if ($total === 0)
                    No treats match these filters. Try another search or size.
                @else
                    Showing {{ $from }}–{{ $to }} of {{ $total }}
                @endif
            </p>

            @if ($total === 0)
                <p class="empty-state">Browse another category or clear your search.</p>
            @else
                <div class="product-grid" data-menu-grid>
                    <x-menu-product-cards :products="$products" />
                </div>

                <div class="menu-more" data-menu-more @if (! $hasMore || ! $products->onFirstPage()) hidden @endif>
                    <button
                        class="btn btn--outline menu-more__btn"
                        type="button"
                        data-menu-load-more
                        data-next-url="{{ $products->nextPageUrl() }}"
                        @disabled(! $hasMore || ! $products->onFirstPage())
                    >
                        Load more treats
                    </button>
                    @if ($hasMore && $products->onFirstPage())
                        <a class="menu-more__page-link" href="{{ $products->nextPageUrl() }}" rel="next">
                            Or go to page {{ $products->currentPage() + 1 }}
                        </a>
                    @endif
                    <div class="menu-more__sentinel" data-menu-sentinel aria-hidden="true"></div>
                </div>

                {{-- Always crawlable pagination links (Google Search Central) --}}
                <div class="menu-pager" data-menu-pager>
                    {{ $products->links('pagination.simple') }}
                </div>
            @endif
        </div>
    </section>
@endsection

@push('scripts')
<script src="{{ asset('js/menu.js') }}" defer></script>
@endpush
