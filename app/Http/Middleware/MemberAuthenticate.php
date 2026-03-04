<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class MemberAuthenticate
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::guard('member')->check()) {
            return redirect()->route('member.login');
        }

        return $next($request);
    }
}
