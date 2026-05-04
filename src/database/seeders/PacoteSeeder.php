<?php

namespace Database\Seeders;

use App\Models\Pacote;
use Illuminate\Database\Seeder;

class PacoteSeeder extends Seeder
{
    public function run(): void
    {
        $pacotes = [
            [
                'nome' => 'Pacote Laser Virilha - 6 Sessões',
                'servico_id' => 16, // Laser Virilha
                'quantidade_sessoes' => 6,
                'valor_total' => 800.00,
                'validade_dias' => 180,
                'ativo' => true,
            ],
            [
                'nome' => 'Pacote Laser Pernas - 8 Sessões',
                'servico_id' => 17, // Laser Pernas
                'quantidade_sessoes' => 8,
                'valor_total' => 1400.00,
                'validade_dias' => 180,
                'ativo' => true,
            ],
            [
                'nome' => 'Pacote Manicure - 10 Sessões',
                'servico_id' => 6, // Manicure
                'quantidade_sessoes' => 10,
                'valor_total' => 350.00,
                'validade_dias' => 90,
                'ativo' => true,
            ],
            [
                'nome' => 'Pacote Pedicure - 10 Sessões',
                'servico_id' => 9, // Pedicure
                'quantidade_sessoes' => 10,
                'valor_total' => 450.00,
                'validade_dias' => 90,
                'ativo' => true,
            ],
            [
                'nome' => 'Pacote Escova Simples - 5 Sessões',
                'servico_id' => 1, // Escova Simples
                'quantidade_sessoes' => 5,
                'valor_total' => 200.00,
                'validade_dias' => 60,
                'ativo' => true,
            ],
            [
                'nome' => 'Pacote Limpeza de Pele - 6 Sessões',
                'servico_id' => 18, // Limpeza de Pele
                'quantidade_sessoes' => 6,
                'valor_total' => 400.00,
                'validade_dias' => 120,
                'ativo' => true,
            ],
            [
                'nome' => 'Pacote Completo Beleza - 12 Serviços',
                'servico_id' => 1, // Escova Simples (principal)
                'quantidade_sessoes' => 12,
                'valor_total' => 900.00,
                'validade_dias' => 180,
                'ativo' => true,
            ],
        ];

        foreach ($pacotes as $pacote) {
            Pacote::create($pacote);
        }
    }
}
