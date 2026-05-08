<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VendaSeeder extends Seeder
{
    public function run(): void
    {
        $vendedores = DB::table('users')
            ->whereIn('cargo', ['recepcionista', 'gerente', 'profissional'])
            ->orderBy('id')
            ->pluck('id')
            ->values();

        $produtos = DB::table('produtos')
            ->orderBy('id_produto')
            ->get()
            ->values();

        if ($vendedores->isEmpty() || $produtos->isEmpty()) {
            return;
        }

        for ($i = 0; $i < 10; $i++) {
            $produto = $produtos[$i % $produtos->count()];
            $quantidade = ($i % 3) + 1;
            $data = Carbon::now()->subDays($i % 7)->setTime(10 + ($i % 6), 0, 0);

            DB::table('vendas')->insert([
                'profissional_id' => $vendedores[$i % $vendedores->count()],
                'produto_id' => $produto->id_produto,
                'servico_id' => null,
                'quantidade' => $quantidade,
                'valor_venda' => ((float) $produto->valor_unitario) * $quantidade,
                'created_at' => $data,
                'updated_at' => $data,
            ]);
        }
    }
}
