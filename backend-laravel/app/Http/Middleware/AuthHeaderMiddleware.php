<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AuthHeaderMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $token = $request->cookie('access_token');

        $request->headers->set('Authorization', 'Bearer '.$token);

        return $next($request);
    }
}
