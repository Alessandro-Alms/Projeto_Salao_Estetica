<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex items-center gap-2">
                <span class="text-[#7B19E5] text-xl">✧</span>
                <h2 class="font-title text-xl text-[#1A002B]">
                    {{ __('REL011: Avaliações e Reputação') }}
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
            @php
                $graficoDistribuicaoAvaliacoesDados = [
                    (int) $distribuicao[5],
                    (int) $distribuicao[4],
                    (int) $distribuicao[3],
                    (int) $distribuicao[2],
                    (int) $distribuicao[1],
                ];
            @endphp

            <!-- Filtro -->
            <div class="glass-card rounded-2xl shadow-xl overflow-hidden mb-6">
                <div class="p-4 bg-white/70 backdrop-blur-sm border border-white/40">
                    <form method="GET" action="{{ route('admin.relatorios.avaliacoes') }}" class="flex flex-wrap items-end gap-4">
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
                            Analisar Reputação
                        </button>
                    </form>
                </div>
            </div>

            <!-- Downloads -->
            <div class="flex gap-2 mb-6 flex-wrap">
                <a href="{{ route('admin.relatorios.avaliacoes.download-excel', ['data_inicio' => $dataInicio, 'data_fim' => $dataFim]) }}" class="flex items-center gap-2 bg-white/50 text-[#00B050] border border-[#FFD6F4] px-4 py-2 rounded-lg hover:bg-white/80 transition font-medium">
                    ✧ Exportar Excel
                </a>
            </div>

            <!-- Cards de resumo -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <!-- Média Geral -->
                <div class="glass-card rounded-2xl shadow-xl overflow-hidden">
                    <div class="p-6 bg-gradient-to-br from-[#7B19E5] to-[#A855F7] text-white">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="text-sm font-bold opacity-90 uppercase tracking-wider mb-2">Média Geral</h3>
                                <div class="flex items-end gap-2 mb-1">
                                    <p class="text-5xl font-black">{{ number_format($mediaGeral, 1) }}</p>
                                    <p class="text-xl font-bold opacity-80 mb-1">/ 5.0</p>
                                </div>
                                <p class="text-sm bg-white/20 px-3 py-1 rounded-full inline-block mt-1">
                                    ✧ {{ $totalAvaliacoes }} avaliações recebidas
                                </p>
                            </div>
                            <div class="opacity-30 text-5xl">✧</div>
                        </div>
                    </div>
                </div>

                <!-- Taxa de Aprovação -->
                <div class="glass-card rounded-2xl shadow-xl overflow-hidden">
                    <div class="p-6 bg-white/70 backdrop-blur-sm border border-white/40">
                        <h3 class="text-gray-500 font-bold uppercase text-sm mb-2">Taxa de Aprovação</h3>
                        <p class="text-4xl font-black text-[#FF2EB6]">{{ number_format($percentualAprovacao, 1) }}%</p>
                        <p class="text-sm text-gray-500 mt-2">Clientes que deram 4 ou 5 estrelas</p>
                    </div>
                </div>

                <!-- Distribuição de Estrelas -->
                <div class="glass-card rounded-2xl shadow-xl overflow-hidden">
                    <div class="p-4 bg-white/70 backdrop-blur-sm border border-white/40">
                        <h3 class="text-gray-500 font-bold uppercase text-xs mb-3 text-center border-b border-[#FFD6F4] pb-2">Resumo das Estrelas</h3>
                        <div class="space-y-2">
                            @foreach([5, 4, 3, 2, 1] as $estrela)
                                @php
                                    $qtd = $distribuicao[$estrela];
                                    $pct = $totalAvaliacoes > 0 ? ($qtd / $totalAvaliacoes) * 100 : 0;
                                    $cor = $estrela >= 4 ? 'bg-[#7B19E5]' : ($estrela == 3 ? 'bg-[#FF2EB6]' : 'bg-gray-400');
                                @endphp
                                <div class="flex items-center text-xs">
                                    <span class="w-12 flex items-center gap-1 font-bold text-[#4A00B9]">{{ $estrela }} ✧</span>
                                    <div class="flex-1 bg-gray-100 rounded-full h-2 mx-2">
                                        <div class="{{ $cor }} h-2 rounded-full" style="width: {{ $pct }}%"></div>
                                    </div>
                                    <span class="w-8 text-right text-gray-500">{{ $qtd }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Graficos -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <div class="glass-card rounded-2xl shadow-xl overflow-hidden">
                    <div class="p-6 bg-white/70 backdrop-blur-sm border border-white/40">
                        <div class="flex items-center gap-2 mb-4">
                            <span class="text-[#7B19E5] text-xl">✧</span>
                            <h3 class="font-title text-[#4A00B9] text-lg">Distribuicao de Estrelas</h3>
                        </div>
                        <div class="h-80">
                            <canvas id="graficoDistribuicaoAvaliacoes"></canvas>
                            <script type="application/json" data-salao-chart="graficoDistribuicaoAvaliacoes">
                                {
                                    "type": "bar",
                                    "data": {
                                        "labels": ["5 estrelas", "4 estrelas", "3 estrelas", "2 estrelas", "1 estrela"],
                                        "datasets": [{
                                            "label": "Avaliacoes",
                                            "data": @json($graficoDistribuicaoAvaliacoesDados),
                                            "backgroundColor": ["#7B19E5", "#A855F7", "#FF2EB6", "#F59E0B", "#9CA3AF"]
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
                        <div class="flex items-center gap-2 mb-4">
                            <span class="text-[#7B19E5] text-xl">✧</span>
                            <h3 class="font-title text-[#4A00B9] text-lg">Ranking dos Profissionais</h3>
                        </div>
                        <div class="h-80">
                            <canvas id="graficoRankingAvaliacoes"></canvas>
                            <script type="application/json" data-salao-chart="graficoRankingAvaliacoes">
                                {
                                    "type": "bar",
                                    "data": {
                                        "labels": @json($rankingProfissionais->pluck('nome')->values()),
                                        "datasets": [
                                            {
                                                "label": "Media",
                                                "data": @json($rankingProfissionais->pluck('media')->values()),
                                                "backgroundColor": "rgba(123, 25, 229, 0.72)",
                                                "borderColor": "#7B19E5",
                                                "borderWidth": 2,
                                                "yAxisID": "media"
                                            },
                                            {
                                                "label": "Quantidade",
                                                "type": "line",
                                                "data": @json($rankingProfissionais->pluck('total_avaliacoes')->values()),
                                                "borderColor": "#FF2EB6",
                                                "backgroundColor": "#FF2EB6",
                                                "pointBackgroundColor": "#FF2EB6",
                                                "pointBorderColor": "#FFFFFF",
                                                "pointBorderWidth": 3,
                                                "pointRadius": 5,
                                                "tension": 0.35,
                                                "yAxisID": "quantidade"
                                            }
                                        ]
                                    },
                                    "options": {
                                        "scales": {
                                            "media": {
                                                "beginAtZero": true,
                                                "max": 5,
                                                "position": "left"
                                            },
                                            "quantidade": {
                                                "beginAtZero": true,
                                                "position": "right",
                                                "grid": {
                                                    "drawOnChartArea": false
                                                },
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

            <!-- Ranking e Feedbacks -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Top Profissionais -->
                <div class="glass-card rounded-2xl shadow-xl overflow-hidden lg:col-span-1">
                    <div class="p-6 bg-white/70 backdrop-blur-sm border border-white/40">
                        <div class="flex items-center gap-2 border-b border-[#FFD6F4] pb-2 mb-4">
                            <span class="text-[#7B19E5] text-xl">✧</span>
                            <h3 class="font-title text-[#4A00B9]">Top Profissionais</h3>
                        </div>
                        @if($rankingProfissionais->count() > 0)
                            <ul class="space-y-4">
                                @foreach($rankingProfissionais as $index => $prof)
                                    <li class="flex justify-between items-center bg-white/50 p-3 rounded-lg border border-[#FFD6F4]">
                                        <div>
                                            <p class="font-bold text-[#1A002B] flex items-center gap-2">
                                                @if($index == 0) ✧ 
                                                @elseif($index == 1) ✦ 
                                                @elseif($index == 2) ✧ 
                                                @else <span class="w-5 text-center text-gray-400">#{{ $index + 1 }}</span>
                                                @endif
                                                {{ $prof->nome }}
                                            </p>
                                            <p class="text-xs text-gray-500 mt-1">{{ $prof->total_avaliacoes }} avaliações</p>
                                        </div>
                                        <div class="text-right">
                                            <span class="bg-[#7B19E5]/10 text-[#7B19E5] px-2 py-1 rounded text-sm font-black flex items-center gap-1">
                                                {{ number_format($prof->media, 1) }} ✧
                                            </span>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-gray-500 text-sm py-4 text-center">✧ Sem dados suficientes para o ranking.</p>
                        @endif
                    </div>
                </div>

                <!-- Feed de Comentários -->
                <div class="glass-card rounded-2xl shadow-xl overflow-hidden lg:col-span-2">
                    <div class="p-6 bg-white/70 backdrop-blur-sm border border-white/40">
                        <div class="flex items-center gap-2 border-b border-[#FFD6F4] pb-2 mb-4">
                            <span class="text-[#7B19E5] text-xl">✧</span>
                            <h3 class="font-title text-[#4A00B9]">Feed de Comentários Recentes</h3>
                        </div>
                        
                        <div class="space-y-4 max-h-[500px] overflow-y-auto pr-2">
                            @forelse($avaliacoes->whereNotNull('comentario')->where('comentario', '!=', '')->take(15) as $av)
                                <div class="bg-white/50 p-4 rounded-xl border-l-4 {{ $av->nota >= 4 ? 'border-[#7B19E5]' : ($av->nota == 3 ? 'border-[#FF2EB6]' : 'border-gray-400') }}">
                                    <div class="flex justify-between items-start mb-2">
                                        <div>
                                            <span class="font-bold text-[#1A002B]">{{ $av->cliente_nome }}</span>
                                            <span class="text-xs text-gray-500 ml-2">avaliou <strong>{{ $av->profissional_nome }}</strong></span>
                                        </div>
                                        <div class="flex gap-1 text-sm">
                                            @for($i = 1; $i <= 5; $i++)
                                                <span class="{{ $i <= $av->nota ? 'text-[#FF2EB6]' : 'text-gray-300' }}">★</span>
                                            @endfor
                                        </div>
                                    </div>
                                    <p class="text-gray-700 text-sm italic">"{{ $av->comentario }}"</p>
                                    <p class="text-xs text-gray-400 mt-2 text-right">
                                        {{ \Carbon\Carbon::parse($av->created_at)->format('d/m/Y H:i') }}
                                    </p>
                                </div>
                            @empty
                                <div class="text-center p-8 bg-white/30 rounded-lg text-gray-500">
                                    <p class="text-4xl mb-3">✧</p>
                                    Nenhum comentário por escrito recebido neste período.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

@php
    $chartDistribuicao = [
        5 => (int) $distribuicao[5],
        4 => (int) $distribuicao[4],
        3 => (int) $distribuicao[3],
        2 => (int) $distribuicao[2],
        1 => (int) $distribuicao[1],
    ];

    $chartRanking = $rankingProfissionais->map(function ($prof) {
        return [
            'nome' => $prof->nome,
            'media' => (float) $prof->media,
            'total' => (int) $prof->total_avaliacoes,
        ];
    })->values();
@endphp

<script>
    (window.SalaoChartQueue = window.SalaoChartQueue || []).push(() => {
        const distribuicao = @json($chartDistribuicao);
        const ranking = @json($chartRanking);

        window.SalaoCharts?.create('graficoDistribuicaoAvaliacoes', {
            type: 'bar',
            data: {
                labels: ['5 estrelas', '4 estrelas', '3 estrelas', '2 estrelas', '1 estrela'],
                datasets: [{
                    label: 'Avaliacoes',
                    data: [distribuicao[5], distribuicao[4], distribuicao[3], distribuicao[2], distribuicao[1]],
                    backgroundColor: ['#7B19E5', '#A855F7', '#FF2EB6', '#F59E0B', '#9CA3AF'],
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

        window.SalaoCharts?.create('graficoRankingAvaliacoes', {
            type: 'bar',
            data: {
                labels: ranking.map(item => item.nome),
                datasets: [
                    {
                        label: 'Media',
                        data: ranking.map(item => item.media),
                        backgroundColor: 'rgba(123, 25, 229, 0.72)',
                        borderColor: '#7B19E5',
                        borderWidth: 2,
                        borderRadius: 12,
                        yAxisID: 'media',
                    },
                    {
                        label: 'Quantidade',
                        data: ranking.map(item => item.total),
                        type: 'line',
                        borderColor: '#FF2EB6',
                        backgroundColor: '#FF2EB6',
                        pointBackgroundColor: '#FF2EB6',
                        pointBorderColor: '#FFFFFF',
                        pointBorderWidth: 3,
                        pointRadius: 5,
                        tension: 0.35,
                        yAxisID: 'quantidade',
                    },
                ],
            },
            options: {
                scales: {
                    media: {
                        beginAtZero: true,
                        max: 5,
                        position: 'left',
                    },
                    quantidade: {
                        beginAtZero: true,
                        position: 'right',
                        grid: {
                            drawOnChartArea: false,
                        },
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