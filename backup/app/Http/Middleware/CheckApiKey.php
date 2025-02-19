<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckApiKey
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
              // Retrieve the API key from the request headers
              $apiKey = $request->header('API-Key');
        
              // Check if the API key is valid
              if ($apiKey !== env('API_KEY')) {
                  // Return unauthorized response if the API key is invalid
                  return response()->json(['error' => 'Unauthorized'], 401);
              }
      
              // If the API key is valid, proceed with the request
              return $next($request);
    }
}
