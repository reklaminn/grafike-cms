<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = session('locale', config('cms.default_language', 'tr'));

        if (in_array($locale, ['tr', 'en', 'de', 'ru', 'fr', 'ar'])) {
            app()->setLocale($locale);
        }

        return $next($request);
    }
}
