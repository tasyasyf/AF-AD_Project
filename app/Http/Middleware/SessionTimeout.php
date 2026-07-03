<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SessionTimeout
{
    /**
     * Idle timeout in seconds. Users are logged out after this many
     * seconds without any request to the server.
     */
    private const TIMEOUT_SECONDS = 120;

    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $lastActivity = $request->session()->get('last_activity_at');

            if ($lastActivity && (now()->getTimestamp() - $lastActivity) > self::TIMEOUT_SECONDS) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                if ($request->expectsJson()) {
                    return response()->json(['message' => 'Session expired due to inactivity.'], 401);
                }

                return redirect()->route('login', ['timeout' => 1]);
            }

            $request->session()->put('last_activity_at', now()->getTimestamp());
        }

        return $next($request);
    }
}
