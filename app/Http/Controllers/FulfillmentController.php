<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveFulfillmentRequest;
use App\Support\Catalog;
use App\Support\Fulfillment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

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
        $payload = $request->fulfillmentPayload();
        $location = Catalog::findLocation($payload['location_id']);

        if ($location === null) {
            return back()
                ->withErrors(['area_id' => 'Please choose a valid Karachi area.'])
                ->withInput()
                ->with('open_fulfillment', true);
        }

        $fee = $payload['method'] === Fulfillment::METHOD_DELIVERY
            ? (float) ($location['delivery_fee'] ?? 0)
            : 0.0;

        $this->fulfillment->put([
            'method' => $payload['method'],
            'area_id' => $payload['area_id'],
            'location_id' => $location['id'],
            'location_name' => $location['name'],
            'address' => $payload['address'],
            'city' => 'Karachi',
            'region' => 'Sindh',
            'postal_code' => null,
            'fee' => $fee,
            'delivery_fee' => $fee,
        ]);

        $label = $payload['method'] === Fulfillment::METHOD_DELIVERY
            ? 'Delivery to '.$payload['address']
            : 'Pickup at '.$location['name'];

        return redirect()
            ->intended(route('menu'))
            ->with('status', $label.' saved. Browse the menu to continue.');
    }

    public function dismiss(Request $request): RedirectResponse|JsonResponse
    {
        $this->fulfillment->markWelcomeSeen();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['ok' => true]);
        }

        return back()->with('status', 'Browse away - choose pickup or delivery anytime you order.');
    }
}
