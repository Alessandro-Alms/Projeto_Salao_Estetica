<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex items-center gap-2">
                <span class="text-[#7B19E5] text-xl">✧</span>
                <h2 class="font-title text-xl text-[#1A002B]">
                    {{ __('REL002: Ocupação da Agenda') }}
                </h2>
            </div>
            <a href="{{ route('admin.relatorios.index') ?? '#' }}" class="text-sm text-[#7B19E5] hover:text-[#FF2EB6] transition-colors inline-flex items-center gap-1">
                <i class="fa-solid fa-arrow-left text-xs"></i> Voltar
            </a>
        </div>
    </x-slot>

    <div class="py-12 relative">
        <!-- Fundo -->
        <div class="fixed inset-0 -z-10 overflow-hidden">
            <div class="absolute top-0 -left-20 w-[600px] h-[600px] bg-[#7B19E5]/20 rounded-full blur-3xl"></div>
            <div class="absolute bottom-0 -right-20 w-[700px] h-[700px] bg-[#FF2EB6]/20 rounded-full blur-3xl"></div>
            <div class="absolute top-1/3 left-1/3 w-[400px] h-[400px] bg-[#A955D3]/15 rounded-full blur-3xl"></div>
            <div class="absolute inset-0 bg-gradient-to-br from-[#7B19E5]/5 via-white/30 to-[#FF2EB6]/5"></div>
        </div>

        <div class="container mx-auto px-4">
            <!-- Filtro -->
            <div class="glass-card rounded-2xl shadow-xl overflow-hidden mb-6">
                <div class="p-4 bg-white/70 backdrop-blur-sm border border-white/40">
                    <form method="GET" action="{{ route('admin.relatorios.ocupacao') }}" class="flex flex-wrap items-end gap-4">
                        <div>
                            <label class="block text-sm font-medium text-[#4A00B9] mb-1">Início</label>
                            <input type="date" name="data_inicio" value="{{ $dataInicio }}" 
                                class="px-4 py-2.5 bg-white/50 border border-[#FFD6F4] rounded-lg focus:outline-none focus:border-[#7B19E5] focus:ring-2 focus:ring-[#7B19E5]/20 transition-all">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-[#4A00B9] mb-1">Fim</label>
                            <input type="date" name="data_fim" value="{{ $dataFim }}" 
                                class="px-4 py-2.5 bg-white/50 border border-[#FFD6F4] rounded-lg focus:outline-none focus:border-[#7B19E5] focus:ring-2 focus:ring-[#7B19E5]/20 transition-all">
                        </div>
                        <button type="submit" class="bg-gradient-to-r from-[#7B19E5] to-[#FF2EB6] text-white px-6 py-2.5 text-sm rounded-full font-medium btn-primary shadow-md hover:shadow-lg transition-all">
                            Analisar Ocupação
                        </button>
                    </form>
                </div>
            </div>
            <!-- Downloads -->
            <div class="flex gap-2 mb-6 flex-wrap">
                <a href="{{ route('admin.relatorios.ocupacao.download-excel', ['data_inicio' => $dataInicio, 'data_fim' => $dataFim]) }}" class="flex items-center gap-2 bg-white/50 text-[#00B050] border border-[#FFD6F4] px-4 py-2 rounded-lg hover:bg-white/80 transition font-medium">
                    ✧ Exportar Excel
                </a>
            </div>
            <!-- Cards de resumo -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <!-- Volume Total -->
                <div class="glass-card rounded-2xl shadow-xl overflow-hidden">
                    <div class="p-6 bg-white/70 backdrop-blur-sm border border-white/40">
                        <h3 class="text-gray-500 font-bold uppercase text-sm mb-2">Volume Total</h3>
                        <p class="text-4xl font-black text-[#7B19E5]">{{ $totalAgendamentos }}</p>
                        <p class="text-sm text-gray-500 mt-2">Agendamentos válidos no período</p>
                    </div>
                </div>

                <!-- Horário de Pico -->
                <div class="glass-card rounded-2xl shadow-xl overflow-hidden">
                    <div class="p-6 bg-gradient-to-br from-[#FF2EB6] to-[#FF69B4] text-white">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="text-sm font-bold opacity-80 uppercase tracking-wider mb-2">Horário de Pico</h3>
                                @if($horarioPico)
                                    <p class="text-4xl font-black mb-1">{{ str_pad($horarioPico->hora, 2, '0', STR_PAD_LEFT) }}:00</p>
                                    <p class="text-sm bg-white/20 px-3 py-1 rounded-full inline-block mt-2">
                                        {{ $horarioPico->total }} atendimentos
                                    </p>
                                @else
                                    <p class="text-xl font-bold">Sem dados</p>
                                @endif
                            </div>
                            <div class="opacity-30 text-4xl">✧</div>
                        </div>
                    </div>
                </div>

                <!-- Horário Morto -->
                <div class="glass-card rounded-2xl shadow-xl overflow-hidden">
                    <div class="p-6 bg-gradient-to-br from-[#7B19E5] to-[#A855F7] text-white">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="text-sm font-bold opacity-80 uppercase tracking-wider mb-2">Horário Morto</h3>
                                @if($horarioMorto)
                                    <p class="text-4xl font-black mb-1">{{ str_pad($horarioMorto->hora, 2, '0', STR_PAD_LEFT) }}:00</p>
                                    <p class="text-sm bg-white/20 px-3 py-1 rounded-full inline-block mt-2">
                                        Apenas {{ $horarioMorto->total }} atendimentos
                                    </p>
                                @else
                                    <p class="text-xl font-bold">Sem dados</p>
                                @endif
                            </div>
                            <div class="opacity-30 text-4xl">✧</div>
                        </div>
                    </div>
                </div>
            </div>

            @php
                $graficoOcupacaoHoraLabels = $ocupacaoPorHora->map(function ($item) {
                    return str_pad($item->hora, 2, '0', STR_PAD_LEFT) . ':00';
                })->values();

                $graficoOcupacaoHoraDados = $ocupacaoPorHora->map(function ($item) {
                    return (int) $item->total;
                })->values();

                $graficoOcupacaoDiaLabels = collect([1, 2, 3, 4, 5, 6, 0])->map(function ($dia) use ($nomesDias) {
                    return $nomesDias[$dia];
                })->values();

                $graficoOcupacaoDiaDados = collect([1, 2, 3, 4, 5, 6, 0])->map(function ($dia) use ($ocupacaoPorDia) {
                    return isset($ocupacaoPorDia[$dia]) ? (int) $ocupacaoPorDia[$dia]->total : 0;
                })->values();
            @endphp

            <!-- Graficos Chart.js -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <div class="glass-card rounded-2xl shadow-xl overflow-hidden">
                    <div class="p-6 bg-white/70 backdrop-blur-sm border border-white/40">
                        <div class="flex items-center gap-2 border-b border-[#FFD6F4] pb-2 mb-4">
                            <span class="text-[#7B19E5] text-xl">✧</span>
                            <h3 class="font-title text-[#4A00B9] text-lg">Agenda por Horario</h3>
                        </div>
                        <div class="h-80">
                            <canvas id="graficoOcupacaoHora"></canvas>
                            <script type="application/json" data-salao-chart="graficoOcupacaoHora">
                                {
                                    "type": "bar",
                                    "data": {
                                        "labels": @json($graficoOcupacaoHoraLabels),
                                        "datasets": [{
                                            "label": "Agendamentos",
                                            "data": @json($graficoOcupacaoHoraDados),
                                            "backgroundColor": "rgba(123, 25, 229, 0.72)",
                                            "borderColor": "#7B19E5",
                                            "borderWidth": 2
                                        }]
                                    },
                                    "options": {
                                        "scales": {
                                            "y": {
                                                "beginAtZero": true,
                                                "ticks": {
                                                    "precision": 0
                                                }
                                            }
                                        }
                                    }
                                }
                            </script>
                        </div>
                    </div>
                </div>

                <div class="glass-card rounded-2xl shadow-xl overflow-hidden">
                    <div class="p-6 bg-white/70 backdrop-blur-sm border border-white/40">
                        <div class="flex items-center gap-2 border-b border-[#FFD6F4] pb-2 mb-4">
                            <span class="text-[#7B19E5] text-xl">✧</span>
                            <h3 class="font-title text-[#4A00B9] text-lg">Agenda por Dia</h3>
                        </div>
                        <div class="h-80">
                            <canvas id="graficoOcupacaoDia"></canvas>
                            <script type="application/json" data-salao-chart="graficoOcupacaoDia">
                                {
                                    "type": "line",
                                    "data": {
                                        "labels": @json($graficoOcupacaoDiaLabels),
                                        "datasets": [{
                                            "label": "Agendamentos",
                                            "data": @json($graficoOcupacaoDiaDados),
                                            "fill": true,
                                            "tension": 0.35,
                                            "backgroundColor": "rgba(255, 46, 182, 0.16)",
                                            "borderColor": "#FF2EB6",
                                            "pointBackgroundColor": "#7B19E5",
                                            "pointBorderColor": "#FFFFFF",
                                            "pointBorderWidth": 3,
                                            "pointRadius": 5
                                        }]
                                    },
                                    "options": {
                                        "scales": {
                                            "y": {
                                                "beginAtZero": true,
                                                "ticks": {
                                                    "precision": 0
                                                }
                                            }
                                        }
                                    }
                                }
                            </script>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Barras detalhadas -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Distribuição por Horário -->
                <div class="glass-card rounded-2xl shadow-xl overflow-hidden">
                    <div class="p-6 bg-white/70 backdrop-blur-sm border border-white/40">
                        <div class="flex items-center gap-2 border-b border-[#FFD6F4] pb-2 mb-4">
                            <span class="text-[#7B19E5] text-xl">✧</span>
                            <h3 class="font-title text-[#4A00B9] text-lg">Distribuição por Horário</h3>
                        </div>
                        
                        @if($ocupacaoPorHora->count() > 0)
                            @php $maxHora = $ocupacaoPorHora->max('total'); @endphp
                            <div class="space-y-3">
                                @foreach($ocupacaoPorHora as $hora)
                                    @php $percentual = $maxHora > 0 ? ($hora->total / $maxHora) * 100 : 0; @endphp
                                    <div class="flex items-center gap-3">
                                        <span class="w-12 text-sm font-bold text-gray-600 text-right">{{ str_pad($hora->hora, 2, '0', STR_PAD_LEFT) }}:00</span>
                                        <div class="flex-1 bg-gray-200 rounded-full h-4 overflow-hidden">
                                            <div class="bg-gradient-to-r from-[#7B19E5] to-[#FF2EB6] h-full rounded-full" style="width: {{ $percentual }}%"></div>
                                        </div>
                                        <span class="w-8 text-sm text-gray-500">{{ $hora->total }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 text-center py-4">✧ Nenhum agendamento encontrado.</p>
                        @endif
                    </div>
                </div>

                <!-- Dias Mais Movimentados -->
                <div class="glass-card rounded-2xl shadow-xl overflow-hidden">
                    <div class="p-6 bg-white/70 backdrop-blur-sm border border-white/40">
                        <div class="flex items-center gap-2 border-b border-[#FFD6F4] pb-2 mb-4">
                            <span class="text-[#7B19E5] text-xl">✧</span>
                            <h3 class="font-title text-[#4A00B9] text-lg">Dias Mais Movimentados</h3>
                        </div>
                        
                        @if($ocupacaoPorDia->count() > 0)
                            @php $maxDia = $ocupacaoPorDia->max('total'); @endphp
                            <div class="space-y-4">
                                @foreach([1, 2, 3, 4, 5, 6, 0] as $diaSemana) 
                                    @php 
                                        $total = isset($ocupacaoPorDia[$diaSemana]) ? $ocupacaoPorDia[$diaSemana]->total : 0;
                                        $percentual = $maxDia > 0 ? ($total / $maxDia) * 100 : 0;
                                    @endphp
                                    <div class="flex items-center gap-3">
                                        <span class="w-20 text-sm font-bold text-gray-600 text-right">{{ $nomesDias[$diaSemana] }}</span>
                                        <div class="flex-1 bg-gray-200 rounded-full h-5 overflow-hidden">
                                            <div class="{{ $percentual == 100 ? 'bg-gradient-to-r from-[#FF2EB6] to-[#FF69B4]' : 'bg-gradient-to-r from-[#7B19E5] to-[#A855F7]' }} h-full rounded-full transition-all" style="width: {{ $percentual }}%"></div>
                                        </div>
                                        <span class="w-8 text-sm text-gray-500 font-medium">{{ $total }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 text-center py-4">✧ Nenhum agendamento encontrado.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

@php
    $chartOcupacaoHora = $ocupacaoPorHora->map(function ($item) {
        return [
            'hora' => str_pad($item->hora, 2, '0', STR_PAD_LEFT) . ':00',
            'total' => (int) $item->total,
        ];
    })->values();

    $chartOcupacaoDia = collect([1, 2, 3, 4, 5, 6, 0])->map(function ($dia) use ($nomesDias, $ocupacaoPorDia) {
        return [
            'nome' => $nomesDias[$dia],
            'total' => isset($ocupacaoPorDia[$dia]) ? (int) $ocupacaoPorDia[$dia]->total : 0,
        ];
    })->values();
@endphp

<script>
    (window.SalaoChartQueue = window.SalaoChartQueue || []).push(() => {
        const porHora = @json($chartOcupacaoHora);
        const dias = @json($chartOcupacaoDia);

        window.SalaoCharts?.create('graficoOcupacaoHora', {
            type: 'bar',
            data: {
                labels: porHora.map(item => item.hora),
                datasets: [{
                    label: 'Agendamentos',
                    data: porHora.map(item => item.total),
                    backgroundColor: 'rgba(123, 25, 229, 0.72)',
                    borderColor: '#7B19E5',
                    borderWidth: 2,
                    borderRadius: 12,
                }],
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0,
                        },
                    },
                },
            },
        });

        window.SalaoCharts?.create('graficoOcupacaoDia', {
            type: 'line',
            data: {
                labels: dias.map(item => item.nome),
                datasets: [{
                    label: 'Agendamentos',
                    data: dias.map(item => item.total),
                    fill: true,
                    tension: 0.35,
                    backgroundColor: 'rgba(255, 46, 182, 0.16)',
                    borderColor: '#FF2EB6',
                    pointBackgroundColor: '#7B19E5',
                    pointBorderColor: '#FFFFFF',
                    pointBorderWidth: 3,
                    pointRadius: 5,
                }],
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0,
                        },
                    },
                },
            },
        });
    });
</script>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700;800&family=Playfair+Display:wght@700&family=Space+Grotesk:wght@300;400;500;600;700&display=swap');
    
    .font-title {
        font-family: 'Playfair Display', serif;
        font-weight: 700;
        letter-spacing: -0.02em;
    }
    
    .glass-card {
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        box-shadow: 0 8px 32px rgba(123, 25, 229, 0.1);
    }
    
    .btn-primary {
        position: relative;
        overflow: hidden;
        transition: all 0.3s ease;
        z-index: 1;
    }
    
    .btn-primary::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.3);
        transform: translate(-50%, -50%);
        transition: width 0.6s ease, height 0.6s ease;
        z-index: -1;
    }
    
    .btn-primary:hover::before {
        width: 300px;
        height: 300px;
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
    }
</style>