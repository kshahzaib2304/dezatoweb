@extends('layouts.admin')

@section('content')
    <div class="admin-toolbar">
        <p class="admin-lead" style="margin:0">A simple snapshot of how the bakery is doing.</p>
        <div class="admin-toolbar__actions">
            <a class="btn btn--outline btn--sm" href="{{ route('admin.reports.export', ['days' => 7]) }}">Export 7 days CSV</a>
            <a class="btn btn--outline btn--sm" href="{{ route('admin.reports.export', ['days' => 30]) }}">Export 30 days CSV</a>
            <a class="btn btn--primary btn--sm" href="{{ route('admin.reports.export', ['days' => 90]) }}">Export 90 days CSV</a>
        </div>
    </div>

    <section class="admin-stats" aria-label="Report summary">
        @foreach ($stats as $stat)
            <article>
                <p>{{ $stat['label'] }}</p>
                <strong>{{ $stat['value'] }}</strong>
                <span class="admin-stat-hint">{{ $stat['hint'] }}</span>
            </article>
        @endforeach
    </section>

    <section class="admin-panel">
        <h2>Top sellers</h2>
        @if ($topItems->isEmpty())
            <p class="admin-empty">No order items yet.</p>
        @else
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Qty sold</th>
                            <th>Revenue</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($topItems as $row)
                            <tr>
                                <td>{{ $row->product_name }}</td>
                                <td>{{ $row->qty }}</td>
                                <td>{{ pkr($row->revenue) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
@endsection
