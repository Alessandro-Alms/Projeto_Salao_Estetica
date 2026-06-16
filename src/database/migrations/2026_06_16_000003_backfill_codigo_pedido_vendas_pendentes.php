<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('vendas', 'codigo_pedido')) {
            return;
        }

        DB::table('vendas')
            ->whereNull('codigo_pedido')
            ->where('status_pagamento', 'pendente')
            ->whereNotNull('produto_id')
            ->orderBy('profissional_id')
            ->orderBy('created_at')
            ->orderBy('id_venda')
            ->select('id_venda', 'profissional_id', 'created_at')
            ->get()
            ->groupBy(fn ($venda) => $venda->profissional_id . '|' . $venda->created_at)
            ->each(function ($vendas) {
                $primeira = $vendas->first();
                $codigo = 'legado-' . $primeira->profissional_id . '-' . preg_replace('/\D/', '', $primeira->created_at);

                DB::table('vendas')
                    ->whereIn('id_venda', $vendas->pluck('id_venda'))
                    ->update(['codigo_pedido' => $codigo]);
            });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('vendas', 'codigo_pedido')) {
            return;
        }

        DB::table('vendas')
            ->where('codigo_pedido', 'like', 'legado-%')
            ->update(['codigo_pedido' => null]);
    }
};
