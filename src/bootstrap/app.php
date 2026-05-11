<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            \App\Http\Middleware\SecurityHeaders::class,
        ]);

        // Railway termina o HTTPS no proxy. Confiar nesses headers
        // garante que o Laravel gere URLs seguras.
        $middleware->trustProxies(
            at: '*',
            headers: Request::HEADER_X_FORWARDED_FOR |
                Request::HEADER_X_FORWARDED_HOST |
                Request::HEADER_X_FORWARDED_PORT |
                Request::HEADER_X_FORWARDED_PROTO |
                Request::HEADER_X_FORWARDED_AWS_ELB
        );
        $middleware->alias([
            'gerente_ou_recepcionista' => \App\Http\Middleware\GerenteOuRecepcionista::class,
            'gerente' => \App\Http\Middleware\Gerente::class,
            'role' => \App\Http\Middleware\EnsureUserRole::class,
            'profissional' => \App\Http\Middleware\CheckProfissional::class,
            'cliente_redirect' => \App\Http\Middleware\EnsureClienteRedirect::class,
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
