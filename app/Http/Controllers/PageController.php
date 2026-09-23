<?php

namespace App\Http\Controllers;

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

        return view('pages.legal', [
            'title' => $page['title'].' | Dezato Cake House',
            'metaDescription' => $page['title'].' for Dezato Cake House, Karachi.',
            'pageTitle' => $page['title'],
            'body' => $page['body'],
            'slug' => $slug,
        ]);
    }
}
