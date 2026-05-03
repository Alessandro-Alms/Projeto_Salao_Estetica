<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Agendamento;
use App\Models\User;
use Illuminate\Support\Facades\DB;

echo "\n=== VERIFICACAO COMPLETA DE COMISSOES ===\n\n";

// 1. Último agendamento criado
$ag = Agendamento::latest()->first();
if ($ag) {
    echo "1. ULTIMO AGENDAMENTO CRIADO:\n";
    echo "   ID: " . $ag->id_agendamento . "\n";
    echo "   Cliente: " . $ag->cliente->name . "\n";
    echo "   Profissional: " . $ag->profissional->name . "\n";
    echo "   Servico: " . $ag->servico->nome . " (R$ " . number_format($ag->servico->preco, 2, ',', '.') . ")\n";
    echo "   Data: " . $ag->data_hora_inicio->format('d/m/Y H:i') . "\n";
    echo "   Status: " . $ag->status . "\n";
    echo "   Valor Total: R$ " . number_format($ag->valor_total, 2, ',', '.') . "\n";
    echo "   Valor Comissao: R$ " . number_format($ag->valor_comissao ?? 0, 2, ',', '.') . "\n";
    echo "   % Comissao: " . ($ag->comissao_paga_percentual ?? 'N/A') . "%\n\n";
}

// 2. Todos os agendamentos executados
echo "2. AGENDAMENTOS EXECUTADOS (Total):\n";
$executados = Agendamento::where('status', 'executado')->get();
echo "   Total de agendamentos executados: " . $executados->count() . "\n";
$totalComissoes = $executados->sum('valor_comissao');
$totalValor = $executados->sum('valor_total');
echo "   Valor total faturado: R$ " . number_format($totalValor, 2, ',', '.') . "\n";
echo "   Comissoes pagadas: R$ " . number_format($totalComissoes, 2, ',', '.') . "\n";
echo "   Media de comissao por agendamento: R$ " . number_format($executados->count() > 0 ? $totalComissoes / $executados->count() : 0, 2, ',', '.') . "\n\n";

// 3. Verificar se as comissoes estao a 50%
echo "3. VERIFICACAO DA TAXA DE COMISSAO (Deve ser 50%):\n";
$verifyComissao = $executados->map(function($a) {
    $taxa = $a->valor_total > 0 ? ($a->valor_comissao / $a->valor_total) * 100 : 0;
    return $taxa;
})->unique();
if ($verifyComissao->count() == 1 && $verifyComissao->first() == 50) {
    echo "   ✓ CORRETO: Todos os agendamentos tem 50% de comissao\n\n";
} else {
    echo "   ✗ ERRO: Encontradas diferentes taxas: " . implode(', ', $verifyComissao->toArray()) . "%\n\n";
}

// 4. Comissoes por profissional
echo "4. COMISSOES POR PROFISSIONAL (Mes Atual):\n";
$mes = date('m');
$ano = date('Y');
$profComissoes = DB::table('agendamentos')
    ->join('users', 'agendamentos.profissional_id', '=', 'users.id')
    ->where('agendamentos.status', 'executado')
    ->whereMonth('agendamentos.updated_at', $mes)
    ->whereYear('agendamentos.updated_at', $ano)
    ->select('users.name', DB::raw('COUNT(*) as total'), DB::raw('SUM(valor_total) as receita'), DB::raw('SUM(valor_comissao) as comissao'))
    ->groupBy('users.id', 'users.name')
    ->get();

foreach ($profComissoes as $prof) {
    echo "   " . $prof->name . ": " . $prof->total . " atendimentos | Faturou R$ " . number_format($prof->receita, 2, ',', '.') . " | Recebeu R$ " . number_format($prof->comissao, 2, ',', '.') . "\n";
}
echo "\n";

// 5. Vendas de produtos
echo "5. VENDAS DE PRODUTOS (Comissao 10%):\n";
$vendas = DB::table('vendas')
    ->whereMonth('created_at', $mes)
    ->whereYear('created_at', $ano)
    ->get();
if ($vendas->count() > 0) {
    $totalVendas = $vendas->sum('valor_venda');
    $comissaoProdutos = $totalVendas * 0.10;
    echo "   Total de vendas: R$ " . number_format($totalVendas, 2, ',', '.') . "\n";
    echo "   Comissoes (10%): R$ " . number_format($comissaoProdutos, 2, ',', '.') . "\n";
    echo "   Quantidade de vendas: " . $vendas->count() . "\n\n";
} else {
    echo "   Nenhuma venda registrada\n\n";
}

// 6. Fechamento de caixa de hoje
echo "6. FECHAMENTO DE CAIXA (Hoje - " . date('d/m/Y') . "):\n";
$hoje = date('Y-m-d');
$agendHoje = Agendamento::where('status', 'executado')
    ->whereDate('updated_at', $hoje)
    ->get();
$vendHoje = DB::table('vendas')
    ->whereDate('created_at', $hoje)
    ->get();

$totalServHoje = $agendHoje->sum('valor_total');
$comissServHoje = $agendHoje->sum('valor_comissao');
$totalVendHoje = $vendHoje->sum('valor_venda');
$comissVendHoje = $totalVendHoje * 0.10;
$totalComissHoje = $comissServHoje + $comissVendHoje;
$lucroHoje = ($totalServHoje + $totalVendHoje) - $totalComissHoje;

echo "   Servicos: R$ " . number_format($totalServHoje, 2, ',', '.') . " | Comissoes: R$ " . number_format($comissServHoje, 2, ',', '.') . "\n";
echo "   Produtos: R$ " . number_format($totalVendHoje, 2, ',', '.') . " | Comissoes: R$ " . number_format($comissVendHoje, 2, ',', '.') . "\n";
echo "   Total Comissoes: R$ " . number_format($totalComissHoje, 2, ',', '.') . "\n";
echo "   Lucro Liquido: R$ " . number_format($lucroHoje, 2, ',', '.') . "\n\n";

echo "=== FIM DA VERIFICACAO ===\n\n";
