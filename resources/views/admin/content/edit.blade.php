@extends('layouts.admin')

@section('content')
    <aside class="admin-media-guide" aria-label="Hero photo guide">
        <strong>{{ $mediaGuide['label'] }} - for every slide</strong>
        <span>{{ $mediaGuide['size'] }} · {{ $mediaGuide['ratio'] }} · {{ $mediaGuide['formats'] }} · max {{ $mediaGuide['max'] }}</span>
        <p>{{ $mediaGuide['tip'] }}</p>
        <ol class="admin-steps">
            <li>Crop a <strong>wide</strong> photo (cakes look best).</li>
            <li>Resize to about <strong>1600 × 1000</strong> pixels.</li>
            <li>Save as JPG or WebP under 3 MB.</li>
            <li>Add up to <strong>{{ $maxSlides }}</strong> slides - the homepage rotates them automatically.</li>
        </ol>
    </aside>

    <form class="admin-form" method="post" action="{{ route('admin.content.update') }}">
        @csrf
        @method('PUT')
        <section class="admin-panel">
            <h2>Top announcement bar</h2>
            <p class="admin-lead">Short line at the very top of every page. Leave blank to hide it.</p>
            <div class="form-grid">
                <div class="form-row form-row--full">
                    <label class="field-label" for="announcement">Announcement text</label>
                    <input
                        id="announcement"
                        class="field-input"
                        type="text"
                        name="announcement"
                        value="{{ old('announcement', $announcement) }}"
                        maxlength="240"
                        placeholder="e.g. Same-day Karachi delivery before 4 PM"
                    >
                </div>
                <div class="form-row">
                    <label class="field-label" for="interval_ms">Slide change speed</label>
                    <select id="interval_ms" class="field-input" name="interval_ms" required>
                        <option value="4000" @selected((int) old('interval_ms', $intervalMs) === 4000)>Every 4 seconds</option>
                        <option value="4500" @selected((int) old('interval_ms', $intervalMs) === 4500)>Every 4.5 seconds (recommended)</option>
                        <option value="5000" @selected((int) old('interval_ms', $intervalMs) === 5000)>Every 5 seconds</option>
                    </select>
                    <p class="field-hint">How long each photo stays before the next one.</p>
                </div>
            </div>
            <div class="admin-form__actions">
                <button class="btn btn--primary" type="submit">Save announcement &amp; timing</button>
            </div>
        </section>
    </form>

    <section class="admin-panel">
        <h2>Hero slides ({{ count($slides) }} / {{ $maxSlides }})</h2>
        <p class="admin-lead">Each slide has its own photo, headline, and short sentence. Inactive slides are hidden on the website.</p>

        @foreach ($slides as $i => $slide)
            <article class="admin-slide-card">
                <div class="admin-slide-card__preview">
                    <img src="{{ $slide['image_url'] }}" alt="" width="160" height="100" loading="lazy" decoding="async">
                    <span>Slide {{ $i + 1 }}@unless($slide['active']) · Hidden @endunless</span>
                </div>
                <form class="admin-form" method="post" action="{{ route('admin.content.slides.update', $slide['id']) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="_form" value="slide-{{ $slide['id'] }}">
                    <div class="form-grid">
                        <div class="form-row form-row--full">
                            <label class="field-label" for="headline-{{ $slide['id'] }}">Headline *</label>
                            <input id="headline-{{ $slide['id'] }}" class="field-input" type="text" name="headline" value="{{ old('_form') === 'slide-'.$slide['id'] ? old('headline') : $slide['headline'] }}" required maxlength="120">
                        </div>
                        <div class="form-row form-row--full">
                            <label class="field-label" for="lede-{{ $slide['id'] }}">Supporting sentence *</label>
                            <input id="lede-{{ $slide['id'] }}" class="field-input" type="text" name="lede" value="{{ old('_form') === 'slide-'.$slide['id'] ? old('lede') : $slide['lede'] }}" required maxlength="240">
                        </div>
                        <div class="form-row form-row--full">
                            <label class="field-label" for="image-{{ $slide['id'] }}">Replace photo (optional)</label>
                            <input id="image-{{ $slide['id'] }}" class="field-input" type="file" name="image" accept="image/jpeg,image/webp,image/png">
                            <p class="field-hint">{{ $mediaGuide['size'] }} · {{ $mediaGuide['formats'] }} · max {{ $mediaGuide['max'] }}</p>
                        </div>
                        <div class="form-row">
                            <label class="check-inline">
                                <input type="hidden" name="active" value="0">
                                <input type="checkbox" name="active" value="1" @checked(filter_var(old('_form') === 'slide-'.$slide['id'] ? old('active') : $slide['active'], FILTER_VALIDATE_BOOLEAN))>
                                <span>Show on homepage</span>
                            </label>
                        </div>
                    </div>
                    <div class="admin-form__actions">
                        <button class="btn btn--outline btn--sm" type="submit">Save this slide</button>
                    </div>
                </form>
                @if (count($slides) > 1)
                    <form method="post" action="{{ route('admin.content.slides.destroy', $slide['id']) }}" onsubmit="return confirm('Remove this slide from the homepage?');">
                        @csrf
                        @method('DELETE')
                        <button class="text-btn text-btn--danger" type="submit">Delete slide</button>
                    </form>
                @endif
            </article>
        @endforeach
    </section>

    @if ($canAddSlide)
        <section class="admin-panel">
            <h2>Add another slide</h2>
            <form class="admin-form" method="post" action="{{ route('admin.content.slides.store') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="_form" value="slide-new">
                <div class="form-grid">
                    <div class="form-row form-row--full">
                        <label class="field-label" for="new-headline">Headline *</label>
                        <input id="new-headline" class="field-input" type="text" name="headline" value="{{ old('_form') === 'slide-new' ? old('headline') : '' }}" required maxlength="120" placeholder="e.g. Custom cakes for every celebration">
                    </div>
                    <div class="form-row form-row--full">
                        <label class="field-label" for="new-lede">Supporting sentence *</label>
                        <input id="new-lede" class="field-input" type="text" name="lede" value="{{ old('_form') === 'slide-new' ? old('lede') : '' }}" required maxlength="240" placeholder="Pickup & delivery across Karachi">
                    </div>
                    <div class="form-row form-row--full">
                        <label class="field-label" for="new-image">Photo * ({{ $mediaGuide['size'] }})</label>
                        <input id="new-image" class="field-input" type="file" name="image" accept="image/jpeg,image/webp,image/png" required>
                        <p class="field-hint">{{ $mediaGuide['formats'] }}, max {{ $mediaGuide['max'] }}</p>
                    </div>
                    <div class="form-row">
                        <label class="check-inline">
                            <input type="hidden" name="active" value="0">
                            <input type="checkbox" name="active" value="1" @checked(filter_var(old('_form') === 'slide-new' ? old('active') : true, FILTER_VALIDATE_BOOLEAN))>
                            <span>Show on homepage</span>
                        </label>
                    </div>
                </div>
                <div class="admin-form__actions">
                    <button class="btn btn--primary" type="submit">Add slide</button>
                </div>
            </form>
        </section>
    @else
        <p class="admin-muted admin-panel--spaced">Maximum of {{ $maxSlides }} slides reached. Delete one to add another.</p>
    @endif

    <form class="admin-form admin-panel--spaced" method="post" action="{{ route('admin.content.showcase') }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <section class="admin-panel">
            <div class="admin-panel__head">
                <h2>Menu category shortcuts</h2>
                <button class="btn btn--outline btn--sm" type="submit" form="add-category-tile">Add tile</button>
            </div>
            <p class="admin-lead">Tiles under the homepage hero. Link example: <code>/menu?category=cakes</code>. Photo ≈ {{ $tileGuide['size'] }}.</p>
            @foreach ($categories as $index => $tile)
                <article class="admin-slide-card">
                    <div class="admin-panel__head">
                        <strong>{{ $tile['label'] }}</strong>
                        @if (count($categories) > 1)
                            <button class="btn btn--ghost btn--sm" type="submit" form="delete-cat-{{ $tile['id'] }}" onclick="return confirm('Remove this tile?')">Remove</button>
                        @endif
                    </div>
                    <input type="hidden" name="categories[{{ $index }}][id]" value="{{ $tile['id'] }}">
                    <input type="hidden" name="categories[{{ $index }}][existing_image]" value="{{ $tile['image'] ?? '' }}">
                    <div class="form-grid">
                        <div class="form-row">
                            <label class="field-label">Label *</label>
                            <input class="field-input" type="text" name="categories[{{ $index }}][label]" value="{{ old('categories.'.$index.'.label', $tile['label']) }}" required>
                        </div>
                        <div class="form-row">
                            <label class="field-label">Link *</label>
                            <input class="field-input" type="text" name="categories[{{ $index }}][href]" value="{{ old('categories.'.$index.'.href', $tile['href']) }}" required>
                        </div>
                        <div class="form-row">
                            <label class="field-label">Colour tone</label>
                            <input class="field-input" type="text" name="categories[{{ $index }}][tone]" value="{{ old('categories.'.$index.'.tone', $tile['tone'] ?? 'cream') }}">
                        </div>
                        <div class="form-row form-row--full">
                            <label class="field-label">Photo</label>
                            <div class="admin-slide-card__preview">
                                <img src="{{ $tile['image_url'] }}" alt="" width="120" height="75" loading="lazy" decoding="async">
                                <input class="field-input" type="file" name="categories[{{ $index }}][image]" accept="image/jpeg,image/png,image/webp">
                            </div>
                        </div>
                    </div>
                </article>
            @endforeach
        </section>

        <section class="admin-panel admin-panel--spaced">
            <div class="admin-panel__head">
                <h2>Occasion tiles</h2>
                <button class="btn btn--outline btn--sm" type="submit" form="add-occasion-tile">Add tile</button>
            </div>
            <p class="admin-lead">“Birthdays”, “Gifting”, etc. Same photo size as category tiles.</p>
            @foreach ($occasions as $index => $tile)
                <article class="admin-slide-card">
                    <div class="admin-panel__head">
                        <strong>{{ $tile['label'] }}</strong>
                        @if (count($occasions) > 1)
                            <button class="btn btn--ghost btn--sm" type="submit" form="delete-occ-{{ $tile['id'] }}" onclick="return confirm('Remove this tile?')">Remove</button>
                        @endif
                    </div>
                    <input type="hidden" name="occasions[{{ $index }}][id]" value="{{ $tile['id'] }}">
                    <input type="hidden" name="occasions[{{ $index }}][existing_image]" value="{{ $tile['image'] ?? '' }}">
                    <div class="form-grid">
                        <div class="form-row">
                            <label class="field-label">Label *</label>
                            <input class="field-input" type="text" name="occasions[{{ $index }}][label]" value="{{ old('occasions.'.$index.'.label', $tile['label']) }}" required>
                        </div>
                        <div class="form-row">
                            <label class="field-label">Link *</label>
                            <input class="field-input" type="text" name="occasions[{{ $index }}][href]" value="{{ old('occasions.'.$index.'.href', $tile['href']) }}" required>
                        </div>
                        <div class="form-row form-row--full">
                            <label class="field-label">Photo</label>
                            <div class="admin-slide-card__preview">
                                <img src="{{ $tile['image_url'] }}" alt="" width="120" height="75" loading="lazy" decoding="async">
                                <input class="field-input" type="file" name="occasions[{{ $index }}][image]" accept="image/jpeg,image/png,image/webp">
                            </div>
                        </div>
                    </div>
                </article>
            @endforeach
        </section>

        <div class="admin-form__actions admin-form__actions--spaced">
            <button class="btn btn--primary" type="submit">Save homepage tiles</button>
        </div>
    </form>

    <form id="add-category-tile" method="post" action="{{ route('admin.content.categories.store') }}" class="sr-only">@csrf</form>
    <form id="add-occasion-tile" method="post" action="{{ route('admin.content.occasions.store') }}" class="sr-only">@csrf</form>

    @foreach ($categories as $tile)
        @if (count($categories) > 1)
            <form id="delete-cat-{{ $tile['id'] }}" method="post" action="{{ route('admin.content.categories.destroy', $tile['id']) }}" class="sr-only">@csrf @method('DELETE')</form>
        @endif
    @endforeach
    @foreach ($occasions as $tile)
        @if (count($occasions) > 1)
            <form id="delete-occ-{{ $tile['id'] }}" method="post" action="{{ route('admin.content.occasions.destroy', $tile['id']) }}" class="sr-only">@csrf @method('DELETE')</form>
        @endif
    @endforeach
@endsection
