<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string $module): Response
    {
        $user = $request->user();

        if ($user->is_admin) {
            return $next($request);
        }

        if (!$user->permisos || !in_array($module, $user->permisos)) {
            abort(403, 'No tienes permisos para acceder al módulo: ' . $module);
        }

        return $next($request);
    }
}
