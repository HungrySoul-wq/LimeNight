<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (
            !auth()->check() ||
            !in_array(auth()->user()->role, ['admin', 'owner'], true)
        ) {
            abort(403, 'У вас нет доступа к административной панели.');
        }

        return $next($request);
    }
}
