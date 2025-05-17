<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class IsVoorman
{
    public function handle($request, Closure $next)
    {
        if (Auth::check() && Auth::user()->role->name === 'voorman') {
            return $next($request);
        }

        abort(403);
    }
}
