<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vendas', function (Blueprint $table) {
            if (! Schema::hasColumn('vendas', 'codigo_pedido')) {
                $table->string('codigo_pedido', 40)->nullable()->after('servico_id')->index();
            }
        });
    }

    public function down(): void
    {
        Schema::table('vendas', function (Blueprint $table) {
            if (Schema::hasColumn('vendas', 'codigo_pedido')) {
                $table->dropColumn('codigo_pedido');
            }
        });
    }
};
