<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cliente_pacotes', function (Blueprint $table) {
            $table->foreignId('vendedor_id')
                ->nullable()
                ->after('pacote_id')
                ->constrained('users')
                ->nullOnDelete();
            $table->decimal('valor_comissao', 10, 2)->default(0)->after('data_validade');
            $table->decimal('comissao_paga_percentual', 5, 2)->default(0)->after('valor_comissao');
        });
    }

    public function down(): void
    {
        Schema::table('cliente_pacotes', function (Blueprint $table) {
            $table->dropForeign(['vendedor_id']);
            $table->dropColumn(['vendedor_id', 'valor_comissao', 'comissao_paga_percentual']);
        });
    }
};
