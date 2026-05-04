<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AgendamentoSeeder extends Seeder
{
    public function run(): void
    {
        $clientes = DB::table('users')->where('cargo', 'cliente')->pluck('id')->toArray();
        $profissionais = DB::table('users')->where('cargo', 'profissional')->pluck('id')->toArray();
        $servicosData = DB::table('servicos')->pluck('preco', 'id_servico')->toArray();

        // Cria agendamentos nos últimos 30 dias
        for ($i = 0; $i < 40; $i++) {
            $cliente = $clientes[array_rand($clientes)];
            $profissional = $profissionais[array_rand($profissionais)];
            
            // Seleciona um serviço aleatório corretamente
            $servicoId = array_rand($servicosData);
            $preco = $servicosData[$servicoId];

            // Data aleatória nos últimos 30 dias
            $data = Carbon::now()->subDays(rand(0, 30))->setHour(rand(8, 17))->setMinute(0);

            // Calcula duração (simplificado)
            $duracao = rand(30, 120);

            $comissaoPercentual = rand(45, 55) / 100;
            $comissao = $preco * $comissaoPercentual;

            // Status varia: alguns executados, alguns confirmados, alguns cancelados
            $statusOpcoes = ['executado', 'executado', 'executado', 'confirmado', 'cancelado', 'falta'];
            $status = $statusOpcoes[array_rand($statusOpcoes)];

            DB::table('agendamentos')->insert([
                'cliente_id' => $cliente,
                'profissional_id' => $profissional,
                'servico_id' => $servicoId,
                'data_hora_inicio' => $data,
                'data_hora_fim' => $data->copy()->addMinutes($duracao),
                'valor_total' => $preco,
                'valor_comissao' => $comissao,
                'comissao_paga_percentual' => $comissaoPercentual * 100,
                'status' => $status,
                'obs' => $status === 'cancelado' ? 'Cliente cancelou' : ($status === 'falta' ? 'Cliente não compareceu' : null),
                'created_at' => $data,
                'updated_at' => $data,
            ]);
        }
    }
}
