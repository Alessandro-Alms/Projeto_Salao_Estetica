<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('agendamentos', function (Blueprint $table) {
            if (! Schema::hasColumn('agendamentos', 'status_pagamento')) {
                $table->string('status_pagamento', 30)->default('pendente')->after('multa_valor');
            }

            if (! Schema::hasColumn('agendamentos', 'forma_pagamento')) {
                $table->string('forma_pagamento', 30)->nullable()->after('status_pagamento');
            }

            if (! Schema::hasColumn('agendamentos', 'pago_em')) {
                $table->timestamp('pago_em')->nullable()->after('forma_pagamento');
            }
        });

        DB::table('agendamentos')
            ->where('status', 'executado')
            ->where(function ($query) {
                $query->whereNull('status_pagamento')->orWhere('status_pagamento', 'pendente');
            })
            ->update([
                'status_pagamento' => 'pago',
                'forma_pagamento' => 'dinheiro',
                'pago_em' => DB::raw('updated_at'),
            ]);
    }

    public function down(): void
    {
        Schema::table('agendamentos', function (Blueprint $table) {
            $columns = array_filter(['status_pagamento', 'forma_pagamento', 'pago_em'], fn ($column) => Schema::hasColumn('agendamentos', $column));

            if ($columns) {
                $table->dropColumn($columns);
            }
        });
    }
};
