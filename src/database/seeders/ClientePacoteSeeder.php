<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClientePacoteSeeder extends Seeder
{
    public function run(): void
    {
        $clientes = DB::table('users')->where('cargo', 'cliente')->orderBy('id')->pluck('id')->values();
        $pacotes = DB::table('pacotes')->orderBy('id_pacote')->get()->values();

        if ($clientes->isEmpty() || $pacotes->isEmpty()) {
            return;
        }

        foreach ($clientes as $index => $clienteId) {
            $pacote = $pacotes[$index % $pacotes->count()];
            $dataCompra = Carbon::now()->subDays(5 + $index);
            $status = $index === 4 ? 'finalizado' : 'ativo';

            DB::table('cliente_pacotes')->insert([
                'cliente_id' => $clienteId,
                'pacote_id' => $pacote->id_pacote,
                'sessoes_restantes' => $status === 'ativo' ? max(1, $pacote->quantidade_sessoes - ($index + 1)) : 0,
                'data_compra' => $dataCompra->toDateString(),
                'data_validade' => $dataCompra->copy()->addDays($pacote->validade_dias)->toDateString(),
                'status' => $status,
                'created_at' => $dataCompra,
                'updated_at' => $dataCompra,
            ]);
        }
    }
}
