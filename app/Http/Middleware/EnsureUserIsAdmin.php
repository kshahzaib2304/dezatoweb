<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user === null) {
            if ($this->wantsMachineResponse($request)) {
                return response()->json(['message' => 'Authentication required.'], 401);
            }

            return redirect()
                ->guest(route('login'))
                ->with('status', 'Please sign in with an admin account to manage the store.');
        }

        if (! $user->isAdmin()) {
            if ($this->wantsMachineResponse($request)) {
                return response()->json(['message' => 'Admin access required.'], 403);
            }

            abort(403);
        }

        return $next($request);
    }

    private function wantsMachineResponse(Request $request): bool
    {
        return $request->expectsJson()
            || $request->ajax()
            || $request->header('X-Requested-With') === 'XMLHttpRequest';
    }
}
