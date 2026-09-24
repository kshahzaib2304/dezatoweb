<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Support\Slugs;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function index(): View
    {
        return view('admin.categories.index', [
            'title' => 'Categories | Dezato Admin',
            'heading' => 'Categories',
            'active' => 'categories',
            'nav' => config('dezato_admin.nav'),
            'categories' => Category::query()->withCount('products')->orderBy('sort_order')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'label' => ['required', 'string', 'max:80'],
            'slug' => ['nullable', 'string', 'max:80', 'unique:categories,slug'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:999'],
        ]);

        Category::query()->create([
            'label' => $data['label'],
            'slug' => $this->slugFor($data['slug'] ?? null, $data['label']),
            'sort_order' => (int) ($data['sort_order'] ?? 0),
            'is_active' => true,
        ]);

        return back()->with('status', 'Category added. You can now assign products to it.');
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $data = $request->validate([
            'label' => ['required', 'string', 'max:80'],
            'slug' => ['required', 'string', 'max:80', Rule::unique('categories', 'slug')->ignore($category->id)],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:999'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $category->update([
            'label' => $data['label'],
            'slug' => $this->slugFor($data['slug'] ?? null, $data['label'], $category->id),
            'sort_order' => (int) ($data['sort_order'] ?? 0),
            'is_active' => $request->boolean('is_active'),
        ]);

        return back()->with('status', 'Category updated.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        if ($category->products()->exists()) {
            return back()->withErrors([
                'category' => 'This category still has products. Move or delete those products first.',
            ]);
        }

        $category->delete();

        return back()->with('status', 'Category deleted.');
    }

    private function slugFor(mixed $requested, string $label, ?int $ignoreId = null): string
    {
        $source = trim((string) $requested);

        if ($source === '' || Str::slug($source) === '') {
            $source = $label;
        }

        return Slugs::unique(Category::class, $source, $ignoreId, 80);
    }
}
