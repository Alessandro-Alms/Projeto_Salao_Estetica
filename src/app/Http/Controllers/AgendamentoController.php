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

    // =========================================================
    // NOVAS FUNÇÕES PARA O AGENDAMENTO PASSO A PASSO (WIZARD)
    // =========================================================

    public function novoAgendamento()
    {
        // Passo 1: Carrega apenas os serviços para a tela inicial
        $servicos = Servico::all();
        // Agora usa a nova view com o layout Google Calendar
        return view('cliente.agendar_novo', compact('servicos')); 
    }

    public function getProfissionaisAjax(Request $request)
    {
        // Suporta tanto servico_id (compatibilidade) quanto servicos_ids (múltiplos)
        $servicoIdSingle = $request->servico_id;
        $servicosIdsString = $request->servicos_ids;
        $dataHoraString = $request->data_hora; // Opcional: "YYYY-MM-DD HH:mm"
        $duracao = (int) $request->duracao; // Opcional: duração total em minutos
        
        // Determinar qual array de serviços usar
        if ($servicosIdsString) {
            $servicosIds = array_map('intval', explode(',', $servicosIdsString));
        } elseif ($servicoIdSingle) {
            $servicosIds = [$servicoIdSingle];
        } else {
            return response()->json([]);
        }
        
        // Passo 1: Busca profissionais que FAZEM TODOS os serviços selecionados
        // Precisa de múltiplos whereHas - um para cada serviço
        $profissionaisCandidatos = User::where('cargo', 'profissional');
        
        foreach ($servicosIds as $servicoId) {
            $profissionaisCandidatos = $profissionaisCandidatos->whereHas('servicos', function($q) use ($servicoId) {
                $q->where('servicos.id_servico', $servicoId);
            });
        }
        
        $profissionaisCandidatos = $profissionaisCandidatos->get(['id', 'name']);
        
        // Se não há data_hora ou duracao, retorna todos os profissionais que fazem os serviços
        if (!$dataHoraString || !$duracao) {
            return response()->json($profissionaisCandidatos);
        }
        
        // Passo 2: Se data_hora foi fornecida, filtra apenas profissionais livres naquele horário
        $dataHora = Carbon::parse($dataHoraString);
        $dataHoraFim = $dataHora->copy()->addMinutes($duracao);
        $diaSemana = $dataHora->dayOfWeek;
        
        // Filtra profissionais que estão LIVRES naquele horário
        $profissionaisLivres = $profissionaisCandidatos->filter(function($prof) use ($dataHora, $dataHoraFim, $diaSemana, $servicosIds) {
            
            // Verificar se o profissional trabalha neste dia da semana
            $escala = HorarioTrabalho::where('profissional_id', $prof->id)
                ->where('dia_semana', $diaSemana)
                ->first();
            
            if (!$escala || !$escala->trabalha) {
                return false;
            }
            
            $horaInicio = $dataHora->format('H:i:s');
            $horaFim = $dataHoraFim->format('H:i:s');
            
            // Verificar se está dentro do expediente
            if ($horaInicio < $escala->hora_inicio || $horaFim > $escala->hora_fim) {
                return false;
            }
            
            // Verificar se invade almoço
            if ($horaInicio < $escala->almoco_fim && $horaFim > $escala->almoco_inicio) {
                return false;
            }
            
            // Verificar bloqueios (folgas, feriados)
            $bloqueios = BloqueioHorario::where(function ($q) use ($prof) {
                $q->whereNull('profissional_id')->orWhere('profissional_id', $prof->id);
            })->whereDate('data_hora_inicio', '<=', $dataHora)
              ->whereDate('data_hora_fim', '>=', $dataHora)
              ->get();
            
            foreach ($bloqueios as $bloqueio) {
                $bqInicio = Carbon::parse($bloqueio->data_hora_inicio);
                $bqFim = Carbon::parse($bloqueio->data_hora_fim);
                if ($dataHora < $bqFim && $dataHoraFim > $bqInicio) {
                    return false;
                }
            }
            
            // Verificar agendamentos existentes
            $agendamentos = Agendamento::where('profissional_id', $prof->id)
                ->where('status', '!=', 'cancelado')
                ->get();
            
            foreach ($agendamentos as $ag) {
                $agInicio = Carbon::parse($ag->data_hora_inicio);
                $agFim = Carbon::parse($ag->data_hora_fim);
                if ($dataHora < $agFim && $dataHoraFim > $agInicio) {
                    return false;
                }
            }
            
            return true;
        });
        
        return response()->json(array_values($profissionaisLivres->toArray()));
    }

    public function getHorariosAjax(Request $request)
    {
        $data = $request->data; // ex: '2026-05-02'
        $servicoId = $request->servico_id;
        $profissionalId = $request->profissional_id; // Opcional - se não tiver, busca de todos
        $duracao = (int) $request->duracao; // Converter para int - opcional - duração customizada (para múltiplos serviços)

        $servico = Servico::find($servicoId);
        if (!$servico) {
            return response()->json([]);
        }

        $inicioDia = Carbon::parse($data);
        $diaSemana = $inicioDia->dayOfWeek;

        // Se profissional foi especificado, usa aquele
        if ($profissionalId) {
            $profissional = User::find($profissionalId);
            if (!$profissional) {
                return response()->json([]);
            }
            $profissionais = collect([$profissional]);
        } else {
            // Senão, busca TODOS os profissionais que fazem este serviço
            $profissionais = User::where('cargo', 'profissional')
                ->whereHas('servicos', function($q) use ($servicoId) {
                    $q->where('servicos.id_servico', $servicoId);
                })
                ->get();
        }

        if ($profissionais->isEmpty()) {
            return response()->json([]);
        }

        // Array para armazenar status de cada horário por profissional
        $horariosStatus = []; // hora => [prof1 => ocupado?, prof2 => ocupado?, ...]

        // Para cada profissional, verificar seus horários
        foreach ($profissionais as $prof) {
            $vinculo = $prof->servicos->find($servicoId);
            
            if (!$vinculo) continue;

            // Usar duração passada como parâmetro (múltiplos serviços) ou duração customizada/padrão
            if ($duracao) {
                $duracaoUsada = $duracao;
            } else {
                $duracaoUsada = $vinculo->pivot->duracao_customizada ?? $vinculo->duracao;
            }

            // 2. Busca a Escala de Trabalho do dia deste profissional
            $escala = HorarioTrabalho::where('profissional_id', $prof->id)
                        ->where('dia_semana', $diaSemana)
                        ->first();

            if (!$escala || !$escala->trabalha) continue;

            // 3. Busca Agendamentos e Bloqueios para este dia
            $agendamentos = Agendamento::where('profissional_id', $prof->id)
                ->whereDate('data_hora_inicio', $data)
                ->where('status', '!=', 'cancelado')
                ->get();

            $bloqueios = BloqueioHorario::where(function ($q) use ($prof) {
                $q->whereNull('profissional_id')->orWhere('profissional_id', $prof->id);
            })->whereDate('data_hora_inicio', '<=', $data)
              ->whereDate('data_hora_fim', '>=', $data)
              ->get();

            // 4. Montar horários para este profissional
            $horaAtual = Carbon::parse($data . ' ' . $escala->hora_inicio);
            $horaFimExpediente = Carbon::parse($data . ' ' . $escala->hora_fim);
            $horaAlmocoInicio = Carbon::parse($data . ' ' . $escala->almoco_inicio);
            $horaAlmocoFim = Carbon::parse($data . ' ' . $escala->almoco_fim);

            while ($horaAtual < $horaFimExpediente) {
                $horaFimEstimado = $horaAtual->copy()->addMinutes($duracaoUsada);
                $ocupado = false;

                // Regra A: O serviço termina depois do expediente?
                if ($horaFimEstimado > $horaFimExpediente) {
                    $ocupado = true;
                }

                // Regra B: O serviço invade o horário de almoço?
                if (!$ocupado && $horaAtual < $horaAlmocoFim && $horaFimEstimado > $horaAlmocoInicio) {
                    $ocupado = true;
                }

                // Regra C: Conflita com agendamentos existentes?
                if (!$ocupado) {
                    foreach ($agendamentos as $ag) {
                        $agInicio = Carbon::parse($ag->data_hora_inicio);
                        $agFim = Carbon::parse($ag->data_hora_fim);
                        if ($horaAtual < $agFim && $horaFimEstimado > $agInicio) {
                            $ocupado = true; break;
                        }
                    }
                }

                // Regra D: Conflita com Bloqueios/Feriados?
                if (!$ocupado) {
                    foreach ($bloqueios as $bq) {
                        $bqInicio = Carbon::parse($bq->data_hora_inicio);
                        $bqFim = Carbon::parse($bq->data_hora_fim);
                        if ($horaAtual < $bqFim && $horaFimEstimado > $bqInicio) {
                            $ocupado = true; break;
                        }
                    }
                }

                // Regra E: Ignorar horários no passado
                if ($horaAtual < now()) {
                    $ocupado = true;
                }

                $hora_str = $horaAtual->format('H:i');

                // Inicializar array de status para este horário se não existir
                if (!isset($horariosStatus[$hora_str])) {
                    $horariosStatus[$hora_str] = [];
                }

                // Armazenar status deste profissional para este horário
                $horariosStatus[$hora_str][$prof->id] = $ocupado;

                $horaAtual->addMinutes(30);
            }
        }

        // Formatar resposta: um horário é DISPONÍVEL se AT LEAST ONE profissional está livre
        $resultado = [];
        foreach ($horariosStatus as $hora => $statusPorProf) {
            // Se pelo menos um profissional está livre neste horário
            $temProfissionalLivre = in_array(false, $statusPorProf, true);
            
            $resultado[] = [
                'hora' => $hora,
                'ocupado' => !$temProfissionalLivre // Ocupado só se TODOS estão ocupados
            ];
        }

        // Ordenar por hora
        usort($resultado, function($a, $b) {
            return strcmp($a['hora'], $b['hora']);
        });

        return response()->json($resultado);
    }

    // =========================================================
    // FUNÇÃO MASTER DE SALVAR (INTACTA - PROTEÇÃO OVERBOOKING)
    // =========================================================
    public function store(Request $request)
    {
        // Suporta tanto servico_id (compatibilidade) quanto servicos_ids (múltiplos)
        $servicos_ids = null;
        if ($request->has('servicos_ids') && is_array($request->servicos_ids)) {
            $servicos_ids = $request->servicos_ids;
        } elseif ($request->has('servicos_ids') && is_string($request->servicos_ids)) {
            $servicos_ids = array_map('intval', explode(',', $request->servicos_ids));
        } elseif ($request->has('servico_id')) {
            $servicos_ids = [$request->servico_id];
        }

        // Validação básica
        $request->validate([
            'cliente_id' => ['required','exists:users,id'],
            'profissional_id' => ['required','exists:users,id'],
            'data_hora' => ['required','date','after:now'],
        ], [
            'data_hora.after' => 'O agendamento deve ser para uma data futura.',
        ]);

        if (!$servicos_ids || empty($servicos_ids)) {
            return back()->withErrors(['servicos' => 'Por favor, escolhe pelo menos um serviço.'])->withInput();
        }

        if (auth()->user()->status === 'bloqueado') {
            return back()->withErrors(['erro' => 'Sua conta está bloqueada para novos agendamentos devido ao excesso de faltas. Entre em contato com o suporte.']);
        }

        $profissional = User::findOrFail($request->profissional_id);
        
        // Definindo Início e Fim
        $inicio = Carbon::parse($request->data_hora);
        $diaSemana = $inicio->dayOfWeek;

        // Busca a duração total de todos os serviços
        $duracao = 0;
        $servicosPrincipais = [];
        $valorTotal = 0;
        
        foreach ($servicos_ids as $servicoId) {
            $servico = Servico::findOrFail($servicoId);
            $vinculo = $profissional->servicos->find($servicoId);

            // Verifica se o profissional realmente executa esse serviço
            if (!$vinculo) {
                return back()->withErrors(['servicos' => "Este profissional não realiza o serviço: {$servico->nome}"])->withInput();
            }

            $duracaoServico = $vinculo->pivot->duracao_customizada ?? $servico->duracao;
            $duracao += $duracaoServico;
            $servicosPrincipais[] = $servico;
            $valorTotal += $servico->preco;
        }

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
        if ($horaInicio < $escala->almoco_fim && $horaFim > $escala->almoco_inicio) {
            return back()->withErrors(['data_hora' => 'Este horário coincide ou invade o intervalo de almoço do profissional.'])->withInput();
        }

        // Validação: Bloqueios (Folgas e Feriados) 
        $bloqueios = BloqueioHorario::where(function ($q) use ($request) {
            $q->whereNull('profissional_id')
              ->orWhere('profissional_id', $request->profissional_id);
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
        $resultadoAgendamento = DB::transaction(function () use ($request, $inicio, $fim, $servicos_ids, $servicosPrincipais, $valorTotal) {
            
            // 1. Trava a "agenda" deste profissional
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

            // Se achou conflito, aborta a missão
            if ($conflito) {
                return 'conflito'; 
            }

            // 3. Salvar Agendamento com o primeiro serviço (para compatibilidade)
            $agendamento = Agendamento::create([
                'cliente_id' => $request->cliente_id,
                'profissional_id' => $request->profissional_id,
                'servico_id' => $servicos_ids[0],
                'data_hora_inicio' => $inicio,
                'data_hora_fim' => $fim,
                'status' => 'confirmado',
                'valor_total' => $valorTotal,
            ]);

            // 4. Adicionar serviços na tabela pivot agendamento_servico
            foreach ($servicos_ids as $servicoId) {
                $servico = $servicosPrincipais[array_search($servicoId, $servicos_ids)];
                $agendamento->servicos()->attach($servicoId, [
                    'duracao' => $servico->duracao,
                    'preco' => $servico->preco
                ]);
            }

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

    // =========================================================
    // MODIFICADO PARA O PASSO A PASSO (WIZARD)
    // =========================================================
    public function storeCliente(Request $request)
    {
        // Se a requisição vier do Novo Wizard (separado), juntamos num único campo 'data_hora'
        if ($request->has('data_agendamento') && $request->has('hora_agendamento')) {
            $request->merge([
                'data_hora' => $request->data_agendamento . ' ' . $request->hora_agendamento
            ]);
        }

        // Se vem servicos_ids (múltiplos), converte para array
        if ($request->has('servicos_ids')) {
            $servicos_ids = array_map('intval', explode(',', $request->servicos_ids));
            $request->merge(['servicos_ids' => $servicos_ids]);
        }

        // Injeta o ID do cliente logado por segurança
        $request->merge(['cliente_id' => auth()->id()]);
        
        // Passa a bola para a tua função store() original
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

    public function salvarAvaliacao(Request $request)
    {
        $request->validate([
            'agendamento_id' => 'required|exists:agendamentos,id_agendamento',
            'nota' => 'required|integer|min:1|max:5',
            'comentario' => 'nullable|string|max:500'
        ]);

        $agendamento = Agendamento::findOrFail($request->agendamento_id);

        // Segurança: só o dono do agendamento avalia e só se estiver executado
        if ($agendamento->cliente_id != auth()->id() || $agendamento->status != 'executado') {
            return back()->with('error', 'Ação não permitida.');
        }

        \App\Models\Avaliacao::create([
            'agendamento_id' => $agendamento->id_agendamento,
            'cliente_id' => $agendamento->cliente_id,
            'profissional_id' => $agendamento->profissional_id,
            'nota' => $request->nota,
            'comentario' => $request->comentario
        ]);

        return back()->with('success', 'Obrigado por avaliar nosso serviço!');
    }
}