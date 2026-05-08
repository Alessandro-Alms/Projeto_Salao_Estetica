<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('agendamentos', function (Blueprint $table) {
            if (!Schema::hasColumn('agendamentos', 'valor_base')) {
                $table->decimal('valor_base', 10, 2)->nullable()->after('valor_total');
            }

            if (!Schema::hasColumn('agendamentos', 'acrescimo_especial')) {
                $table->decimal('acrescimo_especial', 10, 2)->default(0)->after('valor_base');
            }

            if (!Schema::hasColumn('agendamentos', 'motivo_acrescimo')) {
                $table->string('motivo_acrescimo')->nullable()->after('acrescimo_especial');
            }
        });
    }

    public function down(): void
    {
        Schema::table('agendamentos', function (Blueprint $table) {
            $colunas = array_filter([
                Schema::hasColumn('agendamentos', 'valor_base') ? 'valor_base' : null,
                Schema::hasColumn('agendamentos', 'acrescimo_especial') ? 'acrescimo_especial' : null,
                Schema::hasColumn('agendamentos', 'motivo_acrescimo') ? 'motivo_acrescimo' : null,
            ]);

            if ($colunas) {
                $table->dropColumn($colunas);
            }
        });
    }
};
