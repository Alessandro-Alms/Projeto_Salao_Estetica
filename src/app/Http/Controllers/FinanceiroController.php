<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Agendamento;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Carbon\Carbon;

class FinanceiroController extends Controller
{
    public function fechamento(Request $request)
    {
        $dataSelecionada = $request->input('data', now()->format('Y-m-d'));

        // TESTE 1: Buscar TUDO que é executado sem filtro de data
        $todosExecutados = Agendamento::where('status', 'executado')->get();

        // Vamos ver o que tem dentro do primeiro item para comparar a data
        if ($todosExecutados->isNotEmpty()) {
            $exemploDataBanco = $todosExecutados->first()->updated_at->format('Y-m-d');
        } else {
            $exemploDataBanco = "Nenhum dado encontrado no banco com status 'executado'";
        }

        // TESTE 2: Busca real com o filtro
        $agendamentos = Agendamento::where('status', 'executado')
            ->whereDate('updated_at', $dataSelecionada)
            ->with(['profissional', 'servico'])
            ->get();

        // Se a busca real falhar, mas o TESTE 1 tiver dados, o problema é a data.
        // Vamos forçar os totais para o cálculo:
        $totalServicos = $agendamentos->sum('valor_total');
        $totalProdutos = DB::table('vendas')->whereDate('created_at', $dataSelecionada)->sum('valor_venda');

        // ... (restante dos cálculos de comissão que já fizemos) ...
        $totalComissoes = ($totalServicos * 0.5) + ($totalProdutos * 0.1); 
        $lucroLiquido = ($totalServicos + $totalProdutos) - $totalComissoes;

        return view('admin.financeiro.fechamento', compact(
            'totalServicos', 'totalProdutos', 'totalComissoes', 
            'lucroLiquido', 'dataSelecionada', 'exemploDataBanco'
        ));
    }
    public function comissoes(Request $request)
    {
        $profissionais = User::where('cargo', 'profissional')->orderBy('name')->get();
        $profissionalId = $request->input('profissional_id');
        $mes = $request->input('mes', now()->format('m'));
        $ano = $request->input('ano', now()->format('Y'));

        $comissoes = [];
        $totalComissao = 0;

        if ($profissionalId) {
            
            // Busca na tabela de agendamentos os serviços executados por esse profissional no mês/ano selecionado
            $agendamentos = Agendamento::where('profissional_id', $profissionalId)
                ->where('status', 'executado')
                ->whereMonth('updated_at', $mes)
                ->whereYear('updated_at', $ano)
                ->get();

            foreach ($agendamentos as $agenda) {
                $vinculo = $agenda->profissional->servicos->find($agenda->servico_id);
                $porcentagem = $vinculo ? ($vinculo->pivot->comissao_servico ?? 50) : 50; 
                $valorComissao = ($agenda->valor_total * ($porcentagem / 100));
                
                $comissoes[] = [
                    'data' => $agenda->updated_at->format('d/m/Y'),
                    'descricao' => 'Serviço: ' . $agenda->servico->nome,
                    'valor_total' => $agenda->valor_total,
                    'comissao' => $valorComissao
                ];
                $totalComissao += $valorComissao;
            }

            // Busca na tabela de vendas os produtos vendidos por esse profissional
            $vendas = DB::table('vendas')
                ->where('profissional_id', $profissionalId)
                ->whereMonth('created_at', $mes)
                ->whereYear('created_at', $ano)
                ->get();

            foreach ($vendas as $venda) {

                $porcentagemProduto = 10; 
                $valorComissaoProduto = ($venda->valor_venda * ($porcentagemProduto / 100));

                $comissoes[] = [
                    'data' => Carbon::parse($venda->created_at)->format('d/m/Y'),
                    'descricao' => 'Produto (Venda #' . $venda->id_venda . ')',
                    'valor_total' => $venda->valor_venda,
                    'comissao' => $valorComissaoProduto
                ];
                $totalComissao += $valorComissaoProduto;
            }

            // Ordenar por data 
            usort($comissoes, function($a, $b) {
                return Carbon::createFromFormat('d/m/Y', $a['data']) <=> Carbon::createFromFormat('d/m/Y', $b['data']);
            });
        }

        return view('admin.financeiro.comissoes', compact('profissionais', 'comissoes', 'totalComissao', 'profissionalId'));
    }
}
