<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Custom cake builder (UI-first with live client price calc).
 */
class CakeBuilderController extends Controller
{
    public function show(): View
    {
        return view('builder.show', [
            'title' => 'Custom Cake Builder | Dezato Cake House',
            'metaDescription' => 'Design a custom cake — upload a reference, choose size, shape, flavour, and add-ons. Prices in PKR.',
            'builder' => config('dezato_ui.builder'),
        ]);
    }

    public function stub(Request $request): RedirectResponse
    {
        return back()->with(
            'status',
            'Design captured in the UI — saving & cart add will connect in the backend phase.'
        );
    }
}
