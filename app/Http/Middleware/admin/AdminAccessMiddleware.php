<?php

namespace App\Http\Middleware\admin;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAccessMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Check if user is authenticated
        if (Auth::check()) {
            // Get the current URL
            $currentUrl = $request->url();

            // Check if the current user is an admin
            if (Auth::user()->role === 99) {
                // Prevent access to admin pages if accessed via URL
                if (strpos($currentUrl, url('queuing/admin/')) !== true) {
                    return redirect()->route('developers-index')->with('error', 'You do not have permission to access this page.');
                }
            }

            // Add additional checks for other user roles if needed

            // Allow the request to proceed
            return $next($request);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')
            ->withSuccess('You have logged out successfully!');
    }
}
