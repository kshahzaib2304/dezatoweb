@extends('layouts.admin')

@section('content')
    <p class="admin-back"><a href="{{ route('admin.categories.index') }}">← Back to categories</a></p>

    <section class="admin-panel">
        <form
            class="admin-form"
            method="post"
            action="{{ $category->exists ? route('admin.categories.update', $category) : route('admin.categories.store') }}"
        >
            @csrf
            @if ($category->exists)
                @method('PUT')
            @endif

            <div class="form-grid">
                <div class="form-row">
                    <label class="field-label" for="label">Category name *</label>
                    <input id="label" class="field-input" type="text" name="label" value="{{ old('label', $category->label) }}" required maxlength="80" placeholder="e.g. Mini Pies">
                </div>

                <div class="form-row">
                    <label class="field-label" for="sort_order">Display order</label>
                    <input id="sort_order" class="field-input" type="number" name="sort_order" value="{{ old('sort_order', $category->sort_order ?? 0) }}" min="0" max="999">
                    <p class="field-hint">Lower numbers appear first on the menu.</p>
                </div>

                @if ($category->exists)
                    <div class="form-row form-row--full">
                        <label class="field-label" for="slug">URL code</label>
                        <input id="slug" class="field-input" type="text" name="slug" value="{{ old('slug', $category->slug) }}" required maxlength="80">
                        <p class="field-hint">Used in the menu link. Change it only if you need a new address.</p>
                    </div>

                    <div class="form-row form-row--full">
                        <label class="check-inline">
                            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $category->is_active))>
                            <span>Show on menu</span>
                        </label>
                    </div>
                @endif
            </div>

            <div class="admin-form__actions">
                <button class="btn btn--primary" type="submit">
                    {{ $category->exists ? 'Save changes' : 'Add category' }}
                </button>
                <a class="btn btn--ghost" href="{{ route('admin.categories.index') }}">Cancel</a>
            </div>
        </form>
    </section>
@endsection
