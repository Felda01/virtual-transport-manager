<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class LocaleMiddleware
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
        $locale = $request->cookie('locale');

        if ($locale && in_array($locale, config('translatable.available_locales'))) {
            app()->setLocale($locale);
        } else {
            app()->setLocale(config('translatable.fallback_locale'));
        }

        return $next($request);
    }
}
