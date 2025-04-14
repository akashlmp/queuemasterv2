<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Auth;

class AuthCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        return $token = '11';
        // if ($token == "") {
        //     return response()->json(["status"=>false,'message' => 'Unauthenticated!!, Please login first to get access.'], JsonResponse::HTTP_UNAUTHORIZED);
        // }

        // // if(Auth::guard('api')->user())
        // if(Auth::check())
        // {
        //     return $next($request);
        // }else{
        //     return response()->json(["status"=>false,'message' => 'Unauthenticated!!, Please login first to get access.'], JsonResponse::HTTP_UNAUTHORIZED);
        // }
    }
}
