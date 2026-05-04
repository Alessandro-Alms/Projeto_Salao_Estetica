<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AvaliacaoSeeder extends Seeder
{
    public function run(): void
    {
        // Pega agendamentos executados
        $agendamentosExecutados = DB::table('agendamentos')
            ->where('status', 'executado')
            ->get();

        foreach ($agendamentosExecutados as $agendamento) {
            // Nem todos os agendamentos têm avaliação (70% de chance)
            if (rand(1, 100) > 30) {
                $nota = rand(3, 5); // Notas de 3 a 5 (maioria positiva)

                $comentarios = [
                    'Excelente atendimento!',
                    'Muito bom, recomendo',
                    'Ótimo resultado',
                    'Muito satisfeito',
                    'Perfeito!',
                    'Não gostei muito',
                    'Poderia ser melhor',
                    'Atencioso e profissional',
                    'Voltarei com certeza',
                    'Muito legal!',
                ];

                DB::table('avaliacoes')->insert([
                    'agendamento_id' => $agendamento->id_agendamento,
                    'cliente_id' => $agendamento->cliente_id,
                    'profissional_id' => $agendamento->profissional_id,
                    'nota' => $nota,
                    'comentario' => $comentarios[array_rand($comentarios)],
                    'created_at' => Carbon::now()->subDays(rand(0, 30)),
                    'updated_at' => Carbon::now()->subDays(rand(0, 30)),
                ]);
            }
        }
    }
}
