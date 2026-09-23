<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\SiteContent;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

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
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $rules = [
            'about_intro' => ['required', 'string', 'max:2000'],
            'services_intro' => ['required', 'string', 'max:1000'],
        ];

        foreach (array_keys(SiteContent::PAGES) as $key) {
            $rules["pages.$key.title"] = ['required', 'string', 'max:160'];
            $rules["pages.$key.body"] = ['required', 'string', 'max:20000'];
        }

        $data = $request->validate($rules, [
            'about_intro.required' => 'Please write a short About introduction.',
            'services_intro.required' => 'Please write a short Services introduction.',
        ]);

        SiteContent::saveSection('about_intro', $data['about_intro']);
        SiteContent::saveSection('services_intro', $data['services_intro']);

        foreach (array_keys(SiteContent::PAGES) as $key) {
            SiteContent::savePage(
                $key,
                $data['pages'][$key]['title'],
                $data['pages'][$key]['body']
            );
        }

        return back()->with('status', 'Website pages saved. Customers will see the updated text right away.');
    }
}
