<?php

namespace App\Http\Controllers;

use App\Support\CakeBuilder;
use App\Support\Cart;
use App\Support\SiteBrand;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;

class CakeBuilderController extends Controller
{
    public function __construct(private readonly Cart $cart) {}

    public function show(): View
    {
        return view('builder.show', [
            'title' => 'Custom Cake Builder | '.SiteBrand::name(),
            'metaDescription' => 'Design a custom cake - upload a reference, choose size, shape, flavour, and add-ons. Prices in PKR.',
            'canonical' => route('builder.show'),
            'builder' => CakeBuilder::config(),
            'guidelines' => CakeBuilder::guidelines(),
            'messagePlacements' => CakeBuilder::messagePlacements(),
            'maxReferenceImages' => CakeBuilder::MAX_REFERENCE_IMAGES,
            'referenceGuide' => config('dezato_admin.media.reference'),
        ]);
    }

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $maxImages = CakeBuilder::MAX_REFERENCE_IMAGES;

        $data = $request->validate([
            'action' => ['nullable', 'string', 'in:cart,save'],
            'size' => ['required', 'string', 'max:40'],
            'shape' => ['required', 'string', 'max:40'],
            'base' => ['required', 'string', 'max:40'],
            'filling' => ['required', 'string', 'max:40'],
            'frosting' => ['required', 'string', 'max:40'],
            'addons' => ['nullable', 'array'],
            'addons.*' => ['string', 'max:80'],
            'addon_qty' => ['nullable', 'array'],
            'addon_qty.*' => ['nullable', 'integer', 'min:0', 'max:50'],
            'color' => ['required', 'string', 'max:40'],
            'color_hex' => ['nullable', 'string', 'max:7'],
            'color_custom' => ['nullable', 'string', 'max:7'],
            'message_placement' => ['required', 'string', Rule::in(array_keys(CakeBuilder::messagePlacements()))],
            'message' => [
                Rule::requiredIf(fn (): bool => $request->string('message_placement')->toString() !== 'none'),
                'nullable',
                'string',
                'max:60',
            ],
            'notes' => ['nullable', 'string', 'max:1000'],
            'reference_images' => ['nullable', 'array', 'max:'.$maxImages],
            'reference_images.*' => ['image', 'max:3072'],
        ], [
            'reference_images.max' => 'You can upload up to '.$maxImages.' reference photos.',
            'reference_images.*.max' => 'Each reference photo must be smaller than 3 MB.',
            'reference_images.*.image' => 'Please upload JPG, PNG, or WebP photos only.',
            'message.required' => 'Enter the text you want written, or choose “No text”.',
            'message_placement.required' => 'Choose where the text should go.',
        ]);

        $imagePaths = $this->storeReferenceImages($request->file('reference_images', []));

        try {
            $quote = CakeBuilder::quote($data, $imagePaths);
        } catch (InvalidArgumentException $exception) {
            $this->deleteStoredImages($imagePaths);

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

        $message = $quote['name'].' added to your cart · '.pkr($quote['unit_price']);

        if (
            $request->boolean('drawer')
            || $request->expectsJson()
            || $request->ajax()
            || $request->header('X-Cart-Drawer') === '1'
        ) {
            $lines = $this->cart->lines();
            $subtotal = $this->cart->subtotal();

            return response()->json([
                'message' => $message,
                'count' => $this->cart->count(),
                'subtotal' => $subtotal,
                'subtotal_label' => pkr($subtotal),
                'html' => view('components.cart-drawer-body', [
                    'lines' => $lines,
                    'count' => $this->cart->count(),
                    'subtotal' => $subtotal,
                ])->render(),
            ]);
        }

        return back()
            ->with('status', $message)
            ->with('open_cart', true);
    }

    /**
     * @param  array<int, UploadedFile|null>|UploadedFile|null  $files
     * @return list<string>
     */
    private function storeReferenceImages(mixed $files): array
    {
        $uploads = is_array($files) ? $files : ($files instanceof UploadedFile ? [$files] : []);
        $paths = [];

        foreach ($uploads as $file) {
            if (! $file instanceof UploadedFile || ! $file->isValid()) {
                continue;
            }

            $paths[] = $file->store('custom-cakes', 'public');

            if (count($paths) >= CakeBuilder::MAX_REFERENCE_IMAGES) {
                break;
            }
        }

        return $paths;
    }

    /**
     * @param  list<string>  $paths
     */
    private function deleteStoredImages(array $paths): void
    {
        foreach ($paths as $path) {
            Storage::disk('public')->delete($path);
        }
    }
}
