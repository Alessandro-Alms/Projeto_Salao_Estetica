<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Agendamento;
use App\Models\Servico;
use App\Models\User;
use Carbon\Carbon;
use App\Models\HorarioTrabalho;
use App\Models\Produto; 
use Illuminate\Support\Facades\DB;
use App\Models\BloqueioHorario;

class AgendamentoController extends Controller
{
    public function index()
    {
        return view('admin.agenda.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'cliente_id' => ['required','exists:users,id'],
            'profissional_id' => ['required','exists:users,id'],
            'servico_id' => ['required','exists:servicos,id_servico'],
            'data_hora' => ['required','date','after:now'],
        ], [
            'data_hora.after' => 'O agendamento deve ser para uma data futura.',
        ]);

        if (auth()->user()->status === 'bloqueado') {
            return back()->withErrors(['erro' => 'Sua conta está bloqueada para novos agendamentos devido ao excesso de faltas. Entre em contato com o suporte.']);
        }

        $servico = Servico::findOrFail($request->servico_id);
        $profissional = User::findOrFail($request->profissional_id);
        
        // Definindo Início e Fim
        $inicio = Carbon::parse($request->data_hora);
        $diaSemana = $inicio->dayOfWeek; 

        // Busca a duração
        $vinculo = $profissional->servicos->find($servico->id_servico);

        // Verifica se o profissional realmente executa esse serviço
        if (!$vinculo) {
            return back()->withErrors(['servico_id' => 'Este profissional não realiza este tipo de serviço.'])->withInput();
        }

        $duracao = $vinculo->pivot->duracao_customizada ?? $servico->duracao;
        $fim = $inicio->copy()->addMinutes($duracao);

        // Validação: Horário de Trabalho e Almoço
        $escala = HorarioTrabalho::where('profissional_id', $profissional->id)
                    ->where('dia_semana', $diaSemana)
                    ->first();

        if (!$escala || !$escala->trabalha) {
            return back()->withErrors(['data_hora' => 'O profissional não trabalha neste dia da semana.'])->withInput();
        }

        $horaInicio = $inicio->format('H:i:s');
        $horaFim = $fim->format('H:i:s');

        // Verifica expediente geral
        if ($horaInicio < $escala->hora_inicio || $horaFim > $escala->hora_fim) {
            return back()->withErrors(['data_hora' => 'O horário escolhido está fora do expediente do profissional.'])->withInput();
        }

        // Verifica se o atendimento invade o almoço 
        // Se o início for antes do fim do almoço E o fim for depois do início do almoço, há intersecção.
        if ($horaInicio < $escala->almoco_fim && $horaFim > $escala->almoco_inicio) {
            return back()->withErrors(['data_hora' => 'Este horário coincide ou invade o intervalo de almoço do profissional.'])->withInput();
        }

        // Validação: Bloqueios (Folgas e Feriados) 
        $bloqueios = BloqueioHorario::where(function ($q) use ($request) {
            $q->whereNull('profissional_id') // Bloqueio geral
              ->orWhere('profissional_id', $request->profissional_id); // Bloqueio do profissional
        })->get();

        foreach ($bloqueios as $bloqueio) {
            $bloqueioInicio = Carbon::parse($bloqueio->data_hora_inicio);
            $bloqueioFim = Carbon::parse($bloqueio->data_hora_fim);

            if ($inicio < $bloqueioFim && $fim > $bloqueioInicio) {
                return back()->withErrors(['data_hora' => 'O horário escolhido coincide com um bloqueio de agenda: ' . $bloqueio->motivo])->withInput();
            }
        }
        // Validação: Conflito (Colisão)
        $conflito = Agendamento::where('profissional_id', $request->profissional_id)
            ->where('status', '!=', 'cancelado')
            ->where(function ($query) use ($inicio, $fim) {
                $query->where(function ($q) use ($inicio, $fim) {
                    $q->where('data_hora_inicio', '>=', $inicio)
                    ->where('data_hora_inicio', '<', $fim);
                })
                ->orWhere(function ($q) use ($inicio, $fim) {
                    $q->where('data_hora_fim', '>', $inicio)
                    ->where('data_hora_fim', '<=', $fim);
                })
                ->orWhere(function ($q) use ($inicio, $fim) {
                    $q->where('data_hora_inicio', '<=', $inicio)
                    ->where('data_hora_fim', '>=', $fim);
                });
            })->exists();

        if ($conflito) {
            return back()->withErrors(['data_hora' => 'Este profissional já possui um agendamento que sobrepõe este horário.'])->withInput();
        }

        // Salvar Agendamento
        Agendamento::create([
            'cliente_id' => $request->cliente_id,
            'profissional_id' => $request->profissional_id,
            'servico_id' => $request->servico_id,
            'data_hora_inicio' => $inicio,
            'data_hora_fim' => $fim,
            'status' => 'confirmado',
            'valor_total' => $servico->preco, 
        ]);

        if (auth()->user()->cargo === 'cliente') {
            return redirect()->route('cliente.index')->with('status', 'Agendamento realizado com sucesso!');
        }

        return back()->with('status', 'Agendamento realizado com sucesso!');
    }
    public function listarJson()
    {
        $agendamentos = Agendamento::with(['cliente', 'servico', 'profissional'])->get();

        return response()->json($agendamentos->map(function($a) {
            return [
                'id' => $a->id,
                'title' => $a->servico->nome . " (" . $a->cliente->name . ")",
                'start' => $a->data_hora_inicio,
                'end' => $a->data_hora_fim,
                'description' => "Profissional: " . $a->profissional->name,
            ];
        }));
    }
    public function clienteAgendar()
    {
        $profissionais = User::where('cargo', 'profissional')->get();
        $servicos = Servico::all();
        return view('cliente.agendar', compact('profissionais', 'servicos'));
    }
    public function agendaProfissional()
    {
        // Usamos o query builder puro para testar se o Eloquent está bugando
        $agendamentos = Agendamento::where('profissional_id', auth()->id())
                                    ->with(['cliente', 'servico'])
                                    ->orderBy('data_hora_inicio', 'asc')
                                    ->get()
                                    ->groupBy(fn($data) => \Carbon\Carbon::parse($data->data_hora_inicio)->format('d/m/Y'));

        // Verificação de segurança: se a lista não estiver vazia, 
        // o Laravel VAI ter que carregar o ID.
        $produtos = Produto::where('quantidade_estoque', '>', 0)->get();

        return view('profissional.agenda', compact('agendamentos', 'produtos'));
    }
    public function storeCliente(Request $request)
    {
        $request->merge(['cliente_id' => auth()->id()]);
        return $this->store($request);
    }
    public function indexCliente()
    {
        $agendamentos = Agendamento::where('cliente_id', auth()->id())
            ->with(['profissional', 'servico'])
            ->orderBy('data_hora_inicio', 'asc')
            ->get();

        return view('cliente.index', compact('agendamentos'));
    }
    public function cancelarCliente($id_agendamento)
    {
        // Busca o agendamento ou dá erro 404
        $agendamento = Agendamento::findOrFail($id_agendamento);

        $agora = Carbon::now();
        $datainicio = Carbon::parse($agendamento->data_hora_inicio);
        $diferencaHoras = $agora->diffInHours($datainicio, false);

        if ($diferencaHoras < 24) {
            return back()->withErrors(['data_hora' => 'Agendamentos só podem ser cancelados com pelo menos 24 horas de antecedência.'])->withInput();
        }
        $agendamento->update(['status' => 'cancelado']);

        return back()->with('status', 'Agendamento cancelado com sucesso!');
    }
    public function marcarComoExecutado(Request $request, $id_agendamento)
    {
        $agendamento = Agendamento::findOrFail($id_agendamento);

        // Iniciamos uma transação para garantir que ou faz TUDO ou não faz NADA
        return DB::transaction(function () use ($request, $agendamento) {
            
            // 1. Verificação Prévia de Estoque (Para não dar erro no meio do caminho)
            if ($request->has('produtos')) {
                foreach ($request->produtos as $item) {
                    if (!empty($item['id'])) {
                        $produto = Produto::find($item['id']);
                        $qtdPedida = $item['quantidade'] ?? 1;

                        if (!$produto || $produto->quantidade_estoque < $qtdPedida) {
                            // Se um único produto falhar, paramos tudo aqui
                            return back()->withErrors([
                                'estoque' => "Estoque insuficiente para o produto: " . ($produto->nome ?? 'Desconhecido')
                            ])->withInput();
                        }
                    }
                }
            }

            // 2. Se chegou aqui, todos os produtos têm estoque ou não há produtos.
            // Agora sim mudamos o status do agendamento.
            $agendamento->status = 'executado';
            $agendamento->obs = $request->input('observacao');
            $agendamento->save();

            // 3. Registrar as Vendas e Baixar Estoque
            if ($request->has('produtos')) {
                foreach ($request->produtos as $item) {
                    if (!empty($item['id'])) {
                        $produto = Produto::find($item['id']);
                        $qtd = $item['quantidade'] ?? 1;

                        // Baixa o estoque
                        $produto->decrement('quantidade_estoque', $qtd);

                        // Registra a venda (UC007 / UC014)
                        DB::table('vendas')->insert([
                            'profissional_id' => auth()->id(),
                            'produto_id' => $produto->id_produto,
                            'quantidade' => $qtd,
                            'valor_venda' => $produto->valor_unitario * $qtd,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }

            return redirect()->route('profissional.agenda')->with('status', 'Atendimento finalizado com sucesso!');
        });
    }
    public function confirmarPresenca($id)
    {
        $agendamento = Agendamento::findOrFail($id);

        // Se o status for 'confirmado', mudamos para 'presente'
        if ($agendamento->status == 'confirmado') {
            $agendamento->status = 'presente';
            $agendamento->save();
        }

        return back()->with('status', 'Check-in realizado!');
    }
    public function marcarFalta($id)
    {
        $agendamento = Agendamento::findOrFail($id);
        $agendamento->update(['status' => 'falta']);

        $cliente = $agendamento->cliente; 
        $cliente->increment('faltas');

        if ($cliente->faltas >= 3) {
            $cliente->update(['status' => 'bloqueado']);
        }

        return back()->with('success', 'Falta registrada. Cliente bloqueado se atingiu 3 faltas.');
    }
}
