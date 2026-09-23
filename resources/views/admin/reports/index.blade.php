@extends('layouts.admin')

@section('content')
    <p class="admin-lead">A simple snapshot of how the bakery is doing - no spreadsheets needed.</p>

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
