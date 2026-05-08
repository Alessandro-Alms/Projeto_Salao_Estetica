<?php

namespace App\Http\Controllers;

use App\Models\Agendamento;
use App\Models\User;
use App\Services\FinanceiroService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FinanceiroController extends Controller
{
    public function fechamento(Request $request, FinanceiroService $financeiroService)
    {
        $dataSelecionada = $request->input('data', now()->format('Y-m-d'));

        $todosExecutados = Agendamento::where('status', 'executado')->get();
        $exemploDataBanco = $todosExecutados->isNotEmpty()
            ? $todosExecutados->first()->updated_at->format('Y-m-d')
            : "Nenhum dado encontrado no banco com status 'executado'";

        extract($financeiroService->fechamentoDiario($dataSelecionada));

        return view('admin.financeiro.fechamento', compact(
            'totalServicos',
            'totalProdutos',
            'totalPacotes',
            'totalMultas',
            'totalComissoes',
            'lucroLiquido',
            'dataSelecionada',
            'exemploDataBanco',
            'agendamentos',
            'totalComissoesServicos',
            'totalComissoesProdutos',
            'vendas'
        ));
    }

    public function comissoes(Request $request, FinanceiroService $financeiroService)
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
                ->with('servico')
                ->get();

            foreach ($agendamentos as $agenda) {
                $comissoes[] = [
                    'data' => $agenda->updated_at->format('d/m/Y'),
                    'descricao' => 'Serviço: ' . ($agenda->servico->nome ?? 'Serviço não encontrado') . ' (Preço Base: R$ ' . number_format($agenda->servico->preco ?? 0, 2, ',', '.') . ')',
                    'valor_total' => $agenda->valor_total,
                    'valor_comissao' => $agenda->valor_comissao,
                ];

                $totalComissao += $agenda->valor_comissao;
            }

            $vendas = DB::table('vendas')
                ->where('profissional_id', $profissionalId)
                ->whereMonth('created_at', $mes)
                ->whereYear('created_at', $ano)
                ->get();

            foreach ($vendas as $venda) {
                $valorComissaoProduto = $financeiroService->calcularComissaoProduto((float) $venda->valor_venda);

                $comissoes[] = [
                    'data' => Carbon::parse($venda->created_at)->format('d/m/Y'),
                    'descricao' => 'Produto (Venda #' . $venda->id_venda . ')',
                    'valor_total' => $venda->valor_venda,
                    'valor_comissao' => $valorComissaoProduto,
                ];

                $totalComissao += $valorComissaoProduto;
            }

            usort($comissoes, function ($a, $b) {
                return Carbon::createFromFormat('d/m/Y', $a['data']) <=> Carbon::createFromFormat('d/m/Y', $b['data']);
            });
        }

        return view('admin.financeiro.comissoes', compact('profissionais', 'comissoes', 'totalComissao', 'profissionalId'));
    }

    public function exportarFechamentoPdf(Request $request, FinanceiroService $financeiroService)
    {
        $dataSelecionada = $request->input('data', now()->format('Y-m-d'));
        $dadosFechamento = $financeiroService->fechamentoDiario($dataSelecionada);

        $pdf = Pdf::loadView('admin.financeiro.fechamento-pdf', [
            'dataSelecionada' => $dataSelecionada,
            'totalServicos' => $dadosFechamento['totalServicos'],
            'totalProdutos' => $dadosFechamento['totalProdutos'],
            'totalPacotes' => $dadosFechamento['totalPacotes'],
            'totalMultas' => $dadosFechamento['totalMultas'],
            'totalComissoes' => $dadosFechamento['totalComissoes'],
            'lucroLiquido' => $dadosFechamento['lucroLiquido'],
            'agendamentos' => $dadosFechamento['agendamentos'],
        ]);

        return $pdf->download('fechamento-caixa-' . $dataSelecionada . '.pdf');
    }
}
