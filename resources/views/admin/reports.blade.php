@extends('layouts.admin')

@section('content')
    <div class="admin-toolbar">
        <form method="post" action="{{ route('admin.stub') }}">@csrf<button class="btn btn--outline" type="submit">Export Excel</button></form>
    </div>
    <div class="admin-stats admin-stats--reports">
        @foreach ($reports as $report)
            <article>
                <strong>{{ $report }}</strong>
                <form method="post" action="{{ route('admin.stub') }}">@csrf<button class="text-btn" type="submit">Open</button></form>
            </article>
        @endforeach
    </div>
@endsection
