<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'gerente_ou_recepcionista' => \App\Http\Middleware\GerenteOuRecepcionista::class,
            'gerente' => \App\Http\Middleware\Gerente::class,
        ]);
    })
        ->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'profissional' => \App\Http\Middleware\CheckProfissional::class,

    ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Illuminate\Session\TokenMismatchException $exception, \Illuminate\Http\Request $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'SessÃ£o expirada. Atualize a pÃ¡gina e tente novamente.'], 419);
            }

            return redirect()
                ->route('login')
                ->with('status', 'Sua sessÃ£o expirou. Entre novamente.');
        });
    })->create();
