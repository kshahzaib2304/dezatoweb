@extends('layouts.admin')

@section('content')
    <aside class="admin-media-guide">
        <strong>How delivery times work</strong>
        <ol class="admin-steps">
            <li>Write each time window on its <strong>own line</strong> (e.g. <code>4:00 PM  -  6:00 PM</code>).</li>
            <li>Set how many hours of notice you need before the chosen date (e.g. <strong>4</strong> means customers cannot pick a slot sooner than 4 hours from now).</li>
            <li>These windows appear on the checkout page for pickup and delivery.</li>
        </ol>
    </aside>

    <section class="admin-panel">
        <form class="admin-form" method="post" action="{{ route('admin.schedule.update') }}">
            @csrf
            @method('PUT')
            <div class="form-grid">
                <div class="form-row form-row--full">
                    <label class="field-label" for="slots">Time windows (one per line) *</label>
                    <textarea id="slots" class="field-input" name="slots" rows="8" required>{{ old('slots', $slotsText) }}</textarea>
                </div>
                <div class="form-row">
                    <label class="field-label" for="min_hours">Minimum notice (hours) *</label>
                    <input id="min_hours" class="field-input" type="number" name="min_hours" value="{{ old('min_hours', $minHours) }}" min="0" max="168" required>
                    <p class="field-hint">0 = same-day dates are allowed.</p>
                </div>
                <div class="form-row form-row--full">
                    <label class="field-label" for="note">Note shown to customers</label>
                    <input id="note" class="field-input" type="text" name="note" value="{{ old('note', $note) }}" maxlength="300">
                </div>
            </div>
            <div class="admin-form__actions">
                <button class="btn btn--primary" type="submit">Save delivery times</button>
            </div>
        </form>
    </section>
@endsection
