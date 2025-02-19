<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    public function handle($request, Closure $next, $role)
    {
        if (!Auth::check() || !Auth::user()->hasRole($role)) {
            return redirect()->route('login')->withErrors(['message' => 'No tienes permiso para acceder a esta página.']);
        }

        return $next($request);
    }
}
