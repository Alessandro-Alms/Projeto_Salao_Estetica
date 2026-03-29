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
        Schema::create('horarios_trabalho', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('users')->onDelete('cascade');
            $table->integer('dia_semana'); // 0 = Domingo, 1 = Segunda...
            $table->time('hora_inicio')->default('08:00');
            $table->time('hora_fim')->default('18:00');
            $table->boolean('trabalha')->default(true); // Para marcar folgas fixas
            $table->timestamps();
            $table->time('almoco_inicio')->nullable()->default('12:00');
            $table->time('almoco_fim')->nullable()->default('13:00');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('horarios_trabalho');
    }
};
