<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $q = $request->string('q')->trim()->toString();

        $products = Product::query()
            ->with('category')
            ->when($q !== '', function ($query) use ($q): void {
                $query->where(function ($inner) use ($q): void {
                    $inner->where('name', 'like', "%{$q}%")
                        ->orWhere('slug', 'like', "%{$q}%");
                });
            })
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('admin.products.index', [
            'title' => 'Products | Dezato Admin',
            'heading' => 'Products',
            'active' => 'products',
            'nav' => config('dezato_admin.nav'),
            'products' => $products,
            'q' => $q,
            'mediaGuide' => config('dezato_admin.media.product'),
        ]);
    }

    public function create(): View
    {
        return view('admin.products.form', [
            'title' => 'Add product | Dezato Admin',
            'heading' => 'Add a product',
            'active' => 'products',
            'nav' => config('dezato_admin.nav'),
            'product' => new Product(['is_active' => true, 'price' => 1500]),
            'categories' => Category::query()->orderBy('sort_order')->get(),
            'mediaGuide' => config('dezato_admin.media.product'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['image'] = $this->storeImage($request) ?? $data['image'] ?? null;

        Product::query()->create($data);

        return redirect()
            ->route('admin.products.index')
            ->with('status', 'Product saved. It will show on the website if it is marked Active.');
    }

    public function edit(Product $product): View
    {
        return view('admin.products.form', [
            'title' => 'Edit product | Dezato Admin',
            'heading' => 'Edit product',
            'active' => 'products',
            'nav' => config('dezato_admin.nav'),
            'product' => $product,
            'categories' => Category::query()->orderBy('sort_order')->get(),
            'mediaGuide' => config('dezato_admin.media.product'),
        ]);
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $data = $this->validated($request, $product);
        $newImage = $this->storeImage($request);

        if ($newImage !== null) {
            $this->deleteImageIfOwned($product->image);
            $data['image'] = $newImage;
        }

        $product->update($data);

        return redirect()
            ->route('admin.products.index')
            ->with('status', 'Product updated successfully.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $this->deleteImageIfOwned($product->image);
        $product->delete();

        return redirect()
            ->route('admin.products.index')
            ->with('status', 'Product removed from the website.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request, ?Product $product = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:160'],
            'slug' => [
                'nullable',
                'string',
                'max:180',
                Rule::unique('products', 'slug')->ignore($product?->id),
            ],
            'category_id' => ['required', 'exists:categories,id'],
            'description' => ['nullable', 'string', 'max:2000'],
            'price' => ['required', 'integer', 'min:1', 'max:500000'],
            'badge' => ['nullable', 'string', 'max:64'],
            'weight' => ['nullable', 'string', 'max:32'],
            'stock' => ['nullable', 'integer', 'min:0', 'max:100000'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'is_active' => ['nullable', 'boolean'],
            'is_featured' => ['nullable', 'boolean'],
            'image' => ['nullable', 'image', 'max:2048'],
        ], [
            'image.max' => 'Please use a photo smaller than 2 MB.',
            'image.image' => 'Please upload a JPG or WebP photo.',
            'price.required' => 'Enter the price in Pakistani Rupees (numbers only, e.g. 1850).',
        ]);

        $data['slug'] = Str::slug($data['slug'] ?: $data['name']);
        $data['is_active'] = $request->boolean('is_active');
        $data['is_featured'] = $request->boolean('is_featured');
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);
        $data['badge'] = $data['badge'] ?: null;
        $data['weight'] = $data['weight'] ?: null;
        $data['stock'] = array_key_exists('stock', $data) && $data['stock'] !== null && $data['stock'] !== ''
            ? (int) $data['stock']
            : null;

        unset($data['image']);

        return $data;
    }

    private function storeImage(Request $request): ?string
    {
        if (! $request->hasFile('image')) {
            return null;
        }

        return $request->file('image')->store('products', 'public');
    }

    private function deleteImageIfOwned(?string $path): void
    {
        if ($path && str_starts_with($path, 'products/') && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
