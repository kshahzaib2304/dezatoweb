<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'admin@dezato.pk'],
            [
                'name' => 'Dezato Admin',
                'phone' => '+92 300 0000000',
                'role' => User::ROLE_ADMIN,
                'password' => 'DezatoAdmin123!',
            ]
        );

        $categories = collect(config('dezato.menu.categories', []))
            ->reject(fn (array $category): bool => ($category['id'] ?? '') === 'all')
            ->values();

        foreach ($categories as $index => $category) {
            Category::query()->updateOrCreate(
                ['slug' => $category['id']],
                [
                    'label' => $category['label'],
                    'sort_order' => $index + 1,
                    'is_active' => true,
                ]
            );
        }

        $categoryMap = Category::query()->pluck('id', 'slug');

        foreach (config('dezato.menu.products', []) as $index => $product) {
            $categoryId = $categoryMap[$product['category']] ?? null;

            if ($categoryId === null) {
                continue;
            }

            Product::query()->updateOrCreate(
                ['slug' => $product['id']],
                [
                    'category_id' => $categoryId,
                    'name' => $product['name'],
                    'description' => $product['description'] ?? '',
                    'price' => (int) $product['price'],
                    'badge' => $product['badge'] ?? null,
                    'weight' => $product['weight'] ?? null,
                    'image' => $product['image'] ?? null,
                    'is_active' => true,
                    'is_featured' => ($product['badge'] ?? null) !== null,
                    'stock' => null,
                    'sort_order' => $index + 1,
                ]
            );
        }
    }
}
