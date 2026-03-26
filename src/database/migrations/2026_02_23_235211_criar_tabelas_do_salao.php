<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabela de Usuários Unificada (Admins, Profissionais e Clientes)
        Schema::create('users', function (Blueprint $table) {
            $table->id(); 
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->char('cpf', 11)->unique();
            $table->char('telefone', 11);
            $table->enum('cargo', ['gerente', 'recepcionista', 'profissional', 'cliente'])->default('cliente');
            $table->string('endereco', 255)->nullable();
            $table->date('d_nasc')->nullable();
            $table->integer('faltas')->default(0);
            $table->text('obs')->nullable();
            $table->dateTime('ultima_visita')->nullable();

            $table->rememberToken();
            $table->timestamps();
        });

        // 2. Tabela de Serviços
        Schema::create('servicos', function (Blueprint $table) {
            $table->id('id_servico');
            $table->string('nome', 100);
            $table->text('descricao')->nullable();
            $table->decimal('preco', 10, 2);
            $table->integer('duracao'); 
            $table->timestamps();
        });

        // 3. Tabela de Produtos
        Schema::create('produtos', function (Blueprint $table) {
            $table->id('id_produto');
            $table->string('nome', 100);
            $table->text('descricao')->nullable();
            $table->enum('tipo', ['acessorios', 'kits', 'cosmeticos', 'cabelo']);
            $table->decimal('valor_unitario', 10, 2);
            $table->integer('quantidade_estoque')->default(0);
            $table->timestamps();
        });

        // 4. Tabela de Agendamentos
        Schema::create('agendamentos', function (Blueprint $table) {
            $table->id('id_agendamento');
            $table->foreignId('cliente_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('profissional_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('servico_id')->constrained('servicos', 'id_servico')->onDelete('cascade');
            $table->dateTime('data_hora_inicio');
            $table->dateTime('data_hora_fim');
            $table->decimal('valor_total', 10, 2);
            $table->enum('status', ['confirmado', 'cancelado', 'falta', 'executado'])->default('confirmado');
            $table->text('obs')->nullable();
            
            $table->timestamps();
        });

        // 5. Tabela de Atendimentos (Histórico de serviços realizados)
        Schema::create('atendimentos', function (Blueprint $table) {
            $table->id('id_atendimento');
            $table->foreignId('cliente_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('profissional_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('agendamento_id')->nullable()->constrained('agendamentos', 'id_agendamento');
            $table->decimal('valor_total', 10, 2);
            $table->integer('avaliacao')->nullable();
            $table->text('descricao_detalhada')->nullable();
            $table->timestamps();
        });

        // 6. Tabela de Vendas (Produtos e Serviços avulsos)
        Schema::create('vendas', function (Blueprint $table) {
            $table->id('id_venda');
            $table->foreignId('user_id')->constrained('users'); // Quem realizou a venda
            $table->foreignId('id_produto')->nullable()->constrained('produtos', 'id_produto');
            $table->foreignId('id_servico')->nullable()->constrained('servicos', 'id_servico');
            $table->integer('quantidade')->default(1);
            $table->decimal('valor_venda', 10, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendas');
        Schema::dropIfExists('atendimentos');
        Schema::dropIfExists('agendamentos');
        Schema::dropIfExists('produtos');
        Schema::dropIfExists('servicos');
        Schema::dropIfExists('users');
    }
};