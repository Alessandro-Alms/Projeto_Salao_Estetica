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
use App\Services\FinanceiroService;

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
    public function faturamento(Request $request, FinanceiroService $financeiroService)
    {
        $dataInicio = $request->input('data_inicio', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dataFim = $request->input('data_fim', Carbon::now()->endOfMonth()->format('Y-m-d'));

        extract($financeiroService->resumoFaturamentoPeriodo($dataInicio, $dataFim));

        return view('admin.relatorios.faturamento', compact(
            'dataInicio', 'dataFim', 'faturamentoTotal', 'receitaServicos', 'receitaVendas', 'receitaMultas',
            'ticketMedio', 'qtdTransacoes', 'faturamentoAnterior', 'crescimento'
        ));
    }

    public function ocupacao(Request $request)
    {
        $dataInicio = $request->input('data_inicio', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dataFim = $request->input('data_fim', Carbon::now()->endOfMonth()->format('Y-m-d'));

        $inicioQuery = $dataInicio . ' 00:00:00';
        $fimQuery = $dataFim . ' 23:59:59';

        // Trazemos todos os dados brutos primeiro (evita erro do SQLite vs MySQL)
        $agendamentos = DB::table('agendamentos')
            ->whereBetween('data_hora_inicio', [$inicioQuery, $fimQuery])
            ->whereIn('status', ['pendente', 'confirmado', 'executado'])
            ->get();

        $totalAgendamentos = $agendamentos->count();

        // 1. Agrupando por Horário usando o Laravel (Carbon)
        $ocupacaoPorHoraRaw = $agendamentos->groupBy(function($item) {
            return Carbon::parse($item->data_hora_inicio)->format('H'); // Extrai a hora
        })->map->count();

        // Transformando num formato amigável para a View
        $ocupacaoPorHora = $ocupacaoPorHoraRaw->map(function ($total, $hora) {
            return (object) ['hora' => $hora, 'total' => $total];
        })->sortBy('hora')->values();

        $horarioPico = $ocupacaoPorHora->sortByDesc('total')->first();
        $horarioMorto = $ocupacaoPorHora->sortBy('total')->first();

        // 2. Agrupando por Dia da Semana usando o Laravel (Carbon)
        // No Carbon: 0 = Domingo, 1 = Segunda, 2 = Terça... 6 = Sábado
        $ocupacaoPorDiaRaw = $agendamentos->groupBy(function($item) {
            return Carbon::parse($item->data_hora_inicio)->dayOfWeek;
        })->map->count();

        $ocupacaoPorDia = $ocupacaoPorDiaRaw->map(function ($total, $dia) {
            return (object) ['dia_semana' => $dia, 'total' => $total];
        })->keyBy('dia_semana');

        // Mapeamento correto dos dias pelo padrão do Carbon (0 a 6)
        $nomesDias = [
            0 => 'Domingo', 1 => 'Segunda', 2 => 'Terça', 
            3 => 'Quarta', 4 => 'Quinta', 5 => 'Sexta', 6 => 'Sábado'
        ];

        return view('admin.relatorios.ocupacao', compact(
            'dataInicio', 'dataFim', 'totalAgendamentos', 
            'ocupacaoPorHora', 'horarioPico', 'horarioMorto', 
            'ocupacaoPorDia', 'nomesDias'
        ));
    }
    public function desempenho(Request $request)
    {
        $dataInicio = $request->input('data_inicio', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dataFim = $request->input('data_fim', Carbon::now()->endOfMonth()->format('Y-m-d'));

        $inicioQuery = $dataInicio . ' 00:00:00';
        $fimQuery = $dataFim . ' 23:59:59';

        // 1. Pega os serviços e comissões por profissional no período
        $profissionaisRaw = DB::table('users')
            ->where('cargo', 'profissional')
            ->leftJoin('agendamentos', function ($join) use ($inicioQuery, $fimQuery) {
                $join->on('users.id', '=', 'agendamentos.profissional_id')
                     ->where('agendamentos.status', '=', 'executado')
                     ->whereBetween('agendamentos.data_hora_inicio', [$inicioQuery, $fimQuery]);
            })
            ->select(
                'users.id',
                'users.name',
                DB::raw('COUNT(agendamentos.id_agendamento) as total_servicos'),
                DB::raw('SUM(agendamentos.valor_total) as receita_gerada'),
                DB::raw('SUM(agendamentos.valor_comissao) as comissao_total')
            )
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('receita_gerada')
            ->get();

        // 2. Pega as avaliações médias (Geral, independentemente da data, para ver a reputação real)
        $avaliacoes = DB::table('avaliacoes')
            ->select(
                'profissional_id', 
                DB::raw('AVG(nota) as media_nota'), 
                DB::raw('COUNT(id) as total_avaliacoes')
            )
            ->groupBy('profissional_id')
            ->get()
            ->keyBy('profissional_id');

        // 3. Junta tudo numa coleção bonitinha
        $profissionais = $profissionaisRaw->map(function ($prof) use ($avaliacoes) {
            $avaliacao = $avaliacoes->get($prof->id);
            $prof->media_nota = $avaliacao ? round($avaliacao->media_nota, 1) : null;
            $prof->total_avaliacoes = $avaliacao ? $avaliacao->total_avaliacoes : 0;
            return $prof;
        });

        // Highlights para os cards do topo
        $campeaoFaturamento = $profissionais->sortByDesc('receita_gerada')->first();
        $campeaoAvaliacao = $profissionais->where('total_avaliacoes', '>', 0)->sortByDesc('media_nota')->first();

        return view('admin.relatorios.desempenho', compact(
            'dataInicio', 'dataFim', 'profissionais', 'campeaoFaturamento', 'campeaoAvaliacao'
        ));
    }
    public function produtos(Request $request)
    {
        $dataInicio = $request->input('data_inicio', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dataFim = $request->input('data_fim', Carbon::now()->endOfMonth()->format('Y-m-d'));

        $inicioQuery = $dataInicio . ' 00:00:00';
        $fimQuery = $dataFim . ' 23:59:59';

        // Traz o ranking de produtos vendidos no período, somando quantidades e valores
        $produtosVendidos = DB::table('vendas')
            ->join('produtos', 'vendas.produto_id', '=', 'produtos.id_produto')
            ->select(
                'produtos.id_produto',
                'produtos.nome',
                'produtos.quantidade_estoque',
                DB::raw('SUM(vendas.quantidade) as total_vendido'),
                DB::raw('SUM(vendas.valor_venda) as receita_gerada')
            )
            ->whereNotNull('vendas.produto_id') // Garante que são vendas de PRODUTOS e não de serviços
            ->whereBetween('vendas.created_at', [$inicioQuery, $fimQuery])
            ->groupBy('produtos.id_produto', 'produtos.nome', 'produtos.quantidade_estoque')
            ->orderByDesc('total_vendido') // Ordena pelo "Giro" (os que saíram mais)
            ->get();

        // Totais para os cards de destaque
        $totalUnidadesVendidas = $produtosVendidos->sum('total_vendido');
        $receitaTotal = $produtosVendidos->sum('receita_gerada');
        $campeao = $produtosVendidos->first();

        return view('admin.relatorios.produtos', compact(
            'dataInicio', 'dataFim', 'produtosVendidos', 
            'totalUnidadesVendidas', 'receitaTotal', 'campeao'
        ));
    }
    public function fidelizacao(Request $request)
    {
        $dataInicio = $request->input('data_inicio', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dataFim = $request->input('data_fim', Carbon::now()->endOfMonth()->format('Y-m-d'));

        $inicioQuery = $dataInicio . ' 00:00:00';
        $fimQuery = $dataFim . ' 23:59:59';

        // Traz apenas os clientes que visitaram o salão no período selecionado
        $clientes = DB::table('users')
            ->join('agendamentos', 'users.id', '=', 'agendamentos.cliente_id')
            ->where('users.cargo', 'cliente')
            ->where('agendamentos.status', 'executado')
            ->whereBetween('agendamentos.data_hora_inicio', [$inicioQuery, $fimQuery])
            ->select(
                'users.id',
                'users.name',
                'users.telefone',
                'users.contador_fidelidade',
                DB::raw('COUNT(agendamentos.id_agendamento) as total_visitas'),
                DB::raw('SUM(agendamentos.valor_total) as valor_gasto_total'),
                DB::raw('MAX(agendamentos.data_hora_inicio) as ultima_visita')
            )
            ->groupBy('users.id', 'users.name', 'users.telefone', 'users.contador_fidelidade')
            ->orderByDesc('valor_gasto_total') // Ordena do que gastou mais (VIP) para o que gastou menos
            ->get();

        // Cálculos para os Cards de Destaque
        $totalClientesAtendidos = $clientes->count();
        
        // Taxa de Retorno: Clientes que tiveram MAIS DE 1 visita neste período
        $clientesRetornaram = $clientes->where('total_visitas', '>', 1)->count();
        
        $taxaRetorno = $totalClientesAtendidos > 0 
            ? ($clientesRetornaram / $totalClientesAtendidos) * 100 
            : 0;

        $clienteTop1 = $clientes->first(); // O cliente que mais gastou

        return view('admin.relatorios.fidelizacao', compact(
            'dataInicio', 'dataFim', 'clientes', 'totalClientesAtendidos', 
            'clientesRetornaram', 'taxaRetorno', 'clienteTop1'
        ));
    }
    public function cancelamentos(Request $request)
    {
        $dataInicio = $request->input('data_inicio', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dataFim = $request->input('data_fim', Carbon::now()->endOfMonth()->format('Y-m-d'));

        $inicioQuery = $dataInicio . ' 00:00:00';
        $fimQuery = $dataFim . ' 23:59:59';

        // 1. Traz todos os agendamentos do período (para calcular a percentagem)
        $todosAgendamentos = DB::table('agendamentos')
            ->whereBetween('data_hora_inicio', [$inicioQuery, $fimQuery])
            ->get();

        $totalGeral = $todosAgendamentos->count();

        // 2. Filtra apenas as Faltas e Cancelamentos
        $evasoes = $todosAgendamentos->whereIn('status', ['cancelado', 'falta']);
        $totalEvasoes = $evasoes->count();
        $prejuizoTotal = $evasoes->sum('valor_total');
        $totalMultasRecuperadas = $evasoes->sum('multa_valor');
        $prejuizoLiquido = $prejuizoTotal - $totalMultasRecuperadas;
        
        $taxaEvasao = $totalGeral > 0 ? ($totalEvasoes / $totalGeral) * 100 : 0;

        // 3. Horários Críticos (Agrupando por Hora com o Carbon para evitar erro de DB)
        $horariosCriticosRaw = $evasoes->groupBy(function($item) {
            return Carbon::parse($item->data_hora_inicio)->format('H');
        })->map->count();

        // Pega os 5 piores horários
        $horariosCriticos = $horariosCriticosRaw->map(function($total, $hora) {
            return (object) ['hora' => $hora, 'total' => $total];
        })->sortByDesc('total')->take(5);

        $piorHora = $horariosCriticos->first();

        // 4. Clientes Ofensores (Os que mais faltam)
        $ofensoresRaw = $evasoes->groupBy('cliente_id')->map(function($items) {
            return (object) [
                'cliente_id' => $items->first()->cliente_id,
                'total_falhas' => $items->count(),
                'prejuizo' => $items->sum('valor_total')
            ];
        })->sortByDesc('total_falhas')->take(10); // Top 10

        // Busca os nomes e telefones desses clientes ofensores
        $clientesIds = $ofensoresRaw->pluck('cliente_id')->toArray();
        $clientesDetalhados = DB::table('users')
            ->whereIn('id', $clientesIds)
            ->select('id', 'name', 'telefone')
            ->get()
            ->keyBy('id');

        $ofensores = $ofensoresRaw->map(function($ofensor) use ($clientesDetalhados) {
            $cliente = $clientesDetalhados->get($ofensor->cliente_id);
            $ofensor->nome = $cliente ? $cliente->name : 'Desconhecido';
            $ofensor->telefone = $cliente ? $cliente->telefone : null;
            return $ofensor;
        });

        // 5. Motivos (Agrupa as observações 'obs' caso os recepcionistas preencham o motivo)
        $motivos = $evasoes->whereNotNull('obs')->where('obs', '!=', '')
            ->groupBy('obs')->map->count()
            ->sortByDesc(function($count) { return $count; })
            ->take(5);

        return view('admin.relatorios.cancelamentos', compact(
            'dataInicio', 'dataFim', 'totalGeral', 'totalEvasoes', 'prejuizoTotal',
            'totalMultasRecuperadas', 'prejuizoLiquido', 'taxaEvasao', 'horariosCriticos',
            'piorHora', 'ofensores', 'motivos'
        ));
    }
    public function financeiro(Request $request, FinanceiroService $financeiroService)
    {
        $dataInicio = $request->input('data_inicio', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dataFim = $request->input('data_fim', Carbon::now()->endOfMonth()->format('Y-m-d'));

        extract($financeiroService->resumoFinanceiroPeriodo($dataInicio, $dataFim));

        return view('admin.relatorios.financeiro', compact(
            'dataInicio', 'dataFim',
            'receitaServicos', 'receitaProdutos', 'receitaPacotes', 'receitaMultas', 'totalEntradas',
            'despesaComissoes', 'totalSaidas', 'saldoLiquido'
        ));
    }
    public function comissoes(Request $request, FinanceiroService $financeiroService)
    {
        $dataInicio = $request->input('data_inicio', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dataFim = $request->input('data_fim', Carbon::now()->endOfMonth()->format('Y-m-d'));

        $comissoes = $financeiroService->resumoComissoesPeriodo($dataInicio, $dataFim);
        $totalGeralComissoes = $comissoes->sum('comissao_a_pagar');
        $totalServicosRealizados = $comissoes->sum('total_servicos');
        $maiorComissao = $comissoes->first();

        return view('admin.relatorios.comissoes', compact(
            'dataInicio', 'dataFim', 'comissoes',
            'totalGeralComissoes', 'totalServicosRealizados', 'maiorComissao'
        ));
    }

    public function estoque(Request $request)
    {
        // Traz todos os produtos, ordenando pelos que estão com menos estoque primeiro
        $produtos = DB::table('produtos')
            ->orderBy('quantidade_estoque', 'asc')
            ->orderBy('nome', 'asc')
            ->get();

        // Cálculos para os Indicadores
        $totalItens = $produtos->sum('quantidade_estoque');
        
        // Calcula quanto dinheiro está "parado" no estoque (Qtd * Valor Unitário)
        $valorInvestido = $produtos->sum(function ($produto) {
            return $produto->quantidade_estoque * $produto->valor_unitario;
        });

        // Produtos em Alerta (Consideramos alerta se tiver 5 ou menos unidades)
        $produtosAlerta = $produtos->where('quantidade_estoque', '<=', 5);
        $totalAlertas = $produtosAlerta->count();

        return view('admin.relatorios.estoque', compact(
            'produtos', 'totalItens', 'valorInvestido', 'totalAlertas'
        ));
    }
    public function sazonalidade(Request $request)
    {
        $dataInicio = $request->input('data_inicio', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dataFim = $request->input('data_fim', Carbon::now()->endOfMonth()->format('Y-m-d'));

        $inicioQuery = $dataInicio . ' 00:00:00';
        $fimQuery = $dataFim . ' 23:59:59';

        // 1. Trazemos todos os agendamentos executados no período
        $agendamentos = DB::table('agendamentos')
            ->where('status', 'executado')
            ->whereBetween('data_hora_inicio', [$inicioQuery, $fimQuery])
            ->get();

        $totalAgendamentos = $agendamentos->count();

        // 2. Mapeamento dos dias da semana
        $diasSemana = [
            0 => 'Domingo', 1 => 'Segunda-feira', 2 => 'Terça-feira',
            3 => 'Quarta-feira', 4 => 'Quinta-feira', 5 => 'Sexta-feira', 6 => 'Sábado'
        ];

        // 3. Agrupar os agendamentos pelo dia da semana usando o Carbon
        $sazonalidadeDia = $agendamentos->groupBy(function($item) {
            return Carbon::parse($item->data_hora_inicio)->dayOfWeek;
        })->map(function($items, $dia) use ($diasSemana) {
            return (object)[
                'dia_numero' => $dia,
                'dia_nome' => $diasSemana[$dia],
                'total_servicos' => $items->count(),
                'receita_gerada' => $items->sum('valor_total')
            ];
        });

        // 4. Preencher os dias que não tiveram movimento com zero para não sumirem do gráfico/tabela
        $sazonalidadeCompleta = collect($diasSemana)->map(function($nome, $numero) use ($sazonalidadeDia) {
            if ($sazonalidadeDia->has($numero)) {
                return $sazonalidadeDia->get($numero);
            }
            return (object)[
                'dia_numero' => $numero, 
                'dia_nome' => $nome, 
                'total_servicos' => 0, 
                'receita_gerada' => 0
            ];
        })->sortByDesc('total_servicos'); // Ordena do dia mais movimentado para o mais fraco

        // 5. Destacar o melhor e o pior dia (ignorando dias com 0 se possível, para ser mais realista)
        $diaMaisMovimentado = $sazonalidadeCompleta->first();
        $diasComMovimento = $sazonalidadeCompleta->where('total_servicos', '>', 0);
        $diaMenosMovimentado = $diasComMovimento->count() > 0 ? $diasComMovimento->last() : null;

        return view('admin.relatorios.sazonalidade', compact(
            'dataInicio', 'dataFim', 'totalAgendamentos',
            'sazonalidadeCompleta', 'diaMaisMovimentado', 'diaMenosMovimentado'
        ));
    }
    public function avaliacoes(Request $request)
    {
        $dataInicio = $request->input('data_inicio', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dataFim = $request->input('data_fim', Carbon::now()->endOfMonth()->format('Y-m-d'));

        $inicioQuery = $dataInicio . ' 00:00:00';
        $fimQuery = $dataFim . ' 23:59:59';

        // 1. Traz todas as avaliações do período com os nomes do cliente e do profissional
        $avaliacoes = DB::table('avaliacoes')
            ->join('users as clientes', 'avaliacoes.cliente_id', '=', 'clientes.id')
            ->join('users as profissionais', 'avaliacoes.profissional_id', '=', 'profissionais.id')
            ->whereBetween('avaliacoes.created_at', [$inicioQuery, $fimQuery])
            ->select(
                'avaliacoes.*', 
                'clientes.name as cliente_nome', 
                'profissionais.name as profissional_nome'
            )
            ->orderByDesc('avaliacoes.created_at')
            ->get();

        // 2. Indicadores Gerais
        $totalAvaliacoes = $avaliacoes->count();
        $mediaGeral = $totalAvaliacoes > 0 ? $avaliacoes->avg('nota') : 0;
        
        // Quantos promotores temos? (Notas 4 e 5)
        $avaliacoesPositivas = $avaliacoes->whereIn('nota', [4, 5])->count();
        $percentualAprovacao = $totalAvaliacoes > 0 ? ($avaliacoesPositivas / $totalAvaliacoes) * 100 : 0;

        // 3. Distribuição das Estrelas (Quantas de 5, quantas de 4, etc.)
        $distribuicao = [
            5 => $avaliacoes->where('nota', 5)->count(),
            4 => $avaliacoes->where('nota', 4)->count(),
            3 => $avaliacoes->where('nota', 3)->count(),
            2 => $avaliacoes->where('nota', 2)->count(),
            1 => $avaliacoes->where('nota', 1)->count(),
        ];

        // 4. Ranking de Profissionais (Agrupado com Collections para evitar erro no SQLite)
        $rankingProfissionais = $avaliacoes->groupBy('profissional_id')->map(function($items) {
            return (object)[
                'nome' => $items->first()->profissional_nome,
                'media' => $items->avg('nota'),
                'total_avaliacoes' => $items->count()
            ];
        })->sortByDesc('media')->take(5); // Top 5 melhores profissionais

        return view('admin.relatorios.avaliacoes', compact(
            'dataInicio', 'dataFim', 'avaliacoes', 'totalAvaliacoes', 
            'mediaGeral', 'percentualAprovacao', 'distribuicao', 'rankingProfissionais'
        ));
    }
    public function previsao(Request $request)
    {
        $hoje = Carbon::today();
        
        // 1. Analisar as últimas 4 semanas para encontrar a média de cada dia da semana
        $historicoInicio = $hoje->copy()->subWeeks(4)->format('Y-m-d 00:00:00');
        $historicoFim = $hoje->copy()->subDay()->format('Y-m-d 23:59:59');

        $agendamentosPassados = DB::table('agendamentos')
            ->whereBetween('data_hora_inicio', [$historicoInicio, $historicoFim])
            ->whereIn('status', ['executado', 'confirmado']) // Considera apenas os que deram certo
            ->get();

        // 2. Calcula a média diária das últimas 4 semanas
        $mediaPorDiaDaSemana = $agendamentosPassados->groupBy(function($item) {
            return \Carbon\Carbon::parse($item->data_hora_inicio)->dayOfWeek;
        })->map(function($items) {
            return $items->count() / 4; // Divide por 4 semanas
        });

        // 3. Feriados Nacionais Fixos (Exemplo) - Mês-Dia
        $feriados = [
            '01-01' => 'Ano Novo',
            '04-21' => 'Tiradentes',
            '05-01' => 'Dia do Trabalhador',
            '09-07' => 'Independência do Brasil',
            '10-12' => 'Nossa Sra. Aparecida',
            '11-02' => 'Finados',
            '11-15' => 'Proclamação da República',
            '12-25' => 'Natal',
        ];

        $diasSemanaNome = [
            0 => 'Domingo', 1 => 'Segunda-feira', 2 => 'Terça-feira',
            3 => 'Quarta-feira', 4 => 'Quinta-feira', 5 => 'Sexta-feira', 6 => 'Sábado'
        ];

        // 4. Construir a previsão para os próximos 7 dias
        $proximos7Dias = [];
        $totalPrevisao = 0;
        $diasDeBaixa = 0;
        $diasDeAlta = 0;

        for ($i = 0; $i < 7; $i++) {
            $diaAlvo = $hoje->copy()->addDays($i);
            $diaSemana = $diaAlvo->dayOfWeek;
            $dataFormatada = $diaAlvo->format('m-d'); // Para procurar nos feriados
            
            $feriadoNome = $feriados[$dataFormatada] ?? null;
            $mediaHistorica = $mediaPorDiaDaSemana->get($diaSemana, 0);

            // Regra de Negócio de Previsão: 
            // Se for feriado, reduzimos a expectativa pela metade (salões costumam fechar ou ter menos fluxo)
            // Se for sexta ou sábado (5 ou 6), costuma ser alta demanda natural
            $previsaoDia = round($mediaHistorica);
            $tendencia = 'Normal';

            if ($feriadoNome) {
                $previsaoDia = round($mediaHistorica * 0.5); 
                $tendencia = 'Feriado / Baixa';
                $diasDeBaixa++;
            } elseif ($diaSemana == 5 || $diaSemana == 6 || $previsaoDia > 10) {
                $tendencia = 'Alta Demanda';
                $diasDeAlta++;
            } elseif ($previsaoDia < 3) {
                $tendencia = 'Baixa Demanda';
                $diasDeBaixa++;
            }
            
            $totalPrevisao += $previsaoDia;

            $proximos7Dias[] = (object)[
                'data_br' => $diaAlvo->format('d/m/Y'),
                'dia_nome' => $diasSemanaNome[$diaSemana],
                'is_hoje' => $i === 0,
                'feriado' => $feriadoNome,
                'previsao_agendamentos' => $previsaoDia,
                'tendencia' => $tendencia
            ];
        }

        return view('admin.relatorios.previsao', compact(
            'proximos7Dias', 'totalPrevisao', 'diasDeAlta', 'diasDeBaixa'
        ));
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

    // ==========================================
    // DOWNLOADS INDIVIDUAIS POR RELATÓRIO
    // ==========================================

    public function downloadFaturamentoExcel(Request $request, FinanceiroService $financeiroService)
    {
        $dataInicio = $request->input('data_inicio', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dataFim = $request->input('data_fim', Carbon::now()->endOfMonth()->format('Y-m-d'));
        $dados = $financeiroService->resumoFaturamentoPeriodo($dataInicio, $dataFim);

        return Excel::download(new \App\Exports\FaturamentoExport($dataInicio, $dataFim, $dados),
                               'faturamento_' . $dataInicio . '_' . $dataFim . '.xlsx');
    }

    public function downloadComissoesExcel(Request $request, FinanceiroService $financeiroService)
    {
        $dataInicio = $request->input('data_inicio', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dataFim = $request->input('data_fim', Carbon::now()->endOfMonth()->format('Y-m-d'));
        $comissoes = $financeiroService->resumoComissoesPeriodo($dataInicio, $dataFim);

        return Excel::download(new \App\Exports\ComissoesExport($dataInicio, $dataFim, $comissoes),
                               'comissoes_' . $dataInicio . '_' . $dataFim . '.xlsx');
    }

    public function downloadComissoesPdf(Request $request, FinanceiroService $financeiroService)
    {
        $dataInicio = $request->input('data_inicio', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dataFim = $request->input('data_fim', Carbon::now()->endOfMonth()->format('Y-m-d'));
        $comissoes = $financeiroService->resumoComissoesPeriodo($dataInicio, $dataFim);
        $totalGeralComissoes = $comissoes->sum('comissao_a_pagar');
        $totalServicosRealizados = $comissoes->sum('total_servicos');

        $pdf = Pdf::loadView('admin.relatorios.comissoes-pdf', compact(
            'dataInicio', 'dataFim', 'comissoes', 'totalGeralComissoes', 'totalServicosRealizados'
        ));

        return $pdf->download('comissoes_' . $dataInicio . '_' . $dataFim . '.pdf');
    }

    public function downloadProdutosExcel(Request $request)
    {
        $dataInicio = $request->input('data_inicio', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dataFim = $request->input('data_fim', Carbon::now()->endOfMonth()->format('Y-m-d'));

        $inicioQuery = $dataInicio . ' 00:00:00';
        $fimQuery = $dataFim . ' 23:59:59';

        $produtosVendidos = DB::table('vendas')
            ->join('produtos', 'vendas.produto_id', '=', 'produtos.id_produto')
            ->select(
                'produtos.nome',
                'produtos.quantidade_estoque',
                DB::raw('SUM(vendas.quantidade) as total_vendido'),
                DB::raw('SUM(vendas.valor_venda) as receita_gerada')
            )
            ->whereNotNull('vendas.produto_id')
            ->whereBetween('vendas.created_at', [$inicioQuery, $fimQuery])
            ->groupBy('produtos.id_produto', 'produtos.nome', 'produtos.quantidade_estoque')
            ->orderByDesc('total_vendido')
            ->get();

        return Excel::download(new \App\Exports\ProdutosExport($dataInicio, $dataFim, $produtosVendidos), 
                               'produtos_' . $dataInicio . '_' . $dataFim . '.xlsx');
    }

    public function downloadEstoqueExcel(Request $request)
    {
        $produtos = DB::table('produtos')
            ->orderBy('quantidade_estoque', 'asc')
            ->orderBy('nome', 'asc')
            ->get();

        return Excel::download(new \App\Exports\EstoqueExport($produtos), 
                               'estoque_' . Carbon::now()->format('Y-m-d') . '.xlsx');
    }

    public function downloadDesempenhoExcel(Request $request)
    {
        $dataInicio = $request->input('data_inicio', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dataFim = $request->input('data_fim', Carbon::now()->endOfMonth()->format('Y-m-d'));

        $inicioQuery = $dataInicio . ' 00:00:00';
        $fimQuery = $dataFim . ' 23:59:59';

        $profissionaisRaw = DB::table('users')
            ->where('cargo', 'profissional')
            ->leftJoin('agendamentos', function ($join) use ($inicioQuery, $fimQuery) {
                $join->on('users.id', '=', 'agendamentos.profissional_id')
                     ->where('agendamentos.status', '=', 'executado')
                     ->whereBetween('agendamentos.data_hora_inicio', [$inicioQuery, $fimQuery]);
            })
            ->select(
                'users.id',
                'users.name',
                DB::raw('COUNT(agendamentos.id_agendamento) as total_servicos'),
                DB::raw('SUM(agendamentos.valor_total) as receita_gerada'),
                DB::raw('SUM(agendamentos.valor_comissao) as comissao_total')
            )
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('receita_gerada')
            ->get();

        $avaliacoes = DB::table('avaliacoes')
            ->select('profissional_id', DB::raw('AVG(nota) as media_nota'), DB::raw('COUNT(id) as total_avaliacoes'))
            ->groupBy('profissional_id')
            ->get()
            ->keyBy('profissional_id');

        $profissionais = $profissionaisRaw->map(function ($prof) use ($avaliacoes) {
            $avaliacao = $avaliacoes->get($prof->id);
            $prof->media_nota = $avaliacao ? round($avaliacao->media_nota, 1) : null;
            $prof->total_avaliacoes = $avaliacao ? $avaliacao->total_avaliacoes : 0;
            return $prof;
        });

        return Excel::download(new \App\Exports\DesempenhoExport($dataInicio, $dataFim, $profissionais), 
                               'desempenho_' . $dataInicio . '_' . $dataFim . '.xlsx');
    }

    public function downloadFidelizacaoExcel(Request $request)
    {
        $dataInicio = $request->input('data_inicio', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dataFim = $request->input('data_fim', Carbon::now()->endOfMonth()->format('Y-m-d'));

        $inicioQuery = $dataInicio . ' 00:00:00';
        $fimQuery = $dataFim . ' 23:59:59';

        $clientes = DB::table('users')
            ->join('agendamentos', 'users.id', '=', 'agendamentos.cliente_id')
            ->where('users.cargo', 'cliente')
            ->where('agendamentos.status', 'executado')
            ->whereBetween('agendamentos.data_hora_inicio', [$inicioQuery, $fimQuery])
            ->select(
                'users.id',
                'users.name',
                'users.telefone',
                'users.contador_fidelidade',
                DB::raw('COUNT(agendamentos.id_agendamento) as total_visitas'),
                DB::raw('SUM(agendamentos.valor_total) as valor_gasto_total'),
                DB::raw('MAX(agendamentos.data_hora_inicio) as ultima_visita')
            )
            ->groupBy('users.id', 'users.name', 'users.telefone', 'users.contador_fidelidade')
            ->orderByDesc('valor_gasto_total')
            ->get();

        return Excel::download(new \App\Exports\FidelizacaoExport($dataInicio, $dataFim, $clientes), 
                               'fidelizacao_' . $dataInicio . '_' . $dataFim . '.xlsx');
    }

    public function downloadCancelamentosExcel(Request $request)
    {
        $dataInicio = $request->input('data_inicio', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dataFim = $request->input('data_fim', Carbon::now()->endOfMonth()->format('Y-m-d'));

        $inicioQuery = $dataInicio . ' 00:00:00';
        $fimQuery = $dataFim . ' 23:59:59';

        $todosAgendamentos = DB::table('agendamentos')
            ->whereBetween('data_hora_inicio', [$inicioQuery, $fimQuery])
            ->get();

        $totalGeral = $todosAgendamentos->count();
        $evasoes = $todosAgendamentos->whereIn('status', ['cancelado', 'falta']);
        $totalEvasoes = $evasoes->count();
        $prejuizoTotal = $evasoes->sum('valor_total');
        $totalMultasRecuperadas = $evasoes->sum('multa_valor');
        $taxaEvasao = $totalGeral > 0 ? ($totalEvasoes / $totalGeral) * 100 : 0;

        $ofensoresRaw = $evasoes->groupBy('cliente_id')->map(function($items) {
            return (object) [
                'cliente_id' => $items->first()->cliente_id,
                'total_falhas' => $items->count(),
                'prejuizo' => $items->sum('valor_total')
            ];
        })->sortByDesc('total_falhas')->take(10);

        $clientesIds = $ofensoresRaw->pluck('cliente_id')->toArray();
        $clientesDetalhados = DB::table('users')
            ->whereIn('id', $clientesIds)
            ->select('id', 'name', 'telefone')
            ->get()
            ->keyBy('id');

        $ofensores = $ofensoresRaw->map(function($ofensor) use ($clientesDetalhados) {
            $cliente = $clientesDetalhados->get($ofensor->cliente_id);
            $ofensor->nome = $cliente ? $cliente->name : 'Desconhecido';
            $ofensor->telefone = $cliente ? $cliente->telefone : null;
            return $ofensor;
        });

        return Excel::download(new \App\Exports\CancelamentosExport($dataInicio, $dataFim, $ofensores, $totalEvasoes, $prejuizoTotal, $totalMultasRecuperadas, $taxaEvasao), 
                               'cancelamentos_' . $dataInicio . '_' . $dataFim . '.xlsx');
    }

    public function downloadFinanceiroExcel(Request $request, FinanceiroService $financeiroService)
    {
        $dataInicio = $request->input('data_inicio', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dataFim = $request->input('data_fim', Carbon::now()->endOfMonth()->format('Y-m-d'));
        $dados = $financeiroService->resumoFinanceiroPeriodo($dataInicio, $dataFim);

        return Excel::download(new \App\Exports\FinanceiroExport($dataInicio, $dataFim, $dados),
                               'financeiro_' . $dataInicio . '_' . $dataFim . '.xlsx');
    }
    public function downloadSazonalideExcel(Request $request)
    {
        $dataInicio = $request->input('data_inicio', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dataFim = $request->input('data_fim', Carbon::now()->endOfMonth()->format('Y-m-d'));

        $inicioQuery = $dataInicio . ' 00:00:00';
        $fimQuery = $dataFim . ' 23:59:59';

        $agendamentos = DB::table('agendamentos')
            ->where('status', 'executado')
            ->whereBetween('data_hora_inicio', [$inicioQuery, $fimQuery])
            ->get();

        $diasSemana = [
            0 => 'Domingo', 1 => 'Segunda-feira', 2 => 'Terça-feira',
            3 => 'Quarta-feira', 4 => 'Quinta-feira', 5 => 'Sexta-feira', 6 => 'Sábado'
        ];

        $sazonalidadeDia = $agendamentos->groupBy(function($item) {
            return Carbon::parse($item->data_hora_inicio)->dayOfWeek;
        })->map(function($items, $dia) use ($diasSemana) {
            return (object)[
                'dia_numero' => $dia,
                'dia_nome' => $diasSemana[$dia],
                'total_servicos' => $items->count(),
                'receita_gerada' => $items->sum('valor_total')
            ];
        });

        $sazonalidadeCompleta = collect($diasSemana)->map(function($nome, $numero) use ($sazonalidadeDia) {
            if ($sazonalidadeDia->has($numero)) {
                return $sazonalidadeDia->get($numero);
            }
            return (object)[
                'dia_numero' => $numero, 
                'dia_nome' => $nome, 
                'total_servicos' => 0, 
                'receita_gerada' => 0
            ];
        })->sortByDesc('total_servicos');

        return Excel::download(new \App\Exports\SazonalideExport($dataInicio, $dataFim, $sazonalidadeCompleta), 
                               'sazonalidade_' . $dataInicio . '_' . $dataFim . '.xlsx');
    }

    public function downloadAvaliacoesExcel(Request $request)
    {
        $dataInicio = $request->input('data_inicio', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dataFim = $request->input('data_fim', Carbon::now()->endOfMonth()->format('Y-m-d'));

        $inicioQuery = $dataInicio . ' 00:00:00';
        $fimQuery = $dataFim . ' 23:59:59';

        $avaliacoes = DB::table('avaliacoes')
            ->join('users as clientes', 'avaliacoes.cliente_id', '=', 'clientes.id')
            ->join('users as profissionais', 'avaliacoes.profissional_id', '=', 'profissionais.id')
            ->whereBetween('avaliacoes.created_at', [$inicioQuery, $fimQuery])
            ->select(
                'avaliacoes.*', 
                'clientes.name as cliente_nome', 
                'profissionais.name as profissional_nome'
            )
            ->orderByDesc('avaliacoes.created_at')
            ->get();

        return Excel::download(new \App\Exports\AvaliacoesExport($dataInicio, $dataFim, $avaliacoes), 
                               'avaliacoes_' . $dataInicio . '_' . $dataFim . '.xlsx');
    }

    public function downloadOcupacaoExcel(Request $request)
    {
        $dataInicio = $request->input('data_inicio', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dataFim = $request->input('data_fim', Carbon::now()->endOfMonth()->format('Y-m-d'));

        $inicioQuery = $dataInicio . ' 00:00:00';
        $fimQuery = $dataFim . ' 23:59:59';

        $agendamentos = DB::table('agendamentos')
            ->whereBetween('data_hora_inicio', [$inicioQuery, $fimQuery])
            ->whereIn('status', ['pendente', 'confirmado', 'executado'])
            ->get();

        $ocupacaoPorHoraRaw = $agendamentos->groupBy(function($item) {
            return Carbon::parse($item->data_hora_inicio)->format('H');
        })->map->count();

        $ocupacaoPorHora = $ocupacaoPorHoraRaw->map(function ($total, $hora) {
            return (object) ['hora' => $hora, 'total' => $total];
        })->sortBy('hora')->values();

        $ocupacaoPorDiaRaw = $agendamentos->groupBy(function($item) {
            return Carbon::parse($item->data_hora_inicio)->dayOfWeek;
        })->map->count();

        $ocupacaoPorDia = $ocupacaoPorDiaRaw->map(function ($total, $dia) {
            return (object) ['dia_semana' => $dia, 'total' => $total];
        })->keyBy('dia_semana');

        return Excel::download(new \App\Exports\OcupacaoExport($dataInicio, $dataFim, $ocupacaoPorHora, $ocupacaoPorDia), 
                               'ocupacao_' . $dataInicio . '_' . $dataFim . '.xlsx');
    }
}
