<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AuthMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): mixed
    {
        // Not logged in → redirect to login
        if (!session('user')) {
            return redirect()->route('login');
        }

        // Role check (comma-separated roles passed as middleware params)
        if (!empty($roles)) {
            $userRole = session('user.role');
           
        }

        return $next($request);
    }
}
