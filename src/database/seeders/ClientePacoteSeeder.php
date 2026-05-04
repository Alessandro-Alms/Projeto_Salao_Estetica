<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ClientePacoteSeeder extends Seeder
{
    public function run(): void
    {
        $clientes = DB::table('users')->where('cargo', 'cliente')->pluck('id')->toArray();
        $pacotes = DB::table('pacotes')->get();

        // Cada cliente compra 1-2 pacotes aleatoriamente
        foreach ($clientes as $clienteId) {
            $quantidadePacotes = rand(1, 2);
            
            for ($i = 0; $i < $quantidadePacotes; $i++) {
                $pacote = $pacotes->random();
                $dataCompra = Carbon::now()->subDays(rand(0, 90));
                $dataValidade = $dataCompra->copy()->addDays($pacote->validade_dias);

                // 70% chance de estar ativo, 30% vencido ou finalizado
                $randStatus = rand(1, 100);
                if ($randStatus > 70) {
                    $status = 'vencido';
                    $sessoesRestantes = $pacote->quantidade_sessoes; // Não usou
                } elseif ($randStatus > 40) {
                    $status = 'finalizado';
                    $sessoesRestantes = 0;
                } else {
                    $status = 'ativo';
                    $sessoesRestantes = rand(1, $pacote->quantidade_sessoes - 1);
                }

                DB::table('cliente_pacotes')->insert([
                    'cliente_id' => $clienteId,
                    'pacote_id' => $pacote->id_pacote,
                    'sessoes_restantes' => $sessoesRestantes,
                    'data_compra' => $dataCompra,
                    'data_validade' => $dataValidade,
                    'status' => $status,
                    'created_at' => $dataCompra,
                    'updated_at' => $dataCompra,
                ]);
            }
        }
    }
}
