@extends('layouts.admin')

@section('content')
    <aside class="admin-media-guide">
        <strong>How custom cake pricing works</strong>
        <ol class="admin-steps">
            <li>Edit <strong>Bakery rules</strong> (fondant, heart cakes, etc.) — shown to customers.</li>
            <li>Set <strong>size prices</strong> (starting price) and <strong>add-ons</strong> (extra charges).</li>
            <li>For flowers / stems / macarons, choose billing type <strong>Per item</strong> so customers pick a quantity.</li>
            <li>Use numbers only for prices (e.g. <code>1800</code> = ₨ 1,800).</li>
        </ol>
        <p class="admin-muted">
            Customer reference photos: {{ $referenceGuide['size'] }} · {{ $referenceGuide['formats'] }} · max {{ $referenceGuide['max'] }}.
            {{ $referenceGuide['tip'] }}
        </p>
        <form method="post" action="{{ route('admin.cake-builder.reset') }}" onsubmit="return confirm('Replace everything with bakery defaults (sizes, add-ons, guidelines)? Your edits will be overwritten.');">
            @csrf
            <button class="btn btn--outline btn--sm" type="submit">Restore bakery defaults</button>
        </form>
    </aside>

    <form class="admin-form" method="post" action="{{ route('admin.cake-builder.update') }}">
        @csrf
        @method('PUT')

        <section class="admin-panel">
            <div class="admin-panel__head">
                <h2>Bakery rules (shown to customers)</h2>
                <button class="btn btn--outline btn--sm" type="submit" form="add-guideline">Add rule</button>
            </div>
            <p class="admin-lead">Plain language tips about fondant, heart cakes, letter cakes, edible print, etc.</p>
            @foreach (($builder['guidelines'] ?? []) as $i => $line)
                <article class="admin-slide-card">
                    <div class="admin-panel__head">
                        <strong>Rule {{ $i + 1 }}</strong>
                        @if (count($builder['guidelines']) > 1)
                            <button class="btn btn--ghost btn--sm" type="submit" form="delete-guideline-{{ $i }}" onclick="return confirm('Remove this rule?')">Remove</button>
                        @endif
                    </div>
                    <div class="form-row form-row--full">
                        <label class="field-label" for="guideline-{{ $i }}">Text *</label>
                        <textarea id="guideline-{{ $i }}" class="field-input" name="guidelines[{{ $i }}]" rows="2" required maxlength="400">{{ old('guidelines.'.$i, $line) }}</textarea>
                    </div>
                </article>
            @endforeach
        </section>

        <section class="admin-panel admin-panel--spaced">
            <div class="admin-panel__head">
                <h2>Sizes (base price)</h2>
                <button class="btn btn--outline btn--sm" type="submit" form="add-size">Add size</button>
            </div>
            <p class="admin-lead">Starting prices before flavours and add-ons. Fondant work usually from 4 lb+.</p>
            @foreach ($builder['sizes'] as $i => $row)
                <article class="admin-slide-card">
                    <div class="admin-panel__head">
                        <strong>{{ $row['label'] }}</strong>
                        @if (count($builder['sizes']) > 1)
                            <button class="btn btn--ghost btn--sm" type="submit" form="delete-size-{{ $row['id'] }}" onclick="return confirm('Remove this size?')">Remove</button>
                        @endif
                    </div>
                    <input type="hidden" name="sizes[{{ $i }}][id]" value="{{ $row['id'] }}">
                    <div class="admin-price-row">
                        <div class="form-row">
                            <label class="field-label" for="sizes-{{ $i }}-label">Size name *</label>
                            <input id="sizes-{{ $i }}-label" class="field-input" type="text" name="sizes[{{ $i }}][label]" value="{{ old("sizes.$i.label", $row['label']) }}" required>
                        </div>
                        <div class="form-row">
                            <label class="field-label" for="sizes-{{ $i }}-serves">Serves</label>
                            <input id="sizes-{{ $i }}-serves" class="field-input" type="text" name="sizes[{{ $i }}][serves]" value="{{ old("sizes.$i.serves", $row['serves'] ?? '') }}" placeholder="8–10">
                        </div>
                        <div class="form-row">
                            <label class="field-label" for="sizes-{{ $i }}-price">Price (PKR) *</label>
                            <input id="sizes-{{ $i }}-price" class="field-input" type="number" name="sizes[{{ $i }}][price]" value="{{ old("sizes.$i.price", $row['price']) }}" min="0" required>
                        </div>
                    </div>
                </article>
            @endforeach
        </section>

        <section class="admin-panel admin-panel--spaced">
            <h2>Shapes</h2>
            <p class="admin-lead">Heart, number, and letter cakes have bakery rules above — keep those shape names clear.</p>
            @foreach ($builder['shapes'] as $i => $row)
                <div class="form-row" style="margin-bottom:0.65rem">
                    <input type="hidden" name="shapes[{{ $i }}][id]" value="{{ $row['id'] }}">
                    <label class="field-label" for="shapes-{{ $i }}-label">Shape {{ $i + 1 }}</label>
                    <input id="shapes-{{ $i }}-label" class="field-input" type="text" name="shapes[{{ $i }}][label]" value="{{ old("shapes.$i.label", $row['label']) }}" required>
                </div>
            @endforeach
        </section>

        @foreach ([
            'bases' => 'Cake bases',
            'fillings' => 'Fillings',
            'frostings' => 'Frostings',
            'diets' => 'Dietary extras',
        ] as $key => $title)
            <section class="admin-panel admin-panel--spaced">
                <h2>{{ $title }}</h2>
                <p class="admin-lead">Extra charge on top of the size price. Use <strong>0</strong> if included free.</p>
                @foreach (($builder[$key] ?? []) as $i => $row)
                    <div class="admin-price-row">
                        <input type="hidden" name="{{ $key }}[{{ $i }}][id]" value="{{ $row['id'] }}">
                        <div class="form-row">
                            <label class="field-label" for="{{ $key }}-{{ $i }}-label">Name</label>
                            <input id="{{ $key }}-{{ $i }}-label" class="field-input" type="text" name="{{ $key }}[{{ $i }}][label]" value="{{ old("$key.$i.label", $row['label']) }}" required>
                        </div>
                        <div class="form-row">
                            <label class="field-label" for="{{ $key }}-{{ $i }}-price">Extra (PKR)</label>
                            <input id="{{ $key }}-{{ $i }}-price" class="field-input" type="number" name="{{ $key }}[{{ $i }}][price]" value="{{ old("$key.$i.price", $row['price'] ?? 0) }}" min="0" required>
                        </div>
                    </div>
                @endforeach
            </section>
        @endforeach

        <section class="admin-panel admin-panel--spaced">
            <div class="admin-panel__head">
                <h2>Add-ons &amp; décor prices</h2>
                <button class="btn btn--outline btn--sm" type="submit" form="add-addon">Add add-on</button>
            </div>
            <p class="admin-lead">
                <strong>Once</strong> = flat fee (ribbon, topper). <strong>Per item</strong> = customer chooses how many (flowers, stems, macarons).
            </p>
            @foreach (($builder['addons'] ?? []) as $i => $row)
                <article class="admin-slide-card">
                    <div class="admin-panel__head">
                        <strong>{{ $row['label'] }}</strong>
                        @if (count($builder['addons']) > 1)
                            <button class="btn btn--ghost btn--sm" type="submit" form="delete-addon-{{ $row['id'] }}" onclick="return confirm('Remove this add-on?')">Remove</button>
                        @endif
                    </div>
                    <input type="hidden" name="addons[{{ $i }}][id]" value="{{ $row['id'] }}">
                    <div class="form-grid">
                        <div class="form-row">
                            <label class="field-label" for="addons-{{ $i }}-label">Name *</label>
                            <input id="addons-{{ $i }}-label" class="field-input" type="text" name="addons[{{ $i }}][label]" value="{{ old("addons.$i.label", $row['label']) }}" required>
                        </div>
                        <div class="form-row">
                            <label class="field-label" for="addons-{{ $i }}-price">Price (PKR) *</label>
                            <input id="addons-{{ $i }}-price" class="field-input" type="number" name="addons[{{ $i }}][price]" value="{{ old("addons.$i.price", $row['price'] ?? 0) }}" min="0" required>
                        </div>
                        <div class="form-row">
                            <label class="field-label" for="addons-{{ $i }}-billing">Billing *</label>
                            <select id="addons-{{ $i }}-billing" class="field-input" name="addons[{{ $i }}][billing]" required>
                                <option value="flat" @selected(old("addons.$i.billing", $row['billing'] ?? 'flat') === 'flat')>Once (flat fee)</option>
                                <option value="per_unit" @selected(old("addons.$i.billing", $row['billing'] ?? '') === 'per_unit')>Per item (quantity)</option>
                            </select>
                        </div>
                        <div class="form-row">
                            <label class="field-label" for="addons-{{ $i }}-unit">Unit word</label>
                            <input id="addons-{{ $i }}-unit" class="field-input" type="text" name="addons[{{ $i }}][unit_label]" value="{{ old("addons.$i.unit_label", $row['unit_label'] ?? '') }}" placeholder="flower / stem / macaron">
                        </div>
                        <div class="form-row">
                            <label class="field-label" for="addons-{{ $i }}-max">Max quantity</label>
                            <input id="addons-{{ $i }}-max" class="field-input" type="number" name="addons[{{ $i }}][max_qty]" value="{{ old("addons.$i.max_qty", $row['max_qty'] ?? 12) }}" min="1" max="50">
                        </div>
                        <div class="form-row form-row--full">
                            <label class="field-label" for="addons-{{ $i }}-hint">Customer note (optional)</label>
                            <input id="addons-{{ $i }}-hint" class="field-input" type="text" name="addons[{{ $i }}][hint]" value="{{ old("addons.$i.hint", $row['hint'] ?? '') }}" maxlength="240" placeholder="e.g. Subject to availability">
                        </div>
                    </div>
                </article>
            @endforeach
        </section>

        <section class="admin-panel admin-panel--spaced">
            <h2>Icing colour presets</h2>
            <p class="admin-lead">Customers can also pick any custom colour with the colour picker.</p>
            @foreach (($builder['colors'] ?? []) as $i => $row)
                <div class="admin-price-row">
                    <input type="hidden" name="colors[{{ $i }}][id]" value="{{ $row['id'] }}">
                    <div class="form-row">
                        <label class="field-label" for="colors-{{ $i }}-label">Name</label>
                        <input id="colors-{{ $i }}-label" class="field-input" type="text" name="colors[{{ $i }}][label]" value="{{ old("colors.$i.label", $row['label']) }}" required>
                    </div>
                    <div class="form-row">
                        <label class="field-label" for="colors-{{ $i }}-hex">Colour code</label>
                        <input id="colors-{{ $i }}-hex" class="field-input" type="text" name="colors[{{ $i }}][hex]" value="{{ old("colors.$i.hex", $row['hex']) }}" required placeholder="#f7f1e8">
                    </div>
                </div>
            @endforeach
        </section>

        <div class="admin-form__actions admin-form__actions--spaced">
            <button class="btn btn--primary" type="submit">Save custom cake prices</button>
            <a class="btn btn--ghost" href="{{ route('builder.show') }}" target="_blank" rel="noopener">Preview builder</a>
        </div>
    </form>

    <form id="add-guideline" method="post" action="{{ route('admin.cake-builder.guidelines.store') }}" class="sr-only">@csrf</form>
    <form id="add-size" method="post" action="{{ route('admin.cake-builder.sizes.store') }}" class="sr-only">@csrf</form>
    <form id="add-addon" method="post" action="{{ route('admin.cake-builder.addons.store') }}" class="sr-only">@csrf</form>

    @foreach (($builder['guidelines'] ?? []) as $i => $line)
        @if (count($builder['guidelines']) > 1)
            <form id="delete-guideline-{{ $i }}" method="post" action="{{ route('admin.cake-builder.guidelines.destroy', $i) }}" class="sr-only">@csrf @method('DELETE')</form>
        @endif
    @endforeach
    @foreach ($builder['sizes'] as $row)
        @if (count($builder['sizes']) > 1)
            <form id="delete-size-{{ $row['id'] }}" method="post" action="{{ route('admin.cake-builder.sizes.destroy', $row['id']) }}" class="sr-only">@csrf @method('DELETE')</form>
        @endif
    @endforeach
    @foreach (($builder['addons'] ?? []) as $row)
        @if (count($builder['addons']) > 1)
            <form id="delete-addon-{{ $row['id'] }}" method="post" action="{{ route('admin.cake-builder.addons.destroy', $row['id']) }}" class="sr-only">@csrf @method('DELETE')</form>
        @endif
    @endforeach
@endsection
