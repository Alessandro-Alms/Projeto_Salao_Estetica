<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex items-center gap-2">
                <span class="text-[#7B19E5] text-xl">✧</span>
                <h2 class="font-title text-xl text-[#1A002B]">
                    {{ __('REL010: Sazonalidade de Serviços') }}
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
                    <form method="GET" action="{{ route('admin.relatorios.sazonalidade') }}" class="flex flex-wrap items-end gap-4">
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
                            Ver Sazonalidade
                        </button>
                    </form>
                </div>
            </div>

            <!-- Downloads -->
            <div class="flex gap-2 mb-6 flex-wrap">
                <a href="{{ route('admin.relatorios.sazonalidade.download-excel', ['data_inicio' => $dataInicio, 'data_fim' => $dataFim]) }}" class="flex items-center gap-2 bg-white/50 text-[#00B050] border border-[#FFD6F4] px-4 py-2 rounded-lg hover:bg-white/80 transition font-medium">
                    ✧ Exportar Excel
                </a>
            </div>

            <!-- Cards de resumo -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <!-- Dia Mais Forte -->
                <div class="glass-card rounded-2xl shadow-xl overflow-hidden">
                    <div class="p-6 bg-gradient-to-br from-[#7B19E5] to-[#A855F7] text-white">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="text-sm font-bold opacity-80 uppercase tracking-wider mb-2">Dia Mais Forte (Pico)</h3>
                                @if($diaMaisMovimentado && $diaMaisMovimentado->total_servicos > 0)
                                    <p class="text-2xl font-black mb-1">{{ $diaMaisMovimentado->dia_nome }}</p>
                                    <p class="text-sm bg-white/20 px-3 py-1 rounded-full inline-block mt-1">
                                        {{ $diaMaisMovimentado->total_servicos }} serviços realizados
                                    </p>
                                @else
                                    <p class="text-xl font-bold">Sem dados</p>
                                @endif
                            </div>
                            <div class="opacity-30 text-4xl">✧</div>
                        </div>
                    </div>
                </div>

                <!-- Dia Mais Fraco -->
                <div class="glass-card rounded-2xl shadow-xl overflow-hidden">
                    <div class="p-6 bg-white/70 backdrop-blur-sm border border-white/40">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="text-gray-500 font-bold uppercase text-sm mb-2">Dia Mais Fraco (Atenção)</h3>
                                @if($diaMenosMovimentado)
                                    <p class="text-2xl font-black text-[#1A002B]">{{ $diaMenosMovimentado->dia_nome }}</p>
                                    <p class="text-sm text-[#FF2EB6] font-bold mt-1">Ótimo dia para promoções</p>
                                @else
                                    <p class="text-xl font-bold text-gray-400">Sem dados</p>
                                @endif
                            </div>
                            <div class="opacity-30 text-3xl">✧</div>
                        </div>
                    </div>
                </div>

                <!-- Total de Serviços -->
                <div class="glass-card rounded-2xl shadow-xl overflow-hidden">
                    <div class="p-6 bg-white/70 backdrop-blur-sm border border-white/40">
                        <h3 class="text-gray-500 font-bold uppercase text-sm mb-2">Total de Serviços Analisados</h3>
                        <p class="text-4xl font-black text-[#FF2EB6]">{{ $totalAgendamentos }}</p>
                        <p class="text-sm text-gray-500 mt-2">Agendamentos concluídos no período</p>
                    </div>
                </div>
            </div>

            <!-- Gráfico de Volume por Dia -->
            <div class="glass-card rounded-2xl shadow-xl overflow-hidden">
                <div class="p-6 bg-white/70 backdrop-blur-sm border border-white/40">
                    <div class="flex items-center gap-2 border-b border-[#FFD6F4] pb-2 mb-4">
                        <span class="text-[#7B19E5] text-xl">✧</span>
                        <h3 class="font-title text-[#4A00B9] text-lg">Volume de Serviços por Dia da Semana</h3>
                    </div>
                    
                    @php
                        $maxServicos = $diaMaisMovimentado ? $diaMaisMovimentado->total_servicos : 1;
                        if ($maxServicos == 0) $maxServicos = 1;
                    @endphp

                    <div class="space-y-6">
                        @foreach($sazonalidadeCompleta as $index => $dia)
                            @php
                                $percentual = ($dia->total_servicos / $maxServicos) * 100;
                            @endphp
                            <div>
                                <div class="flex justify-between items-end mb-1">
                                    <span class="text-sm font-bold {{ $index == 0 && $dia->total_servicos > 0 ? 'text-[#7B19E5]' : 'text-gray-700' }}">
                                        @if($index == 0 && $dia->total_servicos > 0) ✧ @endif
                                        {{ $dia->dia_nome }}
                                    </span>
                                    <div class="text-right">
                                        <span class="text-lg font-black {{ $index == 0 && $dia->total_servicos > 0 ? 'text-[#7B19E5]' : 'text-[#1A002B]' }}">
                                            {{ $dia->total_servicos }} 
                                            <span class="text-xs font-normal text-gray-500">serviços</span>
                                        </span>
                                        <span class="ml-2 text-sm text-[#FF2EB6] font-medium">(R$ {{ number_format($dia->receita_gerada, 2, ',', '.') }})</span>
                                    </div>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-4 overflow-hidden">
                                    <div class="{{ $index == 0 && $dia->total_servicos > 0 ? 'bg-gradient-to-r from-[#7B19E5] to-[#FF2EB6]' : 'bg-gradient-to-r from-[#7B19E5] to-[#A855F7]' }} h-4 rounded-full transition-all duration-500" style="width: {{ $percentual }}%"></div>
                                </div>
                            </div>
                        @endforeach
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