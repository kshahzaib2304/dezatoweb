@extends('layouts.admin')

@section('content')
    <aside class="admin-media-guide">
        <strong>How promo codes work</strong>
        <ol class="admin-steps">
            <li>Create a code (e.g. <code>DEZATO10</code>).</li>
            <li>Choose <strong>Percent off</strong> (e.g. 10) or <strong>Fixed PKR off</strong> (e.g. 500).</li>
            <li>Customers type the code at checkout. The discount applies to the cart subtotal (before delivery fee).</li>
        </ol>
    </aside>

    <section class="admin-panel">
            <h2>Add a promo code</h2>
            <form class="admin-form" method="post" action="{{ route('admin.promos.store') }}">
                @csrf
                <div class="form-grid">
                    <div class="form-row">
                        <label class="field-label" for="code">Code *</label>
                        <input id="code" class="field-input field-input--code" type="text" name="code" value="{{ old('code') }}" required maxlength="40" placeholder="DEZATO10">
                    </div>
                    <div class="form-row">
                        <label class="field-label" for="label">Internal note</label>
                        <input id="label" class="field-input" type="text" name="label" value="{{ old('label') }}" placeholder="Ramadan launch offer">
                    </div>
                    <div class="form-row">
                        <label class="field-label" for="type">Discount type *</label>
                        <select id="type" class="field-input" name="type" required>
                            <option value="percent" @selected(old('type', 'percent') === 'percent')>Percent off (%)</option>
                            <option value="fixed" @selected(old('type') === 'fixed')>Fixed PKR off</option>
                        </select>
                    </div>
                    <div class="form-row">
                        <label class="field-label" for="value">Amount *</label>
                        <input id="value" class="field-input" type="number" name="value" value="{{ old('value', 10) }}" min="1" required>
                        <p class="field-hint">For percent: 10 means 10%. For fixed: 500 means ₨ 500 off.</p>
                    </div>
                    <div class="form-row">
                        <label class="field-label" for="min_subtotal">Minimum order (PKR)</label>
                        <input id="min_subtotal" class="field-input" type="number" name="min_subtotal" value="{{ old('min_subtotal') }}" min="0" placeholder="Optional">
                    </div>
                    <div class="form-row">
                        <label class="field-label" for="max_uses">Max uses</label>
                        <input id="max_uses" class="field-input" type="number" name="max_uses" value="{{ old('max_uses') }}" min="1" placeholder="Unlimited if blank">
                    </div>
                    <div class="form-row">
                        <label class="field-label" for="starts_at">Starts</label>
                        <input id="starts_at" class="field-input" type="datetime-local" name="starts_at" value="{{ old('starts_at') }}">
                    </div>
                    <div class="form-row">
                        <label class="field-label" for="ends_at">Ends</label>
                        <input id="ends_at" class="field-input" type="datetime-local" name="ends_at" value="{{ old('ends_at') }}">
                    </div>
                    <div class="form-row form-row--full">
                        <label class="check-inline">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1" @checked(filter_var(old('is_active', true), FILTER_VALIDATE_BOOLEAN))>
                            <span>Active (customers can use it)</span>
                        </label>
                    </div>
                </div>
                <button class="btn btn--primary" type="submit">Create promo code</button>
            </form>
        </section>

        <section class="admin-panel">
            <h2>Your promo codes</h2>
            @if ($promos->isEmpty())
                <p class="admin-empty">No promo codes yet.</p>
            @else
                <div class="admin-table-wrap">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Discount</th>
                                <th>Used</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($promos as $promo)
                                <tr>
                                    <td>
                                        <strong>{{ $promo->code }}</strong>
                                        @if ($promo->label)
                                            <div class="admin-muted">{{ $promo->label }}</div>
                                        @endif
                                    </td>
                                    <td>{{ $promo->discountDescription() }}</td>
                                    <td>
                                        {{ $promo->used_count }}
                                        @if ($promo->max_uses)
                                            / {{ $promo->max_uses }}
                                        @endif
                                    </td>
                                    <td>
                                        @if ($promo->is_active)
                                            <span class="admin-badge admin-badge--ok">Active</span>
                                        @else
                                            <span class="admin-badge">Off</span>
                                        @endif
                                    </td>
                                    <td class="admin-actions">
                                        <form method="post" action="{{ route('admin.promos.toggle', $promo) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button class="text-btn" type="submit">{{ $promo->is_active ? 'Turn off' : 'Turn on' }}</button>
                                        </form>
                                        <form method="post" action="{{ route('admin.promos.destroy', $promo) }}" onsubmit="return confirm('Delete this promo code?');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="text-btn text-btn--danger" type="submit">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="admin-pagination">{{ $promos->links() }}                </div>
            @endif
        </section>
@endsection
