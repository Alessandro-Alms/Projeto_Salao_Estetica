<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureClienteRedirect
{
    public function handle(Request $request, Closure $next, string $context = 'default'): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        if ($user->cargo === 'cliente') {
            return $next($request);
        }

        return $this->redirectByRole($user->cargo, $context);
    }

    private function redirectByRole(string $role, string $context): Response
    {
        $message = 'Opcao restrita a clientes e cargos gerenciais.';

        if (in_array($role, ['gerente', 'recepcionista'], true)) {
            return $this->redirectForAdmin($context, $message);
        }

        if ($role === 'profissional') {
            return $this->redirectForProfissional($context, $message);
        }

        return $this->redirectToRoute('dashboard', $message);
    }

    private function redirectForAdmin(string $context, string $message): Response
    {
        return match ($context) {
            'agendar' => $this->redirectToRoute('admin.agendar.cliente', $message),
            'agendamentos' => $this->redirectToRoute('admin.agenda.index', $message),
            'produtos' => $this->redirectToRoute('admin.vendas.produtos.create', $message),
            'pacotes' => $this->redirectToRoute('admin.venda.create', $message),
            default => $this->redirectToRoute('dashboard', $message),
        };
    }

    private function redirectForProfissional(string $context, string $message): Response
    {
        return match ($context) {
            'agendar' => $this->redirectToRoute('profissional.agenda', $message),
            'agendamentos' => $this->redirectToRoute('profissional.agenda', $message),
            default => $this->redirectToRoute('dashboard', $message),
        };
    }

    private function redirectToRoute(string $route, string $message): RedirectResponse
    {
        return redirect()->route($route)->with('acesso_restrito', $message);
    }
}
