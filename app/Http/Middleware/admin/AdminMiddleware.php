<?php

namespace App\Http\Middleware\admin;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Check if user is authenticated
        if (! Auth::check()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('login')
                ->withSuccess('You have logged out successfully!');
        }
        if (Auth::user()->role !== 99) {
            return redirect()->route('login');
        }
        return $next($request);
    }
}
