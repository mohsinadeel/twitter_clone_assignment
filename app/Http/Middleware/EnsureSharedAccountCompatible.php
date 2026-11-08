<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSharedAccountCompatible
{
    public function handle(Request $request, Closure $next): Response
    {
        abort_if((bool) $request->user()?->two_factor_secret, 403,
            'This account uses two-factor authentication in the main portal. Twitter access is unavailable for this account.');

        return $next($request);
    }
}
