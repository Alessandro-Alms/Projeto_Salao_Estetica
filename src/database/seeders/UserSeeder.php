<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Recepcionista Teste',
            'email' => 'recepcao@salao.com',
            'password' => Hash::make('senha123'),
            'cpf' => '98765432101',
            'telefone' => '85987654322',
            'cargo' => 'recepcionista',
            'status' => 'ativo',
        ]);

        $profissionais = [
            ['name' => 'Carolina Silva', 'email' => 'carolina@salao.com', 'cpf' => '11111111111', 'telefone' => '85988881111'],
            ['name' => 'Mariana Costa', 'email' => 'mariana@salao.com', 'cpf' => '22222222222', 'telefone' => '85988882222'],
            ['name' => 'Beatriz Santos', 'email' => 'beatriz@salao.com', 'cpf' => '33333333333', 'telefone' => '85988883333'],
            ['name' => 'Fernanda Oliveira', 'email' => 'fernanda@salao.com', 'cpf' => '44444444444', 'telefone' => '85988884444'],
            ['name' => 'Juliana Pereira', 'email' => 'juliana@salao.com', 'cpf' => '55555555555', 'telefone' => '85988885555'],
        ];

        foreach ($profissionais as $profissional) {
            User::create([
                'name' => $profissional['name'],
                'email' => $profissional['email'],
                'password' => Hash::make('senha123'),
                'cpf' => $profissional['cpf'],
                'telefone' => $profissional['telefone'],
                'cargo' => 'profissional',
                'status' => 'ativo',
            ]);
        }

        $clientes = [
            ['name' => 'Ana Silva', 'email' => 'ana.cliente@email.com', 'cpf' => '66666666661', 'telefone' => '85999990001', 'contador_fidelidade' => 0],
            ['name' => 'Bianca Costa', 'email' => 'bianca.cliente@email.com', 'cpf' => '66666666662', 'telefone' => '85999990002', 'contador_fidelidade' => 2],
            ['name' => 'Camila Santos', 'email' => 'camila.cliente@email.com', 'cpf' => '66666666663', 'telefone' => '85999990003', 'contador_fidelidade' => 4],
            ['name' => 'Daniela Oliveira', 'email' => 'daniela.cliente@email.com', 'cpf' => '66666666664', 'telefone' => '85999990004', 'contador_fidelidade' => 5],
            ['name' => 'Elisa Ferreira', 'email' => 'elisa.cliente@email.com', 'cpf' => '66666666665', 'telefone' => '85999990005', 'contador_fidelidade' => 1],
        ];

        foreach ($clientes as $cliente) {
            User::create([
                'name' => $cliente['name'],
                'email' => $cliente['email'],
                'password' => Hash::make('senha123'),
                'cpf' => $cliente['cpf'],
                'telefone' => $cliente['telefone'],
                'cargo' => 'cliente',
                'status' => 'ativo',
                'contador_fidelidade' => $cliente['contador_fidelidade'],
            ]);
        }
    }
}
