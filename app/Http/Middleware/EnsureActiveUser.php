<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureActiveUser
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->user() && ! $request->user()->estado) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('login')->withErrors(['login' => 'Su cuenta se encuentra inactiva.']);
        }
        $response = $next($request);
        if ($request->user()) {
            $response->headers->set('Cache-Control', 'private, no-store');
        }
        return $response;
    }
}
