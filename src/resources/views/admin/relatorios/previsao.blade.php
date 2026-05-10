<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex items-center gap-2">
                <span class="text-[#7B19E5] text-xl">✧</span>
                <h2 class="font-title text-xl text-[#1A002B]">
                    {{ __('REL012: Previsão de Demanda (Próximos 7 Dias)') }}
                </h2>
            </div>
            <a href="{{ route('admin.relatorios.index') ?? '#' }}" class="text-sm text-[#7B19E5] hover:text-[#FF2EB6] transition-colors inline-flex items-center gap-1">
                <i class="fa-solid fa-arrow-left text-xs"></i> Voltar ao Hub
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
            <!-- Banner de Previsão -->
            <div class="glass-card rounded-2xl shadow-xl overflow-hidden mb-6">
                <div class="p-6 bg-gradient-to-r from-[#7B19E5] to-[#FF2EB6] text-white">
                    <div class="flex justify-between items-center">
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-2xl">✧</span>
                                <h3 class="text-xl font-black">Algoritmo de Previsão Ativado</h3>
                            </div>
                            <p class="text-white/80 text-sm max-w-2xl">
                                O sistema analisou o comportamento dos clientes nas últimas 4 semanas e cruzou com o calendário de feriados para estimar o fluxo dos próximos 7 dias.
                            </p>
                        </div>
                        <div class="hidden md:block text-6xl opacity-30">✧</div>
                    </div>
                </div>
            </div>

            <!-- Cards de resumo -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <!-- Estimativa Total -->
                <div class="glass-card rounded-2xl shadow-xl overflow-hidden">
                    <div class="p-6 bg-white/70 backdrop-blur-sm border border-white/40">
                        <h3 class="text-gray-500 font-bold uppercase text-sm mb-2">Estimativa Total (Semana)</h3>
                        <p class="text-4xl font-black text-[#7B19E5]">{{ $totalPrevisao }}</p>
                        <p class="text-sm text-gray-500 mt-2">Agendamentos esperados</p>
                    </div>
                </div>

                <!-- Dias de Pico -->
                <div class="glass-card rounded-2xl shadow-xl overflow-hidden">
                    <div class="p-6 bg-white/70 backdrop-blur-sm border border-white/40">
                        <h3 class="text-gray-500 font-bold uppercase text-sm mb-2">Dias de Pico (Alta)</h3>
                        <p class="text-4xl font-black text-[#FF2EB6]">{{ $diasDeAlta }}</p>
                        <p class="text-sm text-gray-500 mt-2">Prepara a tua equipe nestes dias</p>
                    </div>
                </div>

                <!-- Dias Fracos -->
                <div class="glass-card rounded-2xl shadow-xl overflow-hidden">
                    <div class="p-6 bg-white/70 backdrop-blur-sm border border-white/40">
                        <h3 class="text-gray-500 font-bold uppercase text-sm mb-2">Dias Fracos / Feriados</h3>
                        <p class="text-4xl font-black text-[#A855F7]">{{ $diasDeBaixa }}</p>
                        <p class="text-sm text-gray-500 mt-2">Ideais para lançar promoções relâmpago</p>
                    </div>
                </div>
            </div>

            <!-- Calendário de Projeção -->
            <div class="glass-card rounded-2xl shadow-xl overflow-hidden">
                <div class="p-6 bg-white/70 backdrop-blur-sm border border-white/40">
                    <div class="flex items-center gap-2 border-b border-[#FFD6F4] pb-2 mb-4">
                        <span class="text-[#7B19E5] text-xl">✧</span>
                        <h3 class="font-title text-[#4A00B9] text-lg">Calendário de Projeção</h3>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse whitespace-nowrap">
                            <thead>
                                <tr class="bg-[#7B19E5]/10 border-b border-[#FFD6F4]">
                                    <th class="p-4 rounded-tl-lg text-[#4A00B9] text-xs font-medium uppercase">Data</th>
                                    <th class="p-4 text-center text-[#4A00B9] text-xs font-medium uppercase">Estimativa de Clientes</th>
                                    <th class="p-4 text-center text-[#4A00B9] text-xs font-medium uppercase">Feriado?</th>
                                    <th class="p-4 text-center rounded-tr-lg text-[#4A00B9] text-xs font-medium uppercase">Tendência / Ação Sugerida</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($proximos7Dias as $dia)
                                    <tr class="border-b border-[#FFD6F4] transition {{ $dia->is_hoje ? 'bg-[#7B19E5]/5' : 'hover:bg-white/50' }}">
                                        <td class="p-4">
                                            <p class="font-bold {{ $dia->is_hoje ? 'text-[#7B19E5]' : 'text-[#1A002B]' }}">
                                                {{ $dia->dia_nome }}
                                                @if($dia->is_hoje) <span class="bg-[#7B19E5]/20 text-[#7B19E5] text-xs px-2 py-0.5 rounded-full ml-2">HOJE</span> @endif
                                            </p>
                                            <p class="text-xs text-gray-500">{{ $dia->data_br }}</p>
                                        </td>
                                        <td class="p-4 text-center">
                                            <span class="text-xl font-black {{ $dia->previsao_agendamentos >= 10 ? 'text-[#7B19E5]' : 'text-gray-700' }}">
                                                {{ $dia->previsao_agendamentos }}
                                            </span>
                                            <span class="ml-1 text-xs text-gray-500">
                                                {{ $dia->previsao_agendamentos === 1 ? 'cliente' : 'clientes' }}
                                            </span>
                                        </td>
                                        <td class="p-4 text-center">
                                            @if($dia->feriado)
                                                <span class="bg-[#FF2EB6]/20 text-[#FF2EB6] px-3 py-1 rounded-full text-xs font-bold flex items-center justify-center gap-1 mx-auto w-max">
                                                    {{ $dia->feriado }}
                                                </span>
                                            @else
                                                <span class="text-gray-300">-</span>
                                            @endif
                                        </td>
                                        <td class="p-4 text-center">
                                            @if($dia->tendencia == 'Alta Demanda')
                                                <span class="text-[#7B19E5] font-bold text-sm flex items-center justify-center gap-1">
                                                    ✧ Alta (Reforçar equipa)
                                                </span>
                                            @elseif($dia->tendencia == 'Baixa Demanda')
                                                <span class="text-[#FF2EB6] font-bold text-sm flex items-center justify-center gap-1">
                                                    ✦ Baixa (Fazer promoções)
                                                </span>
                                            @elseif($dia->tendencia == 'Feriado / Baixa')
                                                <span class="text-[#FF2EB6] font-bold text-sm flex items-center justify-center gap-1">
                                                    ✧ Alerta Feriado
                                                </span>
                                            @else
                                                <span class="text-gray-500 font-medium text-sm">
                                                    ✦ Normal
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4 p-4 bg-white/50 text-gray-500 text-xs rounded-xl border border-[#FFD6F4] text-center">
                        * Os valores apresentados são estimativas matemáticas baseadas no volume de atendimentos das últimas 4 semanas. Resultados reais podem variar.
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