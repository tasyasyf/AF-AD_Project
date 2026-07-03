<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Guards the read-only "Additional Access" viewer routes. A user may reach
 * them only if the admin has granted the matching permission key (admins
 * always pass). These routes are read-only by design, so this is a pure
 * view gate — no write routes ever sit behind it.
 */
class EnsurePermitted
{
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = auth()->user();

        if (!$user || !$user->hasPermission($permission)) {
            abort(403, 'You do not have permission to view this page.');
        }

        return $next($request);
    }
}
