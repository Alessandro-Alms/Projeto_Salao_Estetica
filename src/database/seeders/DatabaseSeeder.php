<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AdminUserSeeder::class,
            UserSeeder::class,
            ServicoSeeder::class,
            ProdutoSeeder::class,
            HorarioTrabalhoSeeder::class,
            ProfissionalServicoSeeder::class,
            PacoteSeeder::class,
            AgendamentoSeeder::class,
            BloqueioHorarioSeeder::class,
            AvaliacaoSeeder::class,
            VendaSeeder::class,
            ClientePacoteSeeder::class,
        ]);
    }
}
