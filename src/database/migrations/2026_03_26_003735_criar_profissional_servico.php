<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('profissional_servico', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('users')->onDelete('cascade'); // O Profissional
            $table->foreignId('servico_id')->constrained('servicos', 'id_servico')->onDelete('cascade');
            $table->decimal('comissao_percentual', 5, 2)->default(50.00); // RN002
            $table->integer('duracao_customizada')->nullable(); // RN006 (em minutos)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
