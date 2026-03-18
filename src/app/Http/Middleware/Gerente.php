<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Gerente
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->user()->cargo === 'gerente') {
        return $next($request);
    }
    // Se for recepcionista ou outro, ele barra
    abort(403, 'Acesso restrito ao Administrador.');
    }
}
