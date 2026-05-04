<?php

namespace Database\Seeders;

use App\Models\Servico;
use Illuminate\Database\Seeder;

class ServicoSeeder extends Seeder
{
    public function run(): void
    {
        $servicos = [
            // Cabelo
            ['nome' => 'Escova Simples', 'descricao' => 'Escova simples e hidratação', 'preco' => 50.00, 'duracao' => 60],
            ['nome' => 'Escova Progressiva', 'descricao' => 'Aplicação de progressiva + escova', 'preco' => 150.00, 'duracao' => 120],
            ['nome' => 'Corte Feminino', 'descricao' => 'Corte com acabamento profissional', 'preco' => 80.00, 'duracao' => 60],
            ['nome' => 'Coloração', 'descricao' => 'Pintura de cabelo com reflexo', 'preco' => 120.00, 'duracao' => 90],
            ['nome' => 'Mechas', 'descricao' => 'Mechas californianas ou ombre', 'preco' => 180.00, 'duracao' => 120],
            
            // Unhas
            ['nome' => 'Manicure', 'descricao' => 'Limpeza, corte e hidratação de unhas', 'preco' => 40.00, 'duracao' => 45],
            ['nome' => 'Manicure com Gel', 'descricao' => 'Manicure com esmalte gel', 'preco' => 70.00, 'duracao' => 60],
            ['nome' => 'Unhas de Acrílico', 'descricao' => 'Alongamento com acrílico', 'preco' => 100.00, 'duracao' => 90],
            ['nome' => 'Pedicure', 'descricao' => 'Limpeza, corte e hidratação dos pés', 'preco' => 50.00, 'duracao' => 60],
            ['nome' => 'Pedicure Premium', 'descricao' => 'Pedicure com massagem relaxante', 'preco' => 80.00, 'duracao' => 75],
            
            // Depilação
            ['nome' => 'Depilação Rosto', 'descricao' => 'Remoção de pelos do rosto', 'preco' => 35.00, 'duracao' => 30],
            ['nome' => 'Depilação Virilha', 'descricao' => 'Depilação completa', 'preco' => 60.00, 'duracao' => 45],
            ['nome' => 'Depilação Pernas', 'descricao' => 'Depilação de pernas completa', 'preco' => 70.00, 'duracao' => 60],
            ['nome' => 'Depilação Axilas', 'descricao' => 'Depilação das axilas', 'preco' => 30.00, 'duracao' => 20],
            
            // Laser
            ['nome' => 'Laser Virilha (Sessão)', 'descricao' => 'Sessão de laser depilação', 'preco' => 150.00, 'duracao' => 60],
            ['nome' => 'Laser Pernas (Sessão)', 'descricao' => 'Laser para depilação de pernas', 'preco' => 200.00, 'duracao' => 75],
            
            // Estética
            ['nome' => 'Limpeza de Pele', 'descricao' => 'Limpeza profunda facial', 'preco' => 80.00, 'duracao' => 60],
            ['nome' => 'Hidratação Facial', 'descricao' => 'Hidratação intensiva da pele', 'preco' => 90.00, 'duracao' => 60],
            ['nome' => 'Peeling', 'descricao' => 'Esfoliação química da pele', 'preco' => 120.00, 'duracao' => 45],
            ['nome' => 'Botox Simulado', 'descricao' => 'Serviço de lifting sem cirurgia', 'preco' => 150.00, 'duracao' => 60],
            
            // Maquiagem
            ['nome' => 'Maquiagem Simples', 'descricao' => 'Maquiagem básica', 'preco' => 60.00, 'duracao' => 45],
            ['nome' => 'Maquiagem Noiva', 'descricao' => 'Maquiagem profissional para noivas', 'preco' => 200.00, 'duracao' => 90],
            ['nome' => 'Microblading', 'descricao' => 'Design de sobrancelhas com microblading', 'preco' => 250.00, 'duracao' => 120],
        ];

        foreach ($servicos as $servico) {
            Servico::create($servico);
        }
    }
}
