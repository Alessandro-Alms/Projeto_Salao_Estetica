<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Agendamento;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\RelatorioExport;

class RelatorioController extends Controller
{
    /**
     * Central de Relatórios (O Menu Principal)
     */
    public function index(Request $request)
    {
        // Filtros de Data para o resumo rápido da central
        $dataInicio = $request->input('data_inicio', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dataFim = $request->input('data_fim', Carbon::now()->endOfMonth()->format('Y-m-d'));

        $inicioQuery = $dataInicio . ' 00:00:00';
        $fimQuery = $dataFim . ' 23:59:59';

        // Resumo para os cards da central
        $faturamentoTotal = Agendamento::where('status', 'executado')
            ->whereBetween('updated_at', [$inicioQuery, $fimQuery])
            ->sum('valor_comissao'); 

        $totalAgendamentos = Agendamento::whereBetween('created_at', [$inicioQuery, $fimQuery])->count();
        $totalExecutados = Agendamento::where('status', 'executado')
            ->whereBetween('updated_at', [$inicioQuery, $fimQuery])->count();

        $desempenhoProfissionais = DB::table('agendamentos')
            ->join('users', 'agendamentos.profissional_id', '=', 'users.id')
            ->select('users.name', DB::raw('COUNT(agendamentos.id_agendamento) as total_atendimentos'), DB::raw('SUM(agendamentos.valor_comissao) as total_gerado'))
            ->where('agendamentos.status', 'executado')
            ->whereBetween('agendamentos.updated_at', [$inicioQuery, $fimQuery])
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('total_atendimentos')
            ->get();

        return view('admin.relatorios.index', compact(
            'faturamentoTotal', 'totalAgendamentos', 'totalExecutados', 
            'desempenhoProfissionais', 'dataInicio', 'dataFim'
        ));
    }

    /**
     * RELATÓRIO 1: Faturamento por Período (Detalhado)
     */
    public function faturamento(Request $request)
    {
        $dataInicio = $request->input('data_inicio', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dataFim = $request->input('data_fim', Carbon::now()->endOfMonth()->format('Y-m-d'));

        $inicioQuery = $dataInicio . ' 00:00:00';
        $fimQuery = $dataFim . ' 23:59:59';

        // 1. Serviços executados por dia
        $servicosPorDia = DB::table('agendamentos')
            ->select(DB::raw('DATE(updated_at) as data'), DB::raw('SUM(valor_total) as total'))
            ->where('status', 'executado')
            ->whereBetween('updated_at', [$inicioQuery, $fimQuery])
            ->groupBy(DB::raw('DATE(updated_at)'))
            ->get();

        // 2. Vendas de produtos por dia
        $vendasPorDia = DB::table('vendas')
            ->select(DB::raw('DATE(created_at) as data'), DB::raw('SUM(valor_venda) as total'))
            ->whereBetween('created_at', [$inicioQuery, $fimQuery])
            ->groupBy(DB::raw('DATE(created_at)'))
            ->get();

        // 3. Monta o gráfico dia a dia (Calendário)
        $dias = [];
        $periodo = CarbonPeriod::create($dataInicio, $dataFim);
        
        foreach ($periodo as $data) {
            $dataFormatada = $data->format('Y-m-d');
            $dias[$dataFormatada] = [
                'data_br' => $data->format('d/m'),
                'servicos' => 0,
                'produtos' => 0,
            ];
        }

        foreach ($servicosPorDia as $s) {
            if (isset($dias[$s->data])) $dias[$s->data]['servicos'] = (float)$s->total;
        }

        foreach ($vendasPorDia as $v) {
            if (isset($dias[$v->data])) $dias[$v->data]['produtos'] = (float)$v->total;
        }

        $dadosGrafico = array_values($dias);
        $totalServicos = array_sum(array_column($dias, 'servicos'));
        $totalProdutos = array_sum(array_column($dias, 'produtos'));
        $faturamentoTotal = $totalServicos + $totalProdutos;

        return view('admin.relatorios.faturamento', compact(
            'dataInicio', 'dataFim', 'faturamentoTotal', 'totalServicos', 'totalProdutos', 'dadosGrafico'
        ));
    }
    public function ocupacao(Request $request)
    {
        $dataInicio = $request->input('data_inicio', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dataFim = $request->input('data_fim', Carbon::now()->endOfMonth()->format('Y-m-d'));

        // Ajustamos para o formato de busca no DateTime
        $inicioQuery = $dataInicio . ' 00:00:00';
        $fimQuery = $dataFim . ' 23:59:59';

        // 1. Agendamentos por Hora (Compatível com SQLite e usando sua coluna correta)
        // No SQLite usamos strftime('%H', coluna) para pegar a hora
        $ocupacaoPorHora = DB::table('agendamentos')
            ->select(DB::raw("strftime('%H', data_hora_inicio) as hora"), DB::raw('count(*) as total'))
            ->whereBetween('data_hora_inicio', [$inicioQuery, $fimQuery])
            ->whereIn('status', ['confirmado', 'executado', 'presente']) // Status que ocupam a agenda no seu banco
            ->groupBy('hora')
            ->orderBy('hora')
            ->get();

        // 2. Agendamentos por Dia da Semana (SQLite usa %w: 0=Domingo, 6=Sábado)
        $ocupacaoPorDiaSemana = DB::table('agendamentos')
            ->select(DB::raw("strftime('%w', data_hora_inicio) as dia_semana"), DB::raw('count(*) as total'))
            ->whereBetween('data_hora_inicio', [$inicioQuery, $fimQuery])
            ->whereIn('status', ['confirmado', 'executado', 'presente'])
            ->groupBy('dia_semana')
            ->get();

        // Tradução dos dias para o gráfico (Ajustando 0-6 do SQLite para nomes)
        $diasNomes = [0 => 'Dom', 1 => 'Seg', 2 => 'Ter', 3 => 'Qua', 4 => 'Qui', 5 => 'Sex', 6 => 'Sáb'];
        $dadosDias = [];
        foreach($diasNomes as $num => $nome) {
            $registro = $ocupacaoPorDiaSemana->firstWhere('dia_semana', (string)$num);
            $dadosDias[] = [
                'label' => $nome,
                'total' => $registro ? $registro->total : 0
            ];
        }

        // 3. Taxa de Ocupação Geral
        $totalProfissionais = DB::table('users')->where('tipo', 'profissional')->count() ?: 1;
        $diasNoPeriodo = Carbon::parse($dataInicio)->diffInDays(Carbon::parse($dataFim)) + 1;
        $capacidadeTotal = $totalProfissionais * $diasNoPeriodo * 8; 
        
        $totalAgendamentos = DB::table('agendamentos')
            ->whereBetween('data_hora_inicio', [$inicioQuery, $fimQuery])
            ->whereIn('status', ['confirmado', 'executado', 'presente'])
            ->count();

        $taxaOcupacao = $capacidadeTotal > 0 ? ($totalAgendamentos / $capacidadeTotal) * 100 : 0;

        return view('admin.relatorios.ocupacao', compact(
            'dataInicio', 'dataFim', 'ocupacaoPorHora', 'dadosDias', 'taxaOcupacao', 'totalAgendamentos'
        ));
    }
    public function desempenho(Request $request)
    {
        $dataInicio = $request->input('data_inicio', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dataFim = $request->input('data_fim', Carbon::now()->endOfMonth()->format('Y-m-d'));

        $inicioQuery = $dataInicio . ' 00:00:00';
        $fimQuery = $dataFim . ' 23:59:59';

        // Busca o desempenho cruzando Agendamentos, Usuários e Avaliações
        $desempenhoProfissionais = DB::table('agendamentos')
            ->join('users', 'agendamentos.profissional_id', '=', 'users.id')
            ->leftJoin('avaliacoes', 'agendamentos.id_agendamento', '=', 'avaliacoes.agendamento_id')
            ->select(
                'users.name',
                DB::raw('COUNT(agendamentos.id_agendamento) as total_atendimentos'),
                DB::raw('SUM(agendamentos.valor_total) as valor_total_gerado'),
                DB::raw('SUM(agendamentos.valor_comissao) as comissao_gerada'),
                DB::raw('AVG(avaliacoes.nota) as media_estrelas'),
                DB::raw('COUNT(avaliacoes.id) as qtd_avaliacoes')
            )
            ->where('agendamentos.status', 'executado')
            ->whereBetween('agendamentos.data_hora_inicio', [$inicioQuery, $fimQuery])
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('total_atendimentos')
            ->get();

        return view('admin.relatorios.desempenho', compact('dataInicio', 'dataFim', 'desempenhoProfissionais'));
    }
public function produtos(Request $request)
    {
        $dataInicio = $request->input('data_inicio', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dataFim = $request->input('data_fim', Carbon::now()->endOfMonth()->format('Y-m-d'));

        $inicioQuery = $dataInicio . ' 00:00:00';
        $fimQuery = $dataFim . ' 23:59:59';

        // Usando EXATAMENTE as colunas do seu Schema
        // produtos.id_produto | vendas.produto_id
        $produtosVendidos = DB::table('vendas')
            ->join('produtos', 'vendas.produto_id', '=', 'produtos.id_produto')
            ->select(
                'produtos.nome',
                DB::raw('SUM(vendas.quantidade) as total_vendido'),
                DB::raw('SUM(vendas.valor_venda) as receita_gerada')
            )
            ->whereNotNull('vendas.produto_id') // Garante que estamos pegando apenas vendas de PRODUTOS, e não serviços avulsos
            ->whereBetween('vendas.created_at', [$inicioQuery, $fimQuery])
            ->groupBy('produtos.id_produto', 'produtos.nome')
            ->orderByDesc('total_vendido')
            ->get();

        return view('admin.relatorios.produtos', compact('dataInicio', 'dataFim', 'produtosVendidos'));
    }

    public function exportarPdf(Request $request)
    {
        // ... (seu código de exportação PDF que já estava aí)
        $dataInicio = $request->input('data_inicio', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dataFim = $request->input('data_fim', Carbon::now()->endOfMonth()->format('Y-m-d'));
        $inicioQuery = $dataInicio . ' 00:00:00';
        $fimQuery = $dataFim . ' 23:59:59';

        $desempenhoProfissionais = DB::table('agendamentos')
            ->join('users', 'agendamentos.profissional_id', '=', 'users.id')
            ->select('users.name', DB::raw('COUNT(agendamentos.id_agendamento) as total_atendimentos'), DB::raw('SUM(agendamentos.valor_comissao) as total_gerado'))
            ->where('agendamentos.status', 'executado')
            ->whereBetween('agendamentos.updated_at', [$inicioQuery, $fimQuery])
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('total_atendimentos')
            ->get();

        $pdf = Pdf::loadView('admin.relatorios.pdf', compact('desempenhoProfissionais', 'dataInicio', 'dataFim'));
        return $pdf->download('relatorio_salao_' . $dataInicio . '.pdf');
    }

    public function exportarExcel(Request $request)
    {
        $dataInicio = $request->input('data_inicio', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dataFim = $request->input('data_fim', Carbon::now()->endOfMonth()->format('Y-m-d'));
        return Excel::download(new RelatorioExport($dataInicio, $dataFim), 'relatorio_salao_' . $dataInicio . '.xlsx');
    }
}