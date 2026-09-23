@extends('layouts.admin')

@section('content')
    <aside class="admin-media-guide">
        <strong>Where do messages come from?</strong>
        <p>When someone fills a form on Services, Custom cake request, or newsletter signup, it appears here. You’ll also get an email if Contact &amp; alerts is set up.</p>
        @if ($unreadCount > 0)
            <p><span class="admin-badge admin-badge--ok">{{ $unreadCount }} unread</span></p>
        @endif
    </aside>

    <section class="admin-panel">
        @if ($inquiries->isEmpty())
            <p class="admin-empty">No messages yet.</p>
        @else
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>When</th>
                            <th>Type</th>
                            <th>From</th>
                            <th>Summary</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($inquiries as $inquiry)
                            <tr>
                                <td>{{ optional($inquiry->created_at)->format('d M, h:i A') }}</td>
                                <td>
                                    {{ $inquiry->typeLabel() }}
                                    @unless ($inquiry->is_read)
                                        <span class="admin-badge admin-badge--soft">New</span>
                                    @endunless
                                </td>
                                <td>
                                    {{ $inquiry->name ?: '-' }}
                                    <div class="admin-muted">{{ $inquiry->email }}</div>
                                </td>
                                <td>{{ $inquiry->summary() }}</td>
                                <td><a href="{{ route('admin.inquiries.show', $inquiry) }}">Open</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="admin-pagination">{{ $inquiries->links() }}</div>
        @endif
    </section>
@endsection
