<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Agendamento;
use App\Models\BloqueioHorario;
use App\Models\ClientePacote;
use App\Models\HorarioTrabalho;
use App\Models\Servico;
use App\Models\User;
use App\Services\FinanceiroService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class UserController extends Controller
{
    private const MOTIVO_INDISPONIBILIDADE_PROFISSIONAL = 'Indisponibilidade informada pelo profissional';
    private const PREFIXO_MOTIVO_FERIADO_PROFISSIONAL = 'Indisponibilidade em feriado: ';

    public function dashboard(Request $request): View
    {
        $user = $request->user();
        $mediaAvaliacao = null;
        $totalAvaliacoes = 0;
        $comentariosAvaliacao = collect();
        $bloqueiosProfissionalFuturos = collect();
        $agendamentoProximo = null;
        $avaliacoesPendentes = 0;
        $pacotesVencendo = collect();
        $mensagemProximaVisita = null;
        $contatoMensagens = collect();
        $contatoMensagensNaoLidas = 0;
        $contatoMensagensLidasCliente = collect();

        if ($user && $user->isGerente() && DB::getSchemaBuilder()->hasTable('contato_mensagens')) {
            $contatoMensagens = DB::table('contato_mensagens')
                ->orderByDesc('created_at')
                ->limit(5)
                ->get();

            $contatoMensagensNaoLidas = DB::table('contato_mensagens')
                ->whereNull('lida_at')
                ->count();
        }

        if ($user && $user->isProfissional()) {
            $mediaAvaliacao = DB::table('avaliacoes')
                ->where('profissional_id', $user->id)
                ->avg('nota');

            $mediaAvaliacao = $mediaAvaliacao ? round($mediaAvaliacao, 1) : null;

            $totalAvaliacoes = DB::table('avaliacoes')
                ->where('profissional_id', $user->id)
                ->count();

            $comentariosAvaliacao = DB::table('avaliacoes')
                ->join('users as clientes', 'avaliacoes.cliente_id', '=', 'clientes.id')
                ->where('avaliacoes.profissional_id', $user->id)
                ->whereNotNull('avaliacoes.comentario')
                ->where('avaliacoes.comentario', '!=', '')
                ->orderByDesc('avaliacoes.created_at')
                ->limit(5)
                ->select(
                    'avaliacoes.nota',
                    'avaliacoes.comentario',
                    'avaliacoes.created_at',
                    'clientes.name as cliente_nome'
                )
                ->get();

            $bloqueiosProfissionalFuturos = BloqueioHorario::where('profissional_id', $user->id)
                ->where('data_hora_fim', '>=', now())
                ->orderBy('data_hora_inicio')
                ->limit(5)
                ->get();
        }

        if ($user && $user->isCliente()) {
            if (
                DB::getSchemaBuilder()->hasTable('contato_mensagens')
                && DB::getSchemaBuilder()->hasColumn('contato_mensagens', 'cliente_notificado_at')
            ) {
                $contatoMensagensLidasCliente = DB::table('contato_mensagens')
                    ->where('email', $user->email)
                    ->whereNotNull('lida_at')
                    ->whereNull('cliente_notificado_at')
                    ->orderByDesc('lida_at')
                    ->limit(3)
                    ->get();

                if ($contatoMensagensLidasCliente->isNotEmpty()) {
                    DB::table('contato_mensagens')
                        ->whereIn('id', $contatoMensagensLidasCliente->pluck('id'))
                        ->update([
                            'cliente_notificado_at' => now(),
                            'updated_at' => now(),
                        ]);
                }
            }

            $agendamentoProximo = Agendamento::where('cliente_id', $user->id)
                ->whereIn('status', ['confirmado', 'presente'])
                ->where('data_hora_inicio', '>', now())
                ->orderBy('data_hora_inicio')
                ->first();

            $avaliacoesPendentes = Agendamento::where('cliente_id', $user->id)
                ->where('status', 'executado')
                ->doesntHave('avaliacao')
                ->count();

            $pacotesVencendo = ClientePacote::where('cliente_id', $user->id)
                ->where('status', 'ativo')
                ->whereDate('data_validade', '>=', now())
                ->whereDate('data_validade', '<=', now()->addDays(7))
                ->with('pacote')
                ->orderBy('data_validade')
                ->get();

            if ($agendamentoProximo) {
                $inicio = $agendamentoProximo->data_hora_inicio;
                $diffMinutosTotal = now()->diffInMinutes($inicio, false);
                $diffHorasTotal = intdiv(max($diffMinutosTotal, 0), 60);
                $diffDias = intdiv($diffHorasTotal, 24);
                $diffHoras = $diffHorasTotal - ($diffDias * 24);

                if ($diffDias > 0 && $diffHoras > 0) {
                    $tempo = $diffDias . ' dias e ' . $diffHoras . ' horas';
                } elseif ($diffDias > 0) {
                    $tempo = $diffDias . ' dias';
                } else {
                    $tempo = $diffHoras . ' horas';
                }

                $mensagemProximaVisita = "Faltam aproximadamente {$tempo} para sua proxima visita.";
            }
        }

        return view('dashboard', compact(
            'user',
            'mediaAvaliacao',
            'totalAvaliacoes',
            'comentariosAvaliacao',
            'bloqueiosProfissionalFuturos',
            'agendamentoProximo',
            'avaliacoesPendentes',
            'pacotesVencendo',
            'mensagemProximaVisita',
            'contatoMensagens',
            'contatoMensagensNaoLidas',
            'contatoMensagensLidasCliente'
        ));
    }

    public function index(Request $request)
    {
        $consulta = User::query();
        $usuarioLogado = auth()->user();
        $quantidadePorPagina = 10;

        if ($request->filled('cargo') && $usuarioLogado->isGerente()) {
            $consulta->where('cargo', $request->cargo);
        }

        if ($request->has('search')) {
            $searchTerm = $request->input('search');
            $consulta->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
                    ->orWhere('email', 'like', '%' . $searchTerm . '%')
                    ->orWhere('cpf', 'like', '%' . $searchTerm . '%');
            });
        }

        if ($usuarioLogado->isGerente()) {
            $consulta->whereIn('cargo', ['gerente', 'recepcionista', 'profissional', 'cliente']);
        } elseif ($usuarioLogado->isRecepcionista()) {
            $consulta->where('cargo', 'cliente');
        } else {
            abort(403, 'Acesso negado.');
        }

        $usuarios = $consulta->orderBy('name', 'asc')->paginate($quantidadePorPagina);

        return view('admin.usuarios.index', compact('usuarios'));
    }

    public function create()
    {
        $cargosPermitidos = auth()->user()->isGerente()
            ? User::ROLES
            : [User::ROLE_CLIENTE];

        return view('admin.usuarios.criar', compact('cargosPermitidos'));
    }

    public function store(Request $request)
    {
        $this->normalizarDocumentoContato($request);

        $dados = $request->validate($this->regrasUsuario(), $this->mensagensUsuario($request));

        User::create([
            'name' => $dados['name'],
            'email' => $dados['email'],
            'cpf' => $dados['cpf'],
            'telefone' => $dados['telefone'],
            'cargo' => auth()->user()->isRecepcionista() ? User::ROLE_CLIENTE : $dados['cargo'],
            'password' => Hash::make($dados['password']),
            'd_nasc' => $dados['d_nasc'] ?? null,
            'endereco' => $dados['endereco'] ?? null,
        ]);

        return redirect()->route('admin.usuarios.index')->with('status', 'Usuário cadastrado com sucesso!');
    }

    public function edit(User $usuario)
    {
        if (auth()->user()->isRecepcionista() && !$usuario->isCliente()) {
            abort(403, 'Você só tem permissão para editar clientes.');
        }

        $usuario->load('servicos');
        $servicos = Servico::all();

        return view('admin.usuarios.editar', compact('usuario', 'servicos'));
    }

    public function update(Request $request, $id)
    {
        $usuario = User::findOrFail($id);

        if (auth()->user()->isRecepcionista() && !$usuario->isCliente()) {
            abort(403, 'Recepcionistas só podem editar clientes.');
        }

        $this->normalizarDocumentoContato($request);

        $dados = $request->validate($this->regrasUsuario($id, false), $this->mensagensUsuario($request));

        if (auth()->user()->isRecepcionista()) {
            $dados['cargo'] = User::ROLE_CLIENTE;
        }

        $usuario->update(collect($dados)
            ->only('name', 'email', 'cpf', 'telefone', 'cargo', 'd_nasc', 'endereco')
            ->toArray());

        if ($request->filled('password')) {
            $usuario->password = Hash::make($dados['password']);
            $usuario->save();
        }

        if ($usuario->isProfissional() && $request->has('servicos')) {
            $this->sincronizarServicosProfissional($usuario, $request->input('servicos', []));
        } elseif (!$usuario->isProfissional()) {
            $usuario->servicos()->sync([]);
        }

        return redirect()->route('admin.usuarios.index')->with('status', 'Usuário atualizado com sucesso!');
    }

    public function destroy(User $usuario)
    {
        if ($usuario->id === auth()->id()) {
            return redirect()->route('admin.usuarios.index')->with('error', 'Você não pode excluir sua própria conta!');
        }

        if (auth()->user()->isRecepcionista() && !$usuario->isCliente()) {
            abort(403, 'Recepcionistas só podem remover clientes.');
        }

        $temHistorico = Agendamento::where('cliente_id', $usuario->id)
            ->orWhere('profissional_id', $usuario->id)
            ->exists()
            || DB::table('vendas')->where('profissional_id', $usuario->id)->exists()
            || DB::table('cliente_pacotes')->where('cliente_id', $usuario->id)->exists();

        if ($temHistorico) {
            return redirect()->route('admin.usuarios.index')->with('error', 'Não é possível remover um usuário com histórico no sistema. Bloqueie ou edite o cadastro para preservar relatórios.');
        }

        $usuario->delete();

        return redirect()->route('admin.usuarios.index')->with('status', 'Usuário removido!');
    }

    public function configuracoesservicos()
    {
        $usuario = auth()->user()->load(['servicos', 'horariosTrabalho']);
        $servicos = Servico::all();
        $inicioDisponibilidade = now()->startOfDay();
        $fimDisponibilidade = now()->addMonths(3)->endOfDay();
        $bloqueiosFuturos = BloqueioHorario::where('profissional_id', $usuario->id)
            ->where('data_hora_fim', '>=', $inicioDisponibilidade)
            ->where('data_hora_inicio', '<=', $fimDisponibilidade)
            ->orderBy('data_hora_inicio')
            ->get();
        $feriadosGeraisFuturos = BloqueioHorario::whereNull('profissional_id')
            ->where('data_hora_fim', '>=', $inicioDisponibilidade)
            ->where('data_hora_inicio', '<=', $fimDisponibilidade)
            ->orderBy('data_hora_inicio')
            ->get();
        $bloqueiosFeriadosProfissional = $bloqueiosFuturos
            ->filter(fn ($bloqueio) => str_starts_with((string) $bloqueio->motivo, self::PREFIXO_MOTIVO_FERIADO_PROFISSIONAL))
            ->keyBy(fn ($bloqueio) => Carbon::parse($bloqueio->data_hora_inicio)->toDateString());

        return view('profissional.configuracoes', compact(
            'usuario',
            'servicos',
            'bloqueiosFuturos',
            'feriadosGeraisFuturos',
            'bloqueiosFeriadosProfissional',
            'inicioDisponibilidade',
            'fimDisponibilidade'
        ));
    }

    public function atualizarconfiguracoesservicos(Request $request)
    {
        $usuario = auth()->user();

        if ($request->has('servicos')) {
            $this->sincronizarServicosProfissional(
                $usuario,
                $request->input('servicos', []),
                FinanceiroService::COMISSAO_SERVICO_PERCENTUAL
            );
        }

        if ($request->has('horarios')) {
            $this->sincronizarHorariosProfissional($usuario, $request->input('horarios', []));
        }

        return back()->with('sucesso', 'Configurações e horários atualizados com sucesso!');
    }

    public function bloquearDiaDisponibilidade(Request $request)
    {
        $usuario = auth()->user();
        $hoje = now()->startOfDay();
        $limite = now()->addMonths(3)->endOfDay();

        $dados = $request->validate([
            'data' => ['required', 'date', 'after_or_equal:' . $hoje->toDateString(), 'before_or_equal:' . $limite->toDateString()],
        ], [
            'data.after_or_equal' => 'Escolha uma data a partir de hoje.',
            'data.before_or_equal' => 'A disponibilidade pode ser ajustada somente para os próximos 3 meses.',
        ]);

        $inicio = Carbon::parse($dados['data'])->startOfDay();
        $fim = Carbon::parse($dados['data'])->endOfDay();

        if ($this->profissionalTemAgendamentoNoPeriodo($usuario->id, $inicio, $fim)) {
            throw ValidationException::withMessages([
                'data' => 'Esse dia já tem agendamento ativo. Reagende ou cancele antes de desativar a disponibilidade.',
            ]);
        }

        BloqueioHorario::updateOrCreate(
            [
                'profissional_id' => $usuario->id,
                'data_hora_inicio' => $inicio,
                'data_hora_fim' => $fim,
            ],
            [
                'motivo' => self::MOTIVO_INDISPONIBILIDADE_PROFISSIONAL,
            ]
        );

        return back()->with('sucesso', 'Dia desativado na agenda com sucesso!');
    }

    public function removerBloqueioDisponibilidade(BloqueioHorario $bloqueio)
    {
        $usuario = auth()->user();

        if ((int) $bloqueio->profissional_id !== (int) $usuario->id) {
            abort(403);
        }

        if ($bloqueio->motivo !== self::MOTIVO_INDISPONIBILIDADE_PROFISSIONAL) {
            abort(403);
        }

        if (Carbon::parse($bloqueio->data_hora_fim)->isPast()) {
            return back()->withErrors([
                'data' => 'Não é possível remover bloqueios que já passaram.',
            ]);
        }

        $bloqueio->delete();

        return back()->with('sucesso', 'Dia reativado na agenda com sucesso!');
    }

    public function atualizarFeriadoDisponibilidade(Request $request, BloqueioHorario $feriado)
    {
        $usuario = auth()->user();

        if ($feriado->profissional_id !== null) {
            abort(404);
        }

        $dados = $request->validate([
            'status' => ['required', 'in:ativo,desativado'],
        ]);

        $inicio = Carbon::parse($feriado->data_hora_inicio)->startOfDay();
        $fim = Carbon::parse($feriado->data_hora_fim)->endOfDay();
        $bloqueioProfissional = $this->buscarBloqueioFeriadoProfissional($usuario->id, $inicio, $fim);

        if ($dados['status'] === 'ativo') {
            if ($bloqueioProfissional) {
                $bloqueioProfissional->delete();
            }

            return back()->with('sucesso', 'Feriado ativado na sua agenda. Clientes poderao agendar com o acrescimo informado.');
        }

        if ($this->profissionalTemAgendamentoNoPeriodo($usuario->id, $inicio, $fim)) {
            throw ValidationException::withMessages([
                'feriado' => 'Esse feriado já tem agendamento ativo. Reagende ou cancele antes de desativar.',
            ]);
        }

        BloqueioHorario::updateOrCreate(
            [
                'profissional_id' => $usuario->id,
                'data_hora_inicio' => $inicio,
                'data_hora_fim' => $fim,
            ],
            [
                'motivo' => self::PREFIXO_MOTIVO_FERIADO_PROFISSIONAL . ($feriado->motivo ?: 'Feriado'),
            ]
        );

        return back()->with('sucesso', 'Feriado desativado na sua agenda. Clientes não poderão agendar com você nesse dia.');
    }

    public function alterarStatus(Request $request, $id)
    {
        if (!auth()->user()->isGerente()) {
            abort(403);
        }

        $user = User::findOrFail($id);

        if ($request->status === 'ativo') {
            $user->update([
                'status' => 'ativo',
                'faltas' => 0
            ]);
        } else {
            $user->update(['status' => 'bloqueado']);
        }

        return back()->with('success', 'Status do usuário atualizado com sucesso!');
    }

    public function extrato(Request $request)
    {
        $mes = $request->get('mes', date('m'));
        $ano = $request->get('ano', date('Y'));
        $profissionalId = auth()->id();

        $agendamentos = Agendamento::with('servico')
            ->where('profissional_id', $profissionalId)
            ->where('status', 'executado')
            ->whereMonth('updated_at', $mes)
            ->whereYear('updated_at', $ano)
            ->get();

        $totalComissaoServicos = $agendamentos->sum('valor_comissao');

        $totalVendasProdutos = DB::table('vendas')
            ->where('profissional_id', $profissionalId)
            ->whereMonth('created_at', $mes)
            ->whereYear('created_at', $ano)
            ->sum('valor_venda');

        $totalComissaoProdutos = DB::table('vendas')
            ->where('profissional_id', $profissionalId)
            ->whereMonth('created_at', $mes)
            ->whereYear('created_at', $ano)
            ->sum('valor_comissao');

        return view('profissional.extrato', compact(
            'agendamentos',
            'totalComissaoServicos',
            'totalComissaoProdutos',
            'mes',
            'ano'
        ));
    }

    private function normalizarDocumentoContato(Request $request): void
    {
        $request->merge([
            'cpf' => preg_replace('/[^0-9]/', '', (string) $request->cpf),
            'telefone' => preg_replace('/[^0-9]/', '', (string) $request->telefone),
        ]);
    }

    private function regrasUsuario(?int $usuarioId = null, bool $senhaObrigatoria = true): array
    {
        $regraSenha = $senhaObrigatoria ? ['required', 'min:8'] : ['nullable', 'min:8'];
        $ignorarUsuario = $usuarioId ? ',' . $usuarioId : '';

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email' . $ignorarUsuario],
            'cpf' => ['required', 'string', 'size:11', 'regex:/^[0-9]+$/', 'unique:users,cpf' . $ignorarUsuario],
            'telefone' => ['required', 'string', 'size:11', 'regex:/^[0-9]+$/'],
            'cargo' => ['required', 'in:gerente,recepcionista,profissional,cliente'],
            'password' => $regraSenha,
            'd_nasc' => ['nullable', 'date'],
            'endereco' => ['nullable', 'string', 'max:255'],
        ];
    }

    private function mensagensUsuario(Request $request): array
    {
        return [
            'cpf.size' => 'O CPF deve ter exatamente 11 números (você enviou ' . strlen($request->cpf) . ').',
            'cpf.unique' => 'Este CPF já está cadastrado.',
            'email.unique' => 'Este e-mail já está em uso.',
            'telefone.size' => 'O telefone deve ter exatamente 11 números (você enviou ' . strlen($request->telefone) . ').',
        ];
    }

    private function sincronizarServicosProfissional(User $usuario, array $servicos, ?float $comissaoFixa = null): void
    {
        $syncData = [];

        foreach ($servicos as $servicoId => $dados) {
            if (!isset($dados['ativo'])) {
                continue;
            }

            $syncData[$servicoId] = [
                'comissao_percentual' => $comissaoFixa ?? ($dados['comissao'] ?? FinanceiroService::COMISSAO_SERVICO_PERCENTUAL),
                'duracao_customizada' => $dados['duracao'] ?? null,
            ];
        }

        $usuario->servicos()->sync($syncData);
    }

    private function sincronizarHorariosProfissional(User $usuario, array $horarios): void
    {
        foreach ($horarios as $dia => $dados) {
            HorarioTrabalho::updateOrCreate(
                [
                    'profissional_id' => $usuario->id,
                    'dia_semana' => $dia
                ],
                [
                    'hora_inicio' => $dados['inicio'] ?? '08:00',
                    'hora_fim' => $dados['fim'] ?? '18:00',
                    'almoco_inicio' => $dados['almoco_inicio'] ?? '11:00',
                    'almoco_fim' => $dados['almoco_fim'] ?? '13:00',
                    'trabalha' => isset($dados['trabalha']) ? 1 : 0
                ]
            );
        }
    }

    private function profissionalTemAgendamentoNoPeriodo(int $profissionalId, Carbon $inicio, Carbon $fim): bool
    {
        return Agendamento::where('profissional_id', $profissionalId)
            ->where('status', '!=', 'cancelado')
            ->where('data_hora_inicio', '<', $fim)
            ->where('data_hora_fim', '>', $inicio)
            ->exists();
    }

    private function buscarBloqueioFeriadoProfissional(int $profissionalId, Carbon $inicio, Carbon $fim): ?BloqueioHorario
    {
        return BloqueioHorario::where('profissional_id', $profissionalId)
            ->where('data_hora_inicio', '<', $fim)
            ->where('data_hora_fim', '>', $inicio)
            ->where('motivo', 'like', self::PREFIXO_MOTIVO_FERIADO_PROFISSIONAL . '%')
            ->first();
    }
}
