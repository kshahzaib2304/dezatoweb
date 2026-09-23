@extends('layouts.admin')

@section('content')
    <aside class="admin-media-guide">
        <strong>How to edit website pages</strong>
        <ol class="admin-steps">
            <li>Update the About / Services introductions (short paragraphs).</li>
            <li>Edit Privacy, Terms, and FAQ below - write in plain language.</li>
            <li>Leave a blank line between paragraphs. For FAQ, put the question on the first line, then the answer.</li>
        </ol>
        <p class="admin-muted">No coding needed. Changes appear on the website after you save.</p>
    </aside>

    <form class="admin-form" method="post" action="{{ route('admin.pages.update') }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <section class="admin-panel">
            <div class="admin-panel__head">
                <h2>About &amp; Services text</h2>
            </div>
            <div class="form-grid">
                <div class="form-row form-row--full">
                    <label class="field-label" for="about_intro">About page intro *</label>
                    <textarea id="about_intro" class="field-input" name="about_intro" rows="5" required maxlength="2000">{{ old('about_intro', $aboutIntro) }}</textarea>
                    <p class="field-hint">Main paragraph on the About Us page. <a href="{{ route('about') }}" target="_blank" rel="noopener">View About page</a></p>
                </div>
                <div class="form-row form-row--full">
                    <label class="field-label" for="services_intro">Services page intro *</label>
                    <textarea id="services_intro" class="field-input" name="services_intro" rows="3" required maxlength="1000">{{ old('services_intro', $servicesIntro) }}</textarea>
                    <p class="field-hint">Short line under the Services title. <a href="{{ route('services') }}" target="_blank" rel="noopener">View Services page</a></p>
                </div>
            </div>
        </section>

        <section class="admin-panel admin-panel--spaced">
            <h2>About timeline (milestones)</h2>
            <p class="admin-lead">Shown on the About Us page under “Our journey”.</p>
            @foreach ($milestones as $index => $item)
                <article class="admin-slide-card">
                    <input type="hidden" name="milestones[{{ $index }}][id]" value="{{ $item['id'] }}">
                    <div class="form-grid">
                        <div class="form-row">
                            <label class="field-label">Year *</label>
                            <input class="field-input" type="text" name="milestones[{{ $index }}][year]" value="{{ old('milestones.'.$index.'.year', $item['year']) }}" required>
                        </div>
                        <div class="form-row">
                            <label class="field-label">Title *</label>
                            <input class="field-input" type="text" name="milestones[{{ $index }}][title]" value="{{ old('milestones.'.$index.'.title', $item['title']) }}" required>
                        </div>
                        <div class="form-row form-row--full">
                            <label class="field-label">Short story *</label>
                            <textarea class="field-input" name="milestones[{{ $index }}][text]" rows="2" required>{{ old('milestones.'.$index.'.text', $item['text']) }}</textarea>
                        </div>
                    </div>
                </article>
            @endforeach
        </section>

        <section class="admin-panel admin-panel--spaced">
            <h2>Services packages</h2>
            <p class="admin-lead">Photo ≈ {{ $packageGuide['size'] }} ({{ $packageGuide['ratio'] }}), max {{ $packageGuide['max'] }}.</p>
            @foreach ($packages as $index => $package)
                <article class="admin-slide-card">
                    <input type="hidden" name="packages[{{ $index }}][id]" value="{{ $package['id'] }}">
                    <input type="hidden" name="packages[{{ $index }}][existing_image]" value="{{ $package['image'] ?? '' }}">
                    <div class="form-grid">
                        <div class="form-row">
                            <label class="field-label">Title *</label>
                            <input class="field-input" type="text" name="packages[{{ $index }}][title]" value="{{ old('packages.'.$index.'.title', $package['title']) }}" required>
                        </div>
                        <div class="form-row">
                            <label class="field-label">Serves</label>
                            <input class="field-input" type="text" name="packages[{{ $index }}][serves]" value="{{ old('packages.'.$index.'.serves', $package['serves']) }}">
                        </div>
                        <div class="form-row">
                            <label class="field-label">Price label</label>
                            <input class="field-input" type="text" name="packages[{{ $index }}][price]" value="{{ old('packages.'.$index.'.price', $package['price']) }}" placeholder="From ₨ 7,500">
                        </div>
                        <div class="form-row form-row--full">
                            <label class="field-label">Description</label>
                            <textarea class="field-input" name="packages[{{ $index }}][blurb]" rows="2">{{ old('packages.'.$index.'.blurb', $package['blurb']) }}</textarea>
                        </div>
                        <div class="form-row form-row--full">
                            <label class="field-label">Photo</label>
                            <div class="admin-slide-card__preview">
                                <img src="{{ $package['image_url'] }}" alt="" width="120" height="75">
                                <input class="field-input" type="file" name="packages[{{ $index }}][image]" accept="image/jpeg,image/png,image/webp">
                            </div>
                        </div>
                    </div>
                </article>
            @endforeach
        </section>

        @foreach ($pages as $page)
            <section class="admin-panel admin-panel--spaced">
                <div class="admin-panel__head">
                    <h2>{{ $page['label'] }}</h2>
                    @if (! empty($page['preview_url']))
                        <a class="btn btn--ghost btn--sm" href="{{ $page['preview_url'] }}" target="_blank" rel="noopener">View on website</a>
                    @endif
                </div>
                <div class="form-grid">
                    <div class="form-row form-row--full">
                        <label class="field-label" for="title-{{ $page['key'] }}">Page title *</label>
                        <input
                            id="title-{{ $page['key'] }}"
                            class="field-input"
                            type="text"
                            name="pages[{{ $page['key'] }}][title]"
                            value="{{ old('pages.'.$page['key'].'.title', $page['title']) }}"
                            required
                            maxlength="160"
                        >
                    </div>
                    <div class="form-row form-row--full">
                        <label class="field-label" for="body-{{ $page['key'] }}">Page content *</label>
                        <textarea
                            id="body-{{ $page['key'] }}"
                            class="field-input"
                            name="pages[{{ $page['key'] }}][body]"
                            rows="10"
                            required
                            maxlength="20000"
                        >{{ old('pages.'.$page['key'].'.body', $page['body']) }}</textarea>
                        @if ($page['key'] === 'faq')
                            <p class="field-hint">Tip: put each question on its own line, then the answer on the next lines. Leave a blank line before the next question.</p>
                        @endif
                    </div>
                </div>
            </section>
        @endforeach

        <div class="admin-form__actions admin-form__actions--spaced">
            <button class="btn btn--primary" type="submit">Save all pages</button>
        </div>
    </form>
@endsection
