<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\MediaPaths;
use App\Support\StoreLocations;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LocationController extends Controller
{
    public function edit(): View
    {
        $locations = collect(StoreLocations::all())->map(function (array $row): array {
            $row['image_url'] = asset(MediaPaths::public($row['image'] ?? null, 'images/home/delivery-pickup.jpg'));
            $row['services_text'] = implode(', ', $row['services'] ?? []);

            return $row;
        })->all();

        return view('admin.locations.edit', [
            'title' => 'Store locations | Dezato Admin',
            'heading' => 'Store locations',
            'active' => 'locations',
            'locations' => $locations,
            'mediaGuide' => config('dezato_admin.media.location'),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $locations = collect($request->input('locations', []))
            ->map(function ($row) {
                if (! is_array($row)) {
                    return $row;
                }
                $row['map_url'] = trim((string) ($row['map_url'] ?? '')) ?: null;

                return $row;
            })
            ->all();
        $request->merge(['locations' => $locations]);

        $data = $request->validate([
            'locations' => ['required', 'array', 'min:1'],
            'locations.*.id' => ['nullable', 'string', 'max:80'],
            'locations.*.name' => ['required', 'string', 'max:120'],
            'locations.*.city' => ['required', 'string', 'max:80'],
            'locations.*.region' => ['required', 'string', 'max:80'],
            'locations.*.address' => ['required', 'string', 'max:255'],
            'locations.*.hours' => ['required', 'string', 'max:255'],
            'locations.*.phone' => ['nullable', 'string', 'max:40'],
            'locations.*.map_url' => ['nullable', 'url', 'max:255'],
            'locations.*.services_text' => ['nullable', 'string', 'max:255'],
            'locations.*.delivery_fee' => ['nullable', 'numeric', 'min:0', 'max:99999'],
            'locations.*.delivers' => ['nullable', 'boolean'],
            'locations.*.active' => ['nullable', 'boolean'],
            'locations.*.image' => ['nullable', 'image', 'max:3072'],
            'locations.*.existing_image' => ['nullable', 'string', 'max:255'],
        ], [
            'locations.min' => 'Keep at least one bakery location.',
            'locations.*.name.required' => 'Each location needs a name.',
        ]);

        $current = collect(StoreLocations::all())->keyBy('id');
        $rows = [];

        foreach ($data['locations'] as $index => $row) {
            $id = trim((string) ($row['id'] ?? ''));
            if ($id === '') {
                $id = Str::slug($row['name']).'-'.Str::lower(Str::random(4));
            }

            $existing = $current->get($id);
            $imagePath = (string) ($row['existing_image'] ?? ($existing['image'] ?? ''));

            if ($request->hasFile("locations.$index.image")) {
                MediaPaths::deleteIfOwned($imagePath);
                $imagePath = $request->file("locations.$index.image")->store('locations', 'public');
            }

            $rows[] = [
                'id' => $id,
                'name' => $row['name'],
                'city' => $row['city'],
                'region' => $row['region'],
                'address' => $row['address'],
                'hours' => $row['hours'],
                'phone' => $row['phone'] ?? '',
                'map_url' => $row['map_url'] ?? '',
                'services' => $row['services_text'] ?? '',
                'delivery_fee' => $row['delivery_fee'] ?? 0,
                'delivers' => $request->boolean("locations.$index.delivers"),
                'active' => $request->boolean("locations.$index.active"),
                'image' => $imagePath,
            ];
        }

        StoreLocations::save($rows);

        return back()->with('status', 'Locations saved. Customers will see the updated addresses and hours.');
    }

    public function store(): RedirectResponse
    {
        StoreLocations::appendBlank();

        return back()->with('status', 'New location added. Fill in the address and hours, then Save locations.');
    }

    public function destroy(string $location): RedirectResponse
    {
        return StoreLocations::remove($location)
            ? back()->with('status', 'Location removed.')
            : back()->withErrors(['locations' => 'Keep at least one bakery location.']);
    }
}
