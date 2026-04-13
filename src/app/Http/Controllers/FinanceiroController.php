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

        // Variáveis para somar os valores corretamente
        $totalServicos = 0;
        $totalComissoesServicos = 0;

        // Loop para calcular a comissão de CADA serviço separadamente
        foreach ($agendamentos as $agenda) {
            // $totalServicos recebe o que o cliente de fato pagou na hora (pode ser 0 se usou pacote, ou 50% se usou fidelidade)
            $totalServicos += $agenda->valor_total;

            // Busca a comissão exata deste profissional para este serviço na tabela pivot
            $pivot = DB::table('profissional_servico')
                ->where('profissional_id', $agenda->profissional_id)
                ->where('servico_id', $agenda->servico_id)
                ->first();

            $comissaoPercentual = $pivot ? $pivot->comissao_percentual : 50.00; // 50% de segurança
            $taxa = $comissaoPercentual / 100;

            // REGRA DE OURO: Comissão sempre calculada sobre o preço cheio do serviço!
            $valorBaseComissao = $agenda->servico->preco;
            $totalComissoesServicos += ($valorBaseComissao * $taxa);
        }

        // Calcula produtos 
        $totalProdutos = DB::table('vendas')->whereDate('created_at', $dataSelecionada)->sum('valor_venda');
        $totalComissoesProdutos = $totalProdutos * 0.10; 

        // Calcula PACOTES vendidos no dia (Adicionado!)
        // Nota: Adapte "preco" para o nome da coluna de valor que está na sua tabela de pacotes, caso seja diferente.
        $totalPacotes = DB::table('cliente_pacotes')
            ->join('pacotes', 'cliente_pacotes.pacote_id', '=', 'pacotes.id_pacote') // Corrigido de 'id' para 'id_pacote'
            ->whereDate('cliente_pacotes.created_at', $dataSelecionada)
            ->sum('pacotes.valor_total');
        // Totais finais
        $totalComissoes = $totalComissoesServicos + $totalComissoesProdutos; 
        
        // Lucro líquido agora soma os pacotes vendidos no dia
        $lucroLiquido = ($totalServicos + $totalProdutos + $totalPacotes) - $totalComissoes;

        return view('admin.financeiro.fechamento', compact(
            'totalServicos', 'totalProdutos', 'totalPacotes', 'totalComissoes', 
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
            
            // Busca na tabela de agendamentos os serviços executados
            $agendamentos = Agendamento::where('profissional_id', $profissionalId)
                ->where('status', 'executado')
                ->whereMonth('updated_at', $mes)
                ->whereYear('updated_at', $ano)
                ->with('servico')
                ->get();

            foreach ($agendamentos as $agenda) {
                // Busca direta e segura na tabela pivot 
                $pivot = DB::table('profissional_servico')
                    ->where('profissional_id', $agenda->profissional_id)
                    ->where('servico_id', $agenda->servico_id)
                    ->first();

                // Puxa a comissao_percentual correta
                $porcentagem = $pivot ? $pivot->comissao_percentual : 50.00; 
                
                // REGRA DE OURO: Comissão sobre o valor base do serviço
                $valorBaseComissao = $agenda->servico->preco;
                $valorComissao = ($valorBaseComissao * ($porcentagem / 100));
                
                $comissoes[] = [
                    'data' => $agenda->updated_at->format('d/m/Y'),
                    'descricao' => 'Serviço: ' . ($agenda->servico->nome ?? 'Serviço não encontrado') . ' (Preço Base: R$ ' . number_format($valorBaseComissao, 2, ',', '.') . ')',
                    'valor_total' => $agenda->valor_total, // Exibe o que o cliente pagou
                    'comissao' => $valorComissao
                ];
                $totalComissao += $valorComissao;
            }

            // Busca na tabela de vendas os produtos vendidos
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