<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminOuRecepcionista
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, $next)
    {
        $cargo = auth()->user()->cargo;
        if ($cargo === 'gerente' || $cargo === 'recepcionista') {
            return $next($request);
        }
        return redirect()->route('dashboard');
    }
}
