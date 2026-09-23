@extends('layouts.admin')

@section('content')
    <aside class="admin-media-guide">
        <strong>How to edit website pages</strong>
        <ol class="admin-steps">
            <li>Update About / Services introductions (short paragraphs).</li>
            <li>Use <strong>Add</strong> buttons for timeline items, packages, and choice cards.</li>
            <li>Edit Privacy, Terms, and FAQ in plain language — blank line between paragraphs.</li>
            <li>For FAQ: question on the first line, then the answer.</li>
        </ol>
        <p class="admin-muted">No coding needed. Changes appear on the website after you save. Package / order photos ≈ {{ $packageGuide['size'] }}.</p>
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
            <div class="admin-panel__head">
                <h2>About timeline (milestones)</h2>
                <button class="btn btn--outline btn--sm" type="submit" form="add-milestone">Add timeline item</button>
            </div>
            <p class="admin-lead">Shown on the About Us page under “Our journey”.</p>
            @foreach ($milestones as $index => $item)
                <article class="admin-slide-card">
                    <div class="admin-panel__head">
                        <strong>{{ $item['year'] }} — {{ $item['title'] }}</strong>
                        @if (count($milestones) > 1)
                            <button class="btn btn--ghost btn--sm" type="submit" form="delete-milestone-{{ $item['id'] }}" onclick="return confirm('Remove this timeline item?')">Remove</button>
                        @endif
                    </div>
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
                            <textarea class="field-input" name="milestones[{{ $index }}][text]" rows="2" required maxlength="500">{{ old('milestones.'.$index.'.text', $item['text']) }}</textarea>
                        </div>
                    </div>
                </article>
            @endforeach
        </section>

        <section class="admin-panel admin-panel--spaced">
            <div class="admin-panel__head">
                <h2>Services packages</h2>
                <button class="btn btn--outline btn--sm" type="submit" form="add-package">Add package</button>
            </div>
            <p class="admin-lead">Photo ≈ {{ $packageGuide['size'] }} ({{ $packageGuide['ratio'] }}), max {{ $packageGuide['max'] }}. {{ $packageGuide['tip'] }}</p>
            @foreach ($packages as $index => $package)
                <article class="admin-slide-card">
                    <div class="admin-panel__head">
                        <strong>{{ $package['title'] }}</strong>
                        @if (count($packages) > 1)
                            <button class="btn btn--ghost btn--sm" type="submit" form="delete-package-{{ $package['id'] }}" onclick="return confirm('Remove this package?')">Remove</button>
                        @endif
                    </div>
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
                            <textarea class="field-input" name="packages[{{ $index }}][blurb]" rows="2" maxlength="400">{{ old('packages.'.$index.'.blurb', $package['blurb']) }}</textarea>
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

        <section class="admin-panel admin-panel--spaced">
            <div class="admin-panel__head">
                <h2>Cake Customization cards</h2>
                <button class="btn btn--outline btn--sm" type="submit" form="add-customization">Add card</button>
            </div>
            <p class="admin-lead">Text cards on the Cake Customization page (flavours, sizes, themes, etc.). <a href="{{ route('customization') }}" target="_blank" rel="noopener">View page</a></p>
            @foreach ($customizationOptions as $index => $option)
                <article class="admin-slide-card">
                    <div class="admin-panel__head">
                        <strong>{{ $option['title'] }}</strong>
                        @if (count($customizationOptions) > 1)
                            <button class="btn btn--ghost btn--sm" type="submit" form="delete-customization-{{ $option['id'] }}" onclick="return confirm('Remove this card?')">Remove</button>
                        @endif
                    </div>
                    <input type="hidden" name="customization[{{ $index }}][id]" value="{{ $option['id'] }}">
                    <div class="form-grid">
                        <div class="form-row">
                            <label class="field-label">Title *</label>
                            <input class="field-input" type="text" name="customization[{{ $index }}][title]" value="{{ old('customization.'.$index.'.title', $option['title']) }}" required maxlength="120">
                        </div>
                        <div class="form-row form-row--full">
                            <label class="field-label">Short description *</label>
                            <textarea class="field-input" name="customization[{{ $index }}][text]" rows="2" required maxlength="400">{{ old('customization.'.$index.'.text', $option['text']) }}</textarea>
                        </div>
                    </div>
                </article>
            @endforeach
        </section>

        <section class="admin-panel admin-panel--spaced">
            <div class="admin-panel__head">
                <h2>Order page choice cards</h2>
                <button class="btn btn--outline btn--sm" type="submit" form="add-order-card">Add card</button>
            </div>
            <p class="admin-lead">
                Pickup / Courier / Catering style cards on the Order page.
                Photo ≈ {{ $orderCardGuide['size'] }} ({{ $orderCardGuide['ratio'] }}), max {{ $orderCardGuide['max'] }}.
                <a href="{{ route('order.start') }}" target="_blank" rel="noopener">View Order page</a>
            </p>
            @foreach ($orderOptions as $index => $card)
                <article class="admin-slide-card">
                    <div class="admin-panel__head">
                        <strong>{{ $card['title'] }}</strong>
                        @if (count($orderOptions) > 1)
                            <button class="btn btn--ghost btn--sm" type="submit" form="delete-order-card-{{ $card['id'] }}" onclick="return confirm('Remove this card?')">Remove</button>
                        @endif
                    </div>
                    <input type="hidden" name="order_cards[{{ $index }}][id]" value="{{ $card['id'] }}">
                    <input type="hidden" name="order_cards[{{ $index }}][existing_image]" value="{{ $card['image'] ?? '' }}">
                    <div class="form-grid">
                        <div class="form-row">
                            <label class="field-label">Title *</label>
                            <input class="field-input" type="text" name="order_cards[{{ $index }}][title]" value="{{ old('order_cards.'.$index.'.title', $card['title']) }}" required maxlength="120">
                        </div>
                        <div class="form-row">
                            <label class="field-label">Button label *</label>
                            <input class="field-input" type="text" name="order_cards[{{ $index }}][cta]" value="{{ old('order_cards.'.$index.'.cta', $card['cta']) }}" required maxlength="60" placeholder="Continue">
                        </div>
                        <div class="form-row">
                            <label class="field-label">Goes to *</label>
                            <select class="field-input" name="order_cards[{{ $index }}][route]" required>
                                @foreach ($orderRouteOptions as $routeKey => $routeLabel)
                                    <option value="{{ $routeKey }}" @selected(old('order_cards.'.$index.'.route', $card['route']) === $routeKey)>{{ $routeLabel }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-row form-row--full">
                            <label class="field-label">Short description *</label>
                            <textarea class="field-input" name="order_cards[{{ $index }}][text]" rows="2" required maxlength="400">{{ old('order_cards.'.$index.'.text', $card['text']) }}</textarea>
                        </div>
                        <div class="form-row form-row--full">
                            <label class="field-label">Photo</label>
                            <div class="admin-slide-card__preview">
                                <img src="{{ $card['image_url'] }}" alt="" width="120" height="75">
                                <input class="field-input" type="file" name="order_cards[{{ $index }}][image]" accept="image/jpeg,image/png,image/webp">
                            </div>
                            <p class="field-hint">{{ $orderCardGuide['tip'] }}</p>
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

    <form id="add-milestone" method="post" action="{{ route('admin.pages.milestones.store') }}" class="sr-only">@csrf</form>
    <form id="add-package" method="post" action="{{ route('admin.pages.packages.store') }}" class="sr-only">@csrf</form>
    <form id="add-customization" method="post" action="{{ route('admin.pages.customization.store') }}" class="sr-only">@csrf</form>
    <form id="add-order-card" method="post" action="{{ route('admin.pages.order-cards.store') }}" class="sr-only">@csrf</form>

    @foreach ($milestones as $item)
        @if (count($milestones) > 1)
            <form id="delete-milestone-{{ $item['id'] }}" method="post" action="{{ route('admin.pages.milestones.destroy', $item['id']) }}" class="sr-only">@csrf @method('DELETE')</form>
        @endif
    @endforeach
    @foreach ($packages as $package)
        @if (count($packages) > 1)
            <form id="delete-package-{{ $package['id'] }}" method="post" action="{{ route('admin.pages.packages.destroy', $package['id']) }}" class="sr-only">@csrf @method('DELETE')</form>
        @endif
    @endforeach
    @foreach ($customizationOptions as $option)
        @if (count($customizationOptions) > 1)
            <form id="delete-customization-{{ $option['id'] }}" method="post" action="{{ route('admin.pages.customization.destroy', $option['id']) }}" class="sr-only">@csrf @method('DELETE')</form>
        @endif
    @endforeach
    @foreach ($orderOptions as $card)
        @if (count($orderOptions) > 1)
            <form id="delete-order-card-{{ $card['id'] }}" method="post" action="{{ route('admin.pages.order-cards.destroy', $card['id']) }}" class="sr-only">@csrf @method('DELETE')</form>
        @endif
    @endforeach
@endsection
