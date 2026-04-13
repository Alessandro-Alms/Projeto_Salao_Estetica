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
            $table->enum('status', ['ativo', 'bloqueado'])->default('ativo');
            $table->text('obs')->nullable();
            $table->dateTime('ultima_visita')->nullable();
            $table->integer('contador_fidelidade')->default(0);
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
            $table->decimal('valor_total', 10, 2)->nullable();
            $table->enum('status', ['confirmado', 'cancelado', 'falta', 'executado', 'presente'])->default('confirmado');
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
            $table->foreignId('profissional_id')->constrained('users'); // Quem realizou a venda
            $table->foreignId('produto_id')->nullable()->constrained('produtos', 'id_produto');
            $table->foreignId('servico_id')->nullable()->constrained('servicos', 'id_servico');
            $table->integer('quantidade')->default(1);
            $table->decimal('valor_venda', 10, 2);
            $table->timestamps();
        });
        // Tabelas de relacionamento para Profissionais
        Schema::create('profissional_servico', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profissional_id')->constrained('users')->onDelete('cascade'); // O Profissional
            $table->foreignId('servico_id')->constrained('servicos', 'id_servico')->onDelete('cascade');
            $table->decimal('comissao_percentual', 5, 2)->default(50.00); // RN002
            $table->integer('duracao_customizada')->nullable(); // RN006 (em minutos)
            $table->timestamps();
        });
        // Tabela de Horários de Trabalho dos Profissionais
        Schema::create('horarios_trabalho', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profissional_id')->constrained('users')->onDelete('cascade');
            $table->integer('dia_semana'); // 0 = Domingo, 1 = Segunda...
            $table->time('hora_inicio')->default('08:00');
            $table->time('hora_fim')->default('18:00');
            $table->boolean('trabalha')->default(true); // Para marcar folgas fixas
            $table->timestamps();
            $table->time('almoco_inicio')->nullable()->default('12:00');
            $table->time('almoco_fim')->nullable()->default('13:00');
        });
        // Tabela de Bloqueios de Horários (Feriados, Folgas, Atestados)
        Schema::create('bloqueios_horarios', function (Blueprint $table) {
            $table->id('id_bloqueio');
            $table->foreignId('profissional_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->dateTime('data_hora_inicio');
            $table->dateTime('data_hora_fim');
            $table->string('motivo')->nullable(); 
            $table->timestamps();
        });
        Schema::create('pacotes', function (Blueprint $table) {
            $table->id('id_pacote');
            $table->string('nome'); // Ex: Pacote Verão Laser
            $table->foreignId('servico_id')->constrained('servicos', 'id_servico'); // A qual serviço pertence
            $table->integer('quantidade_sessoes'); // Ex: 5
            $table->decimal('valor_total', 8, 2); // Ex: 500.00
            $table->integer('validade_dias')->default(90); // Ex: expira em 90 dias
            $table->boolean('ativo')->default(true);
            $table->timestamps();
        });

        // Tabela 2: Os pacotes comprados pelos clientes
        Schema::create('cliente_pacotes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('pacote_id')->constrained('pacotes', 'id_pacote');
            $table->integer('sessoes_restantes'); // Começa com 5, vai caindo até 0
            $table->date('data_compra');
            $table->date('data_validade'); // data_compra + validade_dias do pacote
            $table->enum('status', ['ativo', 'finalizado', 'vencido'])->default('ativo');
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
        Schema::dropIfExists('profissional_servico');
        Schema::dropIfExists('horarios_trabalho');
        Schema::dropIfExists('bloqueios_horarios');
        Schema::dropIfExists('cliente_pacotes');
        Schema::dropIfExists('pacotes');
    }
};