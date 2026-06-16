<?php

namespace App\Services;

use App\Models\Agendamento;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class FinanceiroService
{
    public const COMISSAO_SERVICO_PERCENTUAL = 50.00;
    public const COMISSAO_PRODUTO_PERCENTUAL = 10.00;

    public function calcularComissaoServico(float $valorBase): float
    {
        return round($valorBase * (self::COMISSAO_SERVICO_PERCENTUAL / 100), 2);
    }

    public function calcularComissaoProduto(float $valorVenda): float
    {
        return round($valorVenda * (self::COMISSAO_PRODUTO_PERCENTUAL / 100), 2);
    }

    private function vendasTemComissaoRegistrada(): bool
    {
        return Schema::hasColumn('vendas', 'valor_comissao');
    }

    private function somaComissaoProdutosQuery()
    {
        if ($this->vendasTemComissaoRegistrada()) {
            return DB::table('vendas');
        }

        return DB::table('vendas')
            ->leftJoin('users', 'vendas.profissional_id', '=', 'users.id');
    }

    private function expressaoComissaoProdutos(): string
    {
        if ($this->vendasTemComissaoRegistrada()) {
            return 'valor_comissao';
        }

        $percentual = self::COMISSAO_PRODUTO_PERCENTUAL / 100;

        return "CASE WHEN users.cargo = 'cliente' THEN 0 ELSE ROUND(vendas.valor_venda * {$percentual}, 2) END";
    }

    private function colunaDataVendaPaga(): string
    {
        return Schema::hasColumn('vendas', 'pago_em') ? 'pago_em' : 'created_at';
    }

    private function aplicarVendasPagas($query)
    {
        if (Schema::hasColumn('vendas', 'status_pagamento')) {
            $query->where('vendas.status_pagamento', 'pago');
        }

        return $query;
    }

    private function colunaResponsavelComissaoProduto(): string
    {
        if (Schema::hasColumn('vendas', 'confirmado_por_id')) {
            return 'COALESCE(vendas.confirmado_por_id, vendas.profissional_id)';
        }

        return 'vendas.profissional_id';
    }

    private function aplicarPacotesPagos($query)
    {
        if (Schema::hasColumn('cliente_pacotes', 'status_pagamento')) {
            $query->where('cliente_pacotes.status_pagamento', 'pago');
        }

        return $query;
    }

    private function colunaDataPacotePago(): string
    {
        return Schema::hasColumn('cliente_pacotes', 'pago_em') ? 'pago_em' : 'data_compra';
    }

    private function aplicarAgendamentosPagos($query)
    {
        if (Schema::hasColumn('agendamentos', 'status_pagamento')) {
            $query->where('agendamentos.status_pagamento', 'pago');
        }

        return $query;
    }

    public function fechamentoDiario(string $dataSelecionada): array
    {
        $agendamentos = $this->aplicarAgendamentosPagos(Agendamento::where('status', 'executado'))
            ->whereDate('updated_at', $dataSelecionada)
            ->with(['profissional', 'servico'])
            ->get();

        $totalServicos = $agendamentos->sum('valor_total');
        $totalComissoesPacotes = $this->aplicarPacotesPagos(DB::table('cliente_pacotes'))
            ->whereDate('cliente_pacotes.' . $this->colunaDataPacotePago(), $dataSelecionada)
            ->sum('valor_comissao');
        $totalComissoesServicos = $agendamentos->sum('valor_comissao') + $totalComissoesPacotes;
        $totalProdutos = $this->aplicarVendasPagas(DB::table('vendas'))
            ->whereDate('vendas.' . $this->colunaDataVendaPaga(), $dataSelecionada)
            ->sum('valor_venda');
        $totalComissoesProdutos = $this->aplicarVendasPagas($this->somaComissaoProdutosQuery())
            ->whereDate('vendas.' . $this->colunaDataVendaPaga(), $dataSelecionada)
            ->sum(DB::raw($this->expressaoComissaoProdutos()));

        $totalMultas = Agendamento::where('status', 'cancelado')
            ->whereDate('updated_at', $dataSelecionada)
            ->sum('multa_valor');

        $totalPacotes = $this->aplicarPacotesPagos(DB::table('cliente_pacotes'))
            ->join('pacotes', 'cliente_pacotes.pacote_id', '=', 'pacotes.id_pacote')
            ->whereDate('cliente_pacotes.' . $this->colunaDataPacotePago(), $dataSelecionada)
            ->sum('pacotes.valor_total');

        $totalComissoes = $totalComissoesServicos + $totalComissoesProdutos;
        $lucroLiquido = ($totalServicos + $totalProdutos + $totalPacotes + $totalMultas) - $totalComissoes;
        $vendas = $this->aplicarVendasPagas(DB::table('vendas'))
            ->whereDate('vendas.' . $this->colunaDataVendaPaga(), $dataSelecionada)
            ->get();

        return compact(
            'agendamentos',
            'totalServicos',
            'totalComissoesServicos',
            'totalProdutos',
            'totalComissoesProdutos',
            'totalMultas',
            'totalPacotes',
            'totalComissoes',
            'lucroLiquido',
            'vendas'
        );
    }

    public function resumoFinanceiroPeriodo(string $dataInicio, string $dataFim): array
    {
        $inicioQuery = $dataInicio . ' 00:00:00';
        $fimQuery = $dataFim . ' 23:59:59';

        $receitaServicos = $this->aplicarAgendamentosPagos(DB::table('agendamentos'))
            ->where('status', 'executado')
            ->whereBetween('data_hora_inicio', [$inicioQuery, $fimQuery])
            ->sum('valor_total') ?? 0;

        $receitaProdutos = $this->aplicarVendasPagas(DB::table('vendas'))
            ->whereNotNull('vendas.produto_id')
            ->whereBetween('vendas.' . $this->colunaDataVendaPaga(), [$inicioQuery, $fimQuery])
            ->sum('valor_venda') ?? 0;

        $receitaPacotes = $this->aplicarPacotesPagos(DB::table('cliente_pacotes'))
            ->join('pacotes', 'cliente_pacotes.pacote_id', '=', 'pacotes.id_pacote')
            ->whereBetween('cliente_pacotes.' . $this->colunaDataPacotePago(), [$inicioQuery, $fimQuery])
            ->sum('pacotes.valor_total') ?? 0;

        $receitaMultas = DB::table('agendamentos')
            ->where('status', 'cancelado')
            ->where('multa_valor', '>', 0)
            ->whereBetween('updated_at', [$inicioQuery, $fimQuery])
            ->sum('multa_valor') ?? 0;

        $totalEntradas = $receitaServicos + $receitaProdutos + $receitaPacotes + $receitaMultas;

        $despesaComissoesServicos = $this->aplicarAgendamentosPagos(DB::table('agendamentos'))
            ->where('status', 'executado')
            ->whereBetween('data_hora_inicio', [$inicioQuery, $fimQuery])
            ->sum('valor_comissao') ?? 0;

        $despesaComissoesPacotes = $this->aplicarPacotesPagos(DB::table('cliente_pacotes'))
            ->whereNotNull('vendedor_id')
            ->whereBetween('cliente_pacotes.' . $this->colunaDataPacotePago(), [$inicioQuery, $fimQuery])
            ->sum('valor_comissao') ?? 0;

        $despesaComissoesServicos += $despesaComissoesPacotes;
        $despesaComissoesProdutos = $this->aplicarVendasPagas($this->somaComissaoProdutosQuery())
            ->whereNotNull('vendas.produto_id')
            ->whereBetween('vendas.' . $this->colunaDataVendaPaga(), [$inicioQuery, $fimQuery])
            ->sum(DB::raw($this->expressaoComissaoProdutos())) ?? 0;
        $despesaComissoes = $despesaComissoesServicos + $despesaComissoesProdutos;
        $totalSaidas = $despesaComissoes;
        $saldoLiquido = $totalEntradas - $totalSaidas;

        return compact(
            'receitaServicos',
            'receitaProdutos',
            'receitaPacotes',
            'receitaMultas',
            'totalEntradas',
            'despesaComissoes',
            'totalSaidas',
            'saldoLiquido'
        );
    }

    public function resumoFaturamentoPeriodo(string $dataInicio, string $dataFim): array
    {
        $inicioQuery = $dataInicio . ' 00:00:00';
        $fimQuery = $dataFim . ' 23:59:59';

        $agendamentos = $this->aplicarAgendamentosPagos(DB::table('agendamentos'))
            ->where('status', 'executado')
            ->whereBetween('data_hora_inicio', [$inicioQuery, $fimQuery])
            ->selectRaw('COUNT(id_agendamento) as qtd, SUM(valor_total) as total')
            ->first();

        $vendas = $this->aplicarVendasPagas(DB::table('vendas'))
            ->whereBetween('vendas.' . $this->colunaDataVendaPaga(), [$inicioQuery, $fimQuery])
            ->selectRaw('COUNT(id_venda) as qtd, SUM(valor_venda) as total')
            ->first();

        $multas = DB::table('agendamentos')
            ->where('status', 'cancelado')
            ->where('multa_valor', '>', 0)
            ->whereBetween('updated_at', [$inicioQuery, $fimQuery])
            ->selectRaw('COUNT(id_agendamento) as qtd, SUM(multa_valor) as total')
            ->first();

        $receitaServicos = $agendamentos->total ?? 0;
        $receitaVendas = $vendas->total ?? 0;
        $receitaMultas = $multas->total ?? 0;
        $faturamentoTotal = $receitaServicos + $receitaVendas + $receitaMultas;

        $qtdTransacoes = ($agendamentos->qtd ?? 0) + ($vendas->qtd ?? 0) + ($multas->qtd ?? 0);
        $ticketMedio = $qtdTransacoes > 0 ? $faturamentoTotal / $qtdTransacoes : 0;

        $diasPeriodo = Carbon::parse($dataInicio)->diffInDays(Carbon::parse($dataFim)) + 1;
        $inicioAnterior = Carbon::parse($dataInicio)->subDays($diasPeriodo)->format('Y-m-d') . ' 00:00:00';
        $fimAnterior = Carbon::parse($dataFim)->subDays($diasPeriodo)->format('Y-m-d') . ' 23:59:59';

        $receitaAnteriorAgendamentos = $this->aplicarAgendamentosPagos(DB::table('agendamentos'))
            ->where('status', 'executado')
            ->whereBetween('data_hora_inicio', [$inicioAnterior, $fimAnterior])
            ->sum('valor_total');

        $receitaAnteriorVendas = $this->aplicarVendasPagas(DB::table('vendas'))
            ->whereBetween('vendas.' . $this->colunaDataVendaPaga(), [$inicioAnterior, $fimAnterior])
            ->sum('valor_venda');

        $receitaAnteriorMultas = DB::table('agendamentos')
            ->where('status', 'cancelado')
            ->where('multa_valor', '>', 0)
            ->whereBetween('updated_at', [$inicioAnterior, $fimAnterior])
            ->sum('multa_valor');

        $faturamentoAnterior = $receitaAnteriorAgendamentos + $receitaAnteriorVendas + $receitaAnteriorMultas;

        $crescimento = 0;
        if ($faturamentoAnterior > 0) {
            $crescimento = (($faturamentoTotal - $faturamentoAnterior) / $faturamentoAnterior) * 100;
        } elseif ($faturamentoTotal > 0) {
            $crescimento = 100;
        }

        return compact(
            'receitaServicos',
            'receitaVendas',
            'receitaMultas',
            'faturamentoTotal',
            'ticketMedio',
            'qtdTransacoes',
            'faturamentoAnterior',
            'crescimento'
        );
    }

    public function resumoComissoesPeriodo(string $dataInicio, string $dataFim)
    {
        $inicioQuery = $dataInicio . ' 00:00:00';
        $fimQuery = $dataFim . ' 23:59:59';

        $servicosPorProfissional = $this->aplicarAgendamentosPagos(DB::table('agendamentos'))
            ->where('status', 'executado')
            ->whereBetween('data_hora_inicio', [$inicioQuery, $fimQuery])
            ->select(
                'profissional_id',
                DB::raw('COUNT(id_agendamento) as total_servicos'),
                DB::raw('SUM(valor_total) as receita_servicos'),
                DB::raw('SUM(valor_comissao) as comissao_servicos')
            )
            ->groupBy('profissional_id')
            ->get()
            ->keyBy('profissional_id');

        $pacotesPorVendedor = $this->aplicarPacotesPagos(DB::table('cliente_pacotes'))
            ->join('pacotes', 'cliente_pacotes.pacote_id', '=', 'pacotes.id_pacote')
            ->whereNotNull('cliente_pacotes.vendedor_id')
            ->whereBetween('cliente_pacotes.' . $this->colunaDataPacotePago(), [$inicioQuery, $fimQuery])
            ->select(
                'cliente_pacotes.vendedor_id',
                DB::raw('COUNT(cliente_pacotes.id) as total_pacotes'),
                DB::raw('SUM(pacotes.valor_total) as receita_pacotes'),
                DB::raw('SUM(cliente_pacotes.valor_comissao) as comissao_pacotes')
            )
            ->groupBy('cliente_pacotes.vendedor_id')
            ->get()
            ->keyBy('vendedor_id');

        $vendasPorProfissional = $this->aplicarVendasPagas($this->somaComissaoProdutosQuery())
            ->whereNotNull('vendas.produto_id')
            ->whereBetween('vendas.' . $this->colunaDataVendaPaga(), [$inicioQuery, $fimQuery])
            ->select(
                DB::raw($this->colunaResponsavelComissaoProduto() . ' as profissional_id'),
                DB::raw('COUNT(vendas.id_venda) as total_vendas_produtos'),
                DB::raw('SUM(vendas.valor_venda) as receita_produtos'),
                DB::raw('SUM(' . $this->expressaoComissaoProdutos() . ') as comissao_produtos')
            )
            ->groupBy(DB::raw($this->colunaResponsavelComissaoProduto()))
            ->get()
            ->keyBy('profissional_id');

        return DB::table('users')
            ->whereIn('cargo', ['profissional', 'recepcionista'])
            ->select('id', 'name', 'telefone', 'cargo')
            ->get()
            ->map(function ($profissional) use ($servicosPorProfissional, $pacotesPorVendedor, $vendasPorProfissional) {
                $servicos = $servicosPorProfissional->get($profissional->id);
                $pacotes = $pacotesPorVendedor->get($profissional->id);
                $vendas = $vendasPorProfissional->get($profissional->id);

                $profissional->total_servicos = (int) ($servicos->total_servicos ?? 0) + (int) ($pacotes->total_pacotes ?? 0);
                $profissional->total_vendas_produtos = (int) ($vendas->total_vendas_produtos ?? 0);
                $profissional->receita_servicos = (float) ($servicos->receita_servicos ?? 0);
                $profissional->receita_pacotes = (float) ($pacotes->receita_pacotes ?? 0);
                $profissional->receita_produtos = (float) ($vendas->receita_produtos ?? 0);
                $profissional->receita_gerada = $profissional->receita_servicos + $profissional->receita_pacotes + $profissional->receita_produtos;
                $profissional->comissao_servicos = (float) ($servicos->comissao_servicos ?? 0) + (float) ($pacotes->comissao_pacotes ?? 0);
                $profissional->comissao_produtos = (float) ($vendas->comissao_produtos ?? 0);
                $profissional->comissao_a_pagar = $profissional->comissao_servicos + $profissional->comissao_produtos;

                return $profissional;
            })
            ->filter(function ($profissional) {
                return $profissional->total_servicos > 0 || $profissional->total_vendas_produtos > 0;
            })
            ->sortByDesc('comissao_a_pagar')
            ->values();
    }
}
