<?php

namespace Tests\Feature;

use App\Models\Agendamento;
use App\Models\BloqueioHorario;
use App\Models\Servico;
use App\Models\User;
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
            'cpf' => '12345678901',
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
            'cpf' => '98765432109',
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
