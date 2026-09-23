<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use App\Support\HeroSlider;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ContentController extends Controller
{
    public function edit(): View
    {
        $config = HeroSlider::config();

        $slides = collect($config['slides'])->map(function (array $slide): array {
            return [
                'id' => $slide['id'],
                'headline' => $slide['headline'] ?? '',
                'lede' => $slide['lede'] ?? '',
                'active' => (bool) ($slide['active'] ?? true),
                'image_url' => asset(HeroSlider::publicImagePath($slide['image'] ?? null)),
                'has_image' => ! empty($slide['image']),
            ];
        })->all();

        return view('admin.content.edit', [
            'title' => 'Homepage & text | Dezato Admin',
            'heading' => 'Homepage & website text',
            'active' => 'content',
            'nav' => config('dezato_admin.nav'),
            'announcement' => SiteSetting::getValue(
                'announcement',
                (string) config('dezato.home.announcement', '')
            ),
            'slides' => $slides,
            'intervalMs' => $config['interval_ms'],
            'canAddSlide' => count($slides) < HeroSlider::MAX_SLIDES,
            'maxSlides' => HeroSlider::MAX_SLIDES,
            'mediaGuide' => config('dezato_admin.media.hero'),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'announcement' => ['nullable', 'string', 'max:240'],
            'interval_ms' => ['required', 'integer', 'in:4000,4500,5000'],
        ], [
            'announcement.max' => 'Keep the top banner short — max 240 characters.',
            'interval_ms.in' => 'Choose 4, 4.5, or 5 seconds between slides.',
        ]);

        SiteSetting::putValue('announcement', trim((string) ($data['announcement'] ?? '')) ?: null);

        $config = HeroSlider::config();
        $config['interval_ms'] = (int) $data['interval_ms'];
        HeroSlider::save($config);

        return back()->with('status', 'Announcement and slider timing saved.');
    }

    public function storeSlide(Request $request): RedirectResponse
    {
        $config = HeroSlider::config();

        if (count($config['slides']) >= HeroSlider::MAX_SLIDES) {
            return back()->withErrors([
                'slides' => 'You can have up to '.HeroSlider::MAX_SLIDES.' slides. Delete one first.',
            ]);
        }

        $data = $this->validatedSlide($request, requireImage: true);
        $path = $request->file('image')->store('banners', 'public');

        $config['slides'][] = HeroSlider::makeSlide(
            $data['headline'],
            $data['lede'],
            $path,
            $request->boolean('active', true)
        );

        HeroSlider::save($config);

        return back()->with('status', 'New hero slide added. It will rotate on the homepage.');
    }

    public function updateSlide(Request $request, string $slide): RedirectResponse
    {
        $config = HeroSlider::config();
        $index = $this->findSlideIndex($config['slides'], $slide);

        if ($index === null) {
            return back()->withErrors(['slides' => 'That slide was not found.']);
        }

        $data = $this->validatedSlide($request, requireImage: false);
        $current = $config['slides'][$index];

        if ($request->hasFile('image')) {
            HeroSlider::deleteImage($current['image'] ?? null);
            $current['image'] = $request->file('image')->store('banners', 'public');
        }

        $current['headline'] = $data['headline'];
        $current['lede'] = $data['lede'];
        $current['active'] = $request->boolean('active');
        $config['slides'][$index] = $current;

        HeroSlider::save($config);

        return back()->with('status', 'Slide updated.');
    }

    public function destroySlide(string $slide): RedirectResponse
    {
        $config = HeroSlider::config();
        $index = $this->findSlideIndex($config['slides'], $slide);

        if ($index === null) {
            return back()->withErrors(['slides' => 'That slide was not found.']);
        }

        if (count($config['slides']) <= 1) {
            return back()->withErrors([
                'slides' => 'Keep at least one slide. Edit it instead of deleting.',
            ]);
        }

        HeroSlider::deleteImage($config['slides'][$index]['image'] ?? null);
        array_splice($config['slides'], $index, 1);
        HeroSlider::save($config);

        return back()->with('status', 'Slide removed from the homepage.');
    }

    /**
     * @return array{headline: string, lede: string}
     */
    private function validatedSlide(Request $request, bool $requireImage): array
    {
        $rules = [
            'headline' => ['required', 'string', 'max:120'],
            'lede' => ['required', 'string', 'max:240'],
            'active' => ['nullable', 'boolean'],
            'image' => [$requireImage ? 'required' : 'nullable', 'image', 'max:3072'],
        ];

        return $request->validate($rules, [
            'image.required' => 'Please upload a hero photo for this slide.',
            'image.max' => 'Please use a photo smaller than 3 MB.',
            'image.image' => 'Please upload a JPG or WebP photo.',
            'headline.required' => 'Enter a short headline for this slide.',
        ]);
    }

    /**
     * @param  list<array<string, mixed>>  $slides
     */
    private function findSlideIndex(array $slides, string $id): ?int
    {
        foreach ($slides as $index => $slide) {
            if (($slide['id'] ?? null) === $id) {
                return $index;
            }
        }

        return null;
    }
}
