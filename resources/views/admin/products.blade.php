@extends('layouts.admin')

@section('content')
    <div class="admin-toolbar">
        <form method="get">
            <input class="field-input" type="search" name="q" placeholder="Search products">
        </form>
        <form method="post" action="{{ route('admin.stub') }}">@csrf<button class="btn btn--primary" type="submit">Add product</button></form>
    </div>

    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Badge</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($products as $product)
                    <tr>
                        <td>{{ $product['name'] }}</td>
                        <td>{{ $product['category'] }}</td>
                        <td>{{ pkr($product['price']) }}</td>
                        <td>{{ $product['badge'] ?? '—' }}</td>
                        <td>
                            <form method="post" action="{{ route('admin.stub') }}" class="admin-inline">
                                @csrf
                                <button class="text-btn" type="submit">Edit</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
