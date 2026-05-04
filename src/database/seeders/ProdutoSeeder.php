<?php

namespace Database\Seeders;

use App\Models\Produto;
use Illuminate\Database\Seeder;

class ProdutoSeeder extends Seeder
{
    public function run(): void
    {
        $produtos = [
            // Cosméticos
            ['nome' => 'Shampoo Premium', 'descricao' => 'Shampoo para cabelos tratados', 'tipo' => 'cosmeticos', 'valor_unitario' => 45.00, 'quantidade_estoque' => 30],
            ['nome' => 'Condicionador Intensivo', 'descricao' => 'Condicionador nutritivo', 'tipo' => 'cosmeticos', 'valor_unitario' => 50.00, 'quantidade_estoque' => 25],
            ['nome' => 'Máscara Capilar', 'descricao' => 'Tratamento profundo para cabelos', 'tipo' => 'cosmeticos', 'valor_unitario' => 60.00, 'quantidade_estoque' => 20],
            ['nome' => 'Sérum Anti-Frizz', 'descricao' => 'Sérum para controle de frizz', 'tipo' => 'cosmeticos', 'valor_unitario' => 55.00, 'quantidade_estoque' => 18],
            ['nome' => 'Protetor Térmico', 'descricao' => 'Protetor térmico profissional', 'tipo' => 'cosmeticos', 'valor_unitario' => 40.00, 'quantidade_estoque' => 22],
            
            // Cabelo
            ['nome' => 'Óleos Capilares - Kit', 'descricao' => 'Kit com 3 óleos diferentes', 'tipo' => 'cabelo', 'valor_unitario' => 120.00, 'quantidade_estoque' => 15],
            ['nome' => 'Tintura Profissional', 'descricao' => 'Tintura permanente', 'tipo' => 'cabelo', 'valor_unitario' => 35.00, 'quantidade_estoque' => 40],
            ['nome' => 'Pó Descolorante', 'descricao' => 'Pó descolorante profissional', 'tipo' => 'cabelo', 'valor_unitario' => 25.00, 'quantidade_estoque' => 30],
            
            // Acessórios
            ['nome' => 'Escova de Cabelo Profissional', 'descricao' => 'Escova térmica oval', 'tipo' => 'acessorios', 'valor_unitario' => 150.00, 'quantidade_estoque' => 8],
            ['nome' => 'Secador de Cabelo', 'descricao' => 'Secador profissional 2000W', 'tipo' => 'acessorios', 'valor_unitario' => 250.00, 'quantidade_estoque' => 5],
            ['nome' => 'Pente de Carbono', 'descricao' => 'Pente anti-estático', 'tipo' => 'acessorios', 'valor_unitario' => 35.00, 'quantidade_estoque' => 20],
            ['nome' => 'Presilha Profissional', 'descricao' => 'Set de presilhas variadas', 'tipo' => 'acessorios', 'valor_unitario' => 25.00, 'quantidade_estoque' => 15],
            ['nome' => 'Rolo de Cabelo', 'descricao' => 'Kit com 6 rolos de diferentes tamanhos', 'tipo' => 'acessorios', 'valor_unitario' => 45.00, 'quantidade_estoque' => 12],
            
            // Kits
            ['nome' => 'Kit Inicial Manicure', 'descricao' => 'Kit com essenciais para manicure', 'tipo' => 'kits', 'valor_unitario' => 180.00, 'quantidade_estoque' => 10],
            ['nome' => 'Kit Progressiva', 'descricao' => 'Kit completo de produtos para progressiva', 'tipo' => 'kits', 'valor_unitario' => 280.00, 'quantidade_estoque' => 8],
            ['nome' => 'Kit Profissional Cabelo', 'descricao' => 'Kit com shampoo, condicionador e máscara', 'tipo' => 'kits', 'valor_unitario' => 150.00, 'quantidade_estoque' => 12],
            ['nome' => 'Kit Esmaltes Gel', 'descricao' => 'Kit com 10 cores diferentes', 'tipo' => 'kits', 'valor_unitario' => 220.00, 'quantidade_estoque' => 6],
        ];

        foreach ($produtos as $produto) {
            Produto::create($produto);
        }
    }
}
