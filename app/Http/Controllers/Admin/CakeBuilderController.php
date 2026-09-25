<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\CakeBuilder;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CakeBuilderController extends Controller
{
    public function edit(): View
    {
        return view('admin.cake-builder.edit', [
            'title' => 'Custom cake prices | Dezato Admin',
            'heading' => 'Custom cake prices',
            'active' => 'cake-builder',
            'nav' => config('dezato_admin.nav'),
            'builder' => CakeBuilder::config(),
            'referenceGuide' => config('dezato_admin.media.reference'),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'guidelines' => ['nullable', 'array'],
            'guidelines.*' => ['nullable', 'string', 'max:400'],
            'sizes' => ['required', 'array', 'min:1'],
            'sizes.*.id' => ['nullable', 'string', 'max:80'],
            'sizes.*.label' => ['required', 'string', 'max:40'],
            'sizes.*.serves' => ['nullable', 'string', 'max:40'],
            'sizes.*.price' => ['required', 'integer', 'min:0', 'max:500000'],
            'shapes' => ['required', 'array', 'min:1'],
            'shapes.*.id' => ['nullable', 'string', 'max:80'],
            'shapes.*.label' => ['required', 'string', 'max:40'],
            'bases' => ['required', 'array', 'min:1'],
            'bases.*.id' => ['nullable', 'string', 'max:80'],
            'bases.*.label' => ['required', 'string', 'max:60'],
            'bases.*.price' => ['required', 'integer', 'min:0', 'max:100000'],
            'fillings' => ['required', 'array', 'min:1'],
            'fillings.*.id' => ['nullable', 'string', 'max:80'],
            'fillings.*.label' => ['required', 'string', 'max:60'],
            'fillings.*.price' => ['required', 'integer', 'min:0', 'max:100000'],
            'frostings' => ['required', 'array', 'min:1'],
            'frostings.*.id' => ['nullable', 'string', 'max:80'],
            'frostings.*.label' => ['required', 'string', 'max:60'],
            'frostings.*.price' => ['required', 'integer', 'min:0', 'max:100000'],
            'diets' => ['nullable', 'array'],
            'diets.*.id' => ['nullable', 'string', 'max:80'],
            'diets.*.label' => ['required_with:diets', 'string', 'max:60'],
            'diets.*.price' => ['required_with:diets', 'integer', 'min:0', 'max:100000'],
            'addons' => ['nullable', 'array'],
            'addons.*.id' => ['nullable', 'string', 'max:80'],
            'addons.*.label' => ['required_with:addons', 'string', 'max:120'],
            'addons.*.price' => ['required_with:addons', 'integer', 'min:0', 'max:100000'],
            'addons.*.billing' => ['required_with:addons', Rule::in(['flat', 'per_unit'])],
            'addons.*.unit_label' => ['nullable', 'string', 'max:40'],
            'addons.*.hint' => ['nullable', 'string', 'max:240'],
            'addons.*.max_qty' => ['nullable', 'integer', 'min:1', 'max:50'],
            'colors' => ['nullable', 'array'],
            'colors.*.id' => ['nullable', 'string', 'max:80'],
            'colors.*.label' => ['required_with:colors', 'string', 'max:40'],
            'colors.*.hex' => ['required_with:colors', 'string', 'max:7'],
        ], [
            'sizes.*.price.required' => 'Enter a price in PKR for each size (numbers only).',
        ]);

        $config = [
            'guidelines' => collect($data['guidelines'] ?? [])
                ->map(static fn ($line): string => trim((string) $line))
                ->filter()
                ->values()
                ->all(),
            'sizes' => $this->mapPricedRows($data['sizes'], 'size', withServes: true),
            'shapes' => $this->mapLabelRows($data['shapes'], 'shape'),
            'bases' => $this->mapPricedRows($data['bases'], 'base'),
            'fillings' => $this->mapPricedRows($data['fillings'], 'filling'),
            'frostings' => $this->mapPricedRows($data['frostings'], 'frosting'),
            'diets' => $this->mapPricedRows($data['diets'] ?? [], 'diet'),
            'addons' => $this->mapAddonRows($data['addons'] ?? []),
            'colors' => collect($data['colors'] ?? [])->values()->map(function (array $row, int $index): array {
                $label = trim((string) ($row['label'] ?? ''));
                $hex = trim((string) ($row['hex'] ?? '#f7f1e8'));
                if (! str_starts_with($hex, '#')) {
                    $hex = '#'.$hex;
                }

                return [
                    'id' => (string) ($row['id'] ?? '') ?: (Str::slug($label) ?: 'color-'.($index + 1)),
                    'label' => $label,
                    'hex' => $hex,
                ];
            })->all(),
        ];

        CakeBuilder::saveConfig($config);

        return back()->with('status', 'Custom cake prices and rules saved. Customers see them on the builder.');
    }

    public function storeGuideline(): RedirectResponse
    {
        $config = CakeBuilder::config();
        $config['guidelines'][] = 'New bakery rule - edit this text.';
        CakeBuilder::saveConfig($config);

        return back()->with('status', 'New guideline added. Edit the text and save.');
    }

    public function destroyGuideline(int $index): RedirectResponse
    {
        $config = CakeBuilder::config();
        $lines = $config['guidelines'] ?? [];

        if (count($lines) <= 1) {
            return back()->withErrors(['guidelines' => 'Keep at least one guideline.']);
        }

        if (! array_key_exists($index, $lines)) {
            return back()->withErrors(['guidelines' => 'That guideline was not found.']);
        }

        unset($lines[$index]);
        $config['guidelines'] = array_values($lines);
        CakeBuilder::saveConfig($config);

        return back()->with('status', 'Guideline removed.');
    }

    public function storeAddon(): RedirectResponse
    {
        $config = CakeBuilder::config();
        $config['addons'][] = [
            'id' => 'addon-'.Str::lower(Str::random(4)),
            'label' => 'New add-on',
            'price' => 0,
            'billing' => 'flat',
            'unit_label' => '',
            'hint' => '',
            'max_qty' => 12,
        ];
        CakeBuilder::saveConfig($config);

        return back()->with('status', 'New add-on added. Fill in the name and price, then save.');
    }

    public function destroyAddon(string $item): RedirectResponse
    {
        $config = CakeBuilder::config();
        $addons = $config['addons'] ?? [];

        if (count($addons) <= 1) {
            return back()->withErrors(['addons' => 'Keep at least one add-on.']);
        }

        $filtered = array_values(array_filter(
            $addons,
            static fn (array $row): bool => ($row['id'] ?? '') !== $item
        ));

        if (count($filtered) === count($addons)) {
            return back()->withErrors(['addons' => 'That add-on was not found.']);
        }

        $config['addons'] = $filtered;
        CakeBuilder::saveConfig($config);

        return back()->with('status', 'Add-on removed.');
    }

    public function storeSize(): RedirectResponse
    {
        $config = CakeBuilder::config();
        $config['sizes'][] = [
            'id' => 'size-'.Str::lower(Str::random(4)),
            'label' => 'New size',
            'serves' => '',
            'price' => 0,
        ];
        CakeBuilder::saveConfig($config);

        return back()->with('status', 'New size added. Set the label and price, then save.');
    }

    public function destroySize(string $item): RedirectResponse
    {
        $config = CakeBuilder::config();
        $sizes = $config['sizes'] ?? [];

        if (count($sizes) <= 1) {
            return back()->withErrors(['sizes' => 'Keep at least one size.']);
        }

        $filtered = array_values(array_filter(
            $sizes,
            static fn (array $row): bool => ($row['id'] ?? '') !== $item
        ));

        if (count($filtered) === count($sizes)) {
            return back()->withErrors(['sizes' => 'That size was not found.']);
        }

        $config['sizes'] = $filtered;
        CakeBuilder::saveConfig($config);

        return back()->with('status', 'Size removed.');
    }

    public function resetDefaults(): RedirectResponse
    {
        CakeBuilder::saveConfig(CakeBuilder::defaults());

        return back()->with('status', 'Restored bakery default sizes, add-ons, and guidelines. You can still edit anything.');
    }

    /**
     * @param  list<array<string, mixed>>  $rows
     * @return list<array<string, mixed>>
     */
    private function mapPricedRows(array $rows, string $prefix, bool $withServes = false): array
    {
        return collect($rows)->values()->map(function (array $row, int $index) use ($prefix, $withServes): array {
            $label = trim((string) ($row['label'] ?? ''));
            $mapped = [
                'id' => (string) ($row['id'] ?? '') ?: (Str::slug($label) ?: $prefix.'-'.($index + 1)),
                'label' => $label,
                'price' => (int) ($row['price'] ?? 0),
            ];

            if ($withServes) {
                $mapped['serves'] = trim((string) ($row['serves'] ?? '')) ?: null;
            }

            return $mapped;
        })->all();
    }

    /**
     * @param  list<array<string, mixed>>  $rows
     * @return list<array{id: string, label: string}>
     */
    private function mapLabelRows(array $rows, string $prefix): array
    {
        return collect($rows)->values()->map(function (array $row, int $index) use ($prefix): array {
            $label = trim((string) ($row['label'] ?? ''));

            return [
                'id' => (string) ($row['id'] ?? '') ?: (Str::slug($label) ?: $prefix.'-'.($index + 1)),
                'label' => $label,
            ];
        })->all();
    }

    /**
     * @param  list<array<string, mixed>>  $rows
     * @return list<array<string, mixed>>
     */
    private function mapAddonRows(array $rows): array
    {
        return collect($rows)->values()->map(function (array $row, int $index): array {
            $label = trim((string) ($row['label'] ?? ''));
            $billing = ($row['billing'] ?? 'flat') === 'per_unit' ? 'per_unit' : 'flat';

            return [
                'id' => (string) ($row['id'] ?? '') ?: (Str::slug($label) ?: 'addon-'.($index + 1)),
                'label' => $label,
                'price' => (int) ($row['price'] ?? 0),
                'billing' => $billing,
                'unit_label' => trim((string) ($row['unit_label'] ?? '')),
                'hint' => trim((string) ($row['hint'] ?? '')),
                'max_qty' => max(1, min(50, (int) ($row['max_qty'] ?? 12))),
            ];
        })->all();
    }
}
