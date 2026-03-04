<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackPageView
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Track page view after response (non-blocking)
        if ($response->isSuccessful() && ! $request->ajax()) {
            $page = $request->attributes->get('page');
            if ($page) {
                $page->increment('view_count');
            }
        }

        return $response;
    }
}
