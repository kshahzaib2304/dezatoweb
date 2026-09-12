<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddCartItemRequest;
use App\Http\Requests\UpdateCartItemRequest;
use App\Support\Cart;
use App\Support\Catalog;
use App\Support\Fulfillment;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class CartController extends Controller
{
    public function __construct(
        private readonly Cart $cart,
        private readonly Fulfillment $fulfillment,
    ) {}

    public function show(): View
    {
        $lines = $this->cart->lines();
        $subtotal = $this->cart->subtotal();
        $fulfillment = $this->fulfillment->get();
        $deliveryFee = $this->fulfillment->fee();
        $total = round($subtotal + $deliveryFee, 2);

        return view('pages.cart', [
            'title' => 'Cart | Dezato Cake House',
            'metaDescription' => 'Review your Dezato order for pickup, Karachi delivery, or Pakistan courier.',
            'lines' => $lines,
            'subtotal' => $subtotal,
            'deliveryFee' => $deliveryFee,
            'feeLabel' => $this->fulfillment->feeLabel(),
            'total' => $total,
            'fulfillment' => $fulfillment,
            'fulfillmentSummary' => $this->fulfillment->summary(),
        ]);
    }

    public function store(AddCartItemRequest $request): RedirectResponse
    {
        $productId = $request->string('product_id')->toString();
        $quantity = (int) $request->input('quantity', 1);

        if (Catalog::findProduct($productId) === null) {
            return back()->withErrors(['product_id' => 'That treat is unavailable.']);
        }

        $this->cart->add($productId, $quantity);

        return redirect()
            ->route('cart.show')
            ->with('status', 'Added to your cart.');
    }

    public function update(UpdateCartItemRequest $request, string $product): RedirectResponse
    {
        $this->cart->update($product, (int) $request->integer('quantity'));

        return redirect()
            ->route('cart.show')
            ->with('status', 'Cart updated.');
    }

    public function destroy(string $product): RedirectResponse
    {
        $this->cart->remove($product);

        return redirect()
            ->route('cart.show')
            ->with('status', 'Item removed.');
    }
}
