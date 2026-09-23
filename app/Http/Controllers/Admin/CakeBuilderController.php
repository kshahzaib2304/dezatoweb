<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\CakeBuilder;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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
            'sizes' => ['required', 'array', 'min:1'],
            'sizes.*.label' => ['required', 'string', 'max:40'],
            'sizes.*.serves' => ['nullable', 'string', 'max:40'],
            'sizes.*.price' => ['required', 'integer', 'min:0', 'max:500000'],
            'shapes' => ['required', 'array', 'min:1'],
            'shapes.*.label' => ['required', 'string', 'max:40'],
            'bases' => ['required', 'array', 'min:1'],
            'bases.*.label' => ['required', 'string', 'max:60'],
            'bases.*.price' => ['required', 'integer', 'min:0', 'max:100000'],
            'fillings' => ['required', 'array', 'min:1'],
            'fillings.*.label' => ['required', 'string', 'max:60'],
            'fillings.*.price' => ['required', 'integer', 'min:0', 'max:100000'],
            'frostings' => ['required', 'array', 'min:1'],
            'frostings.*.label' => ['required', 'string', 'max:60'],
            'frostings.*.price' => ['required', 'integer', 'min:0', 'max:100000'],
            'diets' => ['nullable', 'array'],
            'diets.*.label' => ['required_with:diets', 'string', 'max:60'],
            'diets.*.price' => ['required_with:diets', 'integer', 'min:0', 'max:100000'],
            'addons' => ['nullable', 'array'],
            'addons.*.label' => ['required_with:addons', 'string', 'max:60'],
            'addons.*.price' => ['required_with:addons', 'integer', 'min:0', 'max:100000'],
            'colors' => ['nullable', 'array'],
            'colors.*.label' => ['required_with:colors', 'string', 'max:40'],
            'colors.*.hex' => ['required_with:colors', 'string', 'max:7'],
        ], [
            'sizes.*.price.required' => 'Enter a price in PKR for each size (numbers only).',
        ]);

        $current = CakeBuilder::config();

        $config = [
            'sizes' => $this->mapPricedRows($data['sizes'], 'size'),
            'shapes' => $this->mapLabelRows($data['shapes'], 'shape'),
            'bases' => $this->mapPricedRows($data['bases'], 'base'),
            'fillings' => $this->mapPricedRows($data['fillings'], 'filling'),
            'frostings' => $this->mapPricedRows($data['frostings'], 'frosting'),
            'diets' => $this->mapPricedRows($data['diets'] ?? [], 'diet'),
            'addons' => $this->mapPricedRows($data['addons'] ?? [], 'addon'),
            'colors' => collect($data['colors'] ?? $current['colors'] ?? [])->map(function (array $row, int $index): array {
                $label = trim((string) ($row['label'] ?? ''));
                $hex = trim((string) ($row['hex'] ?? '#f7f1e8'));
                if (! str_starts_with($hex, '#')) {
                    $hex = '#'.$hex;
                }

                return [
                    'id' => Str::slug($label) ?: 'color-'.($index + 1),
                    'label' => $label,
                    'hex' => $hex,
                ];
            })->values()->all(),
        ];

        CakeBuilder::saveConfig($config);

        return back()->with('status', 'Custom cake prices updated. Customers will see the new prices on the builder.');
    }

    /**
     * @param  list<array<string, mixed>>  $rows
     * @return list<array<string, mixed>>
     */
    private function mapPricedRows(array $rows, string $prefix): array
    {
        return collect($rows)->values()->map(function (array $row, int $index) use ($prefix): array {
            $label = trim((string) ($row['label'] ?? ''));
            $mapped = [
                'id' => Str::slug($label) ?: $prefix.'-'.($index + 1),
                'label' => $label,
                'price' => (int) ($row['price'] ?? 0),
            ];

            if (array_key_exists('serves', $row)) {
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
                'id' => Str::slug($label) ?: $prefix.'-'.($index + 1),
                'label' => $label,
            ];
        })->all();
    }
}
