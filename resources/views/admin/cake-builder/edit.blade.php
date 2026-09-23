@extends('layouts.admin')

@section('content')
    <aside class="admin-media-guide">
        <strong>How custom cake pricing works</strong>
        <p>Customers pick size + flavours on the website. The total is: <strong>size price + extras</strong>. Enter prices in PKR using numbers only (e.g. <code>1850</code>).</p>
        <p class="admin-muted">
            Reference photos customers upload: {{ $referenceGuide['size'] }} · {{ $referenceGuide['formats'] }} · max {{ $referenceGuide['max'] }}.
        </p>
    </aside>

    <form class="admin-form" method="post" action="{{ route('admin.cake-builder.update') }}">
        @csrf
        @method('PUT')

        <section class="admin-panel">
            <h2>Sizes (base price)</h2>
            <p class="admin-lead">These are the starting prices before flavours and add-ons.</p>
            @foreach ($builder['sizes'] as $i => $row)
                <div class="admin-price-row">
                    <div class="form-row">
                        <label class="field-label" for="sizes-{{ $i }}-label">Size name</label>
                        <input id="sizes-{{ $i }}-label" class="field-input" type="text" name="sizes[{{ $i }}][label]" value="{{ old("sizes.$i.label", $row['label']) }}" required>
                    </div>
                    <div class="form-row">
                        <label class="field-label" for="sizes-{{ $i }}-serves">Serves</label>
                        <input id="sizes-{{ $i }}-serves" class="field-input" type="text" name="sizes[{{ $i }}][serves]" value="{{ old("sizes.$i.serves", $row['serves'] ?? '') }}" placeholder="8–10">
                    </div>
                    <div class="form-row">
                        <label class="field-label" for="sizes-{{ $i }}-price">Price (PKR)</label>
                        <input id="sizes-{{ $i }}-price" class="field-input" type="number" name="sizes[{{ $i }}][price]" value="{{ old("sizes.$i.price", $row['price']) }}" min="0" required>
                    </div>
                </div>
            @endforeach
        </section>

        <section class="admin-panel">
            <h2>Shapes</h2>
            @foreach ($builder['shapes'] as $i => $row)
                <div class="form-row" style="margin-bottom:0.65rem">
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
            'addons' => 'Add-ons',
        ] as $key => $title)
            <section class="admin-panel">
                <h2>{{ $title }}</h2>
                <p class="admin-lead">Extra charge added on top of the size price. Use <strong>0</strong> if included free.</p>
                @foreach (($builder[$key] ?? []) as $i => $row)
                    <div class="admin-price-row">
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

        <section class="admin-panel">
            <h2>Icing colour presets</h2>
            <p class="admin-lead">Customers can also pick any custom colour with the colour picker.</p>
            @foreach (($builder['colors'] ?? []) as $i => $row)
                <div class="admin-price-row">
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

        <div class="admin-form__actions">
            <button class="btn btn--primary" type="submit">Save custom cake prices</button>
        </div>
    </form>
@endsection
