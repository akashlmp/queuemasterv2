<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticateUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if the user is authenticated
        if (! Auth::check()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('login')
                ->withSuccess('You have logged out successfully!');
        }

        if (Auth::user()->role !== 99) {
            // $userId = Auth::id();
            // $subscriptionPlanId = DB::table('queuetb_users')
            //         ->where('id', $userId)
            //         ->value('subscription_plan_id');
            // if($subscriptionPlanId == 0)
            // {
            //     return redirect()->route('subscription');

            // }
            // else
            // {
            return $next($request);
            // }
        }
        return redirect()->route('admin.dash.route');
    }
}
