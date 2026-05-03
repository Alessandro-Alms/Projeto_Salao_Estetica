<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Agendamento;

echo "\n=== AGENDAMENTOS COM PROBLEMAS ===\n\n";

// Encontrar agendamentos com taxa errada
$executados = Agendamento::where('status', 'executado')->get();
$comProblema = [];

foreach ($executados as $ag) {
    $taxaEsperada = 50;
    $taxaReal = $ag->valor_total > 0 ? ($ag->valor_comissao / $ag->valor_total) * 100 : 0;
    
    if (abs($taxaReal - $taxaEsperada) > 0.5) {
        $comProblema[] = [
            'id' => $ag->id_agendamento,
            'taxa' => $taxaReal,
            'valor_total' => $ag->valor_total,
            'comissao' => $ag->valor_comissao,
            'servico' => $ag->servico->nome,
            'data' => $ag->updated_at->format('d/m/Y H:i')
        ];
    }
}

if (count($comProblema) > 0) {
    echo "Encontrados " . count($comProblema) . " agendamento(s) com taxa incorreta:\n";
    echo str_repeat("-", 80) . "\n";
    
    foreach ($comProblema as $prob) {
        echo "ID: " . $prob['id'] . " | Taxa: " . number_format($prob['taxa'], 2) . "% | ";
        echo "Valor: R$ " . number_format($prob['valor_total'], 2, ',', '.') . " | ";
        echo "Comissao: R$ " . number_format($prob['comissao'], 2, ',', '.') . "\n";
        echo "Servico: " . $prob['servico'] . " | Data: " . $prob['data'] . "\n";
        echo str_repeat("-", 80) . "\n";
    }
    
    echo "\nFIXANDO VALORES...\n";
    
    // Corrigir valores
    foreach ($comProblema as $prob) {
        $ag = Agendamento::find($prob['id']);
        $valorComissaoCorreto = $ag->valor_total * 0.50;
        $ag->valor_comissao = $valorComissaoCorreto;
        $ag->comissao_paga_percentual = 50;
        $ag->save();
        echo "✓ Agendamento #" . $ag->id_agendamento . " corrigido\n";
    }
    
    echo "\nCORRECAO CONCLUIDA!\n";
} else {
    echo "✓ Todos os agendamentos estao com 50% de comissao\n";
}

echo "\n";
