<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Agendamento;
use Illuminate\Support\Facades\DB;
use App\Models\User;

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
                    'servico' => $agenda->servico->nome,
                    'valor_total' => $agenda->valor_total,
                    'comissao' => $valorComissao
                ];
                $totalComissao += $valorComissao;
            }
        }

        return view('admin.financeiro.comissoes', compact('profissionais', 'comissoes', 'totalComissao', 'profissionalId'));
    }
}
