<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EnsureUserIsWerkvoorbereider
{
    public function handle(Request $request, Closure $next)
    {
        // pronađemo ID role "werkvoorbereider"
        $werkRoleId = DB::table('roles')
            ->where('name','werkvoorbereider')
            ->value('id');

        // PROVERA
        if (!Auth::check() || Auth::user()->role_id !== $werkRoleId) {
            abort(403);
        }

        return $next($request);
    }
}
