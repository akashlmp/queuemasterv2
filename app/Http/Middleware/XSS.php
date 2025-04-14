<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class XSS
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $input = $request->all();
        array_walk_recursive($input, function (&$input) {
            $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
        });
        $request->merge($input);
        return $next($request);
    }
    // {
    //     $userInput = $request->all();
    //     array_walk_recursive($userInput, function (&$userInput) {
    //         $userInput = strip_tags($userInput);
    //     });
        
    //     // return $userInput;die;
    //     $request->merge($userInput);
    //     return $next($request);
    // }
}
