<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // COMENTE estas linhas abaixo para evitar o erro do 'Test User'
        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        // ADICIONE a chamada para o SEU Seeder pessoal aqui:
        $this->call([
            AdminUserSeeder::class,
        ]);
    }
}