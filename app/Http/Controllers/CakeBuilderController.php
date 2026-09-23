<?php

namespace App\Http\Controllers;

use App\Support\CakeBuilder;
use App\Support\Cart;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;

class CakeBuilderController extends Controller
{
    public function __construct(private readonly Cart $cart) {}

    public function show(): View
    {
        return view('builder.show', [
            'title' => 'Custom Cake Builder | Dezato Cake House',
            'metaDescription' => 'Design a custom cake — upload a reference, choose size, shape, flavour, and add-ons. Prices in PKR.',
            'builder' => CakeBuilder::config(),
            'referenceGuide' => config('dezato_admin.media.reference'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'action' => ['nullable', 'string', 'in:cart,save'],
            'size' => ['required', 'string', 'max:40'],
            'shape' => ['required', 'string', 'max:40'],
            'base' => ['required', 'string', 'max:40'],
            'filling' => ['required', 'string', 'max:40'],
            'frosting' => ['required', 'string', 'max:40'],
            'diets' => ['nullable', 'array'],
            'diets.*' => ['string', 'max:40'],
            'addons' => ['nullable', 'array'],
            'addons.*' => ['string', 'max:40'],
            'color' => ['required', 'string', 'max:40'],
            'color_hex' => ['nullable', 'string', 'max:7'],
            'color_custom' => ['nullable', 'string', 'max:7'],
            'message' => ['nullable', 'string', 'max:60'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'reference_image' => ['nullable', 'image', 'max:3072'],
        ], [
            'reference_image.max' => 'Please use a reference photo smaller than 3 MB.',
            'reference_image.image' => 'Please upload a JPG, PNG, or WebP photo.',
        ]);

        $imagePath = null;

        if ($request->hasFile('reference_image')) {
            $imagePath = $request->file('reference_image')->store('custom-cakes', 'public');
        }

        try {
            $quote = CakeBuilder::quote($data, $imagePath);
        } catch (InvalidArgumentException $exception) {
            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
            }

            throw ValidationException::withMessages(['size' => $exception->getMessage()]);
        }

        $action = $data['action'] ?? 'cart';

        if ($action === 'save') {
            $request->session()->put('dezato.saved_design', $quote);

            return back()->with(
                'status',
                'Design saved on this device for '.$quote['name'].' · '.pkr($quote['unit_price']).'. Add it to cart when you are ready.'
            );
        }

        $this->cart->addCustom($quote);

        return redirect()
            ->route('cart.show')
            ->with('status', $quote['name'].' added to your cart · '.pkr($quote['unit_price']));
    }
}
