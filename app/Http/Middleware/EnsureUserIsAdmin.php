<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Необходима авторизация.',
                ], 401);
            }

            return redirect()->route('login');
        }

        if (! in_array($request->user()->role, ['admin', 'owner'], true)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Доступ запрещен.',
                ], 403);
            }

            abort(403, 'У вас нет доступа к административной панели.');
        }

        return $next($request);
    }
}
