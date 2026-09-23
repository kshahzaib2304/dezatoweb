@extends('layouts.admin')

@section('content')
    <aside class="admin-media-guide" aria-label="Photo size guide">
        <strong>How to add a product photo</strong>
        <ol class="admin-steps">
            <li>Take or choose a clear photo of the cake.</li>
            <li>Crop it to a <strong>square</strong> — recommended size <strong>{{ $mediaGuide['size'] }}</strong>.</li>
            <li>Save as {{ $mediaGuide['formats'] }}, under {{ $mediaGuide['max'] }}.</li>
            <li>Upload it in the Photo field below.</li>
        </ol>
        <p>{{ $mediaGuide['tip'] }}</p>
    </aside>

    <section class="admin-panel">
        <form
            class="admin-form"
            method="post"
            action="{{ $product->exists ? route('admin.products.update', $product) : route('admin.products.store') }}"
            enctype="multipart/form-data"
        >
            @csrf
            @if ($product->exists)
                @method('PUT')
            @endif

            <div class="form-grid">
                <div class="form-row form-row--full">
                    <label class="field-label" for="name">Product name *</label>
                    <input id="name" class="field-input" type="text" name="name" value="{{ old('name', $product->name) }}" required maxlength="160" placeholder="e.g. Chocolate Truffle Cake">
                    <p class="field-hint">This is what customers see on the menu.</p>
                </div>

                <div class="form-row">
                    <label class="field-label" for="category_id">Category *</label>
                    <select id="category_id" class="field-input" name="category_id" required>
                        <option value="">Choose a category…</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected((string) old('category_id', $product->category_id) === (string) $category->id)>
                                {{ $category->label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-row">
                    <label class="field-label" for="price">Price (PKR) *</label>
                    <input id="price" class="field-input" type="number" name="price" value="{{ old('price', $product->price) }}" required min="1" step="1" placeholder="1850">
                    <p class="field-hint">Numbers only — e.g. 1850 (not Rs. 1,850).</p>
                </div>

                <div class="form-row form-row--full">
                    <label class="field-label" for="description">Short description</label>
                    <textarea id="description" class="field-input" name="description" rows="3" maxlength="2000" placeholder="A few lines about flavours, filling, or occasion.">{{ old('description', $product->description) }}</textarea>
                </div>

                <div class="form-row">
                    <label class="field-label" for="badge">Badge (optional)</label>
                    <input id="badge" class="field-input" type="text" name="badge" value="{{ old('badge', $product->badge) }}" maxlength="64" placeholder="Bestseller, New…">
                </div>

                <div class="form-row">
                    <label class="field-label" for="weight">Size / weight (optional)</label>
                    <input id="weight" class="field-input" type="text" name="weight" value="{{ old('weight', $product->weight) }}" maxlength="32" placeholder="1 lb, 2 lb…">
                </div>

                <div class="form-row">
                    <label class="field-label" for="stock">Stock left (optional)</label>
                    <input id="stock" class="field-input" type="number" name="stock" value="{{ old('stock', $product->stock) }}" min="0" placeholder="Leave blank if unlimited">
                    <p class="field-hint">Leave empty if you always bake to order.</p>
                </div>

                <div class="form-row">
                    <label class="field-label" for="sort_order">Display order</label>
                    <input id="sort_order" class="field-input" type="number" name="sort_order" value="{{ old('sort_order', $product->sort_order ?? 0) }}" min="0">
                    <p class="field-hint">Lower numbers appear first on the menu.</p>
                </div>

                <div class="form-row form-row--full">
                    <label class="field-label" for="image">Photo ({{ $mediaGuide['size'] }})</label>
                    @if ($product->exists && $product->image)
                        <div class="admin-current-photo">
                            <img src="{{ asset($product->publicImagePath()) }}" alt="Current photo" width="96" height="96">
                            <span>Current photo — upload a new one only if you want to replace it.</span>
                        </div>
                    @endif
                    <input id="image" class="field-input" type="file" name="image" accept="image/jpeg,image/webp,image/png">
                    <p class="field-hint">{{ $mediaGuide['formats'] }}, max {{ $mediaGuide['max'] }}. Square crop looks best.</p>
                </div>

                <div class="form-row">
                    <label class="check-inline">
                        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $product->is_active ?? true))>
                        <span>Show on website (Active)</span>
                    </label>
                </div>

                <div class="form-row">
                    <label class="check-inline">
                        <input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $product->is_featured ?? false))>
                        <span>Feature on homepage</span>
                    </label>
                </div>
            </div>

            <div class="admin-form__actions">
                <button class="btn btn--primary" type="submit">
                    {{ $product->exists ? 'Save changes' : 'Save product' }}
                </button>
                <a class="btn btn--ghost" href="{{ route('admin.products.index') }}">Cancel</a>
            </div>
        </form>
    </section>
@endsection
