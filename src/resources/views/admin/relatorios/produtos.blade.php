<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex items-center gap-2">
                <span class="text-[#7B19E5] text-xl">✧</span>
                <h2 class="font-title text-xl text-[#1A002B]">
                    {{ __('REL004: Produtos Mais Vendidos') }}
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
                    <form method="GET" action="{{ route('admin.relatorios.produtos') }}" class="flex flex-wrap items-end gap-4">
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
                            Filtrar Vendas
                        </button>
                    </form>
                </div>
            </div>

            <!-- Downloads -->
            <div class="flex gap-2 mb-6 flex-wrap">
                <a href="{{ route('admin.relatorios.produtos.download-excel', ['data_inicio' => $dataInicio, 'data_fim' => $dataFim]) }}" class="flex items-center gap-2 bg-white/50 text-[#00B050] border border-[#FFD6F4] px-4 py-2 rounded-lg hover:bg-white/80 transition font-medium">
                    📊 Exportar Excel
                </a>
            </div>

            <!-- Cards de resumo -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <!-- Produto Campeão -->
                <div class="glass-card rounded-2xl shadow-xl overflow-hidden">
                    <div class="p-6 bg-gradient-to-br from-[#7B19E5] to-[#A855F7] text-white">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="text-sm font-bold opacity-80 uppercase tracking-wider mb-2">Produto Campeão (Giro)</h3>
                                @if($campeao)
                                    <p class="text-xl font-black mb-1 truncate">{{ $campeao->nome }}</p>
                                    <p class="text-sm bg-white/20 px-3 py-1 rounded-full inline-block mt-1">
                                        {{ $campeao->total_vendido }} unidades vendidas
                                    </p>
                                @else
                                    <p class="text-xl font-bold">Sem vendas</p>
                                @endif
                            </div>
                            <div class="opacity-30 text-4xl">✧</div>
                        </div>
                    </div>
                </div>

                <!-- Giro Total -->
                <div class="glass-card rounded-2xl shadow-xl overflow-hidden">
                    <div class="p-6 bg-white/70 backdrop-blur-sm border border-white/40">
                        <h3 class="text-gray-500 font-bold uppercase text-sm mb-2">Giro Total (Unidades)</h3>
                        <div class="flex items-end gap-2">
                            <p class="text-4xl font-black text-[#7B19E5]">{{ $totalUnidadesVendidas }}</p>
                            <p class="text-sm text-gray-500 mb-1">itens saíram</p>
                        </div>
                    </div>
                </div>

                <!-- Receita Total -->
                <div class="glass-card rounded-2xl shadow-xl overflow-hidden">
                    <div class="p-6 bg-white/70 backdrop-blur-sm border border-white/40">
                        <h3 class="text-gray-500 font-bold uppercase text-sm mb-2">Receita com Produtos</h3>
                        <div class="flex items-end gap-2">
                            <p class="text-4xl font-black text-[#FF2EB6]">R$ {{ number_format($receitaTotal, 2, ',', '.') }}</p>
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
                            <h3 class="font-title text-[#4A00B9] text-lg">Unidades Vendidas</h3>
                        </div>
                        <div class="h-80">
                            <canvas id="graficoProdutosVendidos"></canvas>
                            <script type="application/json" data-salao-chart="graficoProdutosVendidos">
                                {
                                    "type": "bar",
                                    "data": {
                                        "labels": @json($produtosVendidos->take(10)->pluck('nome')->values()),
                                        "datasets": [{
                                            "label": "Unidades vendidas",
                                            "data": @json($produtosVendidos->take(10)->pluck('total_vendido')->values()),
                                            "backgroundColor": "rgba(123, 25, 229, 0.72)",
                                            "borderColor": "#7B19E5",
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

                <div class="glass-card rounded-2xl shadow-xl overflow-hidden">
                    <div class="p-6 bg-white/70 backdrop-blur-sm border border-white/40">
                        <div class="flex items-center gap-2 mb-4">
                            <span class="text-[#7B19E5] text-xl">✧</span>
                            <h3 class="font-title text-[#4A00B9] text-lg">Receita por Produto</h3>
                        </div>
                        <div class="h-80">
                            <canvas id="graficoReceitaProdutos"></canvas>
                            <script type="application/json" data-salao-chart="graficoReceitaProdutos">
                                {
                                    "type": "doughnut",
                                    "data": {
                                        "labels": @json($produtosVendidos->take(10)->pluck('nome')->values()),
                                        "datasets": [{
                                            "data": @json($produtosVendidos->take(10)->pluck('receita_gerada')->values()),
                                            "backgroundColor": ["#7B19E5", "#FF2EB6", "#A855F7", "#F59E0B", "#00B050", "#38BDF8", "#FB7185", "#6366F1", "#14B8A6", "#F97316"],
                                            "borderColor": "#FFFFFF",
                                            "borderWidth": 4
                                        }]
                                    },
                                    "options": {
                                        "cutout": "58%"
                                    }
                                }
                            </script>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabela de Ranking -->
            <div class="glass-card rounded-2xl shadow-xl overflow-hidden">
                <div class="p-6 bg-white/70 backdrop-blur-sm border border-white/40">
                    <div class="flex items-center gap-2 mb-4">
                        <span class="text-[#7B19E5] text-xl">✧</span>
                        <h3 class="font-title text-[#4A00B9] text-lg">Ranking, Giro e Estoque Atual</h3>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse whitespace-nowrap">
                            <thead>
                                <tr class="bg-[#7B19E5]/10 border-b border-[#FFD6F4]">
                                    <th class="p-3 rounded-tl-lg text-[#4A00B9] text-xs font-medium uppercase">#</th>
                                    <th class="p-3 text-[#4A00B9] text-xs font-medium uppercase">Produto</th>
                                    <th class="p-3 text-center text-[#4A00B9] text-xs font-medium uppercase">Giro (Qtd Vendida)</th>
                                    <th class="p-3 text-center text-[#4A00B9] text-xs font-medium uppercase">Estoque Atual</th>
                                    <th class="p-3 text-right rounded-tr-lg text-[#4A00B9] text-xs font-medium uppercase">Receita Gerada</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($produtosVendidos as $index => $produto)
                                    <tr class="border-b border-[#FFD6F4] hover:bg-white/50 transition">
                                        <td class="p-3 font-bold text-gray-400">{{ $index + 1 }}º</td>
                                        <td class="p-3 font-bold text-[#1A002B]">{{ $produto->nome }}</td>
                                        <td class="p-3 text-center">
                                            <span class="bg-[#7B19E5]/10 text-[#7B19E5] px-3 py-1 rounded-full font-bold">
                                                {{ $produto->total_vendido }}
                                            </span>
                                        </td>
                                        <td class="p-3 text-center">
                                            @if($produto->quantidade_estoque <= 5)
                                                <span class="bg-[#FF2EB6]/20 text-[#FF2EB6] px-3 py-1 rounded-full font-bold text-sm flex items-center justify-center gap-1 w-max mx-auto" title="Estoque Baixo! Risco de faltar.">
                                                    ⚠️ {{ $produto->quantidade_estoque }}
                                                </span>
                                            @else
                                                <span class="text-gray-600 font-medium">
                                                    {{ $produto->quantidade_estoque }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="p-3 text-right font-bold text-[#7B19E5]">
                                            R$ {{ number_format($produto->receita_gerada, 2, ',', '.') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="p-8 text-center text-gray-500">
                                            ✧ Nenhuma venda de produto registada neste período.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

@php
    $chartProdutos = $produtosVendidos->take(10)->map(function ($produto) {
        return [
            'nome' => $produto->nome,
            'vendido' => (int) $produto->total_vendido,
            'receita' => (float) $produto->receita_gerada,
        ];
    })->values();
@endphp

<script>
    (window.SalaoChartQueue = window.SalaoChartQueue || []).push(() => {
        const produtos = @json($chartProdutos);

        window.SalaoCharts?.create('graficoProdutosVendidos', {
            type: 'bar',
            data: {
                labels: produtos.map(item => item.nome),
                datasets: [{
                    label: 'Unidades vendidas',
                    data: produtos.map(item => item.vendido),
                    backgroundColor: 'rgba(123, 25, 229, 0.72)',
                    borderColor: '#7B19E5',
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

        window.SalaoCharts?.create('graficoReceitaProdutos', {
            type: 'doughnut',
            data: {
                labels: produtos.map(item => item.nome),
                datasets: [{
                    data: produtos.map(item => item.receita),
                    backgroundColor: ['#7B19E5', '#FF2EB6', '#A855F7', '#F59E0B', '#00B050', '#38BDF8', '#FB7185', '#6366F1', '#14B8A6', '#F97316'],
                    borderColor: '#FFFFFF',
                    borderWidth: 4,
                }],
            },
            options: {
                cutout: '58%',
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: context => `${context.label}: ${window.SalaoCharts.money(context.parsed)}`,
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
