@extends('layouts.admin')

@section('content')
    <aside class="admin-media-guide">
        <strong>Storefront chrome</strong>
        <ol class="admin-steps">
            <li>Edit page banners (Menu, About, Cart…), homepage wording, and fulfillment tiles.</li>
            <li>Update product “Good to know” notes and cart / checkout headlines.</li>
            <li>Manage Karachi delivery areas mapped to each bakery counter.</li>
        </ol>
        <p class="admin-muted">Hero photos ≈ {{ $heroGuide['size'] }} · {{ $heroGuide['formats'] }} · max {{ $heroGuide['max'] }}. Tile photos ≈ {{ $tileGuide['size'] }}.</p>
    </aside>

    <form class="admin-form" method="post" action="{{ route('admin.chrome.update') }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <section class="admin-panel">
            <h2>Page heroes</h2>
            <p class="admin-lead">Banner text and photo at the top of each public page.</p>
            @foreach ($heroes as $hero)
                <article class="admin-slide-card">
                    <div class="admin-panel__head">
                        <strong>{{ $hero['label'] }}</strong>
                        <span class="admin-muted">{{ $hero['key'] }}</span>
                    </div>
                    <input type="hidden" name="heroes[{{ $hero['key'] }}][existing_image]" value="{{ $hero['image'] }}">
                    <div class="form-grid">
                        <div class="form-row">
                            <label class="field-label">Eyebrow</label>
                            <input class="field-input" type="text" name="heroes[{{ $hero['key'] }}][eyebrow]" value="{{ old('heroes.'.$hero['key'].'.eyebrow', $hero['eyebrow']) }}">
                        </div>
                        <div class="form-row">
                            <label class="field-label">Title *</label>
                            <input class="field-input" type="text" name="heroes[{{ $hero['key'] }}][title]" value="{{ old('heroes.'.$hero['key'].'.title', $hero['title']) }}" required>
                        </div>
                        <div class="form-row form-row--full">
                            <label class="field-label">Supporting text</label>
                            <input class="field-input" type="text" name="heroes[{{ $hero['key'] }}][text]" value="{{ old('heroes.'.$hero['key'].'.text', $hero['text']) }}">
                        </div>
                        <div class="form-row form-row--full">
                            <label class="field-label">Photo</label>
                            <div class="admin-slide-card__preview">
                                <img src="{{ $hero['image_url'] }}" alt="" width="160" height="100" loading="lazy">
                                <input class="field-input" type="file" name="heroes[{{ $hero['key'] }}][image]" accept="image/jpeg,image/webp,image/png">
                            </div>
                            <p class="field-hint">{{ $heroGuide['size'] }} · {{ $heroGuide['formats'] }} · max {{ $heroGuide['max'] }}</p>
                        </div>
                        <div class="form-row">
                            <label class="check-inline">
                                <input type="hidden" name="heroes[{{ $hero['key'] }}][compact]" value="0">
                                <input type="checkbox" name="heroes[{{ $hero['key'] }}][compact]" value="1" @checked(old('heroes.'.$hero['key'].'.compact', $hero['compact']))>
                                <span>Compact banner height</span>
                            </label>
                        </div>
                    </div>
                </article>
            @endforeach
        </section>

        <section class="admin-panel admin-panel--spaced">
            <h2>Homepage sections</h2>
            <div class="form-grid">
                @foreach ([
                    'hero_cta_primary' => 'Hero CTA (menu ready)',
                    'hero_cta_start' => 'Hero CTA (start order)',
                    'hero_cta_secondary' => 'Hero secondary CTA',
                    'favorites_title' => 'Favourites title',
                    'favorites_link' => 'Favourites link label',
                    'categories_title' => 'Categories title',
                    'categories_text' => 'Categories supporting text',
                    'ways_title' => 'How you’ll get it - title',
                    'ways_text' => 'How you’ll get it - text',
                    'occasions_title' => 'Occasions title',
                    'occasions_text' => 'Occasions text',
                    'story_kicker' => 'Story kicker',
                    'story_title' => 'Story title',
                    'story_text' => 'Story paragraph',
                    'story_cta' => 'Story button',
                    'cater_title' => 'Catering band title',
                    'cater_text' => 'Catering band text',
                    'cater_cta' => 'Catering button',
                    'news_title' => 'Newsletter title',
                    'news_text' => 'Newsletter text',
                    'news_placeholder' => 'Newsletter email placeholder',
                    'news_cta' => 'Newsletter button',
                ] as $field => $label)
                    <div class="form-row {{ str_contains($field, 'text') || str_contains($field, 'paragraph') || $field === 'story_text' || $field === 'categories_text' ? 'form-row--full' : '' }}">
                        <label class="field-label" for="home-{{ $field }}">{{ $label }}</label>
                        <input id="home-{{ $field }}" class="field-input" type="text" name="home[{{ $field }}]" value="{{ old('home.'.$field, $home[$field]) }}" required>
                    </div>
                @endforeach
            </div>

            <input type="hidden" name="home[existing_story_image]" value="{{ $home['story_image'] }}">
            <input type="hidden" name="home[existing_about_intro_image]" value="{{ $home['about_intro_image'] }}">
            <div class="form-grid">
                <div class="form-row form-row--full">
                    <label class="field-label">Homepage story photo</label>
                    <div class="admin-slide-card__preview">
                        <img src="{{ $home['story_image_url'] }}" alt="" width="120" height="150" loading="lazy">
                        <input class="field-input" type="file" name="home[story_image]" accept="image/jpeg,image/webp,image/png">
                    </div>
                    <p class="field-hint">Portrait crop ≈ 800 × 1000 · JPG/WebP · max 3 MB</p>
                </div>
                <div class="form-row form-row--full">
                    <label class="field-label">About page intro photo</label>
                    <div class="admin-slide-card__preview">
                        <img src="{{ $home['about_intro_image_url'] }}" alt="" width="120" height="120" loading="lazy">
                        <input class="field-input" type="file" name="home[about_intro_image]" accept="image/jpeg,image/webp,image/png">
                    </div>
                    <p class="field-hint">{{ $tileGuide['size'] }} · {{ $tileGuide['formats'] }} · max {{ $tileGuide['max'] }}</p>
                </div>
            </div>

            <h3 class="admin-section-title">How you’ll get it tiles</h3>
            @foreach ($home['ways'] as $index => $way)
                <article class="admin-slide-card">
                    <strong>{{ ucfirst($way['action']) }} tile</strong>
                    <input type="hidden" name="home[ways][{{ $index }}][existing_image]" value="{{ $way['image'] }}">
                    <div class="form-grid">
                        <div class="form-row">
                            <label class="field-label">Title *</label>
                            <input class="field-input" type="text" name="home[ways][{{ $index }}][title]" value="{{ old('home.ways.'.$index.'.title', $way['title']) }}" required>
                        </div>
                        <div class="form-row form-row--full">
                            <label class="field-label">Text *</label>
                            <input class="field-input" type="text" name="home[ways][{{ $index }}][text]" value="{{ old('home.ways.'.$index.'.text', $way['text']) }}" required>
                        </div>
                        <div class="form-row form-row--full">
                            <label class="field-label">Photo</label>
                            <div class="admin-slide-card__preview">
                                <img src="{{ $way['image_url'] }}" alt="" width="100" height="100" loading="lazy">
                                <input class="field-input" type="file" name="home[ways][{{ $index }}][image]" accept="image/jpeg,image/webp,image/png">
                            </div>
                            <p class="field-hint">{{ $tileGuide['size'] }} · {{ $tileGuide['formats'] }} · max {{ $tileGuide['max'] }}</p>
                        </div>
                    </div>
                </article>
            @endforeach
        </section>

        <section class="admin-panel admin-panel--spaced">
            <h2>Product notes, cart &amp; checkout</h2>
            <div class="form-grid">
                @foreach ([
                    'product_notes_title' => 'Product notes heading',
                    'product_notes_fresh' => 'Freshness note',
                    'product_notes_allergy' => 'Allergy / kitchen note',
                    'product_notes_custom_cta' => 'Custom cake link label',
                    'product_notes_custom_text' => 'Text after custom cake link',
                    'cart_empty_title' => 'Empty cart title',
                    'cart_empty_text' => 'Empty cart text',
                    'cart_empty_cta' => 'Empty cart button',
                    'cart_summary_title' => 'Cart summary title',
                    'cart_checkout_cta' => 'Cart checkout button',
                    'cart_continue_cta' => 'Cart continue button',
                    'cart_upsell_title' => 'Cart upsell title',
                    'cart_upsell_text' => 'Cart upsell text',
                    'checkout_title' => 'Checkout title',
                    'checkout_lede' => 'Checkout intro',
                    'checkout_contact_title' => 'Checkout contact heading',
                    'checkout_phone_hint' => 'Checkout phone hint',
                    'header_order_cta' => 'Header Order button',
                    'about_story_title' => 'About story heading',
                    'about_journey_title' => 'About timeline title',
                    'about_journey_text' => 'About timeline text',
                    'about_cta_title' => 'About CTA title',
                    'about_cta_text' => 'About CTA text',
                    'about_cta_primary' => 'About primary button',
                    'about_cta_secondary' => 'About secondary button',
                ] as $field => $label)
                    <div class="form-row form-row--full">
                        <label class="field-label" for="copy-{{ $field }}">{{ $label }}</label>
                        @if (
                            (str_contains($field, 'notes_') && $field !== 'product_notes_title')
                            || str_contains($field, 'lede')
                            || str_contains($field, 'allergy')
                            || str_contains($field, 'fresh')
                            || str_contains($field, 'cta_text')
                            || str_contains($field, 'journey_text')
                            || str_contains($field, 'custom_text')
                        )
                            <textarea id="copy-{{ $field }}" class="field-input field-textarea" name="copy[{{ $field }}]" rows="3" required>{{ old('copy.'.$field, $copy[$field]) }}</textarea>
                        @else
                            <input id="copy-{{ $field }}" class="field-input" type="text" name="copy[{{ $field }}]" value="{{ old('copy.'.$field, $copy[$field]) }}" required>
                        @endif
                    </div>
                @endforeach
            </div>
        </section>

        <section class="admin-panel admin-panel--spaced">
            <div class="admin-panel__head">
                <h2>Karachi delivery areas</h2>
                <button class="btn btn--outline btn--sm" type="submit" form="add-area">Add area</button>
            </div>
            <p class="admin-lead">Shown in the order chooser. Each area maps to a bakery counter for delivery routing.</p>
            @foreach ($areas as $index => $area)
                <article class="admin-slide-card">
                    <div class="admin-panel__head">
                        <strong>{{ $area['label'] }}</strong>
                        @if (count($areas) > 1)
                            <button class="btn btn--ghost btn--sm" type="submit" form="delete-area-{{ $area['id'] }}" onclick="return confirm('Remove this area?')">Remove</button>
                        @endif
                    </div>
                    <input type="hidden" name="areas[{{ $index }}][id]" value="{{ $area['id'] }}">
                    <div class="form-grid">
                        <div class="form-row">
                            <label class="field-label">Area name *</label>
                            <input class="field-input" type="text" name="areas[{{ $index }}][label]" value="{{ old('areas.'.$index.'.label', $area['label']) }}" required>
                        </div>
                        <div class="form-row">
                            <label class="field-label">Bakery counter *</label>
                            <select class="field-input" name="areas[{{ $index }}][location_id]" required>
                                @foreach ($locations as $location)
                                    <option value="{{ $location['id'] }}" @selected(old('areas.'.$index.'.location_id', $area['location_id']) === $location['id'])>{{ $location['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </article>
            @endforeach
        </section>

        <div class="admin-form__actions admin-form__actions--spaced">
            <button class="btn btn--primary" type="submit">Save storefront chrome</button>
        </div>
    </form>

    <form id="add-area" method="post" action="{{ route('admin.chrome.areas.store') }}" class="sr-only">@csrf</form>
    @foreach ($areas as $area)
        @if (count($areas) > 1)
            <form id="delete-area-{{ $area['id'] }}" method="post" action="{{ route('admin.chrome.areas.destroy', $area['id']) }}" class="sr-only">@csrf @method('DELETE')</form>
        @endif
    @endforeach
@endsection
