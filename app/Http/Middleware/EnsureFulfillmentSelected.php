<?php

namespace App\Http\Middleware;

use App\Support\Fulfillment;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class EnsureFulfillmentSelected
{
    public function __construct(private readonly Fulfillment $fulfillment) {}

    public function handle(Request $request, Closure $next): Response
    {
        if ($this->fulfillment->has()) {
            return $next($request);
        }

        $homeWithModal = route('home', ['fulfillment' => 1]);

        if (! $request->isMethod('GET')) {
            $intended = $request->headers->get('referer') ?: route('menu');
            $request->session()->put('url.intended', $intended);

            return redirect()
                ->to($homeWithModal)
                ->with('open_fulfillment', true)
                ->with('status', 'Choose pickup, delivery, or shipping to continue your order.');
        }

        return redirect()
            ->guest($homeWithModal)
            ->with('open_fulfillment', true)
            ->with('status', 'Choose pickup, delivery, or shipping to continue your order.');
    }
}
