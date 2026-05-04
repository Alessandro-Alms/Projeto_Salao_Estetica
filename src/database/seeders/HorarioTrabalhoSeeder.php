<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HorarioTrabalhoSeeder extends Seeder
{
    public function run(): void
    {
        // Pega todos os profissionais (IDs 3 a 7)
        $profissionais = DB::table('users')
            ->where('cargo', 'profissional')
            ->pluck('id')
            ->toArray();

        // Horário padrão: Segunda a Sexta (1 a 5)
        foreach ($profissionais as $profissionalId) {
            for ($dia = 1; $dia <= 5; $dia++) { // Segunda a Sexta
                DB::table('horarios_trabalho')->insert([
                    'profissional_id' => $profissionalId,
                    'dia_semana' => $dia,
                    'hora_inicio' => '08:00',
                    'hora_fim' => '18:00',
                    'almoco_inicio' => '12:00',
                    'almoco_fim' => '13:00',
                    'trabalha' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Sábado (6) - horário reduzido
            DB::table('horarios_trabalho')->insert([
                'profissional_id' => $profissionalId,
                'dia_semana' => 6,
                'hora_inicio' => '09:00',
                'hora_fim' => '14:00',
                'almoco_inicio' => null,
                'almoco_fim' => null,
                'trabalha' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Domingo (0) - fechado
            DB::table('horarios_trabalho')->insert([
                'profissional_id' => $profissionalId,
                'dia_semana' => 0,
                'hora_inicio' => '09:00',
                'hora_fim' => '09:00',
                'trabalha' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
