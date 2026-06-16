<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class PagamentoService
{
    public const FORMAS_PAGAMENTO = ['dinheiro', 'pix', 'cartao_debito', 'cartao_credito'];

    public function normalizar(array $pagamentos, float $valorTotal, ?string $formaUnica = null): array
    {
        if ($valorTotal <= 0) {
            return [];
        }

        if (empty($pagamentos) && $formaUnica) {
            $pagamentos = [[
                'forma_pagamento' => $formaUnica,
                'valor' => $valorTotal,
            ]];
        }

        $normalizados = collect($pagamentos)
            ->map(function ($pagamento) {
                return [
                    'forma_pagamento' => $pagamento['forma_pagamento'] ?? null,
                    'valor' => $this->normalizarValor($pagamento['valor'] ?? null),
                ];
            })
            ->filter(fn ($pagamento) => $pagamento['forma_pagamento'] || $pagamento['valor'] > 0)
            ->values();

        if ($normalizados->isEmpty()) {
            throw ValidationException::withMessages([
                'pagamentos' => 'Informe pelo menos uma forma de pagamento.',
            ]);
        }

        foreach ($normalizados as $index => $pagamento) {
            if (! in_array($pagamento['forma_pagamento'], self::FORMAS_PAGAMENTO, true)) {
                throw ValidationException::withMessages([
                    "pagamentos.{$index}.forma_pagamento" => 'Forma de pagamento invalida.',
                ]);
            }

            if ($pagamento['valor'] <= 0) {
                throw ValidationException::withMessages([
                    "pagamentos.{$index}.valor" => 'O valor de cada pagamento deve ser maior que zero.',
                ]);
            }
        }

        $totalCentavos = $this->centavos($valorTotal);
        $somaCentavos = $normalizados->sum(fn ($pagamento) => $this->centavos($pagamento['valor']));

        if ($somaCentavos !== $totalCentavos) {
            throw ValidationException::withMessages([
                'pagamentos' => 'A soma dos pagamentos deve ser exatamente R$ ' . number_format($valorTotal, 2, ',', '.') . '.',
            ]);
        }

        return $normalizados->all();
    }

    public function registrar(Model $pagavel, array $pagamentos, ?int $recebidoPorId = null): void
    {
        if (! method_exists($pagavel, 'pagamentos')) {
            return;
        }

        $pagavel->pagamentos()->delete();

        foreach ($pagamentos as $pagamento) {
            $pagavel->pagamentos()->create([
                'forma_pagamento' => $pagamento['forma_pagamento'],
                'valor' => $pagamento['valor'],
                'recebido_por_id' => $recebidoPorId,
                'pago_em' => now(),
            ]);
        }
    }

    public function formaResumo(array $pagamentos, ?string $fallback = null): ?string
    {
        $formas = collect($pagamentos)->pluck('forma_pagamento')->unique()->values();

        if ($formas->count() === 1) {
            return $formas->first();
        }

        return $formas->count() > 1 ? 'dividido' : $fallback;
    }

    public function descricao(Collection $pagamentos, ?string $fallback = null): string
    {
        if ($pagamentos->isEmpty()) {
            return $fallback ?: '-';
        }

        return $pagamentos
            ->map(fn ($pagamento) => $this->label($pagamento->forma_pagamento) . ' R$ ' . number_format((float) $pagamento->valor, 2, ',', '.'))
            ->join(' + ');
    }

    public function label(?string $forma): string
    {
        return [
            'dinheiro' => 'Dinheiro',
            'pix' => 'PIX',
            'cartao_debito' => 'Cartao de debito',
            'cartao_credito' => 'Cartao de credito',
            'pacote' => 'Pacote',
            'dividido' => 'Pagamento dividido',
        ][$forma] ?? ucfirst((string) $forma);
    }

    private function normalizarValor($valor): float
    {
        if (is_string($valor)) {
            $valor = trim($valor);

            if (str_contains($valor, ',')) {
                $valor = str_replace('.', '', $valor);
                $valor = str_replace(',', '.', $valor);
            }
        }

        return round((float) $valor, 2);
    }

    private function centavos(float $valor): int
    {
        return (int) round($valor * 100);
    }
}
