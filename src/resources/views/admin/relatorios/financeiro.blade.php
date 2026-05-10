<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex items-center gap-2">
                <span class="text-[#7B19E5] text-xl">✧</span>
                <h2 class="font-title text-xl text-[#1A002B]">
                    {{ __('REL007: Financeiro Detalhado') }}
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
                $graficoEntradasFinanceiroDados = [
                    (float) $receitaServicos,
                    (float) $receitaProdutos,
                    (float) $receitaPacotes,
                    (float) $receitaMultas,
                ];

                $graficoSaldoFinanceiroDados = [
                    (float) $totalEntradas,
                    (float) $totalSaidas,
                    (float) $saldoLiquido,
                ];
            @endphp

            <!-- Filtro -->
            <div class="glass-card rounded-2xl shadow-xl overflow-hidden mb-6">
                <div class="p-4 bg-white/70 backdrop-blur-sm border border-white/40">
                    <form method="GET" action="{{ route('admin.relatorios.financeiro') }}" class="flex flex-wrap items-end gap-4">
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
                            Gerar Balanço
                        </button>
                    </form>
                </div>
            </div>

            <!-- Downloads -->
            <div class="flex gap-2 mb-6 flex-wrap">
                <a href="{{ route('admin.relatorios.financeiro.download-excel', ['data_inicio' => $dataInicio, 'data_fim' => $dataFim]) }}" class="flex items-center gap-2 bg-white/50 text-[#00B050] border border-[#FFD6F4] px-4 py-2 rounded-lg hover:bg-white/80 transition font-medium">
                    ✧ Exportar Excel
                </a>
            </div>

            <!-- Cards de resumo -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <!-- Total de Entradas -->
                <div class="glass-card rounded-2xl shadow-xl overflow-hidden">
                    <div class="p-6 bg-white/70 backdrop-blur-sm border border-white/40">
                        <h3 class="text-gray-500 font-bold uppercase text-sm mb-2">Total de Entradas (Bruto)</h3>
                        <p class="text-3xl font-black text-[#7B19E5]">R$ {{ number_format($totalEntradas, 2, ',', '.') }}</p>
                        <p class="text-sm text-gray-500 mt-1">Soma de tudo o que os clientes pagaram</p>
                    </div>
                </div>

                <!-- Total de Saídas -->
                <div class="glass-card rounded-2xl shadow-xl overflow-hidden">
                    <div class="p-6 bg-white/70 backdrop-blur-sm border border-white/40">
                        <h3 class="text-gray-500 font-bold uppercase text-sm mb-2">Total de Saídas (Despesas)</h3>
                        <p class="text-3xl font-black text-[#FF2EB6]">R$ {{ number_format($totalSaidas, 2, ',', '.') }}</p>
                        <p class="text-sm text-gray-500 mt-1">Comissões a pagar no período</p>
                    </div>
                </div>

                <!-- Saldo Líquido -->
                <div class="glass-card rounded-2xl shadow-xl overflow-hidden">
                    <div class="p-6 {{ $saldoLiquido >= 0 ? 'bg-gradient-to-br from-[#7B19E5] to-[#A855F7]' : 'bg-gradient-to-br from-[#FF2EB6] to-[#FF69B4]' }} text-white">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="text-sm font-bold opacity-80 uppercase tracking-wider mb-2">Saldo Líquido Gerado</h3>
                                <p class="text-4xl font-black mb-1">R$ {{ number_format($saldoLiquido, 2, ',', '.') }}</p>
                                <p class="text-sm bg-white/20 px-3 py-1 rounded-full inline-block mt-2">
                                    (Entradas - Saídas)
                                </p>
                            </div>
                            <div class="opacity-30 text-4xl">✧</div>
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
                            <h3 class="font-title text-[#4A00B9] text-lg">Entradas por Categoria</h3>
                        </div>
                        <div class="h-80">
                            <canvas id="graficoEntradasFinanceiro"></canvas>
                            <script type="application/json" data-salao-chart="graficoEntradasFinanceiro">
                                {
                                    "type": "polarArea",
                                    "data": {
                                        "labels": ["Servicos", "Produtos", "Pacotes", "Multas"],
                                        "datasets": [{
                                            "data": @json($graficoEntradasFinanceiroDados),
                                            "backgroundColor": ["rgba(123, 25, 229, 0.72)", "rgba(255, 46, 182, 0.72)", "rgba(168, 85, 247, 0.72)", "rgba(245, 158, 11, 0.72)"],
                                            "borderColor": "#FFFFFF",
                                            "borderWidth": 3
                                        }]
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
                            <h3 class="font-title text-[#4A00B9] text-lg">Entradas x Saidas</h3>
                        </div>
                        <div class="h-80">
                            <canvas id="graficoSaldoFinanceiro"></canvas>
                            <script type="application/json" data-salao-chart="graficoSaldoFinanceiro">
                                {
                                    "type": "bar",
                                    "data": {
                                        "labels": ["Entradas", "Saidas", "Saldo liquido"],
                                        "datasets": [{
                                            "label": "Valor",
                                            "data": @json($graficoSaldoFinanceiroDados),
                                            "backgroundColor": ["rgba(123, 25, 229, 0.72)", "rgba(255, 46, 182, 0.72)", "rgba(0, 176, 80, 0.72)"],
                                            "borderColor": ["#7B19E5", "#FF2EB6", "#00B050"],
                                            "borderWidth": 2
                                        }]
                                    }
                                }
                            </script>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detalhamento -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Entradas por Categoria -->
                <div class="glass-card rounded-2xl shadow-xl overflow-hidden">
                    <div class="p-6 bg-white/70 backdrop-blur-sm border border-white/40">
                        <div class="flex items-center gap-2 border-b border-[#FFD6F4] pb-3 mb-4">
                            <span class="text-[#7B19E5] text-xl">✧</span>
                            <h3 class="font-title text-[#4A00B9] text-lg">Entradas por Categoria</h3>
                        </div>

                        @php
                            $pctServicos = $totalEntradas > 0 ? ($receitaServicos / $totalEntradas) * 100 : 0;
                            $pctProdutos = $totalEntradas > 0 ? ($receitaProdutos / $totalEntradas) * 100 : 0;
                            $pctPacotes = $totalEntradas > 0 ? ($receitaPacotes / $totalEntradas) * 100 : 0;
                            $pctMultas = $totalEntradas > 0 ? ($receitaMultas / $totalEntradas) * 100 : 0;
                        @endphp

                        <div class="space-y-6">
                            <div>
                                <div class="flex justify-between items-end mb-1">
                                    <span class="text-sm font-bold text-gray-700">Serviços Executados</span>
                                    <span class="text-lg font-black text-[#7B19E5]">R$ {{ number_format($receitaServicos, 2, ',', '.') }}</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-3">
                                    <div class="bg-[#7B19E5] h-3 rounded-full" style="width: {{ $pctServicos }}%"></div>
                                </div>
                                <p class="text-xs text-gray-400 mt-1 text-right">{{ number_format($pctServicos, 1) }}% do total</p>
                            </div>

                            <div>
                                <div class="flex justify-between items-end mb-1">
                                    <span class="text-sm font-bold text-gray-700">Venda de Produtos</span>
                                    <span class="text-lg font-black text-[#FF2EB6]">R$ {{ number_format($receitaProdutos, 2, ',', '.') }}</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-3">
                                    <div class="bg-[#FF2EB6] h-3 rounded-full" style="width: {{ $pctProdutos }}%"></div>
                                </div>
                                <p class="text-xs text-gray-400 mt-1 text-right">{{ number_format($pctProdutos, 1) }}% do total</p>
                            </div>

                            <div>
                                <div class="flex justify-between items-end mb-1">
                                    <span class="text-sm font-bold text-gray-700">Venda de Pacotes</span>
                                    <span class="text-lg font-black text-[#A855F7]">R$ {{ number_format($receitaPacotes, 2, ',', '.') }}</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-3">
                                    <div class="bg-[#A855F7] h-3 rounded-full" style="width: {{ $pctPacotes }}%"></div>
                                </div>
                                <p class="text-xs text-gray-400 mt-1 text-right">{{ number_format($pctPacotes, 1) }}% do total</p>
                            </div>

                            <div>
                                <div class="flex justify-between items-end mb-1">
                                    <span class="text-sm font-bold text-gray-700">Multas de Cancelamento</span>
                                    <span class="text-lg font-black text-[#F59E0B]">R$ {{ number_format($receitaMultas, 2, ',', '.') }}</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-3">
                                    <div class="bg-[#F59E0B] h-3 rounded-full" style="width: {{ $pctMultas }}%"></div>
                                </div>
                                <p class="text-xs text-gray-400 mt-1 text-right">{{ number_format($pctMultas, 1) }}% do total</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Despesas Registadas -->
                <div class="glass-card rounded-2xl shadow-xl overflow-hidden">
                    <div class="p-6 bg-white/70 backdrop-blur-sm border border-white/40">
                        <div class="flex items-center gap-2 border-b border-[#FFD6F4] pb-3 mb-4">
                            <span class="text-[#7B19E5] text-xl">✧</span>
                            <h3 class="font-title text-[#4A00B9] text-lg">Despesas Registadas</h3>
                        </div>

                        <div class="bg-[#FF2EB6]/10 border border-[#FFD6F4] p-4 rounded-lg flex items-center justify-between mb-4">
                            <div>
                                <p class="font-bold text-[#FF2EB6]">Pagamento de Comissões</p>
                                <p class="text-sm text-gray-500">Repasse aos profissionais parceiros</p>
                            </div>
                            <p class="text-xl font-black text-[#FF2EB6]">R$ {{ number_format($despesaComissoes, 2, ',', '.') }}</p>
                        </div>

                        <div class="bg-white/50 border border-[#FFD6F4] p-4 rounded-lg flex items-center justify-between opacity-50">
                            <div>
                                <p class="font-bold text-gray-600">Despesas Fixas (Água, Luz, etc.)</p>
                                <p class="text-sm text-gray-500">Módulo ainda não integrado</p>
                            </div>
                            <p class="text-xl font-bold text-gray-400">R$ 0,00</p>
                        </div>
                        
                        <div class="mt-6 p-4 bg-[#7B19E5]/5 text-[#7B19E5] text-sm rounded-xl border border-[#FFD6F4]">
                            <strong class="text-[#7B19E5]">✧ Nota:</strong> Atualmente o sistema calcula automaticamente as despesas geradas pelas comissões de serviços executados. Para calcular o lucro real e total do salão, futuramente podes adicionar um módulo de contas a pagar (fornecedores, impostos e custos fixos).
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

@php
    $chartFinanceiro = [
        'servicos' => (float) $receitaServicos,
        'produtos' => (float) $receitaProdutos,
        'pacotes' => (float) $receitaPacotes,
        'multas' => (float) $receitaMultas,
        'entradas' => (float) $totalEntradas,
        'saidas' => (float) $totalSaidas,
        'saldo' => (float) $saldoLiquido,
    ];
@endphp

<script>
    (window.SalaoChartQueue = window.SalaoChartQueue || []).push(() => {
        const financeiro = @json($chartFinanceiro);

        window.SalaoCharts?.create('graficoEntradasFinanceiro', {
            type: 'polarArea',
            data: {
                labels: ['Servicos', 'Produtos', 'Pacotes', 'Multas'],
                datasets: [{
                    data: [financeiro.servicos, financeiro.produtos, financeiro.pacotes, financeiro.multas],
                    backgroundColor: ['rgba(123, 25, 229, 0.72)', 'rgba(255, 46, 182, 0.72)', 'rgba(168, 85, 247, 0.72)', 'rgba(245, 158, 11, 0.72)'],
                    borderColor: '#FFFFFF',
                    borderWidth: 3,
                }],
            },
            options: {
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: context => `${context.label}: ${window.SalaoCharts.money(context.parsed.r)}`,
                        },
                    },
                },
            },
        });

        window.SalaoCharts?.create('graficoSaldoFinanceiro', {
            type: 'bar',
            data: {
                labels: ['Entradas', 'Saidas', 'Saldo liquido'],
                datasets: [{
                    label: 'Valor',
                    data: [financeiro.entradas, financeiro.saidas, financeiro.saldo],
                    backgroundColor: ['rgba(123, 25, 229, 0.72)', 'rgba(255, 46, 182, 0.72)', 'rgba(0, 176, 80, 0.72)'],
                    borderColor: ['#7B19E5', '#FF2EB6', '#00B050'],
                    borderWidth: 2,
                    borderRadius: 14,
                }],
            },
            options: {
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: context => window.SalaoCharts.money(context.parsed.y),
                        },
                    },
                },
                scales: {
                    y: {
                        ticks: {
                            callback: value => window.SalaoCharts.money(value),
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