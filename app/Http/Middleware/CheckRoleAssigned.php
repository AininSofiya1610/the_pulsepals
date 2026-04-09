<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRoleAssigned
{
    public function handle(Request $request, Closure $next): Response
    {
        // Only check authenticated users
        if (auth()->check()) {
            $user = auth()->user();

            // If user has no role assigned
            if ($user->roles->isEmpty()) {
                // Allow access to pending page and logout only
                if (!$request->routeIs('account.pending') && !$request->routeIs('logout')) {
                    return redirect()->route('account.pending');
                }
            }
        }

        return $next($request);
    }
}
