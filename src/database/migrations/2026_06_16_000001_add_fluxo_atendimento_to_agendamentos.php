<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE agendamentos MODIFY status VARCHAR(40) NOT NULL DEFAULT 'confirmado'");
        }

        Schema::table('agendamentos', function (Blueprint $table) {
            if (! Schema::hasColumn('agendamentos', 'chegada_em')) {
                $table->timestamp('chegada_em')->nullable()->after('pago_em');
            }

            if (! Schema::hasColumn('agendamentos', 'atendimento_iniciado_em')) {
                $table->timestamp('atendimento_iniciado_em')->nullable()->after('chegada_em');
            }

            if (! Schema::hasColumn('agendamentos', 'atendimento_finalizado_em')) {
                $table->timestamp('atendimento_finalizado_em')->nullable()->after('atendimento_iniciado_em');
            }

            if (! Schema::hasColumn('agendamentos', 'saida_em')) {
                $table->timestamp('saida_em')->nullable()->after('atendimento_finalizado_em');
            }
        });

        Schema::table('vendas', function (Blueprint $table) {
            if (! Schema::hasColumn('vendas', 'agendamento_id')) {
                $table->foreignId('agendamento_id')
                    ->nullable()
                    ->after('servico_id')
                    ->constrained('agendamentos', 'id_agendamento')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('vendas', function (Blueprint $table) {
            if (Schema::hasColumn('vendas', 'agendamento_id')) {
                $table->dropConstrainedForeignId('agendamento_id');
            }
        });

        Schema::table('agendamentos', function (Blueprint $table) {
            $columns = array_filter([
                'chegada_em',
                'atendimento_iniciado_em',
                'atendimento_finalizado_em',
                'saida_em',
            ], fn ($column) => Schema::hasColumn('agendamentos', $column));

            if ($columns) {
                $table->dropColumn($columns);
            }
        });
    }
};
