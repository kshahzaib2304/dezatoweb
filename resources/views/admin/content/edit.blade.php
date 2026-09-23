@extends('layouts.admin')

@section('content')
    <aside class="admin-media-guide" aria-label="Banner photo guide">
        <strong>{{ $mediaGuide['label'] }}</strong>
        <span>{{ $mediaGuide['size'] }} · {{ $mediaGuide['ratio'] }} · {{ $mediaGuide['formats'] }} · max {{ $mediaGuide['max'] }}</span>
        <p>{{ $mediaGuide['tip'] }}</p>
        <p class="admin-muted">Homepage hero images are currently managed with the site files. Use the size above when you send new photos to your web partner.</p>
    </aside>

    <section class="admin-panel">
        <h2>Top announcement bar</h2>
        <p class="admin-lead">
            This short line appears at the very top of every page (e.g. delivery hours or a special offer). Leave blank to hide it.
        </p>
        <form class="admin-form" method="post" action="{{ route('admin.content.update') }}">
            @csrf
            @method('PUT')
            <div class="form-row form-row--full">
                <label class="field-label" for="announcement">Announcement text</label>
                <input
                    id="announcement"
                    class="field-input"
                    type="text"
                    name="announcement"
                    value="{{ old('announcement', $announcement) }}"
                    maxlength="240"
                    placeholder="e.g. Same-day Karachi delivery before 4 PM · Free pickup at DHA"
                >
                <p class="field-hint">Keep it one short sentence so it fits on phones.</p>
            </div>
            <div class="admin-form__actions">
                <button class="btn btn--primary" type="submit">Save website text</button>
            </div>
        </form>
    </section>
@endsection
