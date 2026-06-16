<?php

namespace App\Services;

use App\Models\ClientePacote;
use App\Models\Pacote;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;

class ClientePacoteService
{
    public function venderPacote(
        int $clienteId,
        int $pacoteId,
        ?int $vendedorId = null,
        string $statusPagamento = 'pago',
        ?string $formaPagamento = null,
        ?int $confirmadoPorId = null,
        ?array $pagamentos = null
    ): ClientePacote
    {
        $pacote = Pacote::findOrFail($pacoteId);
        $financeiroService = app(FinanceiroService::class);
        $valorComissao = $vendedorId
            ? $financeiroService->calcularComissaoServico((float) $pacote->valor_total)
            : 0;

        $dados = [
            'cliente_id' => $clienteId,
            'pacote_id' => $pacote->id_pacote,
            'vendedor_id' => $vendedorId,
            'sessoes_restantes' => $pacote->quantidade_sessoes,
            'data_compra' => now(),
            'data_validade' => now()->addDays($pacote->validade_dias),
            'valor_comissao' => $valorComissao,
            'comissao_paga_percentual' => $vendedorId ? FinanceiroService::COMISSAO_SERVICO_PERCENTUAL : 0,
            'status' => 'ativo',
        ];

        if (Schema::hasColumn('cliente_pacotes', 'status_pagamento')) {
            $dados['status_pagamento'] = $statusPagamento;
        }

        if (Schema::hasColumn('cliente_pacotes', 'forma_pagamento')) {
            $dados['forma_pagamento'] = $formaPagamento;
        }

        if (Schema::hasColumn('cliente_pacotes', 'pago_em')) {
            $dados['pago_em'] = $statusPagamento === 'pago' ? now() : null;
        }

        if (Schema::hasColumn('cliente_pacotes', 'confirmado_por_id')) {
            $dados['confirmado_por_id'] = $statusPagamento === 'pago' ? $confirmadoPorId : null;
        }

        $clientePacote = ClientePacote::create($dados);

        if ($statusPagamento === 'pago') {
            $pagamentoService = app(PagamentoService::class);
            $pagamentoService->registrar(
                $clientePacote,
                $pagamentos ?: $pagamentoService->normalizar([], (float) $pacote->valor_total, $formaPagamento ?? 'dinheiro'),
                $confirmadoPorId
            );
        }

        return $clientePacote;
    }

    public function consumirSessao(int $clientePacoteId, int $clienteId, int $servicoId): ClientePacote
    {
        $clientePacote = ClientePacote::with('pacote.servicos')
            ->where('id', $clientePacoteId)
            ->where('cliente_id', $clienteId)
            ->where('status', 'ativo')
            ->where('sessoes_restantes', '>', 0)
            ->whereDate('data_validade', '>=', Carbon::today())
            ->when(Schema::hasColumn('cliente_pacotes', 'status_pagamento'), function ($query) {
                $query->where('status_pagamento', 'pago');
            })
            ->first();

        if (!$clientePacote) {
            throw new \RuntimeException('Pacote inválido, vencido ou sem sessões disponíveis.');
        }

        if (! $clientePacote->pacote->aceitaServico($servicoId)) {
            throw new \RuntimeException('Este pacote não pertence ao serviço deste agendamento.');
        }

        $clientePacote->sessoes_restantes -= 1;

        if ($clientePacote->sessoes_restantes === 0) {
            $clientePacote->status = 'finalizado';
        }

        $clientePacote->save();

        return $clientePacote;
    }
}
