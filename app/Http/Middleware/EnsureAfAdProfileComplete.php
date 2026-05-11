<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAfAdProfileComplete
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->isAfAd() && !auth()->user()->profile) {
            return redirect()->route('afad.profile.create')
                ->with('info', 'Complete your profile before accessing other AF/AD features.');
        }

        return $next($request);
    }
}
