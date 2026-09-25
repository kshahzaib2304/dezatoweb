<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\Catalog;
use App\Support\HomeChrome;
use App\Support\KarachiAreas;
use App\Support\PageHeroes;
use App\Support\StorefrontCopy;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ChromeController extends Controller
{
    public function edit(): View
    {
        $home = HomeChrome::all();
        $home['story_image_url'] = asset($home['story_image']);
        $home['about_intro_image_url'] = asset($home['about_intro_image']);
        $home['ways'] = array_map(static function (array $way): array {
            $way['image_url'] = asset($way['image']);

            return $way;
        }, $home['ways']);

        return view('admin.chrome.edit', [
            'title' => 'Storefront chrome | Dezato Admin',
            'heading' => 'Storefront chrome',
            'active' => 'chrome',
            'nav' => config('dezato_admin.nav'),
            'heroes' => PageHeroes::forAdmin(),
            'heroGuide' => config('dezato_admin.media.hero'),
            'home' => $home,
            'tileGuide' => config('dezato_admin.media.tile'),
            'copy' => StorefrontCopy::all(),
            'areas' => KarachiAreas::all(),
            'locations' => Catalog::locations()->all(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $locationIds = Catalog::locations()->pluck('id')->all();
        $homeRules = [
            'home' => ['required', 'array'],
            'home.ways' => ['required', 'array', 'size:3'],
            'home.ways.*.title' => ['required', 'string', 'max:80'],
            'home.ways.*.text' => ['required', 'string', 'max:200'],
            'home.ways.*.existing_image' => ['nullable', 'string', 'max:255'],
            'home.ways.*.image' => ['nullable', 'image', 'max:2048'],
            'home.story_image' => ['nullable', 'image', 'max:3072'],
            'home.about_intro_image' => ['nullable', 'image', 'max:3072'],
            'home.existing_story_image' => ['nullable', 'string', 'max:255'],
            'home.existing_about_intro_image' => ['nullable', 'string', 'max:255'],
        ];

        foreach (array_keys(HomeChrome::defaults()) as $key) {
            if ($key === 'ways' || str_ends_with($key, '_image')) {
                continue;
            }
            $homeRules["home.$key"] = ['required', 'string', 'max:240'];
        }

        $copyRules = ['copy' => ['required', 'array']];
        foreach (array_keys(StorefrontCopy::defaults()) as $key) {
            $copyRules["copy.$key"] = ['required', 'string', 'max:500'];
        }

        $data = $request->validate(array_merge([
            'heroes' => ['required', 'array'],
            'heroes.*.eyebrow' => ['nullable', 'string', 'max:80'],
            'heroes.*.title' => ['required', 'string', 'max:120'],
            'heroes.*.text' => ['nullable', 'string', 'max:240'],
            'heroes.*.existing_image' => ['nullable', 'string', 'max:255'],
            'heroes.*.image' => ['nullable', 'image', 'max:3072'],
            'heroes.*.compact' => ['nullable', 'boolean'],
            'areas' => ['required', 'array', 'min:1'],
            'areas.*.id' => ['nullable', 'string', 'max:80'],
            'areas.*.label' => ['required', 'string', 'max:120'],
            'areas.*.location_id' => ['required', 'string', Rule::in($locationIds)],
        ], $homeRules, $copyRules));

        $heroFiles = [];
        foreach (array_keys(PageHeroes::defaults()) as $key) {
            $heroFiles[$key] = $request->file("heroes.$key.image");
        }
        PageHeroes::save($data['heroes'], $heroFiles);

        $wayFiles = [];
        foreach ([0, 1, 2] as $index) {
            $wayFiles[$index] = $request->file("home.ways.$index.image");
        }

        HomeChrome::save(
            $data['home'],
            $request->file('home.story_image'),
            $request->file('home.about_intro_image'),
            $wayFiles
        );

        StorefrontCopy::save($data['copy']);
        KarachiAreas::save($data['areas']);

        return back()->with('status', 'Storefront chrome saved.');
    }

    public function storeArea(): RedirectResponse
    {
        KarachiAreas::append();

        return back()->with('status', 'New Karachi area added. Fill it in and save.');
    }

    public function destroyArea(string $area): RedirectResponse
    {
        return KarachiAreas::remove($area)
            ? back()->with('status', 'Area removed.')
            : back()->withErrors(['areas' => 'Keep at least one delivery area.']);
    }
}
