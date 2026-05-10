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
use App\Services\AgendaService;
use App\Services\ClientePacoteService;
use App\Services\FinanceiroService;
use App\Services\VendaProdutoService;

class AgendamentoController extends Controller
{
    public function index(Request $request)
    {
        // Filtro de período (padrão: mês atual)
        $dataInicio = $request->input('data_inicio', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dataFim = $request->input('data_fim', Carbon::now()->endOfMonth()->format('Y-m-d'));

        // Buscar todos os agendamentos do período
        $agendamentos = Agendamento::with(['cliente', 'profissional', 'servico', 'servicos'])
            ->whereBetween('data_hora_inicio', [
                Carbon::parse($dataInicio)->startOfDay(),
                Carbon::parse($dataFim)->endOfDay()
            ])
            ->orderBy('data_hora_inicio', 'asc')
            ->get();

        // Agrupar por data para o calendário
        $agendamentosPorData = $agendamentos->groupBy(function($item) {
            return $item->data_hora_inicio->format('Y-m-d');
        });

        // Estatísticas
        $totalAgendamentos = $agendamentos->count();
        $executados = $agendamentos->where('status', 'executado')->count();
        $confirmados = $agendamentos->where('status', 'confirmado')->count();
        $cancelados = $agendamentos->where('status', 'cancelado')->count();
        $faltas = $agendamentos->where('status', 'falta')->count();

        // Profissionais e clientes para dropdown
        $profissionais = User::where('cargo', 'profissional')->orderBy('name')->get();
        $clientes = User::where('cargo', 'cliente')->orderBy('name')->get();
        $servicos = Servico::all();

        return view('admin.agenda.index', compact(
            'agendamentos', 'agendamentosPorData', 'dataInicio', 'dataFim',
            'totalAgendamentos', 'executados', 'confirmados', 'cancelados', 'faltas',
            'profissionais', 'clientes', 'servicos'
        ));
    }

    // =========================================================
    // NOVAS FUNÇÃ•ES PARA O AGENDAMENTO PASSO A PASSO (WIZARD)
    // =========================================================

    public function novoAgendamento(Request $request)
    {
        $user = $request->user();

        if ($user && $user->cargo !== 'cliente') {
            $mensagemRestricao = 'Funcionalidade restrita ao cliente. Para agendar como equipe, use a tela de agendar para cliente.';

            if ($user->cargo === 'profissional') {
                return redirect()
                    ->route('profissional.agendar.cliente')
                    ->with('acesso_restrito', $mensagemRestricao);
            }

            if (in_array($user->cargo, ['gerente', 'recepcionista'], true)) {
                return redirect()
                    ->route('admin.agendar.cliente')
                    ->with('acesso_restrito', $mensagemRestricao);
            }

            return redirect()
                ->route('dashboard')
                ->with('acesso_restrito', $mensagemRestricao);
        }

        // Passo 1: Carrega apenas os servicos para a tela inicial
        $servicos = Servico::all();
        $limiteAgendamento = now()->addMonths(3)->endOfDay();
        // Agora usa a nova view com o layout Google Calendar
        return view('cliente.agendar_novo', compact('servicos', 'limiteAgendamento'));
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
            $servicosIds = array_values(array_unique(array_filter(array_map('intval', explode(',', $servicosIdsString)))));
        } elseif ($servicoIdSingle) {
            $servicosIds = [(int) $servicoIdSingle];
        } else {
            return response()->json([]);
        }

        if (count($servicosIds) > AgendaService::MAX_SERVICOS_POR_AGENDAMENTO) {
            return response()->json([
                'message' => 'Escolha no máximo ' . AgendaService::MAX_SERVICOS_POR_AGENDAMENTO . ' serviços por agendamento.',
            ], 422);
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
        if ($dataHora->greaterThan(now()->addMonths(3)->endOfDay())) {
            return response()->json([]);
        }

        $dataHoraFim = $dataHora->copy()->addMinutes($duracao);
        $diaSemana = $dataHora->dayOfWeek;
        $agendaService = app(AgendaService::class);
        
        // Filtra profissionais que estão LIVRES naquele horário
        $profissionaisLivres = $profissionaisCandidatos->filter(function($prof) use ($dataHora, $dataHoraFim, $diaSemana, $servicosIds, $agendaService) {
            
            // Verificar se o profissional trabalha neste dia da semana
            $escala = HorarioTrabalho::where('profissional_id', $prof->id)
                ->where('dia_semana', $diaSemana)
                ->first();
            
            if (!$escala || !$escala->trabalha) {
                return false;
            }
            
            if (
                $dataHora->lt($agendaService->inicioExpediente($escala, $dataHora))
                || $dataHora->gt($agendaService->fimExpediente($escala, $dataHora))
                || $dataHoraFim->gt($agendaService->limiteSaidaExpediente($escala, $dataHora))
            ) {
                return false;
            }
            
            // Horario de almoco agora fica disponivel como atendimento especial com acrescimo.
            // Bloqueio geral (feriado) vira atendimento especial; bloqueio do profissional continua bloqueando.
            $bloqueioProfissional = $agendaService->buscarBloqueioProfissionalConflitante($prof->id, $dataHora, $dataHoraFim);
            if ($bloqueioProfissional) {
                return false;
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
        
        $valorBase = (float) Servico::whereIn('id_servico', $servicosIds)->sum('preco');
        $bloqueioGeral = $agendaService->buscarBloqueioGeralConflitante($dataHora, $dataHoraFim);

        $quantidadeServicos = count($servicosIds);
        $profissionaisComResumo = $profissionaisLivres->map(function ($prof) use ($dataHora, $dataHoraFim, $diaSemana, $agendaService, $bloqueioGeral, $valorBase, $quantidadeServicos) {
            $escala = HorarioTrabalho::where('profissional_id', $prof->id)
                ->where('dia_semana', $diaSemana)
                ->first();
            $invadeAlmoco = $agendaService->invadeAlmoco($escala, $dataHora, $dataHoraFim);
            $excedeSaidaExpediente = $agendaService->excedeSaidaExpediente($escala, $dataHora, $dataHoraFim);
            $dadosValor = $agendaService->calcularAtendimentoEspecial($valorBase, $invadeAlmoco, $bloqueioGeral, $excedeSaidaExpediente, $dataHora, $quantidadeServicos);

            return [
                'id' => $prof->id,
                'name' => $prof->name,
                'valor_base' => $dadosValor['valor_base'],
                'acrescimo_especial' => $dadosValor['acrescimo_especial'],
                'desconto_servicos' => $dadosValor['desconto_servicos'],
                'motivo_desconto' => $dadosValor['motivo_desconto'],
                'base_comissao' => $dadosValor['base_comissao'],
                'valor_total' => $dadosValor['valor_total'],
                'motivo_acrescimo' => $dadosValor['motivo_acrescimo'],
                'percentual_acrescimo' => $agendaService->percentualAtendimentoEspecial($invadeAlmoco, $bloqueioGeral, $excedeSaidaExpediente, $dataHora),
                'invade_almoco' => $invadeAlmoco,
                'excede_saida_expediente' => $excedeSaidaExpediente,
                'feriado_ou_bloqueio' => (bool) $bloqueioGeral,
            ];
        });

        return response()->json($profissionaisComResumo->values());
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
        if ($inicioDia->greaterThan(now()->addMonths(3)->endOfDay())) {
            return response()->json([]);
        }

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

        $agendaService = app(AgendaService::class);

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

            $bloqueios = BloqueioHorario::where('profissional_id', $prof->id)
                ->whereDate('data_hora_inicio', '<=', $data)
                ->whereDate('data_hora_fim', '>=', $data)
                ->get();

            // 4. Montar horários para este profissional
            $horaAtual = Carbon::parse($data . ' ' . $escala->hora_inicio);
            $horaFimExpediente = Carbon::parse($data . ' ' . $escala->hora_fim);
            $horaLimiteSaida = $agendaService->limiteSaidaExpediente($escala, $inicioDia);

            while ($horaAtual <= $horaFimExpediente) {
                $horaFimEstimado = $horaAtual->copy()->addMinutes($duracaoUsada);
                $ocupado = false;

                // Regra A: O serviço pode terminar ate 30 minutos depois do expediente, com acrescimo.
                if ($horaFimEstimado > $horaLimiteSaida) {
                    $ocupado = true;
                }

                // Regra B: almoço não ocupa mais; será atendimento especial com acréscimo.

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

                $bloqueioGeral = $agendaService->buscarBloqueioGeralConflitante($horaAtual, $horaFimEstimado);
                $invadeAlmoco = $agendaService->invadeAlmoco($escala, $horaAtual, $horaFimEstimado);
                $excedeSaidaExpediente = $agendaService->excedeSaidaExpediente($escala, $horaAtual, $horaFimEstimado);
                $percentualAcrescimo = $ocupado ? 0 : $agendaService->percentualAtendimentoEspecial($invadeAlmoco, $bloqueioGeral, $excedeSaidaExpediente, $horaAtual);
                $motivosAcrescimo = [];

                if (!$ocupado && $invadeAlmoco) {
                    $motivosAcrescimo[] = 'horário de almoço';
                }

                if (!$ocupado && $bloqueioGeral) {
                    $motivosAcrescimo[] = $bloqueioGeral->motivo ?: 'feriado/bloqueio geral';
                }

                if (!$ocupado && $excedeSaidaExpediente) {
                    $motivosAcrescimo[] = 'saida do expediente';
                }

                if (!$ocupado && $agendaService->ehFimDeSemana($horaAtual)) {
                    $motivosAcrescimo[] = 'fim de semana';
                }

                $hora_str = $horaAtual->format('H:i');

                // Inicializar array de status para este horário se não existir
                if (!isset($horariosStatus[$hora_str])) {
                    $horariosStatus[$hora_str] = [];
                }

                // Armazenar status deste profissional para este horário
                $horariosStatus[$hora_str][$prof->id] = [
                    'ocupado' => $ocupado,
                    'percentual_acrescimo' => $percentualAcrescimo,
                    'motivos_acrescimo' => $motivosAcrescimo,
                ];

                $horaAtual->addMinutes(30);
            }
        }

        // Formatar resposta: um horário é DISPONÍVEL se AT LEAST ONE profissional está livre
        $resultado = [];
        foreach ($horariosStatus as $hora => $statusPorProf) {
            // Se pelo menos um profissional está livre neste horário
            $profissionaisLivresNoHorario = collect($statusPorProf)->filter(fn ($status) => !$status['ocupado']);
            $temProfissionalLivre = $profissionaisLivresNoHorario->isNotEmpty();
            $percentualAcrescimo = $profissionaisLivresNoHorario->max('percentual_acrescimo') ?? 0;
            $motivosAcrescimo = $profissionaisLivresNoHorario
                ->flatMap(fn ($status) => $status['motivos_acrescimo'])
                ->unique()
                ->values()
                ->all();
            
            $resultado[] = [
                'hora' => $hora,
                'ocupado' => !$temProfissionalLivre,
                'percentual_acrescimo' => $percentualAcrescimo,
                'motivos_acrescimo' => $motivosAcrescimo,
                'atendimento_especial' => $temProfissionalLivre && $percentualAcrescimo > 0,
            ];
        }

        // Ordenar por hora
        usort($resultado, function($a, $b) {
            return strcmp($a['hora'], $b['hora']);
        });

        return response()->json($resultado);
    }

    // =========================================================
    // Função principal de salvar (proteção contra overbooking)
    // =========================================================
    public function store(Request $request)
    {
        // Suporta tanto servico_id (compatibilidade) quanto servicos_ids (múltiplos)
        $servicos_ids = null;
        if ($request->has('servicos_ids') && is_array($request->servicos_ids)) {
            $servicos_ids = array_values(array_unique(array_filter(array_map('intval', $request->servicos_ids))));
        } elseif ($request->has('servicos_ids') && is_string($request->servicos_ids)) {
            $servicos_ids = array_values(array_unique(array_filter(array_map('intval', explode(',', $request->servicos_ids)))));
        } elseif ($request->has('servico_id')) {
            $servicos_ids = [(int) $request->servico_id];
        }

        // Validação básica
        $request->validate([
            'cliente_id' => ['required','exists:users,id'],
            'profissional_id' => ['required','exists:users,id'],
            'data_hora' => ['required','date','after:now','before_or_equal:' . now()->addMonths(3)->endOfDay()->toDateTimeString()],
        ], [
            'data_hora.after' => 'O agendamento deve ser para uma data futura.',
            'data_hora.before_or_equal' => 'O agendamento deve ser feito dentro dos próximos 3 meses.',
        ]);

        if (!$servicos_ids || empty($servicos_ids)) {
            return back()->withErrors(['servicos' => 'Por favor, escolha pelo menos um serviço.'])->withInput();
        }

        if (count($servicos_ids) > AgendaService::MAX_SERVICOS_POR_AGENDAMENTO) {
            return back()->withErrors([
                'servicos' => 'Escolha no máximo ' . AgendaService::MAX_SERVICOS_POR_AGENDAMENTO . ' serviços por agendamento.',
            ])->withInput();
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
        $agendaService = app(AgendaService::class);

        // Validação: Horário de Trabalho e Almoço
        $escala = HorarioTrabalho::where('profissional_id', $profissional->id)
                    ->where('dia_semana', $diaSemana)
                    ->first();

        if (!$escala || !$escala->trabalha) {
            return back()->withErrors(['data_hora' => 'O profissional não trabalha neste dia da semana.'])->withInput();
        }

        if (
            $inicio->lt($agendaService->inicioExpediente($escala, $inicio))
            || $inicio->gt($agendaService->fimExpediente($escala, $inicio))
            || $fim->gt($agendaService->limiteSaidaExpediente($escala, $inicio))
        ) {
            return back()->withErrors(['data_hora' => 'O horário escolhido está fora do expediente permitido do profissional.'])->withInput();
        }

        $invadeAlmoco = $agendaService->invadeAlmoco($escala, $inicio, $fim);
        $excedeSaidaExpediente = $agendaService->excedeSaidaExpediente($escala, $inicio, $fim);

        $bloqueioProfissional = $agendaService->buscarBloqueioProfissionalConflitante($profissional->id, $inicio, $fim);
        if ($bloqueioProfissional) {
            return back()->withErrors(['data_hora' => 'O profissional não atende neste período: ' . $bloqueioProfissional->motivo])->withInput();
        }

        $dadosValor = $agendaService->calcularAtendimentoEspecial(
            (float) $valorTotal,
            $invadeAlmoco,
            $agendaService->buscarBloqueioGeralConflitante($inicio, $fim),
            $excedeSaidaExpediente,
            $inicio,
            count($servicos_ids)
        );
        // =========================================================================
        // BLINDAGEM CONTRA OVERBOOKING (Fila de Banco de Dados)
        // =========================================================================
        $resultadoAgendamento = DB::transaction(function () use ($request, $inicio, $fim, $servicos_ids, $servicosPrincipais, $dadosValor) {
            
            $agendaService = app(AgendaService::class);

            // 1. Trava a "agenda" deste profissional
            User::where('id', $request->profissional_id)->lockForUpdate()->first();

            // 2. Validação: Conflito (Colisão)
            $conflito = $agendaService->existeConflitoAgendamento($request->profissional_id, $inicio, $fim);
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
                'valor_total' => $dadosValor['valor_total'],
                'valor_base' => $dadosValor['valor_base'],
                'acrescimo_especial' => $dadosValor['acrescimo_especial'],
                'desconto_servicos' => $dadosValor['desconto_servicos'],
                'motivo_desconto' => $dadosValor['motivo_desconto'],
                'base_comissao' => $dadosValor['base_comissao'],
                'motivo_acrescimo' => $dadosValor['motivo_acrescimo'],
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

    public function agendarGerencial()
    {
        // Buscar todos os clientes
        $clientes = User::where('cargo', 'cliente')->orderBy('name')->get();
        
        // Buscar todos os profissionais
        $profissionais = User::where('cargo', 'profissional')->orderBy('name')->get();
        
        // Buscar todos os serviços
        $servicos = Servico::all();
        
        return view('agendamentos.gerencial_agendar', compact('clientes', 'profissionais', 'servicos'));
    }

    public function salvarAgendamentoGerencial(Request $request)
    {
        $request->validate([
            'cliente_id' => ['required', 'exists:users,id'],
            'profissional_id' => ['required', 'exists:users,id'],
            'servico_id' => ['required', 'exists:servicos,id_servico'],
            'data' => ['required', 'date', 'after_or_equal:today'],
            'hora' => ['required', 'date_format:H:i'],
        ], [
            'data.after_or_equal' => 'A data deve ser hoje ou uma data futura.',
        ]);

        // Combinar data e hora
        $data_hora = Carbon::parse($request->data . ' ' . $request->hora);
        if ($data_hora->greaterThan(now()->addMonths(3)->endOfDay())) {
            return back()->withErrors(['data' => 'O agendamento deve ser feito dentro dos próximos 3 meses.'])->withInput();
        }

        // Buscar serviço
        $servico = Servico::findOrFail($request->servico_id);
        $profissional = User::findOrFail($request->profissional_id);

        // Verificar se o profissional faz esse serviço
        $vinculo = $profissional->servicos->find($request->servico_id);
        if (!$vinculo) {
            return back()->withErrors(['servico_id' => 'Este profissional não realiza este serviço.'])->withInput();
        }

        // Verificar dia da semana e expediente
        $diaSemana = $data_hora->dayOfWeek;
        $escala = HorarioTrabalho::where('profissional_id', $profissional->id)
                    ->where('dia_semana', $diaSemana)
                    ->first();

        if (!$escala || !$escala->trabalha) {
            return back()->withErrors(['data' => 'O profissional não trabalha neste dia da semana.'])->withInput();
        }

        $duracaoServico = $vinculo->pivot->duracao_customizada ?? $servico->duracao;
        $data_hora_fim = $data_hora->copy()->addMinutes($duracaoServico);

        $agendaService = app(AgendaService::class);
        if (
            $data_hora->lt($agendaService->inicioExpediente($escala, $data_hora))
            || $data_hora->gt($agendaService->fimExpediente($escala, $data_hora))
            || $data_hora_fim->gt($agendaService->limiteSaidaExpediente($escala, $data_hora))
        ) {
            return back()->withErrors(['hora' => 'O horário está fora do expediente permitido do profissional.'])->withInput();
        }
        $invadeAlmoco = $agendaService->invadeAlmoco($escala, $data_hora, $data_hora_fim);
        $excedeSaidaExpediente = $agendaService->excedeSaidaExpediente($escala, $data_hora, $data_hora_fim);

        $bloqueioProfissional = $agendaService->buscarBloqueioProfissionalConflitante($profissional->id, $data_hora, $data_hora_fim);
        if ($bloqueioProfissional) {
            return back()->withErrors(['data' => 'O profissional não atende neste período: ' . $bloqueioProfissional->motivo])->withInput();
        }

        $dadosValor = $agendaService->calcularAtendimentoEspecial(
            (float) $servico->preco,
            $invadeAlmoco,
            $agendaService->buscarBloqueioGeralConflitante($data_hora, $data_hora_fim),
            $excedeSaidaExpediente,
            $data_hora
        );

        // Verificar conflito com agendamentos existentes
        $conflito = $agendaService->existeConflitoAgendamento($profissional->id, $data_hora, $data_hora_fim);
        if ($conflito) {
            return back()->withErrors(['hora' => 'Este horário já está ocupado.'])->withInput();
        }

        $resultadoAgendamento = DB::transaction(function () use ($request, $data_hora, $data_hora_fim, $servico, $agendaService, $dadosValor) {
            User::where('id', $request->profissional_id)->lockForUpdate()->first();

            if ($agendaService->existeConflitoAgendamento($request->profissional_id, $data_hora, $data_hora_fim)) {
                return 'conflito';
            }

            $agendamento = Agendamento::create([
                'cliente_id' => $request->cliente_id,
                'profissional_id' => $request->profissional_id,
                'servico_id' => $request->servico_id,
                'data_hora_inicio' => $data_hora,
                'data_hora_fim' => $data_hora_fim,
                'status' => 'confirmado',
                'valor_total' => $dadosValor['valor_total'],
                'valor_base' => $dadosValor['valor_base'],
                'acrescimo_especial' => $dadosValor['acrescimo_especial'],
                'desconto_servicos' => $dadosValor['desconto_servicos'],
                'motivo_desconto' => $dadosValor['motivo_desconto'],
                'base_comissao' => $dadosValor['base_comissao'],
                'motivo_acrescimo' => $dadosValor['motivo_acrescimo'],
            ]);

            $agendamento->servicos()->attach($request->servico_id, [
                'duracao' => $servico->duracao,
                'preco' => $servico->preco
            ]);

            return 'sucesso';
        });

        if ($resultadoAgendamento === 'conflito') {
            return back()->withErrors(['hora' => 'Este horário acabou de ser reservado por outra pessoa. Escolha outro horário.'])->withInput();
        }

        return redirect()->route('admin.agenda.index')->with('status', 'Agendamento realizado com sucesso!');
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

    public function agendaProfissional(Request $request)
    {
        $filtro = $request->get('filtro', '7');
        
        $query = Agendamento::where('profissional_id', auth()->id())
                            ->with(['cliente.pacotesAtivos.pacote.servicos', 'servico', 'servicos'])
                            ->orderBy('data_hora_inicio', 'asc');
        
        // Aplicar filtro de período
        if ($filtro !== 'todos') {
            $dias = (int) $filtro;
            $dataLimite = Carbon::now()->addDays($dias);
            $query->whereBetween('data_hora_inicio', [
                Carbon::now()->startOfDay(),
                $dataLimite->endOfDay()
            ]);
        }
        
        $agendamentos = $query->get()
                            ->groupBy(fn($data) => \Carbon\Carbon::parse($data->data_hora_inicio)->format('d/m/Y'));

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

    public function indexCliente(Request $request)
    {
        $filtro = $request->get('filtro', '7');
        
        $query = Agendamento::where('cliente_id', auth()->id())
            ->with(['profissional', 'servico', 'servicos', 'avaliacao'])
            ->orderBy('data_hora_inicio', 'asc');
        
        // Aplicar filtro de período
        if ($filtro !== 'todos') {
            $dias = (int) $filtro;
            $dataLimite = Carbon::now()->addDays($dias);
            $query->whereBetween('data_hora_inicio', [
                Carbon::now()->startOfDay(),
                $dataLimite->endOfDay()
            ]);
        }
        
        $agendamentos = $query->get();

        $pacotes = auth()->user()->pacotesAtivos()->with('pacote.servicos')->get();

        return view('cliente.index', compact('agendamentos', 'pacotes'));
    }

    public function cancelarCliente($id_agendamento)
    {
        // Busca o agendamento ou dá erro 404
        $agendamento = Agendamento::findOrFail($id_agendamento);

        if (!$this->usuarioPodeAlterarAgendamento($agendamento)) {
            abort(403, 'Você não tem permissão para cancelar este agendamento.');
        }

        if ($agendamento->status === 'cancelado') {
            return back()->withErrors(['data_hora' => 'Este agendamento já foi cancelado.'])->withInput();
        }

        $agora = Carbon::now();
        $datainicio = Carbon::parse($agendamento->data_hora_inicio);
        $diferencaHoras = $agora->diffInHours($datainicio, false);

        $valorBase = $agendamento->valor_total ?? ($agendamento->servico->preco ?? 0);
        $multaValor = 0;

        if ($diferencaHoras < 24) {
            $multaValor = round($valorBase * 0.05, 2);
        }

        $agendamento->update([
            'status' => 'cancelado',
            'multa_valor' => $multaValor,
        ]);

        if ($multaValor > 0) {
            $multaFormatada = number_format($multaValor, 2, ',', '.');
            return back()->with('status', "Agendamento cancelado. Multa de R$ {$multaFormatada} aplicada.");
        }

        return back()->with('status', 'Agendamento cancelado com sucesso!');
    }

    public function marcarComoExecutado(Request $request, $id_agendamento)
    {
        $agendamento = Agendamento::findOrFail($id_agendamento);
        $cliente = User::findOrFail($agendamento->cliente_id);

        if (!$this->usuarioPodeGerenciarAgendamento($agendamento)) {
            abort(403, 'Você não tem permissão para finalizar este agendamento.');
        }

        if (in_array($agendamento->status, ['executado', 'cancelado', 'falta'], true)) {
            return back()->withErrors(['status' => 'Este agendamento não pode ser finalizado no status atual.']);
        }

        // Iniciamos uma transação para garantir que ou faz TUDO ou não faz NADA
        return DB::transaction(function () use ($request, $agendamento, $cliente) { 
            $vendaProdutoService = app(VendaProdutoService::class);
            
            // 1. Verificação Prévia de Estoque
            if ($request->has('produtos')) {
                $erroEstoque = $vendaProdutoService->validarEstoque($request->produtos);

                if ($erroEstoque) {
                    return back()->withErrors(['estoque' => $erroEstoque])->withInput();
                }
            }
            // Lógica de Pacotes
            if ($request->has('usar_pacote') && $request->usar_pacote != null) {
                try {
                    $clientePacote = app(ClientePacoteService::class)->consumirSessao(
                        (int) $request->usar_pacote,
                        (int) $agendamento->cliente_id,
                        (int) $agendamento->servico_id
                    );
                } catch (\RuntimeException $exception) {
                    return back()->withErrors(['usar_pacote' => $exception->getMessage()])->withInput();
                }

                // Zera o valor para não somar no caixa do salão (já foi pago na compra)
                $agendamento->valor_total = 0; 
                
                // Adiciona um aviso na observação
                $obsPacote = " (Abatido 1 sessão do pacote: " . $clientePacote->pacote->nome . ")";
                $request->merge(['observacao' => $request->input('observacao') . $obsPacote]);
            }

 
            // Fidelidade: só ganha ponto/desconto se não estiver usando pacote.
            if ($request->has('usar_pacote') && $request->usar_pacote != null) {
                $mensagem = 'Sessão de pacote concluída com sucesso!';
            } else {
                if ($cliente->contador_fidelidade == 5) {
                    // Aplica o desconto de 50%
                    $agendamento->valor_total = $agendamento->valor_total * 0.5;

                    $cliente->contador_fidelidade = 0;
                    $cliente->save();
                    
                    $mensagem = 'Atendimento concluído! O cliente ganhou 50% de desconto pela fidelidade! ';
                } else {
                    // Ganha mais um "selo"
                    $cliente->increment('contador_fidelidade');
                    $mensagem = 'Atendimento concluído com sucesso!';
                }
            }
            
            // Atenção: IMPORTANTE: A comissão SEMPRE deve ser calculada sobre o PREÇO BASE do serviço
            // Descontos dados pelo salão (fidelidade, pacotes, etc.) não afetam a comissão do profissional.
            // O profissional recebe sua comissão sobre o preço original, não sobre o valor com desconto.
            
            $financeiroService = app(FinanceiroService::class);
            $porcentagemComissao = FinanceiroService::COMISSAO_SERVICO_PERCENTUAL;
            
            // Usa SEMPRE o preço base do serviço, NUNCA o valor_total (que pode ter descontos)
            $baseComissao = (float) ($agendamento->base_comissao ?? (($agendamento->valor_base ?? $agendamento->servico->preco) + ($agendamento->acrescimo_especial ?? 0)));
            $valorComissao = $financeiroService->calcularComissaoServico($baseComissao);
            
            // Exemplo: Se o serviço custa R$50,00 e ganhou desconto de 50%, o cliente paga R$25,00
            // mas o profissional recebe comissão de R$25,00 (50% de R$50,00), não R$12,50

            // 3. Se chegou aqui, tudo certo! Mudamos o status do agendamento.
            $agendamento->status = 'executado';
            $agendamento->obs = $request->input('observacao');
            $agendamento->valor_comissao = $valorComissao; // Armazena o valor da comissão
            $agendamento->comissao_paga_percentual = $porcentagemComissao; // Armazena a porcentagem da comissão
            $agendamento->save();

            // 4. Registrar as Vendas e Baixar Estoque
            if ($request->has('produtos')) {
                $vendaProdutoService->registrarVendas(auth()->id(), $request->produtos);
            }

            // 5. Retornamos usando a $mensagem dinâmica que criamos ali em cima!
            return redirect()->route('profissional.agenda')->with('status', $mensagem);
        });
    }

    public function confirmarPresenca($id)
    {
        $agendamento = Agendamento::findOrFail($id);

        if (!$this->usuarioPodeAlterarAgendamento($agendamento)) {
            abort(403, 'Você não tem permissão para confirmar presença neste agendamento.');
        }

        $toleranciaMinutos = 15;
        $inicio = Carbon::parse($agendamento->data_hora_inicio);
        $limiteCheckin = $inicio->copy()->addMinutes($toleranciaMinutos);

        if (now()->greaterThan($limiteCheckin)) {
            return back()->withErrors([
                'presenca' => "Confirmação de presença indisponível: tolerância de {$toleranciaMinutos} minutos excedida."
            ]);
        }

        // Se o status for 'confirmado', mudamos para 'presente'
        if ($agendamento->status == 'confirmado') {
            $agendamento->status = 'presente';
            $agendamento->save();
        }

        return back()->with('status', 'Presença confirmada com sucesso!');
    }

    public function marcarFalta($id)
    {
        $agendamento = Agendamento::findOrFail($id);

        if (!$this->usuarioPodeGerenciarAgendamento($agendamento)) {
            abort(403, 'Você não tem permissão para marcar falta neste agendamento.');
        }

        if (in_array($agendamento->status, ['executado', 'cancelado'], true)) {
            return back()->withErrors(['status' => 'Não é possível marcar falta em um agendamento executado ou cancelado.']);
        }

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

    private function usuarioPodeAlterarAgendamento(Agendamento $agendamento): bool
    {
        $usuario = auth()->user();

        if (!$usuario) {
            return false;
        }

        if ($agendamento->cliente_id === $usuario->id) {
            return true;
        }

        return $this->usuarioPodeGerenciarAgendamento($agendamento);
    }

    private function usuarioPodeGerenciarAgendamento(Agendamento $agendamento): bool
    {
        $usuario = auth()->user();

        if (!$usuario) {
            return false;
        }

        if (in_array($usuario->cargo, ['gerente', 'recepcionista'], true)) {
            return true;
        }

        return $usuario->cargo === 'profissional' && $agendamento->profissional_id === $usuario->id;
    }
}

