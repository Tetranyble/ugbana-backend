<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {

        //$guard = $request->wantsJson() ? 'api' : 'web';

        if (auth('api')->check() && auth('api')->user()->hasRoles($roles)) {

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
