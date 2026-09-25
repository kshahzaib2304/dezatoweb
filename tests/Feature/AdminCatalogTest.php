<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use App\Support\GatewayCredentials;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCatalogTest extends TestCase
{
    use RefreshDatabase;

    public function test_updating_a_product_without_a_slug_keeps_the_existing_link(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $category = Category::query()->create([
            'slug' => 'cakes',
            'label' => 'Cakes',
            'sort_order' => 1,
            'is_active' => true,
        ]);
        $product = Product::query()->create([
            'category_id' => $category->id,
            'slug' => 'coffee-chocolate-cake',
            'name' => 'Coffee Chocolate Cake',
            'description' => 'Soft coffee sponge.',
            'price' => 1500,
            'weight' => '2.5 lbs',
            'is_active' => true,
            'is_featured' => false,
            'sort_order' => 1,
        ]);

        $response = $this->actingAs($admin)->put(route('admin.products.update', $product), [
            'name' => 'Coffee Chocolate Cakeeeeeeeeee',
            'category_id' => $category->id,
            'price' => 1500,
            'description' => 'Soft coffee sponge layered with chocolate frosting.',
            'weight' => '2.5 lbs',
            'sort_order' => 1,
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('admin.products.index'));
        $product->refresh();

        $this->assertSame('Coffee Chocolate Cakeeeeeeeeee', $product->name);
        $this->assertSame('coffee-chocolate-cake', $product->slug);
        $this->assertTrue($product->is_active);
        $this->assertFalse($product->is_featured);
    }

    public function test_creating_a_product_builds_a_unique_slug_from_the_name(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $category = Category::query()->create([
            'slug' => 'cakes',
            'label' => 'Cakes',
            'sort_order' => 1,
            'is_active' => true,
        ]);
        Product::query()->create([
            'category_id' => $category->id,
            'slug' => 'chocolate-cake',
            'name' => 'Chocolate Cake',
            'price' => 1800,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $response = $this->actingAs($admin)->post(route('admin.products.store'), [
            'name' => 'Chocolate Cake',
            'category_id' => $category->id,
            'price' => 1900,
        ]);

        $response->assertRedirect(route('admin.products.index'));
        $this->assertDatabaseHas('products', [
            'name' => 'Chocolate Cake',
            'price' => 1900,
            'slug' => 'chocolate-cake-2',
        ]);
    }

    public function test_creating_a_category_without_a_slug_uses_the_name(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $response = $this->actingAs($admin)->post(route('admin.categories.store'), [
            'label' => 'Mini Pies',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('categories', [
            'label' => 'Mini Pies',
            'slug' => 'mini-pies',
        ]);
    }

    public function test_an_unchecked_promo_stays_off_and_a_checked_one_is_active(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)->post(route('admin.promos.store'), [
            'code' => 'off10',
            'type' => 'percent',
            'value' => 10,
            'is_active' => '0',
        ])->assertRedirect();

        $this->assertDatabaseHas('promos', [
            'code' => 'OFF10',
            'is_active' => 0,
        ]);

        $this->actingAs($admin)->post(route('admin.promos.store'), [
            'code' => 'on10',
            'type' => 'percent',
            'value' => 10,
            'is_active' => '1',
        ])->assertRedirect();

        $this->assertDatabaseHas('promos', [
            'code' => 'ON10',
            'is_active' => 1,
        ]);
    }

    public function test_unchecking_gateway_sandbox_turns_test_mode_off(): void
    {
        GatewayCredentials::save([], [
            'jazzcash' => ['online_enabled' => '1'],
        ]);

        $this->assertFalse(GatewayCredentials::sandbox('jazzcash'));
        $this->assertTrue(GatewayCredentials::onlineEnabled('jazzcash'));

        GatewayCredentials::save([], [
            'jazzcash' => ['sandbox' => '1'],
        ]);

        $this->assertTrue(GatewayCredentials::sandbox('jazzcash'));
        $this->assertFalse(GatewayCredentials::onlineEnabled('jazzcash'));
    }
}
