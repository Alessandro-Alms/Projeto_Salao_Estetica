<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Agendamento;
use App\Models\ClientePacote;
use App\Models\HorarioTrabalho;
use App\Models\Servico;
use App\Models\User;
use App\Services\FinanceiroService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
{
    public function dashboard(Request $request): View
    {
        $user = $request->user();
        $mediaAvaliacao = null;
        $totalAvaliacoes = 0;
        $comentariosAvaliacao = collect();
        $agendamentoProximo = null;
        $avaliacoesPendentes = 0;
        $pacotesVencendo = collect();
        $mensagemProximaVisita = null;

        if ($user && $user->cargo === 'profissional') {
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
        }

        if ($user && $user->cargo === 'cliente') {
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
            'agendamentoProximo',
            'avaliacoesPendentes',
            'pacotesVencendo',
            'mensagemProximaVisita'
        ));
    }

    public function index(Request $request)
    {
        $consulta = User::query();
        $usuarioLogado = auth()->user();
        $quantidadePorPagina = 10;

        if ($request->filled('cargo')) {
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

        if ($usuarioLogado->cargo === 'gerente') {
            $consulta->whereIn('cargo', ['gerente', 'recepcionista', 'profissional', 'cliente']);
        } elseif ($usuarioLogado->cargo === 'recepcionista') {
            $consulta->where('cargo', 'cliente');
        } else {
            return redirect()->route('dashboard')->with('error', 'Acesso negado.');
        }

        $usuarios = $consulta->orderBy('name', 'asc')->paginate($quantidadePorPagina);

        return view('admin.usuarios.index', compact('usuarios'));
    }

    public function create()
    {
        return view('admin.usuarios.criar');
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
            'cargo' => auth()->user()->cargo === 'recepcionista' ? 'cliente' : $dados['cargo'],
            'password' => Hash::make($dados['password']),
            'd_nasc' => $dados['d_nasc'] ?? null,
            'endereco' => $dados['endereco'] ?? null,
        ]);

        return redirect()->route('admin.usuarios.index')->with('status', 'Usuário cadastrado com sucesso!');
    }

    public function edit(User $usuario)
    {
        if (auth()->user()->cargo === 'recepcionista' && $usuario->cargo !== 'cliente') {
            abort(403, 'Você só tem permissão para editar clientes.');
        }

        $usuario->load('servicos');
        $servicos = Servico::all();

        return view('admin.usuarios.editar', compact('usuario', 'servicos'));
    }

    public function update(Request $request, $id)
    {
        $usuario = User::findOrFail($id);

        if (auth()->user()->cargo === 'recepcionista' && $usuario->cargo !== 'cliente') {
            abort(403, 'Recepcionistas sÃ³ podem editar clientes.');
        }

        $this->normalizarDocumentoContato($request);

        $dados = $request->validate($this->regrasUsuario($id, false), $this->mensagensUsuario($request));

        if (auth()->user()->cargo === 'recepcionista') {
            $dados['cargo'] = 'cliente';
        }

        $usuario->update(collect($dados)
            ->only('name', 'email', 'cpf', 'telefone', 'cargo', 'd_nasc', 'endereco')
            ->toArray());

        if ($request->filled('password')) {
            $usuario->password = Hash::make($dados['password']);
            $usuario->save();
        }

        if ($usuario->cargo === 'profissional' && $request->has('servicos')) {
            $this->sincronizarServicosProfissional($usuario, $request->input('servicos', []));
        } elseif ($usuario->cargo !== 'profissional') {
            $usuario->servicos()->sync([]);
        }

        return redirect()->route('admin.usuarios.index')->with('status', 'Usuário atualizado com sucesso!');
    }

    public function destroy(User $usuario)
    {
        if ($usuario->id === auth()->id()) {
            return redirect()->route('admin.usuarios.index')->with('error', 'Você não pode deletar sua própria conta!');
        }

        if (auth()->user()->cargo === 'recepcionista' && $usuario->cargo !== 'cliente') {
            abort(403, 'Recepcionistas sÃ³ podem remover clientes.');
        }

        $temHistorico = Agendamento::where('cliente_id', $usuario->id)
            ->orWhere('profissional_id', $usuario->id)
            ->exists()
            || DB::table('vendas')->where('profissional_id', $usuario->id)->exists()
            || DB::table('cliente_pacotes')->where('cliente_id', $usuario->id)->exists();

        if ($temHistorico) {
            return redirect()->route('admin.usuarios.index')->with('error', 'NÃ£o Ã© possÃ­vel remover um usuÃ¡rio com histÃ³rico no sistema. Bloqueie ou edite o cadastro para preservar relatÃ³rios.');
        }

        $usuario->delete();

        return redirect()->route('admin.usuarios.index')->with('status', 'Usuário removido!');
    }

    public function configuracoesservicos()
    {
        $usuario = auth()->user()->load(['servicos', 'horariosTrabalho']);
        $servicos = Servico::all();

        return view('profissional.configuracoes', compact('usuario', 'servicos'));
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

    public function alterarStatus(Request $request, $id)
    {
        if (auth()->user()->cargo !== 'gerente') {
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

        $totalComissaoProdutos = app(FinanceiroService::class)
            ->calcularComissaoProduto((float) $totalVendasProdutos);

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
                    'almoco_inicio' => $dados['almoco_inicio'] ?? '12:00',
                    'almoco_fim' => $dados['almoco_fim'] ?? '13:00',
                    'trabalha' => isset($dados['trabalha']) ? 1 : 0
                ]
            );
        }
    }
}
