<?php

use App\Services\FinanceiroService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vendas', function (Blueprint $table) {
            $table->decimal('valor_comissao', 10, 2)->default(0)->after('valor_venda');
            $table->decimal('comissao_paga_percentual', 5, 2)->default(0)->after('valor_comissao');
        });

        DB::table('vendas')
            ->whereNotNull('produto_id')
            ->update([
                'valor_comissao' => DB::raw('ROUND(valor_venda * ' . (FinanceiroService::COMISSAO_PRODUTO_PERCENTUAL / 100) . ', 2)'),
                'comissao_paga_percentual' => FinanceiroService::COMISSAO_PRODUTO_PERCENTUAL,
            ]);
    }

    public function down(): void
    {
        Schema::table('vendas', function (Blueprint $table) {
            $table->dropColumn(['valor_comissao', 'comissao_paga_percentual']);
        });
    }
};
