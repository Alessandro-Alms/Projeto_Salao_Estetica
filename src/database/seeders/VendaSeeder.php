<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class VendaSeeder extends Seeder
{
    public function run(): void
    {
        $profissionais = DB::table('users')->where('cargo', 'profissional')->pluck('id')->toArray();
        $produtosData = DB::table('produtos')->pluck('valor_unitario', 'id_produto')->toArray();

        // Cria vendas dos últimos 30 dias
        for ($i = 0; $i < 25; $i++) {
            $profissional = $profissionais[array_rand($profissionais)];
            
            // Seleciona um produto aleatório corretamente
            $produtoId = array_rand($produtosData);
            $preco = $produtosData[$produtoId];
            $quantidade = rand(1, 3);

            $data = Carbon::now()->subDays(rand(0, 30));

            DB::table('vendas')->insert([
                'profissional_id' => $profissional,
                'produto_id' => $produtoId,
                'servico_id' => null,
                'quantidade' => $quantidade,
                'valor_venda' => $preco * $quantidade,
                'created_at' => $data,
                'updated_at' => $data,
            ]);
        }
    }
}
