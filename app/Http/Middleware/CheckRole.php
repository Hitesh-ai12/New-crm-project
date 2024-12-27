<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $role)
    {
        // Check if the user is logged in and has the required role
        if (Auth::check() && Auth::user()->role == $role) {
            return $next($request);
        }

        // Redirect if user doesn't have the right role
        return redirect('/home')->with('error', 'You do not have access to this page.');
    }
}
