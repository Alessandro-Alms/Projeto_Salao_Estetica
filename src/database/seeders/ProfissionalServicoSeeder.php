<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProfissionalServicoSeeder extends Seeder
{
    public function run(): void
    {
        // Pega todos os profissionais
        $profissionais = DB::table('users')
            ->where('cargo', 'profissional')
            ->pluck('id')
            ->toArray();

        // Pega todos os serviços
        $servicos = DB::table('servicos')
            ->pluck('id_servico')
            ->toArray();

        // Cada profissional pode fazer certos serviços
        // Vamos variar para não todos fazerem tudo
        $comissoesPadrao = [50.00, 45.00, 55.00, 50.00, 48.00];

        foreach ($profissionais as $index => $profissionalId) {
            // Seleciona serviços aleatórios para cada profissional
            $servicosSelecionados = array_slice($servicos, 0, rand(15, count($servicos)));
            
            foreach ($servicosSelecionados as $servicoId) {
                DB::table('profissional_servico')->insert([
                    'profissional_id' => $profissionalId,
                    'servico_id' => $servicoId,
                    'comissao_percentual' => $comissoesPadrao[$index % count($comissoesPadrao)],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
