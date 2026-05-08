<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProfissionalServicoSeeder extends Seeder
{
    public function run(): void
    {
        $profissionais = DB::table('users')
            ->where('cargo', 'profissional')
            ->orderBy('id')
            ->pluck('id')
            ->values();

        $servicos = DB::table('servicos')
            ->orderBy('id_servico')
            ->pluck('duracao', 'id_servico');

        foreach ($profissionais as $index => $profissionalId) {
            foreach ($servicos as $servicoId => $duracao) {
                if (($servicoId + $index) % 5 === 0) {
                    continue;
                }

                DB::table('profissional_servico')->insert([
                    'profissional_id' => $profissionalId,
                    'servico_id' => $servicoId,
                    'comissao_percentual' => 50.00,
                    'duracao_customizada' => $duracao,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
