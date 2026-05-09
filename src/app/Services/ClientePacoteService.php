<?php

namespace App\Services;

use App\Models\ClientePacote;
use App\Models\Pacote;
use Carbon\Carbon;

class ClientePacoteService
{
    public function venderPacote(int $clienteId, int $pacoteId, ?int $vendedorId = null): ClientePacote
    {
        $pacote = Pacote::findOrFail($pacoteId);
        $financeiroService = app(FinanceiroService::class);
        $valorComissao = $vendedorId
            ? $financeiroService->calcularComissaoServico((float) $pacote->valor_total)
            : 0;

        return ClientePacote::create([
            'cliente_id' => $clienteId,
            'pacote_id' => $pacote->id_pacote,
            'vendedor_id' => $vendedorId,
            'sessoes_restantes' => $pacote->quantidade_sessoes,
            'data_compra' => now(),
            'data_validade' => now()->addDays($pacote->validade_dias),
            'valor_comissao' => $valorComissao,
            'comissao_paga_percentual' => $vendedorId ? FinanceiroService::COMISSAO_SERVICO_PERCENTUAL : 0,
            'status' => 'ativo',
        ]);
    }

    public function consumirSessao(int $clientePacoteId, int $clienteId, int $servicoId): ClientePacote
    {
        $clientePacote = ClientePacote::with('pacote.servicos')
            ->where('id', $clientePacoteId)
            ->where('cliente_id', $clienteId)
            ->where('status', 'ativo')
            ->where('sessoes_restantes', '>', 0)
            ->whereDate('data_validade', '>=', Carbon::today())
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
