<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Agendamento;
use Illuminate\Support\Facades\DB;

echo "\n=== VERIFICACAO DE COMISSOES NO BANCO ===\n\n";

// Agendamentos executados
$ag = Agendamento::where('status', 'executado')->orderBy('updated_at', 'desc')->limit(5)->get();

echo "AGENDAMENTOS EXECUTADOS (Ultimos 5):\n";
echo str_repeat("-", 100) . "\n";
echo "ID | Valor Total | Comissao | % Comissao | Taxa Calculada | Status\n";
echo str_repeat("-", 100) . "\n";

foreach ($ag as $a) {
    $taxa = $a->valor_total > 0 ? ($a->valor_comissao / $a->valor_total) * 100 : 0;
    printf("%2d | R$ %7.2f | R$ %6.2f | %9.2f%% | %14.2f%% | %s\n",
        $a->id_agendamento,
        $a->valor_total,
        $a->valor_comissao ?? 0,
        $a->comissao_paga_percentual ?? 0,
        $taxa,
        $a->status
    );
}

echo str_repeat("-", 100) . "\n\n";

// Verificacao de integridade
$allExecutados = Agendamento::where('status', 'executado')->get();
$totalComissoes = $allExecutados->sum('valor_comissao');
$totalValor = $allExecutados->sum('valor_total');

echo "RESUMO TOTAL:\n";
echo "Total de agendamentos executados: " . $allExecutados->count() . "\n";
echo "Valor total faturado: R$ " . number_format($totalValor, 2, ',', '.') . "\n";
echo "Total em comissoes: R$ " . number_format($totalComissoes, 2, ',', '.') . "\n";

// Verificar se a taxa e consistente (50%)
$taxas = $allExecutados->map(function($a) {
    return $a->valor_total > 0 ? ($a->valor_comissao / $a->valor_total) * 100 : 0;
})->unique();

echo "\nTaxas de comissao encontradas: " . implode(", ", $taxas->toArray()) . "%\n";

if ($taxas->count() == 1 && abs($taxas->first() - 50) < 0.01) {
    echo "STATUS: ✓ OK - Todos com 50%\n\n";
} else {
    echo "STATUS: ✗ ERRO - Taxas inconsistentes!\n\n";
}

// Vendas de produtos
$vendas = DB::table('vendas')->get();
$totalVendas = $vendas->sum('valor_venda');
$comissVendas = $totalVendas * 0.10;

echo "VENDAS DE PRODUTOS:\n";
echo "Total de vendas: R$ " . number_format($totalVendas, 2, ',', '.') . "\n";
echo "Comissoes (10%): R$ " . number_format($comissVendas, 2, ',', '.') . "\n";
echo "Quantidade: " . $vendas->count() . "\n\n";

echo "=== FIM DA VERIFICACAO ===\n\n";
