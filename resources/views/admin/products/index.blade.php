@extends('layouts.admin')

@section('content')
    <div class="admin-toolbar">
        <form method="get" action="{{ route('admin.products.index') }}" class="admin-search">
            <label class="sr-only" for="q">Search products</label>
            <input id="q" class="field-input" type="search" name="q" value="{{ $q }}" placeholder="Search by name…">
            <button class="btn btn--outline" type="submit">Search</button>
        </form>
        <div class="admin-toolbar__actions">
            @if ($placeholderCount > 0)
                <a class="btn btn--outline" href="{{ route('admin.products.index', ['needs_photo' => 1]) }}">
                    Needs real photo ({{ $placeholderCount }})
                </a>
            @endif
            @if ($needsPhoto)
                <a class="btn btn--ghost" href="{{ route('admin.products.index') }}">Show all</a>
            @endif
            <a class="btn btn--primary" href="{{ route('admin.products.create') }}">Add product</a>
        </div>
    </div>

    <aside class="admin-media-guide" aria-label="Photo size guide">
        <strong>{{ $mediaGuide['label'] }}</strong>
        <span>{{ $mediaGuide['size'] }} · {{ $mediaGuide['ratio'] }} · {{ $mediaGuide['formats'] }} · max {{ $mediaGuide['max'] }}</span>
        <p>{{ $mediaGuide['tip'] }}</p>
        <p class="admin-muted">Many items still reuse homepage placeholders. Open each product and upload a real cake photo (1200 × 1200).</p>
    </aside>

    <section class="admin-panel">
        @if ($products->isEmpty())
            <p class="admin-empty">No products found. Click <strong>Add product</strong> to put items on your menu.</p>
        @else
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Photo</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>On website?</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($products as $product)
                            <tr>
                                <td>
                                    <img
                                        class="admin-thumb"
                                        src="{{ asset($product->publicImagePath()) }}"
                                        alt=""
                                        width="48"
                                        height="48"
                                        loading="lazy"
                                        decoding="async"
                                    >
                                </td>
                                <td>
                                    <strong>{{ $product->name }}</strong>
                                    @if ($product->is_featured)
                                        <span class="admin-badge admin-badge--soft">Featured</span>
                                    @endif
                                </td>
                                <td>{{ $product->category?->label ?? '-' }}</td>
                                <td>{{ pkr($product->price) }}</td>
                                <td>
                                    @if ($product->is_active)
                                        <span class="admin-badge admin-badge--ok">Yes · Active</span>
                                    @else
                                        <span class="admin-badge">Hidden</span>
                                    @endif
                                </td>
                                <td class="admin-actions">
                                    <a href="{{ route('admin.products.edit', $product) }}">Edit</a>
                                    <form method="post" action="{{ route('admin.products.destroy', $product) }}" onsubmit="return confirm('Remove this product from the website?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="text-btn text-btn--danger" type="submit">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="admin-pagination">{{ $products->links() }}</div>
        @endif
    </section>
@endsection
