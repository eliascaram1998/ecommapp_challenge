<?php

namespace App\Http\Middleware;

use Closure;

class SetAuthenticatedSession
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!$request->session()->has('isAuthenticated')) {
            $request->session()->put('isAuthenticated', false);
        }

        return $next($request);
    }
}
