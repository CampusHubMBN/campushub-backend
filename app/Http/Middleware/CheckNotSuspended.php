<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckNotSuspended
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->user()?->suspended_at) {
            return response()->json([
                'message' => 'Votre compte a été suspendu. Contactez l\'administration.',
                'code'    => 'ACCOUNT_SUSPENDED',
            ], 403);
        }

        return $next($request);
    }
}