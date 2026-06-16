<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pagamentos', function (Blueprint $table) {
            $table->id();
            $table->morphs('pagavel');
            $table->string('forma_pagamento', 30);
            $table->decimal('valor', 10, 2);
            $table->foreignId('recebido_por_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('pago_em')->nullable();
            $table->timestamps();

            $table->index(['forma_pagamento', 'pago_em']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pagamentos');
    }
};
