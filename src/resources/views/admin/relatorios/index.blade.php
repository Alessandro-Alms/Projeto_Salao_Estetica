<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <span class="text-[#7B19E5] text-xl">✧</span>
            <h2 class="font-title text-xl text-[#1A002B]">
                {{ __('Central de Relatórios e Inteligência') }}
            </h2>
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
                $graficoStatusAgendamentosDados = [
                    (int) ($totalExecutados ?? 0),
                    max((int) ($totalAgendamentos ?? 0) - (int) ($totalExecutados ?? 0), 0),
                ];
            @endphp
            
            <!-- Filtro e Exportação -->
            <div class="glass-card rounded-2xl shadow-xl overflow-hidden mb-6">
                <div class="p-4 bg-white/70 backdrop-blur-sm border border-white/40 flex flex-wrap justify-between items-center gap-4">
                    <form method="GET" action="{{ route('admin.relatorios.index') }}" class="flex flex-wrap items-end gap-3">
                        <div>
                            <label class="block text-sm text-[#4A00B9] font-medium mb-1">Data Início</label>
                            <input type="date" name="data_inicio" value="{{ $dataInicio }}" 
                                class="px-4 py-2.5 bg-white/50 border border-[#FFD6F4] rounded-lg focus:outline-none focus:border-[#7B19E5] focus:ring-2 focus:ring-[#7B19E5]/20 transition-all">
                        </div>
                        <div>
                            <label class="block text-sm text-[#4A00B9] font-medium mb-1">Data Fim</label>
                            <input type="date" name="data_fim" value="{{ $dataFim }}" 
                                class="px-4 py-2.5 bg-white/50 border border-[#FFD6F4] rounded-lg focus:outline-none focus:border-[#7B19E5] focus:ring-2 focus:ring-[#7B19E5]/20 transition-all">
                        </div>
                        <button type="submit" class="bg-gradient-to-r from-[#7B19E5] to-[#FF2EB6] text-white px-6 py-2.5 text-sm rounded-full font-medium btn-primary shadow-md hover:shadow-lg transition-all">
                            Atualizar Visão Geral
                        </button>
                    </form>

                    <div class="flex gap-2">
                        <a href="{{ route('admin.relatorios.exportarPdf', ['data_inicio' => $dataInicio, 'data_fim' => $dataFim]) }}" class="flex items-center gap-2 bg-white/50 text-[#7B19E5] border border-[#FFD6F4] px-4 py-2 rounded-lg hover:bg-white/80 transition font-medium">
                            ✧ Exportar PDF
                        </a>
                        <a href="{{ route('admin.relatorios.exportarExcel', ['data_inicio' => $dataInicio, 'data_fim' => $dataFim]) }}" class="flex items-center gap-2 bg-white/50 text-[#FF2EB6] border border-[#FFD6F4] px-4 py-2 rounded-lg hover:bg-white/80 transition font-medium">
                            ✧ Exportar Excel
                        </a>
                    </div>
                </div>
            </div>

            <!-- Cards de resumo -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
                <div class="glass-card rounded-2xl shadow-xl overflow-hidden">
                    <div class="p-6 bg-white/70 backdrop-blur-sm border border-white/40">
                        <h3 class="text-gray-500 font-bold uppercase text-xs mb-2">Faturamento (Executados)</h3>
                        <p class="text-3xl font-black text-[#7B19E5]">R$ {{ number_format($faturamentoTotal ?? 0, 2, ',', '.') }}</p>
                    </div>
                </div>

                <div class="glass-card rounded-2xl shadow-xl overflow-hidden">
                    <div class="p-6 bg-white/70 backdrop-blur-sm border border-white/40">
                        <h3 class="text-gray-500 font-bold uppercase text-xs mb-2">Agendamentos Executados</h3>
                        <p class="text-3xl font-black text-[#1A002B]">{{ $totalExecutados ?? 0 }} <span class="text-sm font-bold text-gray-400">/ {{ $totalAgendamentos ?? 0 }}</span></p>
                    </div>
                </div>

                <div class="glass-card rounded-2xl shadow-xl overflow-hidden">
                    <div class="p-6 bg-white/70 backdrop-blur-sm border border-white/40">
                        <h3 class="text-gray-500 font-bold uppercase text-xs mb-2">Taxa de Ocupação/Sucesso</h3>
                        @php
                            $taxa = ($totalAgendamentos ?? 0) > 0 ? (($totalExecutados ?? 0) / $totalAgendamentos) * 100 : 0;
                        @endphp
                        <p class="text-3xl font-black text-[#FF2EB6]">{{ number_format($taxa, 1, ',', '.') }}%</p>
                    </div>
                </div>
            </div>

            <!-- Graficos da central -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-10">
                <div class="glass-card rounded-2xl shadow-xl overflow-hidden">
                    <div class="p-6 bg-white/70 backdrop-blur-sm border border-white/40">
                        <div class="flex items-center gap-2 mb-4">
                            <span class="text-[#7B19E5] text-xl">✧</span>
                            <h3 class="font-title text-[#4A00B9] text-lg">Status dos Agendamentos</h3>
                        </div>
                        <div class="h-80">
                            <canvas id="graficoStatusAgendamentos"></canvas>
                            <script type="application/json" data-salao-chart="graficoStatusAgendamentos">
                                {
                                    "type": "doughnut",
                                    "data": {
                                        "labels": ["Executados", "Outros status"],
                                        "datasets": [{
                                            "data": @json($graficoStatusAgendamentosDados),
                                            "backgroundColor": ["#7B19E5", "#FFD6F4"],
                                            "borderColor": "#FFFFFF",
                                            "borderWidth": 4
                                        }]
                                    },
                                    "options": {
                                        "cutout": "62%"
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
                            <h3 class="font-title text-[#4A00B9] text-lg">Top Profissionais no período</h3>
                        </div>
                        <div class="h-80">
                            <canvas id="graficoTopProfissionaisCentral"></canvas>
                            <script type="application/json" data-salao-chart="graficoTopProfissionaisCentral">
                                {
                                    "type": "bar",
                                    "data": {
                                        "labels": @json($desempenhoProfissionais->take(8)->pluck('name')->values()),
                                        "datasets": [{
                                            "label": "Atendimentos",
                                            "data": @json($desempenhoProfissionais->take(8)->pluck('total_atendimentos')->values()),
                                            "backgroundColor": "rgba(255, 46, 182, 0.72)",
                                            "borderColor": "#FF2EB6",
                                            "borderWidth": 2
                                        }]
                                    },
                                    "options": {
                                        "indexAxis": "y",
                                        "scales": {
                                            "x": {
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

            <!-- Título Módulos -->
            <div class="mb-6 border-b border-[#FFD6F4] pb-2">
                <div class="flex items-center gap-2">
                    <span class="text-[#7B19E5] text-xl">✧</span>
                    <h2 class="text-2xl font-title text-[#1A002B]">Módulos de Relatórios</h2>
                </div>
                <p class="text-gray-500 text-sm mt-1">Selecione uma categoria abaixo para ver análises aprofundadas.</p>
            </div>

            <!-- Cards de módulos -->
            @php 
                $query = ['data_inicio' => $dataInicio, 'data_fim' => $dataFim]; 
            @endphp

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                
                <a href="{{ route('admin.relatorios.faturamento', $query) }}" class="glass-card rounded-2xl shadow-xl overflow-hidden hover-lift group block">
                    <div class="p-5 bg-white/70 backdrop-blur-sm border border-white/40">
                        <div class="text-3xl mb-3 group-hover:scale-110 transition-transform origin-left">✧</div>
                        <h3 class="font-title text-[#4A00B9] text-lg mb-1 group-hover:text-[#7B19E5] transition-colors">Faturamento</h3>
                        <p class="text-xs text-gray-500">Total de receitas, ticket médio e comparativo com período anterior.</p>
                    </div>
                </a>

                <a href="{{ route('admin.relatorios.ocupacao', $query) }}" class="glass-card rounded-2xl shadow-xl overflow-hidden hover-lift group block">
                    <div class="p-5 bg-white/70 backdrop-blur-sm border border-white/40">
                        <div class="text-3xl mb-3 group-hover:scale-110 transition-transform origin-left">✧</div>
                        <h3 class="font-title text-[#4A00B9] text-lg mb-1 group-hover:text-[#7B19E5] transition-colors">Ocupação da Agenda</h3>
                        <p class="text-xs text-gray-500">Taxa de preenchimento de horários, identificação de picos e horas mortas.</p>
                    </div>
                </a>

                <a href="{{ route('admin.relatorios.desempenho', $query) }}" class="glass-card rounded-2xl shadow-xl overflow-hidden hover-lift group block">
                    <div class="p-5 bg-white/70 backdrop-blur-sm border border-white/40">
                        <div class="text-3xl mb-3 group-hover:scale-110 transition-transform origin-left">★</div>
                        <h3 class="font-title text-[#4A00B9] text-lg mb-1 group-hover:text-[#7B19E5] transition-colors">Desempenho da Equipe</h3>
                        <p class="text-xs text-gray-500">Ranking de profissionais por serviços feitos, avaliações e valores gerados.</p>
                    </div>
                </a>

                <a href="{{ route('admin.relatorios.produtos', $query) }}" class="glass-card rounded-2xl shadow-xl overflow-hidden hover-lift group block">
                    <div class="p-5 bg-white/70 backdrop-blur-sm border border-white/40">
                        <div class="text-3xl mb-3 group-hover:scale-110 transition-transform origin-left">✧</div>
                        <h3 class="font-title text-[#4A00B9] text-lg mb-1 group-hover:text-[#7B19E5] transition-colors">Produtos Mais Vendidos</h3>
                        <p class="text-xs text-gray-500">Ranking de vendas físicas, giro de prateleira e lucro direto por produto.</p>
                    </div>
                </a>

                <a href="{{ route('admin.relatorios.fidelizacao', $query) }}" class="glass-card rounded-2xl shadow-xl overflow-hidden hover-lift group block">
                    <div class="p-5 bg-white/70 backdrop-blur-sm border border-white/40">
                        <div class="text-3xl mb-3 group-hover:scale-110 transition-transform origin-left">✧</div>
                        <h3 class="font-title text-[#4A00B9] text-lg mb-1 group-hover:text-[#7B19E5] transition-colors">Fidelização e VIPs</h3>
                        <p class="text-xs text-gray-500">Taxa de retorno de clientes e ranking dos clientes que mais investem no salão.</p>
                    </div>
                </a>

                <a href="{{ route('admin.relatorios.cancelamentos', $query) }}" class="glass-card rounded-2xl shadow-xl overflow-hidden hover-lift group block">
                    <div class="p-5 bg-white/70 backdrop-blur-sm border border-white/40">
                        <div class="text-3xl mb-3 group-hover:scale-110 transition-transform origin-left">✧</div>
                        <h3 class="font-title text-[#4A00B9] text-lg mb-1 group-hover:text-[#7B19E5] transition-colors">Análise de Cancelamentos</h3>
                        <p class="text-xs text-gray-500">Motivos de desistência, cálculo de prejuízos e ranking de clientes ofensores.</p>
                    </div>
                </a>

                <a href="{{ route('admin.relatorios.financeiro', $query) }}" class="glass-card rounded-2xl shadow-xl overflow-hidden hover-lift group block">
                    <div class="p-5 bg-white/70 backdrop-blur-sm border border-white/40">
                        <div class="text-3xl mb-3 group-hover:scale-110 transition-transform origin-left">✧</div>
                        <h3 class="font-title text-[#4A00B9] text-lg mb-1 group-hover:text-[#7B19E5] transition-colors">Financeiro Detalhado</h3>
                        <p class="text-xs text-gray-500">Balanço de entradas (serviços e produtos) vs despesas (comissões geradas).</p>
                    </div>
                </a>

                <a href="{{ route('admin.relatorios.comissoes', $query) }}" class="glass-card rounded-2xl shadow-xl overflow-hidden hover-lift group block">
                    <div class="p-5 bg-white/70 backdrop-blur-sm border border-white/40">
                        <div class="text-3xl mb-3 group-hover:scale-110 transition-transform origin-left">✧</div>
                        <h3 class="font-title text-[#4A00B9] text-lg mb-1 group-hover:text-[#7B19E5] transition-colors">Comissões a Pagar</h3>
                        <p class="text-xs text-gray-500">Folha de pagamento exata baseada nos serviços executados. Exportável.</p>
                    </div>
                </a>

                <a href="{{ route('admin.relatorios.estoque', $query) }}" class="glass-card rounded-2xl shadow-xl overflow-hidden hover-lift group block">
                    <div class="p-5 bg-white/70 backdrop-blur-sm border border-white/40">
                        <div class="text-3xl mb-3 group-hover:scale-110 transition-transform origin-left">✧</div>
                        <h3 class="font-title text-[#4A00B9] text-lg mb-1 group-hover:text-[#7B19E5] transition-colors">Estoque de Produtos</h3>
                        <p class="text-xs text-gray-500">Saldo atual, capital empatado na prateleira e alertas de reposição urgente.</p>
                    </div>
                </a>

                <a href="{{ route('admin.relatorios.sazonalidade', $query) }}" class="glass-card rounded-2xl shadow-xl overflow-hidden hover-lift group block">
                    <div class="p-5 bg-white/70 backdrop-blur-sm border border-white/40">
                        <div class="text-3xl mb-3 group-hover:scale-110 transition-transform origin-left">Calendário</div>
                        <h3 class="font-title text-[#4A00B9] text-lg mb-1 group-hover:text-[#7B19E5] transition-colors">Sazonalidade</h3>
                        <p class="text-xs text-gray-500">Descobre quais são os dias da semana mais fortes e fracos do teu salão.</p>
                    </div>
                </a>

                <a href="{{ route('admin.relatorios.avaliacoes', $query) }}" class="glass-card rounded-2xl shadow-xl overflow-hidden hover-lift group block">
                    <div class="p-5 bg-white/70 backdrop-blur-sm border border-white/40">
                        <div class="text-3xl mb-3 group-hover:scale-110 transition-transform origin-left">✧</div>
                        <h3 class="font-title text-[#4A00B9] text-lg mb-1 group-hover:text-[#7B19E5] transition-colors">Avaliações e Reputação</h3>
                        <p class="text-xs text-gray-500">Média de estrelas, taxa de aprovação dos clientes e feed de comentários.</p>
                    </div>
                </a>

                <a href="{{ route('admin.relatorios.previsao', $query) }}" class="glass-card rounded-2xl shadow-xl overflow-hidden hover-lift group block">
                    <div class="p-5 bg-white/70 backdrop-blur-sm border border-white/40">
                        <div class="text-3xl mb-3 group-hover:scale-110 transition-transform origin-left">✧</div>
                        <h3 class="font-title text-[#4A00B9] text-lg mb-1 group-hover:text-[#7B19E5] transition-colors">Previsão de Demanda</h3>
                        <p class="text-xs text-gray-500">Projeção inteligente de clientes para os próximos 7 dias baseada no histórico.</p>
                    </div>
                </a>

            </div>
        </div>
    </div>
</x-app-layout>

@php
    $chartResumo = [
        'executados' => (int) ($totalExecutados ?? 0),
        'demais' => max((int) ($totalAgendamentos ?? 0) - (int) ($totalExecutados ?? 0), 0),
    ];

    $chartProfissionais = $desempenhoProfissionais->take(8)->map(function ($prof) {
        return [
            'nome' => $prof->name,
            'atendimentos' => (int) $prof->total_atendimentos,
            'gerado' => (float) $prof->total_gerado,
        ];
    })->values();
@endphp

<script>
    (window.SalaoChartQueue = window.SalaoChartQueue || []).push(() => {
        const resumo = @json($chartResumo);
        const profissionais = @json($chartProfissionais);

        window.SalaoCharts?.create('graficoStatusAgendamentos', {
            type: 'doughnut',
            data: {
                labels: ['Executados', 'Outros status'],
                datasets: [{
                    data: [resumo.executados, resumo.demais],
                    backgroundColor: ['#7B19E5', '#FFD6F4'],
                    borderColor: '#FFFFFF',
                    borderWidth: 4,
                }],
            },
            options: {
                cutout: '62%',
            },
        });

        window.SalaoCharts?.create('graficoTopProfissionaisCentral', {
            type: 'bar',
            data: {
                labels: profissionais.map(item => item.nome),
                datasets: [{
                    label: 'Atendimentos',
                    data: profissionais.map(item => item.atendimentos),
                    backgroundColor: 'rgba(255, 46, 182, 0.72)',
                    borderColor: '#FF2EB6',
                    borderWidth: 2,
                    borderRadius: 12,
                }],
            },
            options: {
                indexAxis: 'y',
                scales: {
                    x: {
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
    
    .hover-lift {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .hover-lift:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 25px -12px rgba(123, 25, 229, 0.25);
    }
</style>
