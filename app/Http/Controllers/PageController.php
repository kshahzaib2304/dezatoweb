<?php

namespace App\Http\Controllers;

use App\Support\SiteBrand;
use App\Support\SiteContent;
use Illuminate\Contracts\View\View;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PageController extends Controller
{
    public function show(string $slug): View
    {
        if (! isset(SiteContent::PAGES[$slug])) {
            throw new NotFoundHttpException;
        }

        $page = SiteContent::page($slug);
        $brand = SiteBrand::name();

        return view('pages.legal', [
            'title' => $page['title'].' | '.$brand,
            'metaDescription' => $page['title'].' for '.$brand.', Karachi.',
            'canonical' => url()->current(),
            'pageTitle' => $page['title'],
            'body' => $page['body'],
            'slug' => $slug,
        ]);
    }
}
