<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class AdminUserSeeder extends Seeder
{
public function run(): void
{
    User::create([
        'name'     => env('ADMIN_NAME'),
        'email'    => env('ADMIN_EMAIL'),
        'password' => bcrypt(env('ADMIN_PASSWORD')),
        'cpf'      => env('ADMIN_CPF'),
        'telefone' => env('ADMIN_PHONE'),
        'cargo'    => 'gerente',
    ]);
}
}
