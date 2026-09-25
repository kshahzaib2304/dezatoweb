<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddCartItemRequest;
use App\Http\Requests\UpdateCartItemRequest;
use App\Support\Cart;
use App\Support\Catalog;
use App\Support\Fulfillment;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

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
            'title' => 'Cart | '.\App\Support\SiteBrand::name(),
            'metaDescription' => 'Review your Dezato order for pickup, Karachi delivery, or Pakistan courier.',
            'robots' => 'noindex, follow',
            'canonical' => route('cart.show'),
            'lines' => $lines,
            'subtotal' => $subtotal,
            'deliveryFee' => $deliveryFee,
            'feeLabel' => $this->fulfillment->feeLabel(),
            'total' => $total,
            'fulfillment' => $fulfillment,
            'fulfillmentSummary' => $this->fulfillment->summary(),
        ]);
    }

    public function store(AddCartItemRequest $request): RedirectResponse|JsonResponse
    {
        $productId = $request->string('product_id')->toString();
        $quantity = (int) $request->input('quantity', 1);

        if (Catalog::findProduct($productId) === null) {
            if ($this->wantsCartJson($request)) {
                return response()->json(['message' => 'That treat is unavailable.'], 422);
            }

            return back()->withErrors(['product_id' => 'That treat is unavailable.']);
        }

        $this->cart->add($productId, $quantity);

        return $this->cartMutationResponse($request, 'Added to your cart.');
    }

    public function update(UpdateCartItemRequest $request, string $product): RedirectResponse|JsonResponse
    {
        $this->cart->update($product, (int) $request->integer('quantity'));

        return $this->cartMutationResponse($request, 'Cart updated.');
    }

    public function destroy(Request $request, string $product): RedirectResponse|JsonResponse
    {
        $this->cart->remove($product);

        return $this->cartMutationResponse($request, 'Item removed.');
    }

    private function wantsCartJson(Request $request): bool
    {
        return $request->boolean('drawer')
            || $request->expectsJson()
            || $request->ajax()
            || $request->header('X-Cart-Drawer') === '1';
    }

    private function cartMutationResponse(Request $request, string $message): RedirectResponse|JsonResponse
    {
        if ($this->wantsCartJson($request)) {
            return response()->json($this->drawerPayload($message));
        }

        // Prefer returning to the browsing page with the quick cart open.
        if ($request->routeIs('cart.items.store')) {
            return back()
                ->with('status', $message)
                ->with('open_cart', true);
        }

        return redirect()
            ->route('cart.show')
            ->with('status', $message);
    }

    /**
     * @return array{message: string, count: int, subtotal: float, subtotal_label: string, html: string}
     */
    private function drawerPayload(string $message): array
    {
        $lines = $this->cart->lines();
        $subtotal = $this->cart->subtotal();

        return [
            'message' => $message,
            'count' => $this->cart->count(),
            'subtotal' => $subtotal,
            'subtotal_label' => pkr($subtotal),
            'html' => view('components.cart-drawer-body', [
                'lines' => $lines,
                'count' => $this->cart->count(),
                'subtotal' => $subtotal,
            ])->render(),
        ];
    }
}
