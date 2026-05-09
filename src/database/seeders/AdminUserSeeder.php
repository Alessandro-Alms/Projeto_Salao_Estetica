<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = env('ADMIN_EMAIL', 'gerente@salao.com');

        User::updateOrCreate(
            ['email' => $email],
            [
                'name' => env('ADMIN_NAME', 'Gerente Principal'),
                'password' => Hash::make(env('ADMIN_PASSWORD', 'senha123')),
                'cpf' => env('ADMIN_CPF', '12345678901'),
                'telefone' => env('ADMIN_PHONE', '85987654321'),
                'cargo' => 'gerente',
                'status' => 'ativo',
            ]
        );
    }
}
