<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $permissions = null): Response
    {
        $guard = $request->wantsJson() ? 'api' : 'web';
        if (auth($guard)->check() && auth($guard)->user()->hasPermissions($permissions)) {
            return $next($request);
        }

        return \response()
            ->json([
                'status' => false,
                'message' => 'This action is unauthorized.',
            ],
                403
            );

    }
}
