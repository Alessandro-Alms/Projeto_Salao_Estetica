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
        if (! Schema::hasColumn('vendas', 'valor_comissao')) {
            Schema::table('vendas', function (Blueprint $table) {
                $table->decimal('valor_comissao', 10, 2)->default(0)->after('valor_venda');
            });
        }

        if (! Schema::hasColumn('vendas', 'comissao_paga_percentual')) {
            Schema::table('vendas', function (Blueprint $table) {
                $table->decimal('comissao_paga_percentual', 5, 2)->default(0)->after('valor_comissao');
            });
        }

        DB::table('vendas')
            ->whereNotNull('produto_id')
            ->update([
                'valor_comissao' => DB::raw('ROUND(valor_venda * ' . (FinanceiroService::COMISSAO_PRODUTO_PERCENTUAL / 100) . ', 2)'),
                'comissao_paga_percentual' => FinanceiroService::COMISSAO_PRODUTO_PERCENTUAL,
            ]);
    }

    public function down(): void
    {
        if (Schema::hasColumn('vendas', 'valor_comissao') || Schema::hasColumn('vendas', 'comissao_paga_percentual')) {
            Schema::table('vendas', function (Blueprint $table) {
                $columns = [];

                if (Schema::hasColumn('vendas', 'valor_comissao')) {
                    $columns[] = 'valor_comissao';
                }

                if (Schema::hasColumn('vendas', 'comissao_paga_percentual')) {
                    $columns[] = 'comissao_paga_percentual';
                }

                $table->dropColumn($columns);
            });
        }
    }
};
