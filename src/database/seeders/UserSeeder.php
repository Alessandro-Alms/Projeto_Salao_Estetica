<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Usuários administrativos
        User::create([
            'name' => 'Gerente Principal',
            'email' => 'gerente@salao.com',
            'password' => Hash::make('senha123'),
            'cpf' => '12345678901',
            'telefone' => '85987654321',
            'cargo' => 'gerente',
            'status' => 'ativo',
        ]);

        User::create([
            'name' => 'Recepcionista',
            'email' => 'recepcao@salao.com',
            'password' => Hash::make('senha123'),
            'cpf' => '98765432101',
            'telefone' => '85987654322',
            'cargo' => 'recepcionista',
            'status' => 'ativo',
        ]);

        // Profissionais
        $profissionais = [
            ['name' => 'Carolina Silva', 'email' => 'carolina@salao.com', 'cpf' => '11111111111', 'telefone' => '85988881111'],
            ['name' => 'Mariana Costa', 'email' => 'mariana@salao.com', 'cpf' => '22222222222', 'telefone' => '85988882222'],
            ['name' => 'Beatriz Santos', 'email' => 'beatriz@salao.com', 'cpf' => '33333333333', 'telefone' => '85988883333'],
            ['name' => 'Fernanda Oliveira', 'email' => 'fernanda@salao.com', 'cpf' => '44444444444', 'telefone' => '85988884444'],
            ['name' => 'Juliana Pereira', 'email' => 'juliana@salao.com', 'cpf' => '55555555555', 'telefone' => '85988885555'],
        ];

        foreach ($profissionais as $prof) {
            User::create([
                'name' => $prof['name'],
                'email' => $prof['email'],
                'password' => Hash::make('senha123'),
                'cpf' => $prof['cpf'],
                'telefone' => $prof['telefone'],
                'cargo' => 'profissional',
                'status' => 'ativo',
            ]);
        }

        // Clientes
        $nomes = ['Ana Silva', 'Beatriz Costa', 'Camila Santos', 'Diana Oliveira', 'Elisa Ferreira', 
                  'Fabiana Gomes', 'Gabriela Martins', 'Helena Souza', 'Iris Rocha', 'Joana Alves',
                  'Karina Dias', 'Larissa Mendes', 'Marilia Neves', 'Natalia Campos', 'Oona Ribeiro'];
        
        foreach ($nomes as $index => $nome) {
            User::create([
                'name' => $nome,
                'email' => 'cliente' . ($index + 1) . '@email.com',
                'password' => Hash::make('senha123'),
                'cpf' => str_pad($index + 1, 11, '6'),
                'telefone' => '8599' . str_pad($index + 1, 6, '0'),
                'cargo' => 'cliente',
                'status' => 'ativo',
                'contador_fidelidade' => rand(0, 50),
            ]);
        }
    }
}
