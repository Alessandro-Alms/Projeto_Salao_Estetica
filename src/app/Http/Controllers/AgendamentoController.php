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
use App\Services\PagamentoService;
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
            ->get();

        $agora = Carbon::now();

        // Agendamentos futuros: do mais próximo para o mais distante
        $proximos = $agendamentos
            ->filter(function ($agendamento) use ($agora) {
                return $agendamento->data_hora_inicio->greaterThanOrEqualTo($agora);
            })
            ->sortBy('data_hora_inicio');

        // Agendamentos passados: ficam embaixo, do mais recente para o mais antigo
        $passados = $agendamentos
            ->filter(function ($agendamento) use ($agora) {
                return $agendamento->data_hora_inicio->lessThan($agora);
            })
            ->sortByDesc('data_hora_inicio');

        // Junta primeiro os próximos, depois os passados
        $agendamentos = $proximos->concat($passados)->values();

        // Agrupar por data para o calendário mantendo essa ordem
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
            $mensagemRestricao = 'Opcao restrita a clientes e cargos gerenciais.';

            if ($user->cargo === 'profissional') {
                return redirect()
                    ->route('profissional.agenda')
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

        $profissionaisIds = $profissionaisCandidatos->pluck('id');
        if ($profissionaisIds->isEmpty()) {
            return response()->json([]);
        }

        $escalaPorProfissional = HorarioTrabalho::whereIn('profissional_id', $profissionaisIds)
            ->where('dia_semana', $diaSemana)
            ->get()
            ->keyBy('profissional_id');

        $agendamentosConflitantes = Agendamento::whereIn('profissional_id', $profissionaisIds)
            ->where('status', '!=', 'cancelado')
            ->where('data_hora_inicio', '<', $dataHoraFim)
            ->where('data_hora_fim', '>', $dataHora)
            ->get(['profissional_id', 'data_hora_inicio', 'data_hora_fim'])
            ->groupBy('profissional_id');

        $bloqueiosConflitantes = BloqueioHorario::whereIn('profissional_id', $profissionaisIds)
            ->where('data_hora_inicio', '<', $dataHoraFim)
            ->where('data_hora_fim', '>', $dataHora)
            ->get(['profissional_id', 'data_hora_inicio', 'data_hora_fim'])
            ->groupBy('profissional_id');

        // Filtra profissionais que estão LIVRES naquele horário
        $profissionaisLivres = $profissionaisCandidatos->filter(function ($prof) use ($dataHora, $dataHoraFim, $diaSemana, $agendaService, $escalaPorProfissional, $agendamentosConflitantes, $bloqueiosConflitantes) {
            $escala = $escalaPorProfissional->get($prof->id);

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

            if ($bloqueiosConflitantes->get($prof->id)?->isNotEmpty()) {
                return false;
            }

            if ($agendamentosConflitantes->get($prof->id)?->isNotEmpty()) {
                return false;
            }

            return true;
        });
        
        $valorBase = (float) Servico::whereIn('id_servico', $servicosIds)->sum('preco');
        $bloqueioGeral = $agendaService->buscarBloqueioGeralConflitante($dataHora, $dataHoraFim);

        $quantidadeServicos = count($servicosIds);
        $profissionaisComResumo = $profissionaisLivres->map(function ($prof) use ($dataHora, $dataHoraFim, $agendaService, $bloqueioGeral, $valorBase, $quantidadeServicos, $escalaPorProfissional) {
            $escala = $escalaPorProfissional->get($prof->id);
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
        $servicosIdsString = $request->servicos_ids;
        $servicoId = $request->servico_id;
        $profissionalId = $request->profissional_id; // Opcional - se nao tiver, busca de todos
        $duracao = (int) $request->duracao; // Opcional - duracao customizada ou total enviada pelo wizard

        if ($servicosIdsString) {
            $servicosIds = array_values(array_unique(array_filter(array_map('intval', explode(',', $servicosIdsString)))));
        } elseif ($servicoId) {
            $servicosIds = [(int) $servicoId];
        } else {
            return response()->json([]);
        }

        if (count($servicosIds) > AgendaService::MAX_SERVICOS_POR_AGENDAMENTO) {
            return response()->json([
                'message' => 'Escolha no maximo ' . AgendaService::MAX_SERVICOS_POR_AGENDAMENTO . ' servicos por agendamento.',
            ], 422);
        }

        if (Servico::whereIn('id_servico', $servicosIds)->count() !== count($servicosIds)) {
            return response()->json([]);
        }

        $inicioDia = Carbon::parse($data)->startOfDay();
        if ($inicioDia->greaterThan(now()->addMonths(3)->endOfDay())) {
            return response()->json([]);
        }

        $diaSemana = $inicioDia->dayOfWeek;

        if ($profissionalId) {
            $profissionalQuery = User::where('id', $profissionalId)
                ->where('cargo', 'profissional')
                ->with(['servicos' => function($q) use ($servicosIds) {
                    $q->whereIn('servicos.id_servico', $servicosIds);
                }]);

            foreach ($servicosIds as $idServicoSelecionado) {
                $profissionalQuery->whereHas('servicos', function($q) use ($idServicoSelecionado) {
                    $q->where('servicos.id_servico', $idServicoSelecionado);
                });
            }

            $profissional = $profissionalQuery->first();

            if (!$profissional || $profissional->servicos->count() !== count($servicosIds)) {
                return response()->json([]);
            }

            $profissionais = collect([$profissional]);
        } else {
            $profissionaisQuery = User::where('cargo', 'profissional')
                ->with(['servicos' => function($q) use ($servicosIds) {
                    $q->whereIn('servicos.id_servico', $servicosIds);
                }]);

            foreach ($servicosIds as $idServicoSelecionado) {
                $profissionaisQuery->whereHas('servicos', function($q) use ($idServicoSelecionado) {
                    $q->where('servicos.id_servico', $idServicoSelecionado);
                });
            }

            $profissionais = $profissionaisQuery->get();
        }

        if ($profissionais->isEmpty()) {
            return response()->json([]);
        }

        $agendaService = app(AgendaService::class);
        $profissionaisIds = $profissionais->pluck('id');
        $fimDia = $inicioDia->copy()->endOfDay();
        $agora = now();

        $escalaPorProfissional = HorarioTrabalho::whereIn('profissional_id', $profissionaisIds)
            ->where('dia_semana', $diaSemana)
            ->get()
            ->keyBy('profissional_id');

        $agendamentosPorProfissional = Agendamento::whereIn('profissional_id', $profissionaisIds)
            ->whereDate('data_hora_inicio', $inicioDia->toDateString())
            ->where('status', '!=', 'cancelado')
            ->get()
            ->groupBy('profissional_id');

        $bloqueiosPorProfissional = BloqueioHorario::whereIn('profissional_id', $profissionaisIds)
            ->where('data_hora_inicio', '<=', $fimDia)
            ->where('data_hora_fim', '>=', $inicioDia)
            ->get()
            ->groupBy('profissional_id');

        $bloqueiosGerais = BloqueioHorario::whereNull('profissional_id')
            ->where('data_hora_inicio', '<=', $fimDia)
            ->where('data_hora_fim', '>=', $inicioDia)
            ->get();

        $bloqueioGeralConflitante = function (Carbon $inicio, Carbon $fim) use ($bloqueiosGerais) {
            foreach ($bloqueiosGerais as $bloqueio) {
                if ($bloqueio->data_hora_inicio < $fim && $bloqueio->data_hora_fim > $inicio) {
                    return $bloqueio;
                }
            }

            return null;
        };

        $horariosStatus = [];

        foreach ($profissionais as $prof) {
            if ($prof->servicos->count() !== count($servicosIds)) {
                continue;
            }

            $duracaoUsada = $duracao ?: $prof->servicos->sum(function ($servico) {
                return $servico->pivot->duracao_customizada ?? $servico->duracao;
            });

            $escala = $escalaPorProfissional->get($prof->id);

            if (!$escala || !$escala->trabalha) continue;

            $agendamentos = $agendamentosPorProfissional->get($prof->id, collect());
            $bloqueios = $bloqueiosPorProfissional->get($prof->id, collect());

            $horaAtual = Carbon::parse($data . ' ' . $escala->hora_inicio);
            $horaFimExpediente = Carbon::parse($data . ' ' . $escala->hora_fim);
            $horaLimiteSaida = $agendaService->limiteSaidaExpediente($escala, $inicioDia);

            while ($horaAtual <= $horaFimExpediente) {
                $horaFimEstimado = $horaAtual->copy()->addMinutes($duracaoUsada);
                $ocupado = false;

                if ($horaFimEstimado > $horaLimiteSaida) {
                    $ocupado = true;
                }

                if (!$ocupado) {
                    foreach ($agendamentos as $ag) {
                        $agInicio = Carbon::parse($ag->data_hora_inicio);
                        $agFim = Carbon::parse($ag->data_hora_fim);
                        if ($horaAtual < $agFim && $horaFimEstimado > $agInicio) {
                            $ocupado = true; break;
                        }
                    }
                }

                if (!$ocupado) {
                    foreach ($bloqueios as $bq) {
                        $bqInicio = Carbon::parse($bq->data_hora_inicio);
                        $bqFim = Carbon::parse($bq->data_hora_fim);
                        if ($horaAtual < $bqFim && $horaFimEstimado > $bqInicio) {
                            $ocupado = true; break;
                        }
                    }
                }

                if ($horaAtual < $agora) {
                    $ocupado = true;
                }

                $bloqueioGeral = $bloqueioGeralConflitante($horaAtual, $horaFimEstimado);
                $invadeAlmoco = $agendaService->invadeAlmoco($escala, $horaAtual, $horaFimEstimado);
                $excedeSaidaExpediente = $agendaService->excedeSaidaExpediente($escala, $horaAtual, $horaFimEstimado);
                $percentualAcrescimo = $ocupado ? 0 : $agendaService->percentualAtendimentoEspecial($invadeAlmoco, $bloqueioGeral, $excedeSaidaExpediente, $horaAtual);
                $motivosAcrescimo = [];

                if (!$ocupado && $invadeAlmoco) {
                    $motivosAcrescimo[] = 'horario de almoco';
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

                if (!isset($horariosStatus[$hora_str])) {
                    $horariosStatus[$hora_str] = [];
                }

                $horariosStatus[$hora_str][$prof->id] = [
                    'ocupado' => $ocupado,
                    'percentual_acrescimo' => $percentualAcrescimo,
                    'motivos_acrescimo' => $motivosAcrescimo,
                ];

                $horaAtual->addMinutes(30);
            }
        }

        $resultado = [];
        foreach ($horariosStatus as $hora => $statusPorProf) {
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

        usort($resultado, function($a, $b) {
            return strcmp($a['hora'], $b['hora']);
        });

        return response()->json($resultado);
    }

    public function disponibilidadeProfissionais(Request $request)
    {
        $this->autorizarConsultaDisponibilidade($request);

        $limiteAgendamento = now()->addMonths(3)->endOfDay();

        return view('agendamentos.disponibilidade_profissionais', compact('limiteAgendamento'));
    }

    public function getProfissionaisDisponiveisAjax(Request $request)
    {
        $this->autorizarConsultaDisponibilidade($request);

        $dados = $request->validate([
            'data' => ['required', 'date', 'after_or_equal:today'],
            'hora' => ['nullable', 'date_format:H:i'],
            'duracao' => ['nullable', 'integer', 'min:15', 'max:480'],
        ], [
            'data.after_or_equal' => 'Escolha hoje ou uma data futura.',
        ]);

        $duracao = (int) ($dados['duracao'] ?? 30);
        $inicioDia = Carbon::parse($dados['data'])->startOfDay();
        $fimDia = $inicioDia->copy()->endOfDay();
        $horaSelecionada = $dados['hora'] ?? null;
        $inicio = $horaSelecionada ? Carbon::parse($dados['data'] . ' ' . $horaSelecionada) : $inicioDia->copy();

        if ($inicioDia->greaterThan(now()->addMonths(3)->endOfDay())) {
            return response()->json([
                'profissionais' => [],
                'total' => 0,
                'message' => 'Escolha uma data dentro dos proximos 3 meses.',
            ]);
        }

        if ($horaSelecionada && $inicio->lessThan(now())) {
            return response()->json([
                'profissionais' => [],
                'total' => 0,
                'message' => 'Escolha um horario futuro dentro dos proximos 3 meses.',
            ]);
        }

        $fim = $horaSelecionada ? $inicio->copy()->addMinutes($duracao) : null;
        $diaSemana = $inicioDia->dayOfWeek;
        $agendaService = app(AgendaService::class);

        $profissionais = User::where('cargo', 'profissional')
            ->where('status', 'ativo')
            ->with('servicos:id_servico,nome')
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'telefone']);

        if ($profissionais->isEmpty()) {
            return response()->json([
                'profissionais' => [],
                'total' => 0,
            ]);
        }

        $profissionaisIds = $profissionais->pluck('id');

        $escalaPorProfissional = HorarioTrabalho::whereIn('profissional_id', $profissionaisIds)
            ->where('dia_semana', $diaSemana)
            ->get()
            ->keyBy('profissional_id');

        if (!$horaSelecionada) {
            $agendamentosPorProfissional = Agendamento::whereIn('profissional_id', $profissionaisIds)
                ->where('status', '!=', 'cancelado')
                ->where('data_hora_inicio', '<', $fimDia)
                ->where('data_hora_fim', '>', $inicioDia)
                ->get(['profissional_id', 'data_hora_inicio', 'data_hora_fim'])
                ->groupBy('profissional_id');

            $bloqueiosPorProfissional = BloqueioHorario::whereIn('profissional_id', $profissionaisIds)
                ->where('data_hora_inicio', '<', $fimDia)
                ->where('data_hora_fim', '>', $inicioDia)
                ->get(['profissional_id', 'data_hora_inicio', 'data_hora_fim'])
                ->groupBy('profissional_id');

            $bloqueiosGerais = BloqueioHorario::whereNull('profissional_id')
                ->where('data_hora_inicio', '<', $fimDia)
                ->where('data_hora_fim', '>', $inicioDia)
                ->get();

            $disponiveisDia = $profissionais
                ->map(function ($profissional) use ($duracao, $agendaService, $escalaPorProfissional, $agendamentosPorProfissional, $bloqueiosPorProfissional, $bloqueiosGerais, $inicioDia) {
                    $escala = $escalaPorProfissional->get($profissional->id);

                    if (!$escala || !$escala->trabalha) {
                        return null;
                    }

                    $horaAtual = $agendaService->inicioExpediente($escala, $inicioDia);
                    $fimExpediente = $agendaService->fimExpediente($escala, $inicioDia);
                    $limiteSaida = $agendaService->limiteSaidaExpediente($escala, $inicioDia);
                    $horarios = [];
                    $maiorAcrescimo = 0;

                    while ($horaAtual->copy()->addMinutes($duracao)->lte($limiteSaida) && $horaAtual->lte($fimExpediente)) {
                        $horaFim = $horaAtual->copy()->addMinutes($duracao);
                        $livre = !$horaAtual->lt(now());

                        if ($livre && $agendamentosPorProfissional->get($profissional->id)?->contains(function ($agendamento) use ($horaAtual, $horaFim) {
                            $agInicio = Carbon::parse($agendamento->data_hora_inicio);
                            $agFim = Carbon::parse($agendamento->data_hora_fim);
                            return $horaAtual->lt($agFim) && $horaFim->gt($agInicio);
                        })) {
                            $livre = false;
                        }

                        if ($livre && $bloqueiosPorProfissional->get($profissional->id)?->contains(function ($bloqueio) use ($horaAtual, $horaFim) {
                            $blInicio = Carbon::parse($bloqueio->data_hora_inicio);
                            $blFim = Carbon::parse($bloqueio->data_hora_fim);
                            return $horaAtual->lt($blFim) && $horaFim->gt($blInicio);
                        })) {
                            $livre = false;
                        }

                        $bloqueioGeral = null;
                        foreach ($bloqueiosGerais as $bloqueio) {
                            if ($bloqueio->data_hora_inicio < $horaFim && $bloqueio->data_hora_fim > $horaAtual) {
                                $bloqueioGeral = $bloqueio;
                                break;
                            }
                        }

                        if ($livre) {
                            $invadeAlmoco = $agendaService->invadeAlmoco($escala, $horaAtual, $horaFim);
                            $excedeSaidaExpediente = $agendaService->excedeSaidaExpediente($escala, $horaAtual, $horaFim);
                            $percentualAcrescimo = $agendaService->percentualAtendimentoEspecial($invadeAlmoco, $bloqueioGeral, $excedeSaidaExpediente, $horaAtual);
                            $maiorAcrescimo = max($maiorAcrescimo, $percentualAcrescimo);

                            $horarios[] = [
                                'hora' => $horaAtual->format('H:i'),
                                'atendimento_especial' => $percentualAcrescimo > 0,
                                'percentual_acrescimo' => $percentualAcrescimo,
                            ];
                        }

                        $horaAtual->addMinutes(30);
                    }

                    if (empty($horarios)) {
                        return null;
                    }

                    return [
                        'id' => $profissional->id,
                        'name' => $profissional->name,
                        'email' => $profissional->email,
                        'telefone' => $profissional->telefone,
                        'servicos' => $profissional->servicos->pluck('nome')->values(),
                        'horarios_disponiveis' => $horarios,
                        'atendimento_especial' => $maiorAcrescimo > 0,
                        'percentual_acrescimo' => $maiorAcrescimo,
                    ];
                })
                ->filter()
                ->values();

            return response()->json([
                'profissionais' => $disponiveisDia,
                'total' => $disponiveisDia->count(),
                'modo' => 'dia',
            ]);
        }

        $agendamentosConflitantes = Agendamento::whereIn('profissional_id', $profissionaisIds)
            ->where('status', '!=', 'cancelado')
            ->where('data_hora_inicio', '<', $fim)
            ->where('data_hora_fim', '>', $inicio)
            ->get(['profissional_id'])
            ->groupBy('profissional_id');

        $bloqueiosConflitantes = BloqueioHorario::whereIn('profissional_id', $profissionaisIds)
            ->where('data_hora_inicio', '<', $fim)
            ->where('data_hora_fim', '>', $inicio)
            ->get(['profissional_id'])
            ->groupBy('profissional_id');

        $bloqueioGeral = $agendaService->buscarBloqueioGeralConflitante($inicio, $fim);

        $disponiveis = $profissionais
            ->filter(function ($profissional) use ($inicio, $fim, $agendaService, $escalaPorProfissional, $agendamentosConflitantes, $bloqueiosConflitantes) {
                $escala = $escalaPorProfissional->get($profissional->id);

                if (!$escala || !$escala->trabalha) {
                    return false;
                }

                if (
                    $inicio->lt($agendaService->inicioExpediente($escala, $inicio))
                    || $inicio->gt($agendaService->fimExpediente($escala, $inicio))
                    || $fim->gt($agendaService->limiteSaidaExpediente($escala, $inicio))
                ) {
                    return false;
                }

                if ($agendamentosConflitantes->get($profissional->id)?->isNotEmpty()) {
                    return false;
                }

                if ($bloqueiosConflitantes->get($profissional->id)?->isNotEmpty()) {
                    return false;
                }

                return true;
            })
            ->map(function ($profissional) use ($inicio, $fim, $agendaService, $escalaPorProfissional, $bloqueioGeral) {
                $escala = $escalaPorProfissional->get($profissional->id);
                $invadeAlmoco = $agendaService->invadeAlmoco($escala, $inicio, $fim);
                $excedeSaidaExpediente = $agendaService->excedeSaidaExpediente($escala, $inicio, $fim);
                $percentualAcrescimo = $agendaService->percentualAtendimentoEspecial($invadeAlmoco, $bloqueioGeral, $excedeSaidaExpediente, $inicio);

                return [
                    'id' => $profissional->id,
                    'name' => $profissional->name,
                    'email' => $profissional->email,
                    'telefone' => $profissional->telefone,
                    'servicos' => $profissional->servicos->pluck('nome')->values(),
                    'atendimento_especial' => $percentualAcrescimo > 0,
                    'percentual_acrescimo' => $percentualAcrescimo,
                ];
            })
            ->values();

        return response()->json([
            'profissionais' => $disponiveis,
            'total' => $disponiveis->count(),
        ]);
    }

    public function getHorariosDisponibilidadeAjax(Request $request)
    {
        $this->autorizarConsultaDisponibilidade($request);

        $dados = $request->validate([
            'data' => ['required', 'date', 'after_or_equal:today'],
            'duracao' => ['nullable', 'integer', 'min:15', 'max:480'],
        ], [
            'data.after_or_equal' => 'Escolha hoje ou uma data futura.',
        ]);

        $duracao = (int) ($dados['duracao'] ?? 30);
        $inicioDia = Carbon::parse($dados['data'])->startOfDay();

        if ($inicioDia->greaterThan(now()->addMonths(3)->endOfDay())) {
            return response()->json([]);
        }

        $diaSemana = $inicioDia->dayOfWeek;
        $agendaService = app(AgendaService::class);

        $profissionais = User::where('cargo', 'profissional')
            ->where('status', 'ativo')
            ->orderBy('name')
            ->get(['id', 'name']);

        if ($profissionais->isEmpty()) {
            return response()->json([]);
        }

        $profissionaisIds = $profissionais->pluck('id');
        $escalaPorProfissional = HorarioTrabalho::whereIn('profissional_id', $profissionaisIds)
            ->where('dia_semana', $diaSemana)
            ->get()
            ->keyBy('profissional_id');

        $escalasValidas = $escalaPorProfissional->filter(fn ($escala) => $escala && $escala->trabalha);
        if ($escalasValidas->isEmpty()) {
            return response()->json([]);
        }

        $fimDia = $inicioDia->copy()->endOfDay();
        $agendamentosConflitantes = Agendamento::whereIn('profissional_id', $profissionaisIds)
            ->where('status', '!=', 'cancelado')
            ->where('data_hora_inicio', '<', $fimDia)
            ->where('data_hora_fim', '>', $inicioDia)
            ->get(['profissional_id', 'data_hora_inicio', 'data_hora_fim'])
            ->groupBy('profissional_id');

        $bloqueiosConflitantes = BloqueioHorario::whereIn('profissional_id', $profissionaisIds)
            ->where('data_hora_inicio', '<', $fimDia)
            ->where('data_hora_fim', '>', $inicioDia)
            ->get(['profissional_id', 'data_hora_inicio', 'data_hora_fim'])
            ->groupBy('profissional_id');

        $bloqueiosGerais = BloqueioHorario::whereNull('profissional_id')
            ->where('data_hora_inicio', '<=', $fimDia)
            ->where('data_hora_fim', '>=', $inicioDia)
            ->get();

        $inicioRange = $escalasValidas->map(function ($escala) use ($dados) {
            return Carbon::parse($dados['data'] . ' ' . $escala->hora_inicio);
        })->sort()->first();

        $fimRange = $escalasValidas->map(function ($escala) use ($dados) {
            return Carbon::parse($dados['data'] . ' ' . $escala->hora_fim);
        })->sort()->last();

        if (!$inicioRange || !$fimRange) {
            return response()->json([]);
        }

        $resultado = [];
        $horaAtual = $inicioRange->copy();

        while ($horaAtual->copy()->addMinutes($duracao)->lte($fimRange)) {
            $horaFim = $horaAtual->copy()->addMinutes($duracao);
            $profissionaisLivres = collect();
            $temProfissionalNoHorario = false;

            foreach ($profissionais as $profissional) {
                $escala = $escalaPorProfissional->get($profissional->id);

                if (!$escala || !$escala->trabalha) {
                    continue;
                }

                $limiteInicio = $agendaService->inicioExpediente($escala, $horaAtual);
                $limiteFim = $agendaService->limiteSaidaExpediente($escala, $horaAtual);

                if (
                    $horaAtual->lt($limiteInicio)
                    || $horaAtual->gt($agendaService->fimExpediente($escala, $horaAtual))
                    || $horaFim->gt($limiteFim)
                ) {
                    continue;
                }

                $temProfissionalNoHorario = true;

                if ($horaAtual->lt(now())) {
                    continue;
                }

                if ($agendamentosConflitantes->get($profissional->id)?->contains(function ($agendamento) use ($horaAtual, $horaFim) {
                    $agInicio = Carbon::parse($agendamento->data_hora_inicio);
                    $agFim = Carbon::parse($agendamento->data_hora_fim);
                    return $horaAtual->lt($agFim) && $horaFim->gt($agInicio);
                })) {
                    continue;
                }

                if ($bloqueiosConflitantes->get($profissional->id)?->contains(function ($bloqueio) use ($horaAtual, $horaFim) {
                    $blInicio = Carbon::parse($bloqueio->data_hora_inicio);
                    $blFim = Carbon::parse($bloqueio->data_hora_fim);
                    return $horaAtual->lt($blFim) && $horaFim->gt($blInicio);
                })) {
                    continue;
                }

                $bloqueioGeral = null;
                foreach ($bloqueiosGerais as $bloqueio) {
                    if ($bloqueio->data_hora_inicio < $horaFim && $bloqueio->data_hora_fim > $horaAtual) {
                        $bloqueioGeral = $bloqueio;
                        break;
                    }
                }

                $invadeAlmoco = $agendaService->invadeAlmoco($escala, $horaAtual, $horaFim);
                $excedeSaidaExpediente = $agendaService->excedeSaidaExpediente($escala, $horaAtual, $horaFim);
                $percentualAcrescimo = $agendaService->percentualAtendimentoEspecial($invadeAlmoco, $bloqueioGeral, $excedeSaidaExpediente, $horaAtual);

                $profissionaisLivres->push([
                    'id' => $profissional->id,
                    'percentual_acrescimo' => $percentualAcrescimo,
                ]);
            }

            if ($temProfissionalNoHorario) {
                $percentualAcrescimo = $profissionaisLivres->max('percentual_acrescimo') ?? 0;

                if ($profissionaisLivres->isEmpty()) {
                    $horaAtual->addMinutes(30);
                    continue;
                }

                $resultado[] = [
                    'hora' => $horaAtual->format('H:i'),
                    'ocupado' => false,
                    'percentual_acrescimo' => $percentualAcrescimo,
                    'atendimento_especial' => $percentualAcrescimo > 0,
                ];
            }

            $horaAtual->addMinutes(30);
        }

        return response()->json($resultado);
    }

    private function autorizarConsultaDisponibilidade(Request $request): void
    {
        if (!in_array($request->user()?->cargo, ['cliente', 'gerente', 'recepcionista'], true)) {
            abort(403, 'Voce nao tem permissao para consultar a disponibilidade dos profissionais.');
        }
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
        $servicosIds = [];
        if ($request->has('servicos_ids') && is_array($request->servicos_ids)) {
            $servicosIds = array_values(array_unique(array_filter(array_map('intval', $request->servicos_ids))));
        } elseif ($request->has('servicos_ids') && is_string($request->servicos_ids)) {
            $servicosIds = array_values(array_unique(array_filter(array_map('intval', explode(',', $request->servicos_ids)))));
        } elseif ($request->has('servico_id')) {
            $servicosIds = [(int) $request->servico_id];
        }

        $request->validate([
            'cliente_id' => ['required', 'exists:users,id'],
            'profissional_id' => ['required', 'exists:users,id'],
            'data' => ['required', 'date', 'after_or_equal:today'],
            'hora' => ['required', 'date_format:H:i'],
        ], [
            'data.after_or_equal' => 'A data deve ser hoje ou uma data futura.',
        ]);

        if (empty($servicosIds)) {
            return back()->withErrors(['servicos' => 'Por favor, escolha pelo menos um servico.'])->withInput();
        }

        if (count($servicosIds) > AgendaService::MAX_SERVICOS_POR_AGENDAMENTO) {
            return back()->withErrors([
                'servicos' => 'Escolha no maximo ' . AgendaService::MAX_SERVICOS_POR_AGENDAMENTO . ' servicos por agendamento.',
            ])->withInput();
        }

        if (Servico::whereIn('id_servico', $servicosIds)->count() !== count($servicosIds)) {
            return back()->withErrors(['servicos' => 'Um ou mais servicos selecionados nao foram encontrados.'])->withInput();
        }

        // Combinar data e hora
        $data_hora = Carbon::parse($request->data . ' ' . $request->hora);
        if ($data_hora->greaterThan(now()->addMonths(3)->endOfDay())) {
            return back()->withErrors(['data' => 'O agendamento deve ser feito dentro dos próximos 3 meses.'])->withInput();
        }

        // Buscar serviço
        $profissional = User::with('servicos')->findOrFail($request->profissional_id);
        $servicosSelecionados = Servico::whereIn('id_servico', $servicosIds)->get()->keyBy('id_servico');
        $duracaoTotal = 0;
        $valorBase = 0.0;
        $servicosParaAnexar = [];
        $request->merge(['servico_id' => $servicosIds[0]]);
        $servico = $servicosSelecionados->get($servicosIds[0]);

        foreach ($servicosIds as $servicoId) {
            $servicoAtual = $servicosSelecionados->get($servicoId);
            $vinculoAtual = $profissional->servicos->find($servicoId);

            if (!$vinculoAtual) {
                return back()->withErrors(['servicos' => "Este profissional nao realiza o servico: {$servicoAtual->nome}"])->withInput();
            }

            $duracao = (int) ($vinculoAtual->pivot->duracao_customizada ?? $servicoAtual->duracao);
            $duracaoTotal += $duracao;
            $valorBase += (float) $servicoAtual->preco;
            $servicosParaAnexar[$servicoId] = [
                'duracao' => $duracao,
                'preco' => $servicoAtual->preco,
            ];
        }

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

        $data_hora_fim = $data_hora->copy()->addMinutes($duracaoTotal);

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
            (float) $valorBase,
            $invadeAlmoco,
            $agendaService->buscarBloqueioGeralConflitante($data_hora, $data_hora_fim),
            $excedeSaidaExpediente,
            $data_hora,
            count($servicosIds)
        );

        // Verificar conflito com agendamentos existentes
        $conflito = $agendaService->existeConflitoAgendamento($profissional->id, $data_hora, $data_hora_fim);
        if ($conflito) {
            return back()->withErrors(['hora' => 'Este horário já está ocupado.'])->withInput();
        }

        $resultadoAgendamento = DB::transaction(function () use ($request, $data_hora, $data_hora_fim, $servicosIds, $servicosParaAnexar, $agendaService, $dadosValor) {
            User::where('id', $request->profissional_id)->lockForUpdate()->first();

            if ($agendaService->existeConflitoAgendamento($request->profissional_id, $data_hora, $data_hora_fim)) {
                return 'conflito';
            }

            $agendamento = Agendamento::create([
                'cliente_id' => $request->cliente_id,
                'profissional_id' => $request->profissional_id,
                'servico_id' => $servicosIds[0],
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

            $agendamento->servicos()->attach($servicosParaAnexar);

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

    public function agendaCliente(Request $request)
    {
        $filtro = $request->get('filtro', '7');
        $agora = Carbon::now();

        $query = Agendamento::where('cliente_id', auth()->id())
            ->with(['profissional', 'servico', 'servicos']);

        if ($filtro === 'hoje') {
            $query->whereDate('data_hora_inicio', Carbon::today());
        } elseif ($filtro !== 'todos') {
            $dias = (int) $filtro;
            $dataLimite = Carbon::now()->addDays($dias);

            $query->whereBetween('data_hora_inicio', [
                Carbon::now(),
                $dataLimite->endOfDay()
            ]);
        }

        $query
            ->orderByRaw("CASE WHEN status IN ('pendente', 'confirmado') THEN 0 ELSE 1 END ASC")
            ->orderBy('data_hora_inicio');

        $agendamentos = $query->get();

        return view('cliente.agenda', compact('agendamentos'));
    }

    public function agendaProfissional(Request $request)
    {
        $filtro = $request->get('filtro', '7');
        $query = Agendamento::where('profissional_id', auth()->id())
            ->with(['cliente.pacotesAtivos.pacote.servicos', 'servico', 'servicos']);

        // Aplicar filtro de período
        if ($filtro === 'hoje') {
            $query->whereDate('data_hora_inicio', Carbon::today());
        } elseif ($filtro !== 'todos') {
            $dias = (int) $filtro;
            $dataLimite = Carbon::now()->addDays($dias);

            $query->whereBetween('data_hora_inicio', [
                Carbon::now()->startOfDay(),
                $dataLimite->endOfDay()
            ]);
        }

        // Ordenação:
        // 1. Agendamentos que ainda não passaram ficam primeiro
        // 2. Os futuros ficam do mais próximo para o mais distante
        // 3. Os passados descem para o final, do mais recente para o mais antigo
        $query
            ->orderByRaw("CASE WHEN status IN ('pendente', 'confirmado') THEN 0 ELSE 1 END ASC")
            ->orderBy('data_hora_inicio');

        $agendamentos = $query->get()
            ->groupBy(fn($data) => Carbon::parse($data->data_hora_inicio)->format('d/m/Y'));

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
        $formasPagamento = ['dinheiro', 'pix', 'cartao_debito', 'cartao_credito'];

        if (!$this->usuarioPodeGerenciarAgendamento($agendamento)) {
            abort(403, 'Você não tem permissão para finalizar este agendamento.');
        }

        if (in_array($agendamento->status, ['executado', 'cancelado', 'falta'], true)) {
            return back()->withErrors(['status' => 'Este agendamento não pode ser finalizado no status atual.']);
        }

        // Iniciamos uma transação para garantir que ou faz TUDO ou não faz NADA
        $request->validate([
            'forma_pagamento' => ['nullable', 'in:' . implode(',', $formasPagamento)],
            'pagamentos' => ['nullable', 'array'],
            'pagamentos.*.forma_pagamento' => ['nullable', 'in:' . implode(',', $formasPagamento)],
            'pagamentos.*.valor' => ['nullable'],
        ], [
            'pagamentos.required_without' => 'Informe o pagamento antes de finalizar o atendimento.',
        ]);

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
            
            $pagamentoService = app(PagamentoService::class);
            $pagamentos = [];

            if (! ($request->has('usar_pacote') && $request->usar_pacote != null)) {
                $pagamentos = $pagamentoService->normalizar(
                    $request->input('pagamentos', []),
                    (float) $agendamento->valor_total,
                    $request->input('forma_pagamento')
                );
            }

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
            $agendamento->status_pagamento = 'pago';
            $agendamento->forma_pagamento = $request->has('usar_pacote') && $request->usar_pacote != null
                ? 'pacote'
                : $pagamentoService->formaResumo($pagamentos, $request->input('forma_pagamento'));
            $agendamento->pago_em = now();
            $agendamento->save();

            if ($pagamentos) {
                $pagamentoService->registrar($agendamento, $pagamentos, auth()->id());
            }

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

        if (in_array($agendamento->status, ['pendente', 'confirmado'], true)) {
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
