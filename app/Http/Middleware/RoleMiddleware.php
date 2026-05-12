<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * @param  array<int, string>  ...$roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], HttpResponse::HTTP_UNAUTHORIZED);
            }

            return redirect()->route('login');
        }

        if (! in_array($user->role, $roles, true)) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Forbidden.'], HttpResponse::HTTP_FORBIDDEN);
            }

            return redirect()->route('dashboard')->with('error', 'Akses ditolak.');
        }

        return $next($request);
    }
}
