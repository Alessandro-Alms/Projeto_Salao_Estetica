<?php

namespace Database\Seeders;

use App\Models\Agendamento;
use App\Models\Atendimento;
use App\Models\Avaliacao;
use App\Models\ClientePacote;
use App\Models\Produto;
use App\Models\Servico;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->limparBanco();

        $usuarios = $this->criarUsuarios();
        $servicos = $this->criarServicos();
        $produtos = $this->criarProdutos();

        $this->criarHorariosDoProfissional($usuarios['antony']->id);
        $this->vincularServicosAoProfissional($usuarios['antony']->id, $servicos);
        $pacotes = $this->criarPacotes($servicos);
        $this->criarBloqueiosCoerentes($usuarios['antony']->id);
        $this->criarAgendaHistoricoEVendas($usuarios, $servicos, $produtos, $pacotes);
    }

    private function limparBanco(): void
    {
        Schema::disableForeignKeyConstraints();

        foreach ([
            'agendamento_servico',
            'avaliacoes',
            'cliente_pacotes',
            'pacote_servico',
            'pacotes',
            'bloqueios_horarios',
            'horarios_trabalho',
            'profissional_servico',
            'vendas',
            'atendimentos',
            'agendamentos',
            'produtos',
            'servicos',
            'users',
        ] as $table) {
            DB::table($table)->truncate();
        }

        Schema::enableForeignKeyConstraints();
    }

    private function criarUsuarios(): array
    {
        $usuarios = [
            'alessandro' => [
                'name' => 'alessandro lima',
                'email' => 'alessandromaia071@gmail.com',
                'password' => 'Alms170906#',
                'cpf' => '09885376305',
                'telefone' => '85991989160',
                'cargo' => 'gerente',
                'endereco' => 'Fortaleza - CE',
                'd_nasc' => '2006-09-17',
            ],
            'geovanna' => [
                'name' => 'geovanna vieira',
                'email' => 'geovanna.vieira@gmail.com',
                'password' => 'senha123',
                'cpf' => '10000000001',
                'telefone' => '85991000001',
                'cargo' => 'gerente',
                'endereco' => 'Fortaleza - CE',
                'd_nasc' => '1998-04-12',
            ],
            'eliene' => [
                'name' => 'eliene de sousa',
                'email' => 'eliene.sousa@gmail.com',
                'password' => 'senha123',
                'cpf' => '10000000002',
                'telefone' => '85991000002',
                'cargo' => 'recepcionista',
                'endereco' => 'Fortaleza - CE',
                'd_nasc' => '1994-08-20',
            ],
            'antony' => [
                'name' => 'antony almeida',
                'email' => 'antony.almeida@gmail.com',
                'password' => 'senha123',
                'cpf' => '10000000003',
                'telefone' => '85991000003',
                'cargo' => 'profissional',
                'endereco' => 'Fortaleza - CE',
                'd_nasc' => '1996-02-15',
                'obs' => 'Especialista em cabelo, sobrancelhas e estetica facial.',
            ],
            'sophia' => [
                'name' => 'sophia ribeiro',
                'email' => 'sophia.ribeiro@gmail.com',
                'password' => 'senha123',
                'cpf' => '10000000004',
                'telefone' => '85991000004',
                'cargo' => 'cliente',
                'endereco' => 'Fortaleza - CE',
                'd_nasc' => '2001-11-05',
            ],
            'beatriz' => [
                'name' => 'beatriz santos',
                'email' => 'beatriz.santos@example.com',
                'password' => 'senha123',
                'cpf' => '10000000005',
                'telefone' => '85991000005',
                'cargo' => 'cliente',
                'endereco' => 'Fortaleza - CE',
                'd_nasc' => '1999-07-18',
            ],
            'juliana' => [
                'name' => 'juliana pereira',
                'email' => 'juliana.pereira@example.com',
                'password' => 'senha123',
                'cpf' => '10000000006',
                'telefone' => '85991000006',
                'cargo' => 'cliente',
                'endereco' => 'Caucaia - CE',
                'd_nasc' => '1992-01-30',
            ],
            'fernanda' => [
                'name' => 'fernanda oliveira',
                'email' => 'fernanda.oliveira@example.com',
                'password' => 'senha123',
                'cpf' => '10000000007',
                'telefone' => '85991000007',
                'cargo' => 'cliente',
                'endereco' => 'Maracanau - CE',
                'd_nasc' => '1988-10-03',
                'faltas' => 1,
            ],
            'mariana' => [
                'name' => 'mariana costa',
                'email' => 'mariana.costa@example.com',
                'password' => 'senha123',
                'cpf' => '10000000008',
                'telefone' => '85991000008',
                'cargo' => 'cliente',
                'endereco' => 'Fortaleza - CE',
                'd_nasc' => '1995-05-24',
            ],
        ];

        $resultado = [];

        foreach ($usuarios as $apelido => $dados) {
            $senha = $dados['password'];
            unset($dados['password']);

            $resultado[$apelido] = User::create([
                ...$dados,
                'password' => Hash::make($senha),
                'status' => 'ativo',
                'faltas' => $dados['faltas'] ?? 0,
                'contador_fidelidade' => 0,
            ]);
        }

        return $resultado;
    }

    private function criarServicos(): array
    {
        return [
            'corte' => Servico::create([
                'nome' => 'Corte feminino',
                'descricao' => 'Corte, alinhamento do visual e finalizacao.',
                'preco' => 80.00,
                'duracao' => 60,
            ]),
            'escova' => Servico::create([
                'nome' => 'Escova modelada',
                'descricao' => 'Escova com acabamento modelado.',
                'preco' => 60.00,
                'duracao' => 45,
            ]),
            'sobrancelha' => Servico::create([
                'nome' => 'Design de sobrancelhas',
                'descricao' => 'Design personalizado com acabamento natural.',
                'preco' => 45.00,
                'duracao' => 30,
            ]),
            'limpeza' => Servico::create([
                'nome' => 'Limpeza de pele',
                'descricao' => 'Higienizacao facial completa com mascara calmante.',
                'preco' => 120.00,
                'duracao' => 90,
            ]),
            'hidratacao' => Servico::create([
                'nome' => 'Hidratacao capilar',
                'descricao' => 'Tratamento nutritivo para recuperacao dos fios.',
                'preco' => 110.00,
                'duracao' => 75,
            ]),
        ];
    }

    private function criarProdutos(): array
    {
        return [
            'shampoo' => Produto::create([
                'nome' => 'Shampoo hidratacao intensa',
                'descricao' => 'Shampoo profissional para manutencao em casa.',
                'tipo' => 'cabelo',
                'valor_unitario' => 45.00,
                'quantidade_estoque' => 18,
            ]),
            'mascara' => Produto::create([
                'nome' => 'Mascara nutritiva',
                'descricao' => 'Mascara de tratamento capilar.',
                'tipo' => 'cosmeticos',
                'valor_unitario' => 65.00,
                'quantidade_estoque' => 12,
            ]),
            'kit' => Produto::create([
                'nome' => 'Kit pos-atendimento',
                'descricao' => 'Kit de cuidados para cabelos e pele.',
                'tipo' => 'kits',
                'valor_unitario' => 95.00,
                'quantidade_estoque' => 8,
            ]),
            'presilha' => Produto::create([
                'nome' => 'Presilha premium',
                'descricao' => 'Acessorio para finalizacao de penteados.',
                'tipo' => 'acessorios',
                'valor_unitario' => 18.00,
                'quantidade_estoque' => 30,
            ]),
        ];
    }

    private function criarHorariosDoProfissional(int $profissionalId): void
    {
        foreach ([0, 1, 2, 3, 4, 5, 6] as $dia) {
            DB::table('horarios_trabalho')->insert([
                'profissional_id' => $profissionalId,
                'dia_semana' => $dia,
                'hora_inicio' => '08:00',
                'hora_fim' => '18:00',
                'almoco_inicio' => '11:00',
                'almoco_fim' => '13:00',
                'trabalha' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    private function vincularServicosAoProfissional(int $profissionalId, array $servicos): void
    {
        foreach ($servicos as $chave => $servico) {
            DB::table('profissional_servico')->insert([
                'profissional_id' => $profissionalId,
                'servico_id' => $servico->id_servico,
                'comissao_percentual' => 50.00,
                'duracao_customizada' => $chave === 'hidratacao' ? 80 : null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    private function criarPacotes(array $servicos): array
    {
        $ids = [];

        foreach ([
            'escovas' => [
                'nome' => 'Pacote 5 escovas modeladas',
                'servico_id' => $servicos['escova']->id_servico,
                'servicos_ids' => [$servicos['escova']->id_servico, $servicos['hidratacao']->id_servico],
                'quantidade_sessoes' => 5,
                'valor_total' => 260.00,
                'validade_dias' => 90,
            ],
            'pele' => [
                'nome' => 'Pacote pele iluminada',
                'servico_id' => $servicos['limpeza']->id_servico,
                'servicos_ids' => [$servicos['limpeza']->id_servico, $servicos['sobrancelha']->id_servico],
                'quantidade_sessoes' => 3,
                'valor_total' => 300.00,
                'validade_dias' => 120,
            ],
        ] as $chave => $pacote) {
            $ids[$chave] = DB::table('pacotes')->insertGetId([
                ...collect($pacote)->except('servicos_ids')->all(),
                'ativo' => true,
                'created_at' => now()->subDays(20),
                'updated_at' => now()->subDays(20),
            ]);

            foreach ($pacote['servicos_ids'] as $servicoId) {
                DB::table('pacote_servico')->insert([
                    'pacote_id' => $ids[$chave],
                    'servico_id' => $servicoId,
                    'created_at' => now()->subDays(20),
                    'updated_at' => now()->subDays(20),
                ]);
            }
        }

        return $ids;
    }

    private function criarBloqueiosCoerentes(int $profissionalId): void
    {
        DB::table('bloqueios_horarios')->insert([
            [
                'profissional_id' => $profissionalId,
                'data_hora_inicio' => Carbon::create(2026, 5, 19, 14, 0),
                'data_hora_fim' => Carbon::create(2026, 5, 19, 16, 0),
                'motivo' => 'Treinamento profissional',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'profissional_id' => null,
                'data_hora_inicio' => Carbon::create(2026, 5, 29, 0, 0),
                'data_hora_fim' => Carbon::create(2026, 5, 29, 23, 59),
                'motivo' => 'Feriado municipal',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    private function criarAgendaHistoricoEVendas(array $usuarios, array $servicos, array $produtos, array $pacotes): void
    {
        $this->registrarCompraPacote($usuarios['sophia']->id, $pacotes['escovas'], Carbon::create(2026, 4, 15, 10, 30), 4);

        $agenda = [
            [
                'cliente' => 'sophia',
                'servico' => 'corte',
                'inicio' => Carbon::create(2026, 4, 14, 9, 0),
                'status' => 'executado',
                'obs' => 'Corte finalizado com escova leve.',
                'nota' => 5,
                'comentario' => 'Atendimento excelente e pontual.',
            ],
            [
                'cliente' => 'beatriz',
                'servico' => 'limpeza',
                'inicio' => Carbon::create(2026, 4, 19, 10, 0),
                'status' => 'executado',
                'obs' => 'Atendimento de domingo com acrescimo aplicado.',
                'nota' => 5,
                'comentario' => 'Adorei o cuidado e o resultado.',
            ],
            [
                'cliente' => 'juliana',
                'servico' => 'escova',
                'inicio' => Carbon::create(2026, 4, 25, 15, 0),
                'status' => 'executado',
                'obs' => 'Escova em sabado com acrescimo de fim de semana.',
                'nota' => 4,
                'comentario' => 'Ficou muito bonito, atendimento rapido.',
            ],
            [
                'cliente' => 'sophia',
                'servico' => 'sobrancelha',
                'inicio' => Carbon::create(2026, 5, 2, 10, 0),
                'status' => 'executado',
                'obs' => 'Design de sobrancelhas finalizado.',
                'nota' => 5,
                'comentario' => 'Ficou perfeito, voltarei mais vezes.',
            ],
            [
                'cliente' => 'fernanda',
                'servico' => 'hidratacao',
                'inicio' => Carbon::create(2026, 5, 6, 13, 30),
                'status' => 'presente',
                'obs' => 'Cliente fez check-in e aguarda conclusao do atendimento.',
            ],
            [
                'cliente' => 'mariana',
                'servico' => 'corte',
                'inicio' => Carbon::create(2026, 5, 8, 16, 0),
                'status' => 'cancelado',
                'obs' => 'Cancelamento registrado fora da janela ideal.',
                'multa_valor' => 4.00,
            ],
            [
                'cliente' => 'sophia',
                'servico' => 'limpeza',
                'inicio' => Carbon::create(2026, 5, 10, 9, 0),
                'status' => 'confirmado',
                'obs' => 'Agendamento futuro em domingo com acrescimo visivel.',
            ],
            [
                'cliente' => 'beatriz',
                'servico' => 'escova',
                'inicio' => Carbon::create(2026, 5, 12, 16, 30),
                'status' => 'confirmado',
                'obs' => 'Agendamento futuro em horario normal.',
            ],
        ];

        foreach ($agenda as $item) {
            $agendamento = $this->criarAgendamento(
                $usuarios[$item['cliente']],
                $usuarios['antony'],
                $servicos[$item['servico']],
                $item['inicio'],
                $item['status'],
                $item['obs'],
                $item['multa_valor'] ?? 0
            );

            if ($item['status'] === 'executado') {
                $this->registrarAtendimento($agendamento, $item['nota'], $item['obs']);
                $this->avaliar(
                    $agendamento,
                    $item['nota'],
                    $item['comentario'],
                    $agendamento->data_hora_fim->copy()->addHours(2)
                );
            }
        }

        $this->registrarVenda($usuarios['antony']->id, $produtos['shampoo']->id_produto, 1, Carbon::create(2026, 4, 14, 10, 15));
        $this->registrarVenda($usuarios['antony']->id, $produtos['mascara']->id_produto, 1, Carbon::create(2026, 4, 25, 16, 0));
        $this->registrarVenda($usuarios['eliene']->id, $produtos['kit']->id_produto, 1, Carbon::create(2026, 5, 2, 11, 0));
        $this->registrarVenda($usuarios['eliene']->id, $produtos['presilha']->id_produto, 2, Carbon::create(2026, 5, 6, 14, 30));

        $this->atualizarResumoDosClientes($usuarios);
    }

    private function criarAgendamento(
        User $cliente,
        User $profissional,
        Servico $servico,
        Carbon $inicio,
        string $status,
        ?string $obs,
        float $multa = 0
    ): Agendamento {
        $fim = $inicio->copy()->addMinutes($servico->duracao);
        $valorBase = (float) $servico->preco;
        $percentual = 0;
        $motivos = [];

        if ($inicio->isWeekend()) {
            $percentual += 25;
            $motivos[] = 'Fim de semana +25%';
        }

        if ($inicio->format('H:i') < '13:00' && $fim->format('H:i') > '11:00') {
            $percentual += 50;
            $motivos[] = 'Horario de almoco +50%';
        }

        $acrescimo = round($valorBase * ($percentual / 100), 2);
        $baseComissao = round($valorBase + $acrescimo, 2);
        $valorTotal = $baseComissao;
        $executado = $status === 'executado';
        $criadoEm = $inicio->copy()->subDays(7);
        $atualizadoEm = in_array($status, ['executado', 'presente'], true)
            ? $fim->copy()->addMinutes(20)
            : $criadoEm;

        $agendamento = Agendamento::create([
            'cliente_id' => $cliente->id,
            'profissional_id' => $profissional->id,
            'servico_id' => $servico->id_servico,
            'data_hora_inicio' => $inicio,
            'data_hora_fim' => $fim,
            'status' => $status,
            'valor_base' => $valorBase,
            'acrescimo_especial' => $acrescimo,
            'desconto_servicos' => 0,
            'motivo_desconto' => null,
            'base_comissao' => $baseComissao,
            'motivo_acrescimo' => $motivos ? implode(' + ', $motivos) : null,
            'valor_total' => $valorTotal,
            'valor_comissao' => $executado ? round($baseComissao * 0.5, 2) : null,
            'comissao_paga_percentual' => $executado ? 50.00 : null,
            'multa_valor' => $multa,
            'obs' => $obs,
            'created_at' => $criadoEm,
            'updated_at' => $atualizadoEm,
        ]);

        $agendamento->servicos()->attach($servico->id_servico, [
            'duracao' => $servico->duracao,
            'preco' => $valorTotal,
            'created_at' => $criadoEm,
            'updated_at' => $criadoEm,
        ]);

        return $agendamento;
    }

    private function registrarAtendimento(Agendamento $agendamento, int $avaliacao, ?string $descricao): void
    {
        Atendimento::create([
            'cliente_id' => $agendamento->cliente_id,
            'profissional_id' => $agendamento->profissional_id,
            'agendamento_id' => $agendamento->id_agendamento,
            'valor_total' => $agendamento->valor_total,
            'avaliacao' => $avaliacao,
            'descricao_detalhada' => $descricao,
            'created_at' => $agendamento->data_hora_fim->copy()->addMinutes(20),
            'updated_at' => $agendamento->data_hora_fim->copy()->addMinutes(20),
        ]);
    }

    private function avaliar(Agendamento $agendamento, int $nota, string $comentario, Carbon $data): void
    {
        Avaliacao::create([
            'agendamento_id' => $agendamento->id_agendamento,
            'cliente_id' => $agendamento->cliente_id,
            'profissional_id' => $agendamento->profissional_id,
            'nota' => $nota,
            'comentario' => $comentario,
            'created_at' => $data,
            'updated_at' => $data,
        ]);
    }

    private function registrarVenda(int $vendedorId, int $produtoId, int $quantidade, Carbon $data): void
    {
        $produto = Produto::findOrFail($produtoId);
        $produto->decrement('quantidade_estoque', $quantidade);

        DB::table('vendas')->insert([
            'profissional_id' => $vendedorId,
            'produto_id' => $produtoId,
            'servico_id' => null,
            'quantidade' => $quantidade,
            'valor_venda' => $produto->valor_unitario * $quantidade,
            'created_at' => $data,
            'updated_at' => $data,
        ]);
    }

    private function registrarCompraPacote(int $clienteId, int $pacoteId, Carbon $dataCompra, int $sessoesRestantes): void
    {
        $pacote = DB::table('pacotes')->where('id_pacote', $pacoteId)->first();

        ClientePacote::create([
            'cliente_id' => $clienteId,
            'pacote_id' => $pacoteId,
            'sessoes_restantes' => $sessoesRestantes,
            'data_compra' => $dataCompra->toDateString(),
            'data_validade' => $dataCompra->copy()->addDays($pacote->validade_dias)->toDateString(),
            'status' => 'ativo',
            'created_at' => $dataCompra,
            'updated_at' => $dataCompra,
        ]);
    }

    private function atualizarResumoDosClientes(array $usuarios): void
    {
        foreach (['sophia', 'beatriz', 'juliana'] as $apelido) {
            $ultimoExecutado = Agendamento::where('cliente_id', $usuarios[$apelido]->id)
                ->where('status', 'executado')
                ->latest('data_hora_fim')
                ->first();

            if (! $ultimoExecutado) {
                continue;
            }

            $usuarios[$apelido]->update([
                'ultima_visita' => $ultimoExecutado->data_hora_fim,
                'contador_fidelidade' => Agendamento::where('cliente_id', $usuarios[$apelido]->id)
                    ->where('status', 'executado')
                    ->count(),
            ]);
        }
    }
}
