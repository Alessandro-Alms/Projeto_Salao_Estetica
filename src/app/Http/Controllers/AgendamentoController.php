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
use App\Models\ClientePacote;

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

        // =========================================================================
        // BLINDAGEM CONTRA OVERBOOKING (Fila de Banco de Dados)
        // =========================================================================
        $resultadoAgendamento = DB::transaction(function () use ($request, $inicio, $fim, $servico) {
            
            // 1. A MÁGICA: Trava a "agenda" deste profissional por alguns milissegundos.
            // Se outra pessoa tentar agendar ao mesmo tempo, o banco de dados vai colocar ela 
            // numa fila de espera até essa verificação terminar.
            User::where('id', $request->profissional_id)->lockForUpdate()->first();

            // 2. Validação: Conflito (Colisão)
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

            // Se achou conflito, aborta a missão e avisa o controller
            if ($conflito) {
                return 'conflito'; 
            }

            // 3. Salvar Agendamento se a via estiver livre
            Agendamento::create([
                'cliente_id' => $request->cliente_id,
                'profissional_id' => $request->profissional_id,
                'servico_id' => $request->servico_id,
                'data_hora_inicio' => $inicio,
                'data_hora_fim' => $fim,
                'status' => 'confirmado',
                'valor_total' => $servico->preco, 
            ]);

            return 'sucesso';
        });

        // RESPOSTA AO USUÁRIO APÓS A TENTATIVA
        if ($resultadoAgendamento === 'conflito') {
            return back()->withErrors(['data_hora' => 'Poxa, alguém foi mais rápido! Este horário acabou de ser reservado por outra pessoa. Por favor, atualize a página e escolha outro horário.'])->withInput();
        }

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

        $pacotes = auth()->user()->pacotesAtivos()->with('pacote')->get();

        return view('cliente.index', compact('agendamentos', 'pacotes'));
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
        $cliente = User::findOrFail($agendamento->cliente_id);

        // Iniciamos uma transação para garantir que ou faz TUDO ou não faz NADA
        return DB::transaction(function () use ($request, $agendamento, $cliente) { 
            
            // 1. Verificação Prévia de Estoque
            if ($request->has('produtos')) {
                foreach ($request->produtos as $item) {
                    if (!empty($item['id'])) {
                        $produto = Produto::find($item['id']);
                        $qtdPedida = $item['quantidade'] ?? 1;

                        if (!$produto || $produto->quantidade_estoque < $qtdPedida) {
                            // Se um único produto falhar, paramos tudo e NADA é salvo no banco
                            return back()->withErrors([
                                'estoque' => "Estoque insuficiente para o produto: " . ($produto->nome ?? 'Desconhecido')
                            ])->withInput();
                        }
                    }
                }
            }
            // Lógica de Pacotes
            if ($request->has('usar_pacote') && $request->usar_pacote != null) {
                $clientePacote = ClientePacote::find($request->usar_pacote);

                if ($clientePacote && $clientePacote->sessoes_restantes > 0) {
                    // Desconta 1 sessão
                    $clientePacote->sessoes_restantes -= 1;
                    
                    // Se zerou as sessões, finaliza a carteirinha
                    if ($clientePacote->sessoes_restantes == 0) {
                        $clientePacote->status = 'finalizado';
                    }
                    $clientePacote->save();

                    // Zera o valor para não somar no caixa do salão (já foi pago na compra)
                    $agendamento->valor_total = 0; 
                    
                    // Adiciona um aviso na observação
                    $obsPacote = " (Abatido 1 sessão do pacote: " . $clientePacote->pacote->nome . ")";
                    $request->merge(['observacao' => $request->input('observacao') . $obsPacote]);
                }
            }

 
            // Fidelidade (Só ganha ponto/desconto se NÃO estiver usando pacote)
            if ($request->has('usar_pacote') && $request->usar_pacote != null) {
                $mensagem = 'Sessão de pacote concluída com sucesso!';
            } else {
                if ($cliente->contador_fidelidade == 5) {
                    // Aplica o desconto de 50%
                    $agendamento->valor_total = $agendamento->valor_total * 0.5;

                    $cliente->contador_fidelidade = 0;
                    $cliente->save();
                    
                    $mensagem = 'Atendimento concluído! O cliente ganhou 50% de desconto pela fidelidade! 🎉';
                } else {
                    // Ganha mais um "selo"
                    $cliente->increment('contador_fidelidade');
                    $mensagem = 'Atendimento concluído com sucesso!';
                }
            }
            
            // Carrega o profissional para buscar a regra de comissão dele
            $profissional = User::find($agendamento->profissional_id);
            $vinculo = $profissional->servicos->find($agendamento->servico_id);
            
            // Pega a comissão customizada da tabela pivot ou usa 50% como padrão
            $porcentagemComissao = $vinculo->pivot->comissao_percentual ?? 50; 
            
            // Pega o valor cheio do serviço (ignorando descontos de pacote ou fidelidade)
            // Certifique-se de que a relação ->servico e a coluna ->preco estão corretas com o seu banco
            $precoBase = $agendamento->servico->preco;
            $valorComissao = $precoBase * ($porcentagemComissao / 100);

            // 3. Se chegou aqui, tudo certo! Mudamos o status do agendamento.
            $agendamento->status = 'executado';
            $agendamento->obs = $request->input('observacao');
            $agendamento->valor_comissao = $valorComissao; // Armazena o valor da comissão
            $agendamento->comissao_paga_percentual = $porcentagemComissao; // Armazena a porcentagem da comissão
            $agendamento->save();

            // 4. Registrar as Vendas e Baixar Estoque
            if ($request->has('produtos')) {
                foreach ($request->produtos as $item) {
                    if (!empty($item['id'])) {
                        $produto = Produto::find($item['id']);
                        $qtd = $item['quantidade'] ?? 1;

                        // Baixa o estoque
                        $produto->decrement('quantidade_estoque', $qtd);

                        // Registra a venda
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

            // 5. Retornamos usando a $mensagem dinâmica que criamos ali em cima!
            return redirect()->route('profissional.agenda')->with('status', $mensagem);
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
