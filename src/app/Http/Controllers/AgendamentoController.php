<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Agendamento;
use App\Models\Servico;
use App\Models\User;
use Carbon\Carbon;

class AgendamentoController extends Controller
{
    public function index()
    {
        // Por enquanto, apenas retorna uma view vazia que criaremos
        return view('admin.agenda.index');
    }

    public function store(Request $request)
    {
        // 1. Validação Básica dos Campos
        $request->validate([
            'cliente_id' => 'required|exists:users,id',
            'profissional_id' => 'required|exists:users,id',
            'servico_id' => 'required|exists:servicos,id_servico',
            'data_hora' => 'required|date|after:now',
        ], [
            'data_hora.after' => 'O agendamento deve ser para uma data futura.',
        ]);

        // Instanciando os modelos para cálculos
        $servico = \App\Models\Servico::findOrFail($request->servico_id);
        $profissional = \App\Models\User::findOrFail($request->profissional_id);
        
        // 2. Definindo Início e Fim (usando Carbon)
        $inicio = \Carbon\Carbon::parse($request->data_hora);
        $diaSemana = $inicio->dayOfWeek; // 0 (dom) a 6 (sab)
        
        // Busca a duração personalizada do profissional ou a padrão do serviço
        $vinculo = $profissional->servicos->find($servico->id_servico);
        $duracao = $vinculo ? ($vinculo->pivot->duracao_customizada ?? $servico->duracao) : $servico->duracao;
        $fim = $inicio->copy()->addMinutes($duracao);

        // 3. Validação: Horário de Trabalho e Almoço
        $escala = \App\Models\HorarioTrabalho::where('usuario_id', $profissional->id)
                    ->where('dia_semana', $diaSemana)
                    ->first();

        if (!$escala || !$escala->trabalha) {
            return back()->withErrors(['data_hora' => 'O profissional não trabalha neste dia da semana.'])->withInput();
        }

        $horaFormatada = $inicio->format('H:i:s');
        
        // Verifica se está fora do expediente geral
        if ($horaFormatada < $escala->hora_inicio || $inicio->copy()->addMinutes($duracao)->format('H:i:s') > $escala->hora_fim) {
            return back()->withErrors(['data_hora' => 'O horário escolhido está fora do expediente do profissional.'])->withInput();
        }

        // Verifica se cai no almoço
        if ($horaFormatada >= $escala->almoco_inicio && $horaFormatada < $escala->almoco_fim) {
            return back()->withErrors(['data_hora' => 'Este horário coincide com o intervalo de almoço do profissional.'])->withInput();
        }

        // 4. Validação: Conflito com outros Agendamentos (Colisão)
        $conflito = \App\Models\Agendamento::where('profissional_id', $request->profissional_id)
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

        // 5. Salvar Agendamento
        \App\Models\Agendamento::create([
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
        $agendamentos = \App\Models\Agendamento::with(['cliente', 'servico', 'profissional'])->get();

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
        $profissionais = \App\Models\User::where('cargo', 'profissional')->get();
        $servicos = \App\Models\Servico::all();
        return view('cliente.agendar', compact('profissionais', 'servicos'));
    }
    public function agendaProfissional()
    {
        // Usamos o query builder puro para testar se o Eloquent está bugando
        $agendamentos = \App\Models\Agendamento::where('profissional_id', auth()->id())
            ->with(['cliente', 'servico'])
            ->orderBy('data_hora_inicio', 'asc')
            ->get();

        // Verificação de segurança: se a lista não estiver vazia, 
        // o Laravel VAI ter que carregar o ID.
        $agrupados = $agendamentos->groupBy(function($item) {
            return \Carbon\Carbon::parse($item->data_hora_inicio)->format('d/m/Y');
        });

        return view('profissional.agenda', ['agendamentos' => $agrupados]);
    }
    public function storeCliente(Request $request)
    {
        $request->merge(['cliente_id' => auth()->id()]);
        return $this->store($request);
    }
    public function indexCliente()
    {
        $agendamentos = \App\Models\Agendamento::where('cliente_id', auth()->id())
            ->with(['profissional', 'servico'])
            ->orderBy('data_hora_inicio', 'asc')
            ->get();

        return view('cliente.index', compact('agendamentos'));
    }
    public function cancelarCliente($id_agendamento)
    {
        // Busca o agendamento ou dá erro 404
        $agendamento = \App\Models\Agendamento::findOrFail($id_agendamento);

        $agora = Carbon::now();
        $datainicio = Carbon::parse($agendamento->data_hora_inicio);
        $diferencaHoras = $agora->diffInHours($datainicio, false);

        if ($diferencaHoras < 24) {
            return back()->withErrors(['data_hora' => 'Agendamentos só podem ser cancelados com pelo menos 24 horas de antecedência.'])->withInput();
        }
        $agendamento->update(['status' => 'cancelado']);

        return back()->with('status', 'Agendamento cancelado com sucesso!');
    }
    public function marcarComoExecutado($agendamento_id)
    {
        $agendamento = \App\Models\Agendamento::findOrFail($agendamento_id);
        $agora = Carbon::now();
        $horarioagendamento = Carbon::parse($agendamento->data_hora_inicio);

        if ($agora->diffInMinutes($horarioagendamento, false) < -15) {
            $agendamento->observacao = "Cliente chegou com mais de 15min de atraso.";
        }

        // Verifica se é o profissional certo
        if ($agendamento->profissional_id !== auth()->id()) {
            abort(403);
        }
        $agendamento->status = 'executado';
        $agendamento->save();

        return back()->with('status', 'Atendimento finalizado!');
    }
}
