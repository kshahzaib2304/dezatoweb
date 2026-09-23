@extends('layouts.admin')

@section('content')
    <aside class="admin-media-guide" aria-label="Location photo guide">
        <strong>Store locations</strong>
        <ol class="admin-steps">
            <li>Edit each bakery address, hours, phone, and delivery fee.</li>
            <li>Paste a Google Maps link so customers can open directions.</li>
            <li>Use <strong>Add location</strong> for a new shop. Photo: <strong>{{ $mediaGuide['size'] }}</strong>.</li>
        </ol>
        <p class="admin-muted">{{ $mediaGuide['tip'] }}</p>
    </aside>

    <div class="admin-toolbar">
        <p class="admin-lead" style="margin:0">Shown on the Locations page and in the order chooser.</p>
        <form method="post" action="{{ route('admin.locations.store') }}">
            @csrf
            <button class="btn btn--primary" type="submit">Add location</button>
        </form>
    </div>

    <form class="admin-form" method="post" action="{{ route('admin.locations.update') }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        @foreach ($locations as $index => $location)
            <section class="admin-panel admin-panel--spaced">
                <div class="admin-panel__head">
                    <h2>{{ $location['name'] !== '' ? $location['name'] : 'Location '.($index + 1) }}</h2>
                    <div class="admin-toolbar__actions">
                        <label class="check-inline">
                            <input type="checkbox" name="locations[{{ $index }}][active]" value="1" @checked(old('locations.'.$index.'.active', $location['active'] ?? true))>
                            <span>Show on website</span>
                        </label>
                        @if (count($locations) > 1)
                            <button
                                class="btn btn--ghost btn--sm"
                                type="submit"
                                form="delete-location-{{ $location['id'] }}"
                                onclick="return confirm('Remove this location?')"
                            >Remove</button>
                        @endif
                    </div>
                </div>
                <input type="hidden" name="locations[{{ $index }}][id]" value="{{ $location['id'] }}">
                <input type="hidden" name="locations[{{ $index }}][existing_image]" value="{{ $location['image'] ?? '' }}">

                <div class="form-grid">
                    <div class="form-row">
                        <label class="field-label">Name *</label>
                        <input class="field-input" type="text" name="locations[{{ $index }}][name]" value="{{ old('locations.'.$index.'.name', $location['name']) }}" required>
                    </div>
                    <div class="form-row">
                        <label class="field-label">City *</label>
                        <input class="field-input" type="text" name="locations[{{ $index }}][city]" value="{{ old('locations.'.$index.'.city', $location['city']) }}" required>
                    </div>
                    <div class="form-row">
                        <label class="field-label">Region / Province *</label>
                        <input class="field-input" type="text" name="locations[{{ $index }}][region]" value="{{ old('locations.'.$index.'.region', $location['region']) }}" required>
                    </div>
                    <div class="form-row">
                        <label class="field-label">Phone</label>
                        <input class="field-input" type="tel" name="locations[{{ $index }}][phone]" value="{{ old('locations.'.$index.'.phone', $location['phone']) }}" placeholder="Uses bakery phone if blank">
                    </div>
                    <div class="form-row form-row--full">
                        <label class="field-label">Street address *</label>
                        <input class="field-input" type="text" name="locations[{{ $index }}][address]" value="{{ old('locations.'.$index.'.address', $location['address']) }}" required>
                    </div>
                    <div class="form-row form-row--full">
                        <label class="field-label">Opening hours *</label>
                        <input class="field-input" type="text" name="locations[{{ $index }}][hours]" value="{{ old('locations.'.$index.'.hours', $location['hours']) }}" required>
                    </div>
                    <div class="form-row form-row--full">
                        <label class="field-label">Google Maps link</label>
                        <input class="field-input" type="url" name="locations[{{ $index }}][map_url]" value="{{ old('locations.'.$index.'.map_url', $location['map_url'] ?? '') }}" placeholder="https://maps.google.com/...">
                    </div>
                    <div class="form-row form-row--full">
                        <label class="field-label">Services (comma-separated)</label>
                        <input class="field-input" type="text" name="locations[{{ $index }}][services_text]" value="{{ old('locations.'.$index.'.services_text', $location['services_text']) }}" placeholder="Pickup, Delivery, Custom cakes">
                    </div>
                    <div class="form-row">
                        <label class="field-label">Local delivery fee (PKR)</label>
                        <input class="field-input" type="number" min="0" step="1" name="locations[{{ $index }}][delivery_fee]" value="{{ old('locations.'.$index.'.delivery_fee', $location['delivery_fee']) }}">
                    </div>
                    <div class="form-row">
                        <label class="check-inline" style="margin-top:1.6rem">
                            <input type="checkbox" name="locations[{{ $index }}][delivers]" value="1" @checked(old('locations.'.$index.'.delivers', $location['delivers'] ?? true))>
                            <span>Offers local delivery from this store</span>
                        </label>
                    </div>
                    <div class="form-row form-row--full">
                        <label class="field-label">Location photo</label>
                        <div class="admin-slide-card__preview">
                            <img src="{{ $location['image_url'] }}" alt="" width="120" height="75">
                            <input class="field-input" type="file" name="locations[{{ $index }}][image]" accept="image/jpeg,image/png,image/webp">
                        </div>
                    </div>
                </div>
            </section>
        @endforeach

        <div class="admin-form__actions admin-form__actions--spaced">
            <button class="btn btn--primary" type="submit">Save locations</button>
        </div>
    </form>

    @foreach ($locations as $location)
        @if (count($locations) > 1)
            <form id="delete-location-{{ $location['id'] }}" method="post" action="{{ route('admin.locations.destroy', $location['id']) }}" class="sr-only">
                @csrf
                @method('DELETE')
            </form>
        @endif
    @endforeach
@endsection
