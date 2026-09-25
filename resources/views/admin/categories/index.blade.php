@extends('layouts.admin')

@section('content')
    <div class="admin-toolbar">
        <p class="admin-lead">Categories group the menu. Add one, then assign products to it.</p>
        <a class="btn btn--primary" href="{{ route('admin.categories.create') }}">Add category</a>
    </div>

    <section class="admin-panel">
        @if ($categories->isEmpty())
            <p class="admin-empty">No categories yet. Click <strong>Add category</strong> to create the first one.</p>
        @else
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>URL code</th>
                            <th>Order</th>
                            <th>Products</th>
                            <th>On menu?</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($categories as $category)
                            <tr>
                                <td><strong>{{ $category->label }}</strong></td>
                                <td>{{ $category->slug }}</td>
                                <td>{{ $category->sort_order }}</td>
                                <td>{{ $category->products_count }}</td>
                                <td>
                                    @if ($category->is_active)
                                        <span class="admin-badge admin-badge--ok">Yes</span>
                                    @else
                                        <span class="admin-badge">Hidden</span>
                                    @endif
                                </td>
                                <td class="admin-actions">
                                    <a href="{{ route('admin.categories.edit', $category) }}">Edit</a>
                                    <form method="post" action="{{ route('admin.categories.destroy', $category) }}" onsubmit="return confirm('Delete this category? Only works if it has no products.');">
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
        @endif
    </section>
@endsection
