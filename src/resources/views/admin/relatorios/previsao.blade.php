<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('REL012: Previsão de Demanda (Próximos 7 Dias)') }}
            </h2>
            <a href="{{ route('admin.relatorios.index') ?? '#' }}" class="text-sm text-blue-600 hover:underline">⬅ Voltar ao Hub</a>
        </div>
    </x-slot>

    <div class="container mx-auto px-4 py-6">
        
        <div class="bg-gradient-to-r from-cyan-600 to-blue-700 rounded-xl shadow-lg p-6 mb-8 text-white flex items-center justify-between">
            <div>
                <h3 class="text-xl font-black mb-1 flex items-center gap-2">
                    🤖 Algoritmo de Previsão Ativado
                </h3>
                <p class="text-cyan-100 text-sm max-w-2xl">
                    O sistema analisou o comportamento dos clientes nas últimas 4 semanas e cruzou com o calendário de feriados para estimar o fluxo dos próximos 7 dias.
                </p>
            </div>
            <div class="hidden md:block text-6xl opacity-50">🔮</div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white border-l-4 border-cyan-500 p-6 rounded-xl shadow flex flex-col justify-center">
                <h3 class="text-gray-500 font-bold uppercase text-sm mb-2">Estimativa Total (Semana)</h3>
                <p class="text-4xl font-black text-cyan-600">{{ $totalPrevisao }}</p>
                <p class="text-sm text-gray-500 mt-2">Agendamentos esperados</p>
            </div>

            <div class="bg-white border-l-4 border-green-500 p-6 rounded-xl shadow flex flex-col justify-center">
                <h3 class="text-gray-500 font-bold uppercase text-sm mb-2">Dias de Pico (Alta)</h3>
                <p class="text-4xl font-black text-green-600">{{ $diasDeAlta }}</p>
                <p class="text-sm text-gray-500 mt-2">Prepara a tua equipa nestes dias</p>
            </div>

            <div class="bg-white border-l-4 border-orange-400 p-6 rounded-xl shadow flex flex-col justify-center relative">
                <h3 class="text-gray-500 font-bold uppercase text-sm mb-2">Dias Fracos / Feriados</h3>
                <p class="text-4xl font-black text-orange-500">{{ $diasDeBaixa }}</p>
                <p class="text-sm text-gray-500 mt-2">Ideais para lançar promoções relâmpago</p>
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow overflow-hidden border border-gray-100">
            <h3 class="font-bold mb-4 text-gray-800 text-lg flex items-center gap-2 border-b pb-2">
                📅 Calendário de Projeção
            </h3>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse whitespace-nowrap">
                    <thead>
                        <tr class="bg-gray-50 text-gray-600 border-b border-gray-200">
                            <th class="p-4 rounded-tl-lg">Data</th>
                            <th class="p-4 text-center">Estimativa de Clientes</th>
                            <th class="p-4 text-center">Feriado?</th>
                            <th class="p-4 text-center rounded-tr-lg">Tendência / Ação Sugerida</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($proximos7Dias as $dia)
                            <tr class="border-b border-gray-100 transition {{ $dia->is_hoje ? 'bg-cyan-50/30' : 'hover:bg-gray-50' }}">
                                <td class="p-4">
                                    <p class="font-bold {{ $dia->is_hoje ? 'text-cyan-700' : 'text-gray-800' }}">
                                        {{ $dia->dia_nome }}
                                        @if($dia->is_hoje) <span class="bg-cyan-100 text-cyan-800 text-xs px-2 py-0.5 rounded-full ml-2">HOJE</span> @endif
                                    </p>
                                    <p class="text-xs text-gray-500">{{ $dia->data_br }}</p>
                                </td>
                                
                                <td class="p-4 text-center">
                                    <span class="text-xl font-black {{ $dia->previsao_agendamentos >= 10 ? 'text-green-600' : 'text-gray-700' }}">
                                        ~{{ $dia->previsao_agendamentos }}
                                    </span>
                                </td>

                                <td class="p-4 text-center">
                                    @if($dia->feriado)
                                        <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs font-bold flex items-center justify-center gap-1 mx-auto w-max">
                                            🎉 {{ $dia->feriado }}
                                        </span>
                                    @else
                                        <span class="text-gray-300">-</span>
                                    @endif
                                </td>

                                <td class="p-4 text-center">
                                    @if($dia->tendencia == 'Alta Demanda')
                                        <span class="text-green-600 font-bold text-sm flex items-center justify-center gap-1">
                                            📈 Alta (Reforçar equipa)
                                        </span>
                                    @elseif($dia->tendencia == 'Baixa Demanda')
                                        <span class="text-orange-500 font-bold text-sm flex items-center justify-center gap-1">
                                            📉 Baixa (Fazer promoções)
                                        </span>
                                    @elseif($dia->tendencia == 'Feriado / Baixa')
                                        <span class="text-red-500 font-bold text-sm flex items-center justify-center gap-1">
                                            🏖️ Alerta Feriado
                                        </span>
                                    @else
                                        <span class="text-gray-500 font-medium text-sm">
                                            ⚖️ Normal
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4 p-4 bg-gray-50 text-gray-500 text-xs rounded-lg border border-gray-200 text-center">
                * Os valores apresentados são estimativas matemáticas baseadas no volume de atendimentos das últimas 4 semanas. Resultados reais podem variar.
            </div>
        </div>
    </div>
</x-app-layout>