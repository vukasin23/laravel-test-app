<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class IsManager
{
    public function handle($request, Closure $next)
    {
        if (Auth::check() && Auth::user()->role->name === 'manager') {
            return $next($request);
        }

        abort(403);
    }
}
