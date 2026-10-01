<?php

namespace Tests\Feature;

use App\Support\Fulfillment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FulfillmentChooserTest extends TestCase
{
    use RefreshDatabase;

    public function test_delivery_saves_neighbourhood_and_store(): void
    {
        $response = $this->post(route('order.fulfillment.store'), [
            'method' => Fulfillment::METHOD_DELIVERY,
            'area_id' => 'clifton',
            'location_id' => 'gizri',
        ]);

        $response->assertRedirect(route('menu'));
        $response->assertSessionHas('dezato.fulfillment');

        $saved = session(Fulfillment::SESSION_KEY);
        $this->assertSame(Fulfillment::METHOD_DELIVERY, $saved['method']);
        $this->assertSame('clifton', $saved['area_id']);
        $this->assertSame('gizri', $saved['location_id']);
        $this->assertSame('Clifton', $saved['address']);
    }

    public function test_pickup_saves_store_without_neighbourhood(): void
    {
        $response = $this->post(route('order.fulfillment.store'), [
            'method' => Fulfillment::METHOD_PICKUP,
            'location_id' => 'dha-phase-6',
        ]);

        $response->assertRedirect(route('menu'));

        $saved = session(Fulfillment::SESSION_KEY);
        $this->assertSame(Fulfillment::METHOD_PICKUP, $saved['method']);
        $this->assertNull($saved['area_id']);
        $this->assertSame('dha-phase-6', $saved['location_id']);
        $this->assertNull($saved['address']);
        $this->assertSame(0.0, (float) $saved['fee']);
    }

    public function test_pickup_rejects_unknown_store(): void
    {
        $response = $this->from(route('home'))->post(route('order.fulfillment.store'), [
            'method' => Fulfillment::METHOD_PICKUP,
            'location_id' => 'not-a-store',
        ]);

        $response->assertRedirect(route('home', ['fulfillment' => 1]));
        $response->assertSessionHasErrors('location_id');
    }

    public function test_delivery_requires_area(): void
    {
        $response = $this->from(route('home'))->post(route('order.fulfillment.store'), [
            'method' => Fulfillment::METHOD_DELIVERY,
            'location_id' => 'gizri',
        ]);

        $response->assertRedirect(route('home', ['fulfillment' => 1]));
        $response->assertSessionHasErrors('area_id');
    }

    public function test_home_renders_two_pickup_stores(): void
    {
        $response = $this->get(route('home', ['fulfillment' => 1]));

        $response->assertOk();
        $response->assertSee('data-fulfillment-panel="pickup"', false);
        $response->assertSee('DHA Phase 6');
        $response->assertSee('Gizri');
        $response->assertSee('data-fulfillment-store', false);
    }
}
