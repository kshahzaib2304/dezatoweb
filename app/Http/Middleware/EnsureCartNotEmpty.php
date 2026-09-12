<?php

namespace App\Http\Middleware;

use App\Support\Cart;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class EnsureCartNotEmpty
{
    public function __construct(private readonly Cart $cart) {}

    public function handle(Request $request, Closure $next): Response
    {
        if ($this->cart->count() > 0) {
            return $next($request);
        }

        return redirect()
            ->route('cart.show')
            ->with('status', 'Add something sweet to your cart before checkout.');
    }
}
