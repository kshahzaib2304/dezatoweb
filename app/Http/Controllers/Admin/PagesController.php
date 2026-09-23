<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\MediaPaths;
use App\Support\SiteContent;
use App\Support\StorefrontCards;
use App\Support\StoryBlocks;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PagesController extends Controller
{
    public function edit(): View
    {
        $pages = [];

        foreach (SiteContent::PAGES as $key => $meta) {
            $content = SiteContent::page($key);
            $pages[$key] = [
                'key' => $key,
                'label' => $meta['label'],
                'title' => $content['title'],
                'body' => $content['body'],
                'preview_url' => route($meta['route']),
            ];
        }

        $packages = collect(StoryBlocks::packages())->map(function (array $row): array {
            $row['image_url'] = asset(MediaPaths::public($row['image'] ?? null, 'images/home/promo-catering.png'));

            return $row;
        })->all();

        $orderOptions = collect(StorefrontCards::orderOptions())->map(function (array $row): array {
            $row['image_url'] = asset(MediaPaths::public($row['image'] ?? null, 'images/home/delivery-pickup.png'));

            return $row;
        })->all();

        return view('admin.pages.edit', [
            'title' => 'Website pages | Dezato Admin',
            'heading' => 'Website pages',
            'active' => 'pages',
            'nav' => config('dezato_admin.nav'),
            'pages' => $pages,
            'aboutIntro' => SiteContent::section(
                'about_intro',
                (string) config('dezato.about.intro', '')
            ),
            'servicesIntro' => SiteContent::section(
                'services_intro',
                'Catering, dessert tables, office sweet boxes, and thoughtful corporate gifting - baked fresh in Karachi.'
            ),
            'milestones' => StoryBlocks::milestones(),
            'packages' => $packages,
            'packageGuide' => config('dezato_admin.media.package'),
            'customizationOptions' => StorefrontCards::customizationOptions(),
            'orderOptions' => $orderOptions,
            'orderRouteOptions' => StorefrontCards::orderRoutes(),
            'orderCardGuide' => config('dezato_admin.media.order_card'),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $orderRoutes = array_keys(StorefrontCards::orderRoutes());

        $rules = [
            'about_intro' => ['required', 'string', 'max:2000'],
            'services_intro' => ['required', 'string', 'max:1000'],
            'milestones' => ['required', 'array', 'min:1'],
            'milestones.*.id' => ['nullable', 'string', 'max:80'],
            'milestones.*.year' => ['required', 'string', 'max:20'],
            'milestones.*.title' => ['required', 'string', 'max:120'],
            'milestones.*.text' => ['required', 'string', 'max:500'],
            'packages' => ['required', 'array', 'min:1'],
            'packages.*.id' => ['nullable', 'string', 'max:80'],
            'packages.*.title' => ['required', 'string', 'max:120'],
            'packages.*.serves' => ['nullable', 'string', 'max:80'],
            'packages.*.price' => ['nullable', 'string', 'max:80'],
            'packages.*.blurb' => ['nullable', 'string', 'max:400'],
            'packages.*.existing_image' => ['nullable', 'string', 'max:255'],
            'packages.*.image' => ['nullable', 'image', 'max:3072'],
            'customization' => ['required', 'array', 'min:1'],
            'customization.*.id' => ['nullable', 'string', 'max:80'],
            'customization.*.title' => ['required', 'string', 'max:120'],
            'customization.*.text' => ['required', 'string', 'max:400'],
            'order_cards' => ['required', 'array', 'min:1'],
            'order_cards.*.id' => ['nullable', 'string', 'max:80'],
            'order_cards.*.title' => ['required', 'string', 'max:120'],
            'order_cards.*.text' => ['required', 'string', 'max:400'],
            'order_cards.*.cta' => ['required', 'string', 'max:60'],
            'order_cards.*.route' => ['required', 'string', Rule::in($orderRoutes)],
            'order_cards.*.existing_image' => ['nullable', 'string', 'max:255'],
            'order_cards.*.image' => ['nullable', 'image', 'max:3072'],
        ];

        foreach (array_keys(SiteContent::PAGES) as $key) {
            $rules["pages.$key.title"] = ['required', 'string', 'max:160'];
            $rules["pages.$key.body"] = ['required', 'string', 'max:20000'];
        }

        $data = $request->validate($rules);

        SiteContent::saveSection('about_intro', $data['about_intro']);
        SiteContent::saveSection('services_intro', $data['services_intro']);

        foreach (array_keys(SiteContent::PAGES) as $key) {
            SiteContent::savePage(
                $key,
                $data['pages'][$key]['title'],
                $data['pages'][$key]['body']
            );
        }

        StoryBlocks::saveMilestones($data['milestones']);
        StorefrontCards::saveCustomization($data['customization']);

        $packages = [];
        foreach ($data['packages'] as $index => $row) {
            $image = (string) ($row['existing_image'] ?? '');
            if ($request->hasFile("packages.$index.image")) {
                MediaPaths::deleteIfOwned($image);
                $image = $request->file("packages.$index.image")->store('packages', 'public');
            }
            $packages[] = [
                'id' => $row['id'] ?? null,
                'title' => $row['title'],
                'serves' => $row['serves'] ?? '',
                'price' => $row['price'] ?? '',
                'blurb' => $row['blurb'] ?? '',
                'image' => $image,
            ];
        }
        StoryBlocks::savePackages($packages);

        $orderCards = [];
        foreach ($data['order_cards'] as $index => $row) {
            $image = (string) ($row['existing_image'] ?? '');
            if ($request->hasFile("order_cards.$index.image")) {
                MediaPaths::deleteIfOwned($image);
                $image = $request->file("order_cards.$index.image")->store('order-cards', 'public');
            }
            $orderCards[] = [
                'id' => $row['id'] ?? null,
                'title' => $row['title'],
                'text' => $row['text'],
                'cta' => $row['cta'],
                'route' => $row['route'],
                'image' => $image,
            ];
        }
        StorefrontCards::saveOrderOptions($orderCards);

        return back()->with('status', 'Website pages and cards saved.');
    }

    public function storeMilestone(): RedirectResponse
    {
        StoryBlocks::appendMilestone();

        return back()->with('status', 'New timeline item added. Fill it in and save.');
    }

    public function destroyMilestone(string $item): RedirectResponse
    {
        return StoryBlocks::removeMilestone($item)
            ? back()->with('status', 'Timeline item removed.')
            : back()->withErrors(['milestones' => 'Keep at least one timeline item.']);
    }

    public function storePackage(): RedirectResponse
    {
        StoryBlocks::appendPackage();

        return back()->with('status', 'New package added. Fill it in and save.');
    }

    public function destroyPackage(string $item): RedirectResponse
    {
        return StoryBlocks::removePackage($item)
            ? back()->with('status', 'Package removed.')
            : back()->withErrors(['packages' => 'Keep at least one package.']);
    }

    public function storeCustomization(): RedirectResponse
    {
        StorefrontCards::appendCustomization();

        return back()->with('status', 'New customization card added. Fill it in and save.');
    }

    public function destroyCustomization(string $item): RedirectResponse
    {
        return StorefrontCards::removeCustomization($item)
            ? back()->with('status', 'Customization card removed.')
            : back()->withErrors(['customization' => 'Keep at least one card.']);
    }

    public function storeOrderCard(): RedirectResponse
    {
        StorefrontCards::appendOrderOption();

        return back()->with('status', 'New order card added. Fill it in and save.');
    }

    public function destroyOrderCard(string $item): RedirectResponse
    {
        return StorefrontCards::removeOrderOption($item)
            ? back()->with('status', 'Order card removed.')
            : back()->withErrors(['order_cards' => 'Keep at least one order card.']);
    }
}
