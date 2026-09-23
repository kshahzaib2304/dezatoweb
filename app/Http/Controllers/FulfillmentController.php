<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveFulfillmentRequest;
use App\Support\Catalog;
use App\Support\Fulfillment;
use Illuminate\Http\RedirectResponse;

class FulfillmentController extends Controller
{
    public function __construct(private readonly Fulfillment $fulfillment) {}

    public function start(): RedirectResponse
    {
        return redirect()
            ->route('home', ['fulfillment' => 1])
            ->with('open_fulfillment', true);
    }

    public function store(SaveFulfillmentRequest $request): RedirectResponse
    {
        $method = $request->string('method')->toString();

        if ($method === Fulfillment::METHOD_SHIPPING) {
            $this->fulfillment->put([
                'method' => Fulfillment::METHOD_SHIPPING,
                'location_id' => null,
                'location_name' => \App\Support\ShippingSettings::label(),
                'address' => $request->string('address')->trim()->toString(),
                'city' => $request->string('city')->trim()->toString(),
                'region' => $request->string('region')->trim()->toString(),
                'postal_code' => $request->string('postal_code')->trim()->toString(),
                'fee' => \App\Support\ShippingSettings::fee(),
            ]);

            return redirect()
                ->intended(route('menu'))
                ->with('status', 'Pakistan courier selected - browse the menu to continue.');
        }

        $location = Catalog::findLocation($request->string('location_id')->toString());

        if ($location === null) {
            return back()
                ->withErrors(['location_id' => 'Please choose a valid bakery location.'])
                ->withInput()
                ->with('open_fulfillment', true);
        }

        $fee = $method === Fulfillment::METHOD_DELIVERY
            ? (float) ($location['delivery_fee'] ?? 0)
            : 0.0;

        $this->fulfillment->put([
            'method' => $method,
            'location_id' => $location['id'],
            'location_name' => $location['name'],
            'address' => $method === Fulfillment::METHOD_DELIVERY
                ? $request->string('address')->trim()->toString()
                : null,
            'city' => null,
            'region' => null,
            'postal_code' => null,
            'fee' => $fee,
            'delivery_fee' => $fee,
        ]);

        return redirect()
            ->intended(route('menu'))
            ->with('status', 'Great - your order preferences are saved.');
    }

    public function dismiss(): RedirectResponse
    {
        $this->fulfillment->markWelcomeSeen();

        return back()->with('status', 'Browse away - choose pickup, delivery, or courier anytime you order.');
    }
}
