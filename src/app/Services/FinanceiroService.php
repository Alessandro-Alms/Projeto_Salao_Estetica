<?php

namespace App\Services;

use App\Models\Agendamento;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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

    public function fechamentoDiario(string $dataSelecionada): array
    {
        $agendamentos = Agendamento::where('status', 'executado')
            ->whereDate('updated_at', $dataSelecionada)
            ->with(['profissional', 'servico'])
            ->get();

        $totalServicos = $agendamentos->sum('valor_total');
        $totalComissoesServicos = $agendamentos->sum('valor_comissao');
        $totalProdutos = DB::table('vendas')->whereDate('created_at', $dataSelecionada)->sum('valor_venda');
        $totalComissoesProdutos = $this->calcularComissaoProduto((float) $totalProdutos);

        $totalMultas = Agendamento::where('status', 'cancelado')
            ->whereDate('updated_at', $dataSelecionada)
            ->sum('multa_valor');

        $totalPacotes = DB::table('cliente_pacotes')
            ->join('pacotes', 'cliente_pacotes.pacote_id', '=', 'pacotes.id_pacote')
            ->whereDate('cliente_pacotes.created_at', $dataSelecionada)
            ->sum('pacotes.valor_total');

        $totalComissoes = $totalComissoesServicos + $totalComissoesProdutos;
        $lucroLiquido = ($totalServicos + $totalProdutos + $totalPacotes + $totalMultas) - $totalComissoes;
        $vendas = DB::table('vendas')->whereDate('created_at', $dataSelecionada)->get();

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

        $receitaServicos = DB::table('agendamentos')
            ->where('status', 'executado')
            ->whereBetween('data_hora_inicio', [$inicioQuery, $fimQuery])
            ->sum('valor_total') ?? 0;

        $receitaProdutos = DB::table('vendas')
            ->whereNotNull('produto_id')
            ->whereBetween('created_at', [$inicioQuery, $fimQuery])
            ->sum('valor_venda') ?? 0;

        $receitaPacotes = DB::table('cliente_pacotes')
            ->join('pacotes', 'cliente_pacotes.pacote_id', '=', 'pacotes.id_pacote')
            ->whereBetween('cliente_pacotes.data_compra', [$dataInicio, $dataFim])
            ->sum('pacotes.valor_total') ?? 0;

        $receitaMultas = DB::table('agendamentos')
            ->where('status', 'cancelado')
            ->where('multa_valor', '>', 0)
            ->whereBetween('updated_at', [$inicioQuery, $fimQuery])
            ->sum('multa_valor') ?? 0;

        $totalEntradas = $receitaServicos + $receitaProdutos + $receitaPacotes + $receitaMultas;

        $despesaComissoesServicos = DB::table('agendamentos')
            ->where('status', 'executado')
            ->whereBetween('data_hora_inicio', [$inicioQuery, $fimQuery])
            ->sum('valor_comissao') ?? 0;

        $despesaComissoesProdutos = $this->calcularComissaoProduto((float) $receitaProdutos);
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

        $agendamentos = DB::table('agendamentos')
            ->where('status', 'executado')
            ->whereBetween('data_hora_inicio', [$inicioQuery, $fimQuery])
            ->selectRaw('COUNT(id_agendamento) as qtd, SUM(valor_total) as total')
            ->first();

        $vendas = DB::table('vendas')
            ->whereBetween('created_at', [$inicioQuery, $fimQuery])
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

        $receitaAnteriorAgendamentos = DB::table('agendamentos')
            ->where('status', 'executado')
            ->whereBetween('data_hora_inicio', [$inicioAnterior, $fimAnterior])
            ->sum('valor_total');

        $receitaAnteriorVendas = DB::table('vendas')
            ->whereBetween('created_at', [$inicioAnterior, $fimAnterior])
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

        $servicosPorProfissional = DB::table('agendamentos')
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

        $vendasPorProfissional = DB::table('vendas')
            ->whereNotNull('produto_id')
            ->whereBetween('created_at', [$inicioQuery, $fimQuery])
            ->select(
                'profissional_id',
                DB::raw('COUNT(id_venda) as total_vendas_produtos'),
                DB::raw('SUM(valor_venda) as receita_produtos')
            )
            ->groupBy('profissional_id')
            ->get()
            ->keyBy('profissional_id');

        return DB::table('users')
            ->where('cargo', 'profissional')
            ->select('id', 'name', 'telefone')
            ->get()
            ->map(function ($profissional) use ($servicosPorProfissional, $vendasPorProfissional) {
                $servicos = $servicosPorProfissional->get($profissional->id);
                $vendas = $vendasPorProfissional->get($profissional->id);

                $profissional->total_servicos = (int) ($servicos->total_servicos ?? 0);
                $profissional->total_vendas_produtos = (int) ($vendas->total_vendas_produtos ?? 0);
                $profissional->receita_servicos = (float) ($servicos->receita_servicos ?? 0);
                $profissional->receita_produtos = (float) ($vendas->receita_produtos ?? 0);
                $profissional->receita_gerada = $profissional->receita_servicos + $profissional->receita_produtos;
                $profissional->comissao_servicos = (float) ($servicos->comissao_servicos ?? 0);
                $profissional->comissao_produtos = $this->calcularComissaoProduto($profissional->receita_produtos);
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
