<?php

namespace Tests\Feature;

use App\Models\Agendamento;
use App\Models\BloqueioHorario;
use App\Models\Pacote;
use App\Models\Produto;
use App\Models\Servico;
use App\Models\User;
use App\Services\ClientePacoteService;
use App\Services\FinanceiroService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccessControlTest extends TestCase
{
    use RefreshDatabase;

    public function test_cliente_nao_acessa_area_admin(): void
    {
        $cliente = User::factory()->create(['cargo' => User::ROLE_CLIENTE]);

        $this->actingAs($cliente)
            ->get('/admin/agenda')
            ->assertForbidden();
    }

    public function test_recepcionista_nao_acessa_area_exclusiva_do_gerente(): void
    {
        $recepcionista = User::factory()->create(['cargo' => User::ROLE_RECEPCIONISTA]);

        $this->actingAs($recepcionista)
            ->get('/admin/relatorios')
            ->assertForbidden();
    }

    public function test_profissional_nao_acessa_rota_gerencial_de_agendamento(): void
    {
        $profissional = User::factory()->create(['cargo' => User::ROLE_PROFISSIONAL]);

        $this->actingAs($profissional)
            ->get('/profissional/agendar-cliente')
            ->assertNotFound();
    }

    public function test_recepcionista_nao_consegue_criar_usuario_com_cargo_privilegiado(): void
    {
        $recepcionista = User::factory()->create(['cargo' => User::ROLE_RECEPCIONISTA]);

        $this->actingAs($recepcionista)->post('/admin/usuarios', [
            'name' => 'Tentativa Privilegiada',
            'email' => 'privilegio@example.com',
            'cpf' => '52998224725',
            'telefone' => '11999999999',
            'cargo' => User::ROLE_GERENTE,
            'password' => 'password123',
        ])->assertRedirect('/admin/usuarios');

        $this->assertDatabaseHas('users', [
            'email' => 'privilegio@example.com',
            'cargo' => User::ROLE_CLIENTE,
        ]);
    }

    public function test_visitante_nao_acessa_rotas_admin(): void
    {
        $this->get('/admin/usuarios')
            ->assertRedirect('/login');
    }

    public function test_registro_publico_ignora_tentativa_de_cargo_privilegiado(): void
    {
        $this->post('/register', [
            'name' => 'Cadastro Publico',
            'email' => 'cadastro-publico@example.com',
            'cpf' => '11144477735',
            'telefone' => '11988888888',
            'cargo' => User::ROLE_GERENTE,
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertRedirect('/dashboard');

        $this->assertDatabaseHas('users', [
            'email' => 'cadastro-publico@example.com',
            'cargo' => User::ROLE_CLIENTE,
        ]);
    }

    public function test_cliente_nao_cancela_agendamento_de_outro_cliente(): void
    {
        $clienteAtacante = User::factory()->create(['cargo' => User::ROLE_CLIENTE]);
        $clienteDono = User::factory()->create(['cargo' => User::ROLE_CLIENTE]);
        $profissional = User::factory()->create(['cargo' => User::ROLE_PROFISSIONAL]);
        $agendamento = $this->criarAgendamento($clienteDono, $profissional);

        $this->actingAs($clienteAtacante)
            ->post("/agendamento/{$agendamento->id_agendamento}/cancelar")
            ->assertForbidden();

        $this->assertDatabaseHas('agendamentos', [
            'id_agendamento' => $agendamento->id_agendamento,
            'status' => 'confirmado',
        ]);
    }

    public function test_profissional_nao_finaliza_agendamento_de_outro_profissional(): void
    {
        $cliente = User::factory()->create(['cargo' => User::ROLE_CLIENTE]);
        $profissionalDono = User::factory()->create(['cargo' => User::ROLE_PROFISSIONAL]);
        $profissionalAtacante = User::factory()->create(['cargo' => User::ROLE_PROFISSIONAL]);
        $agendamento = $this->criarAgendamento($cliente, $profissionalDono);

        $this->actingAs($profissionalAtacante)
            ->post("/profissional/agendamento/{$agendamento->id_agendamento}/executado")
            ->assertForbidden();

        $this->assertDatabaseHas('agendamentos', [
            'id_agendamento' => $agendamento->id_agendamento,
            'status' => 'confirmado',
        ]);
    }

    public function test_recepcionista_nao_altera_status_de_usuario(): void
    {
        $recepcionista = User::factory()->create(['cargo' => User::ROLE_RECEPCIONISTA]);
        $cliente = User::factory()->create(['cargo' => User::ROLE_CLIENTE, 'status' => 'ativo']);

        $this->actingAs($recepcionista)
            ->patch("/admin/usuarios/{$cliente->id}/status", ['status' => 'bloqueado'])
            ->assertForbidden();

        $this->assertDatabaseHas('users', [
            'id' => $cliente->id,
            'status' => 'ativo',
        ]);
    }

    public function test_profissional_nao_remove_bloqueio_de_outro_profissional(): void
    {
        $profissionalDono = User::factory()->create(['cargo' => User::ROLE_PROFISSIONAL]);
        $profissionalAtacante = User::factory()->create(['cargo' => User::ROLE_PROFISSIONAL]);
        $bloqueio = BloqueioHorario::create([
            'profissional_id' => $profissionalDono->id,
            'data_hora_inicio' => now()->addDay()->startOfDay(),
            'data_hora_fim' => now()->addDay()->endOfDay(),
            'motivo' => 'Indisponibilidade informada pelo profissional',
        ]);

        $this->actingAs($profissionalAtacante)
            ->delete("/profissional/configuracoes/bloqueios/{$bloqueio->id_bloqueio}")
            ->assertForbidden();

        $this->assertDatabaseHas('bloqueios_horarios', [
            'id_bloqueio' => $bloqueio->id_bloqueio,
        ]);
    }

    public function test_respostas_tem_headers_basicos_de_seguranca(): void
    {
        $this->get('/')
            ->assertHeader('X-Frame-Options', 'SAMEORIGIN')
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin')
            ->assertHeader('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');
    }

    public function test_cliente_compra_pacote_para_si_mesmo(): void
    {
        $cliente = User::factory()->create(['cargo' => User::ROLE_CLIENTE]);
        $servico = Servico::create([
            'nome' => 'Pacote Teste',
            'descricao' => 'Servico usado no pacote.',
            'preco' => 100,
            'duracao' => 60,
        ]);
        $pacote = Pacote::create([
            'nome' => 'Combo Cliente',
            'servico_id' => $servico->id_servico,
            'quantidade_sessoes' => 5,
            'valor_total' => 400,
            'validade_dias' => 90,
            'ativo' => true,
        ]);

        $this->actingAs($cliente)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Comprar pacotes');

        $this->actingAs($cliente)
            ->get(route('cliente.pacotes.index'))
            ->assertOk()
            ->assertSee('Pacotes')
            ->assertSee('Comprar para mim')
            ->assertSee($pacote->nome);

        $this->actingAs($cliente)
            ->post(route('cliente.pacotes.comprar'), ['pacote_id' => $pacote->id_pacote])
            ->assertRedirect(route('cliente.pacotes.index'));

        $this->assertDatabaseHas('cliente_pacotes', [
            'cliente_id' => $cliente->id,
            'pacote_id' => $pacote->id_pacote,
            'vendedor_id' => null,
            'sessoes_restantes' => 5,
            'valor_comissao' => 0,
            'comissao_paga_percentual' => 0,
            'status' => 'ativo',
        ]);
    }

    public function test_venda_de_pacote_por_recepcionista_gera_comissao_de_servico(): void
    {
        $recepcionista = User::factory()->create(['cargo' => User::ROLE_RECEPCIONISTA]);
        $cliente = User::factory()->create(['cargo' => User::ROLE_CLIENTE]);
        $servico = Servico::create([
            'nome' => 'Massagem',
            'descricao' => 'Servico usado no pacote.',
            'preco' => 100,
            'duracao' => 60,
        ]);
        $pacote = Pacote::create([
            'nome' => 'Combo Recepcao',
            'servico_id' => $servico->id_servico,
            'quantidade_sessoes' => 4,
            'valor_total' => 300,
            'validade_dias' => 90,
            'ativo' => true,
        ]);

        $this->actingAs($recepcionista)
            ->post(route('admin.venda.store'), [
                'cliente_id' => $cliente->id,
                'pacote_id' => $pacote->id_pacote,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('cliente_pacotes', [
            'cliente_id' => $cliente->id,
            'pacote_id' => $pacote->id_pacote,
            'vendedor_id' => $recepcionista->id,
            'valor_comissao' => 150,
            'comissao_paga_percentual' => FinanceiroService::COMISSAO_SERVICO_PERCENTUAL,
        ]);

        $comissoes = app(FinanceiroService::class)->resumoComissoesPeriodo(now()->toDateString(), now()->toDateString());
        $comissaoRecepcionista = $comissoes->firstWhere('id', $recepcionista->id);

        $this->assertNotNull($comissaoRecepcionista);
        $this->assertSame(1, $comissaoRecepcionista->total_servicos);
        $this->assertSame(150.0, $comissaoRecepcionista->comissao_servicos);
        $this->assertSame(150.0, $comissaoRecepcionista->comissao_a_pagar);
    }

    public function test_cliente_compra_produto_para_si_mesmo_sem_gerar_comissao(): void
    {
        $cliente = User::factory()->create(['cargo' => User::ROLE_CLIENTE]);
        $produto = Produto::create([
            'nome' => 'Finalizador',
            'descricao' => 'Produto para teste de compra.',
            'tipo' => 'cosmeticos',
            'valor_unitario' => 50,
            'quantidade_estoque' => 3,
        ]);

        $this->actingAs($cliente)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Comprar produtos');

        $this->actingAs($cliente)
            ->get(route('cliente.produtos.index'))
            ->assertOk()
            ->assertSee('Comprar Produtos')
            ->assertSee('Comprar para mim')
            ->assertSee($produto->nome);

        $this->actingAs($cliente)
            ->post(route('cliente.produtos.comprar'), [
                'produto_id' => $produto->id_produto,
                'quantidade' => 2,
            ])
            ->assertRedirect(route('cliente.produtos.index'));

        $this->assertDatabaseHas('vendas', [
            'profissional_id' => $cliente->id,
            'produto_id' => $produto->id_produto,
            'quantidade' => 2,
            'valor_venda' => 100,
            'valor_comissao' => 0,
            'comissao_paga_percentual' => 0,
        ]);

        $this->assertDatabaseHas('produtos', [
            'id_produto' => $produto->id_produto,
            'quantidade_estoque' => 1,
        ]);
    }

    public function test_venda_de_produto_por_recepcionista_gera_comissao_de_produto(): void
    {
        $recepcionista = User::factory()->create(['cargo' => User::ROLE_RECEPCIONISTA]);
        $produto = Produto::create([
            'nome' => 'Oleo capilar',
            'descricao' => 'Produto para teste de venda.',
            'tipo' => 'cabelo',
            'valor_unitario' => 80,
            'quantidade_estoque' => 5,
        ]);

        $this->actingAs($recepcionista)
            ->post(route('admin.vendas.produtos.store'), [
                'produto_id' => $produto->id_produto,
                'quantidade' => 1,
            ])
            ->assertRedirect(route('admin.vendas.produtos.create'));

        $this->assertDatabaseHas('vendas', [
            'profissional_id' => $recepcionista->id,
            'produto_id' => $produto->id_produto,
            'valor_venda' => 80,
            'valor_comissao' => 8,
            'comissao_paga_percentual' => FinanceiroService::COMISSAO_PRODUTO_PERCENTUAL,
        ]);

        $comissoes = app(FinanceiroService::class)->resumoComissoesPeriodo(now()->toDateString(), now()->toDateString());
        $comissaoRecepcionista = $comissoes->firstWhere('id', $recepcionista->id);

        $this->assertNotNull($comissaoRecepcionista);
        $this->assertSame(1, $comissaoRecepcionista->total_vendas_produtos);
        $this->assertSame(8.0, $comissaoRecepcionista->comissao_produtos);
    }

    public function test_cliente_nao_usa_rota_admin_para_vender_pacote_a_outro_cliente(): void
    {
        $cliente = User::factory()->create(['cargo' => User::ROLE_CLIENTE]);

        $this->actingAs($cliente)
            ->get(route('admin.venda.create'))
            ->assertForbidden();
    }

    public function test_pacote_pode_ser_usado_em_qualquer_servico_vinculado(): void
    {
        $cliente = User::factory()->create(['cargo' => User::ROLE_CLIENTE]);
        $servicoPrincipal = Servico::create([
            'nome' => 'Limpeza de pele',
            'descricao' => 'Servico principal do pacote.',
            'preco' => 120,
            'duracao' => 60,
        ]);
        $servicoExtra = Servico::create([
            'nome' => 'Design de sobrancelhas',
            'descricao' => 'Servico extra do mesmo pacote.',
            'preco' => 45,
            'duracao' => 30,
        ]);
        $pacote = Pacote::create([
            'nome' => 'Pele completa',
            'servico_id' => $servicoPrincipal->id_servico,
            'quantidade_sessoes' => 3,
            'valor_total' => 300,
            'validade_dias' => 90,
            'ativo' => true,
        ]);
        $pacote->servicos()->sync([$servicoPrincipal->id_servico, $servicoExtra->id_servico]);

        $clientePacote = app(ClientePacoteService::class)->venderPacote($cliente->id, $pacote->id_pacote);

        app(ClientePacoteService::class)->consumirSessao($clientePacote->id, $cliente->id, $servicoExtra->id_servico);

        $this->assertDatabaseHas('cliente_pacotes', [
            'id' => $clientePacote->id,
            'sessoes_restantes' => 2,
            'status' => 'ativo',
        ]);
    }

    private function criarAgendamento(User $cliente, User $profissional): Agendamento
    {
        $servico = Servico::create([
            'nome' => 'Teste Seguro',
            'descricao' => 'Servico usado apenas nos testes de seguranca.',
            'preco' => 100,
            'duracao' => 60,
        ]);

        return Agendamento::create([
            'cliente_id' => $cliente->id,
            'profissional_id' => $profissional->id,
            'servico_id' => $servico->id_servico,
            'data_hora_inicio' => now()->addDays(2),
            'data_hora_fim' => now()->addDays(2)->addHour(),
            'valor_total' => 100,
            'valor_base' => 100,
            'status' => 'confirmado',
        ]);
    }
}
