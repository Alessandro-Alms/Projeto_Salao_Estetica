<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class GerenteOuRecepcionista
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, $next)
    {
        if ($request->user()?->hasRole(['gerente', 'recepcionista'])) {
            return $next($request);
        }

        abort(403, 'Acesso negado para o seu nível de permissão.');
    }
}
