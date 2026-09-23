<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdministrador
{
    public function handle(Request $request, Closure $next): Response
    {
        $usuario = $request->user();

        if (! $usuario || ! $usuario->estado || ! $usuario->esAdministrador()) {
            abort(403, 'No tiene permisos para acceder a esta opción.');
        }

        return $next($request);
    }
}
