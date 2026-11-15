<?php

namespace App\Http\Middleware;

use App\Services\AnniversaryPuzzle;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAnniversaryPinIsVerified
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->session()->get(AnniversaryPuzzle::PIN_VERIFIED_SESSION_KEY) !== true) {
            return to_route('anniversary.pin');
        }

        return $next($request);
    }
}
