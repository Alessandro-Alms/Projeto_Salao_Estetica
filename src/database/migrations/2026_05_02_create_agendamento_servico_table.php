<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabela para relacionar múltiplos serviços a cada agendamento
        Schema::create('agendamento_servico', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agendamento_id')
                ->constrained('agendamentos', 'id_agendamento')
                ->onDelete('cascade');
            $table->foreignId('servico_id')
                ->constrained('servicos', 'id_servico')
                ->onDelete('cascade');
            $table->integer('duracao')->nullable(); // Duração do serviço neste agendamento (em minutos)
            $table->decimal('preco', 10, 2)->nullable(); // Preço do serviço neste agendamento
            $table->timestamps();
            
            // Evitar duplicatas: um serviço só pode estar uma vez por agendamento
            $table->unique(['agendamento_id', 'servico_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agendamento_servico');
    }
};
