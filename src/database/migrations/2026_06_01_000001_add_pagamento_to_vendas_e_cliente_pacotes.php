<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vendas', function (Blueprint $table) {
            if (! Schema::hasColumn('vendas', 'status_pagamento')) {
                $table->string('status_pagamento', 30)->default('pago')->after('comissao_paga_percentual');
            }

            if (! Schema::hasColumn('vendas', 'forma_pagamento')) {
                $table->string('forma_pagamento', 30)->nullable()->after('status_pagamento');
            }

            if (! Schema::hasColumn('vendas', 'pago_em')) {
                $table->timestamp('pago_em')->nullable()->after('forma_pagamento');
            }

            if (! Schema::hasColumn('vendas', 'confirmado_por_id')) {
                $table->foreignId('confirmado_por_id')->nullable()->after('pago_em')->constrained('users')->nullOnDelete();
            }
        });

        Schema::table('cliente_pacotes', function (Blueprint $table) {
            if (! Schema::hasColumn('cliente_pacotes', 'status_pagamento')) {
                $table->string('status_pagamento', 30)->default('pago')->after('status');
            }

            if (! Schema::hasColumn('cliente_pacotes', 'forma_pagamento')) {
                $table->string('forma_pagamento', 30)->nullable()->after('status_pagamento');
            }

            if (! Schema::hasColumn('cliente_pacotes', 'pago_em')) {
                $table->timestamp('pago_em')->nullable()->after('forma_pagamento');
            }

            if (! Schema::hasColumn('cliente_pacotes', 'confirmado_por_id')) {
                $table->foreignId('confirmado_por_id')->nullable()->after('pago_em')->constrained('users')->nullOnDelete();
            }
        });

        DB::table('vendas')
            ->whereNull('pago_em')
            ->update(['pago_em' => DB::raw('created_at')]);

        DB::table('cliente_pacotes')
            ->whereNull('pago_em')
            ->update(['pago_em' => DB::raw('created_at')]);
    }

    public function down(): void
    {
        Schema::table('cliente_pacotes', function (Blueprint $table) {
            if (Schema::hasColumn('cliente_pacotes', 'confirmado_por_id')) {
                $table->dropConstrainedForeignId('confirmado_por_id');
            }

            $columns = array_filter(['status_pagamento', 'forma_pagamento', 'pago_em'], fn ($column) => Schema::hasColumn('cliente_pacotes', $column));

            if ($columns) {
                $table->dropColumn($columns);
            }
        });

        Schema::table('vendas', function (Blueprint $table) {
            if (Schema::hasColumn('vendas', 'confirmado_por_id')) {
                $table->dropConstrainedForeignId('confirmado_por_id');
            }

            $columns = array_filter(['status_pagamento', 'forma_pagamento', 'pago_em'], fn ($column) => Schema::hasColumn('vendas', $column));

            if ($columns) {
                $table->dropColumn($columns);
            }
        });
    }
};
