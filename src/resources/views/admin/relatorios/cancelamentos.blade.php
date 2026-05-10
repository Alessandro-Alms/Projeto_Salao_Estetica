<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex items-center gap-2">
                <span class="text-[#7B19E5] text-xl">✧</span>
                <h2 class="font-title text-xl text-[#1A002B]">
                    {{ __('REL006: Análise de Cancelamentos e Faltas') }}
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
                    <form method="GET" action="{{ route('admin.relatorios.cancelamentos') }}" class="flex flex-wrap items-end gap-4">
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
                            Analisar Evasão
                        </button>
                    </form>
                </div>
            </div>

            <!-- Downloads -->
            <div class="flex gap-2 mb-6 flex-wrap">
                <a href="{{ route('admin.relatorios.cancelamentos.download-excel', ['data_inicio' => $dataInicio, 'data_fim' => $dataFim]) }}" class="flex items-center gap-2 bg-white/50 text-[#00B050] border border-[#FFD6F4] px-4 py-2 rounded-lg hover:bg-white/80 transition font-medium">
                    ✧ Exportar Excel
                </a>
            </div>

            <!-- Cards de resumo -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <!-- Taxa de Evasão -->
                <div class="glass-card rounded-2xl shadow-xl overflow-hidden">
                    <div class="p-6 bg-white/70 backdrop-blur-sm border border-white/40">
                        <h3 class="text-gray-500 font-bold uppercase text-sm mb-2">Taxa de Evasão Global</h3>
                        <div class="flex items-end gap-2">
                            <p class="text-4xl font-black text-[#FF2EB6]">{{ number_format($taxaEvasao, 1) }}%</p>
                        </div>
                        <p class="text-sm text-gray-500 mt-2">{{ $totalEvasoes }} faltas em {{ $totalGeral }} agendamentos totais</p>
                    </div>
                </div>

                <!-- Prejuízo Estimado -->
                <div class="glass-card rounded-2xl shadow-xl overflow-hidden">
                    <div class="p-6 bg-gradient-to-br from-[#7B19E5] to-[#A855F7] text-white">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="text-sm font-bold opacity-80 uppercase tracking-wider mb-2">Prejuízo Estimado</h3>
                                <p class="text-3xl font-black mb-1">R$ {{ number_format($prejuizoTotal, 2, ',', '.') }}</p>
                                <p class="text-sm bg-white/20 px-3 py-1 rounded-full inline-block mt-2">
                                    Valor de serviços que não ocorreram
                                </p>
                                <p class="text-xs opacity-80 mt-2">Prejuízo líquido: R$ {{ number_format($prejuizoLiquido, 2, ',', '.') }}</p>
                            </div>
                            <div class="opacity-30 text-3xl">✧</div>
                        </div>
                    </div>
                </div>

                <!-- Multas Recuperadas -->
                <div class="glass-card rounded-2xl shadow-xl overflow-hidden">
                    <div class="p-6 bg-white/70 backdrop-blur-sm border border-white/40">
                        <h3 class="text-gray-500 font-bold uppercase text-sm mb-2">Multas Recuperadas</h3>
                        <div class="flex items-end gap-2">
                            <p class="text-4xl font-black text-[#7B19E5]">R$ {{ number_format($totalMultasRecuperadas, 2, ',', '.') }}</p>
                        </div>
                        <p class="text-sm text-gray-500 mt-2">Valores recebidos por cancelamento</p>
                    </div>
                </div>

                <!-- Pior Horário -->
                <div class="glass-card rounded-2xl shadow-xl overflow-hidden">
                    <div class="p-6 bg-white/70 backdrop-blur-sm border border-white/40">
                        <h3 class="text-gray-500 font-bold uppercase text-sm mb-2">Pior Horário (Maior Fuga)</h3>
                        @if($piorHora)
                            <p class="text-4xl font-black text-[#1A002B]">{{ str_pad($piorHora->hora, 2, '0', STR_PAD_LEFT) }}:00</p>
                            <p class="text-sm text-[#FF2EB6] font-bold mt-2">Teve {{ $piorHora->total }} cancelamentos</p>
                        @else
                            <p class="text-xl font-bold text-gray-400">Sem dados</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Horários Críticos e Motivos -->
                <div class="glass-card rounded-2xl shadow-xl overflow-hidden lg:col-span-1">
                    <div class="p-6 bg-white/70 backdrop-blur-sm border border-white/40">
                        <div class="flex items-center gap-2 border-b border-[#FFD6F4] pb-2 mb-4">
                            <span class="text-[#7B19E5] text-xl">✧</span>
                            <h3 class="font-title text-[#4A00B9]">Top 5 Horários Críticos</h3>
                        </div>
                        @if($horariosCriticos->count() > 0)
                            <ul class="space-y-3">
                                @foreach($horariosCriticos as $hc)
                                    <li class="flex justify-between items-center">
                                        <span class="font-bold text-[#1A002B]">{{ str_pad($hc->hora, 2, '0', STR_PAD_LEFT) }}:00</span>
                                        <span class="bg-[#FF2EB6]/20 text-[#FF2EB6] px-3 py-1 rounded-full text-sm font-bold">
                                            {{ $hc->total }} faltas
                                        </span>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-gray-500 text-sm py-4">Excelente! Ninguém cancelou neste período.</p>
                        @endif

                        <div class="flex items-center gap-2 border-b border-[#FFD6F4] pb-2 mt-8 mb-4">
                            <span class="text-[#7B19E5] text-xl">✧</span>
                            <h3 class="font-title text-[#4A00B9]">Principais Motivos</h3>
                        </div>
                        @if($motivos->count() > 0)
                            <ul class="space-y-3">
                                @foreach($motivos as $motivo => $qtd)
                                    <li class="flex justify-between items-center text-sm">
                                        <span class="text-gray-600 truncate mr-2" title="{{ $motivo }}">{{ $motivo }}</span>
                                        <span class="text-gray-500 font-bold">{{ $qtd }}x</span>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-gray-500 text-sm py-4">Nenhum motivo registado nas observações.</p>
                        @endif
                    </div>
                </div>

                <!-- Ranking de Ofensores -->
                <div class="glass-card rounded-2xl shadow-xl overflow-hidden lg:col-span-2">
                    <div class="p-6 bg-white/70 backdrop-blur-sm border border-white/40">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="text-[#7B19E5] text-xl">✧</span>
                            <h3 class="font-title text-[#4A00B9] text-lg">Ranking de Ofensores (Top 10)</h3>
                        </div>
                        <p class="text-sm text-gray-500 mb-4">Clientes que mais desmarcaram ou faltaram no período.</p>

                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse whitespace-nowrap">
                                <thead>
                                    <tr class="bg-[#7B19E5]/10 border-b border-[#FFD6F4] text-sm">
                                        <th class="p-3 rounded-tl-lg text-[#4A00B9]">Cliente</th>
                                        <th class="p-3 text-center text-[#4A00B9]">Faltas/Cancelamentos</th>
                                        <th class="p-3 text-right text-[#4A00B9]">Potencial Prejuízo</th>
                                        <th class="p-3 text-center rounded-tr-lg text-[#4A00B9]">Ação</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($ofensores as $ofensor)
                                        <tr class="border-b border-[#FFD6F4] hover:bg-white/50 transition">
                                            <td class="p-3">
                                                <p class="font-bold text-[#1A002B]">{{ $ofensor->nome }}</p>
                                                <p class="text-xs text-gray-500">{{ $ofensor->telefone ?: 'Sem contacto' }}</p>
                                            </td>
                                            <td class="p-3 text-center">
                                                <span class="bg-gradient-to-r from-[#7B19E5] to-[#FF2EB6] text-white px-3 py-1 rounded-full font-bold shadow-sm text-xs">
                                                    {{ $ofensor->total_falhas }}
                                                </span>
                                            </td>
                                            <td class="p-3 text-right font-medium text-gray-600">
                                                R$ {{ number_format($ofensor->prejuizo, 2, ',', '.') }}
                                            </td>
                                            <td class="p-3 text-center">
                                                @if($ofensor->telefone)
                                                    <a href="https://wa.me/55{{ preg_replace('/[^0-9]/', '', $ofensor->telefone) }}" target="_blank" class="inline-block bg-gradient-to-r from-green-500 to-green-600 text-white px-3 py-1.5 rounded-full text-xs font-bold hover:shadow-lg transition-all">
                                                        WhatsApp
                                                    </a>
                                                @endif
                                            <td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="p-8 text-center text-gray-500">
                                                ✧ Nenhum cliente ofensor encontrado neste período.
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
    </div>
</x-app-layout>

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