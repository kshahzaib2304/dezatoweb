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
            return redirect()
                ->route('login')
                ->with('status', 'Please sign in with an admin account to manage the store.');
        }

        if (! $user->isAdmin()) {
            abort(403, 'Admin access required.');
        }

        return $next($request);
    }
}
