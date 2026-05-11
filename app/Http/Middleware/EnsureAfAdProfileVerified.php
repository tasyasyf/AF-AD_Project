<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAfAdProfileVerified
{
    public function handle(Request $request, Closure $next): Response
    {
        $profile = auth()->user()?->profile;

        if (auth()->check() && auth()->user()->isAfAd() && (!$profile || $profile->status !== 'verified')) {
            return redirect()->route('afad.profile.show')
                ->with('info', 'Your profile is waiting for School Executive verification. You can submit claims after your profile is verified.');
        }

        return $next($request);
    }
}
