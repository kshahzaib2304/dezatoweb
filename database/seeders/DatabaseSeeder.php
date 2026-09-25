<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Promo;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::query()->updateOrCreate(
            ['email' => 'admin@dezato.pk'],
            [
                'name' => 'Dezato Admin',
                'phone' => '+92 300 0000000',
                'password' => 'DezatoAdmin123!',
            ]
        );

        if ($admin->role !== User::ROLE_ADMIN) {
            $admin->forceFill(['role' => User::ROLE_ADMIN])->save();
        }

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

        Promo::query()->updateOrCreate(
            ['code' => 'DEZATO10'],
            [
                'label' => 'Welcome - 10% off',
                'type' => Promo::TYPE_PERCENT,
                'value' => 10,
                'min_subtotal' => 2000,
                'max_uses' => null,
                'is_active' => true,
            ]
        );
    }
}
