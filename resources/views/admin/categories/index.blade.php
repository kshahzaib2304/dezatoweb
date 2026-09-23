@extends('layouts.admin')

@section('content')
    <p class="admin-lead">
        Categories organise your menu (Cakes, Cupcakes, Brownies…). Add a category first, then assign products to it.
    </p>

    <div class="admin-split">
        <section class="admin-panel">
            <h2>Add a category</h2>
            <form class="admin-form" method="post" action="{{ route('admin.categories.store') }}">
                @csrf
                <div class="form-row">
                    <label class="field-label" for="label">Category name *</label>
                    <input id="label" class="field-input" type="text" name="label" required maxlength="80" placeholder="e.g. Mini Pies">
                </div>
                <div class="form-row">
                    <label class="field-label" for="sort_order">Display order</label>
                    <input id="sort_order" class="field-input" type="number" name="sort_order" value="0" min="0">
                </div>
                <button class="btn btn--primary" type="submit">Add category</button>
            </form>
        </section>

        <section class="admin-panel">
            <h2>Your categories</h2>
            @if ($categories->isEmpty())
                <p class="admin-empty">No categories yet.</p>
            @else
                <ul class="admin-cat-list">
                    @foreach ($categories as $category)
                        <li>
                            <form class="admin-cat-row" method="post" action="{{ route('admin.categories.update', $category) }}">
                                @csrf
                                @method('PUT')
                                <div class="form-grid">
                                    <div class="form-row">
                                        <label class="field-label" for="label-{{ $category->id }}">Name</label>
                                        <input id="label-{{ $category->id }}" class="field-input" type="text" name="label" value="{{ $category->label }}" required>
                                    </div>
                                    <div class="form-row">
                                        <label class="field-label" for="slug-{{ $category->id }}">URL code</label>
                                        <input id="slug-{{ $category->id }}" class="field-input" type="text" name="slug" value="{{ $category->slug }}" required>
                                        <p class="field-hint">Usually leave this as-is.</p>
                                    </div>
                                    <div class="form-row">
                                        <label class="field-label" for="sort-{{ $category->id }}">Order</label>
                                        <input id="sort-{{ $category->id }}" class="field-input" type="number" name="sort_order" value="{{ $category->sort_order }}" min="0">
                                    </div>
                                    <div class="form-row">
                                        <label class="check-inline">
                                            <input type="checkbox" name="is_active" value="1" @checked($category->is_active)>
                                            <span>Show on menu</span>
                                        </label>
                                        <p class="admin-muted">{{ $category->products_count }} product(s)</p>
                                    </div>
                                </div>
                                <div class="admin-form__actions">
                                    <button class="btn btn--outline btn--sm" type="submit">Save</button>
                                </div>
                            </form>
                            <form method="post" action="{{ route('admin.categories.destroy', $category) }}" onsubmit="return confirm('Delete this category? Only works if it has no products.');">
                                @csrf
                                @method('DELETE')
                                <button class="text-btn text-btn--danger" type="submit">Delete</button>
                            </form>
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>
    </div>
@endsection
