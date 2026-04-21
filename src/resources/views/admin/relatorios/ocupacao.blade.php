<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('REL002: Ocupação da Agenda') }}
            </h2>
            <a href="{{ route('admin.relatorios.index') ?? '#' }}" class="text-sm text-blue-600 hover:underline">⬅ Voltar</a>
        </div>
    </x-slot>

    <div class="container mx-auto px-4 py-6">
        <div class="bg-white p-4 rounded-xl shadow mb-6">
            <form method="GET" action="{{ route('admin.relatorios.ocupacao') }}" class="flex flex-wrap items-end gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Início</label>
                    <input type="date" name="data_inicio" value="{{ $dataInicio }}" class="border border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fim</label>
                    <input type="date" name="data_fim" value="{{ $dataFim }}" class="border border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <button type="submit" class="bg-indigo-600 text-white px-5 py-2.5 rounded-lg font-bold hover:bg-indigo-700 transition shadow-sm">
                    Analisar Ocupação
                </button>
            </form>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white border-l-4 border-indigo-500 p-6 rounded-xl shadow flex flex-col justify-center">
                <h3 class="text-gray-500 font-bold uppercase text-sm mb-2">Volume Total</h3>
                <p class="text-4xl font-black text-gray-800">{{ $totalAgendamentos }}</p>
                <p class="text-sm text-gray-500 mt-2">Agendamentos válidos no período</p>
            </div>

            <div class="bg-gradient-to-br from-orange-400 to-red-500 p-6 rounded-xl shadow text-white relative overflow-hidden flex flex-col justify-center">
                <div class="absolute right-4 top-4 opacity-20 text-5xl">🔥</div>
                <h3 class="text-sm font-bold opacity-80 uppercase tracking-wider mb-2">Horário de Pico</h3>
                @if($horarioPico)
                    <p class="text-4xl font-black mb-1">{{ str_pad($horarioPico->hora, 2, '0', STR_PAD_LEFT) }}:00</p>
                    <p class="text-sm bg-white/20 px-3 py-1 rounded-full inline-block mt-2 self-start">
                        {{ $horarioPico->total }} atendimentos
                    </p>
                @else
                    <p class="text-xl font-bold">Sem dados</p>
                @endif
            </div>

            <div class="bg-gradient-to-br from-blue-400 to-cyan-500 p-6 rounded-xl shadow text-white relative overflow-hidden flex flex-col justify-center">
                <div class="absolute right-4 top-4 opacity-20 text-5xl">❄️</div>
                <h3 class="text-sm font-bold opacity-80 uppercase tracking-wider mb-2">Horário Morto</h3>
                @if($horarioMorto)
                    <p class="text-4xl font-black mb-1">{{ str_pad($horarioMorto->hora, 2, '0', STR_PAD_LEFT) }}:00</p>
                    <p class="text-sm bg-white/20 px-3 py-1 rounded-full inline-block mt-2 self-start">
                        Apenas {{ $horarioMorto->total }} atendimentos
                    </p>
                @else
                    <p class="text-xl font-bold">Sem dados</p>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white p-6 rounded-xl shadow">
                <h3 class="font-bold mb-6 text-gray-800 text-lg border-b pb-2">Distribuição por Horário</h3>
                
                @if($ocupacaoPorHora->count() > 0)
                    @php $maxHora = $ocupacaoPorHora->max('total'); @endphp
                    <div class="space-y-3">
                        @foreach($ocupacaoPorHora as $hora)
                            @php $percentual = ($hora->total / $maxHora) * 100; @endphp
                            <div class="flex items-center gap-3">
                                <span class="w-12 text-sm font-bold text-gray-600 text-right">{{ str_pad($hora->hora, 2, '0', STR_PAD_LEFT) }}:00</span>
                                <div class="flex-1 bg-gray-100 rounded-full h-4 overflow-hidden">
                                    <div class="bg-indigo-500 h-full rounded-full" style="width: {{ $percentual }}%"></div>
                                </div>
                                <span class="w-8 text-sm text-gray-500">{{ $hora->total }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-center py-4">Nenhum agendamento encontrado.</p>
                @endif
            </div>

            <div class="bg-white p-6 rounded-xl shadow">
                <h3 class="font-bold mb-6 text-gray-800 text-lg border-b pb-2">Dias Mais Movimentados</h3>
                
                @if($ocupacaoPorDia->count() > 0)
                    @php $maxDia = $ocupacaoPorDia->max('total'); @endphp
                    <div class="space-y-4">
                        {{-- Nova ordem do Carbon: 1=Segunda, 2=Terça... 6=Sábado, 0=Domingo --}}
                        @foreach([1, 2, 3, 4, 5, 6, 0] as $diaSemana) 
                            @php 
                                $total = isset($ocupacaoPorDia[$diaSemana]) ? $ocupacaoPorDia[$diaSemana]->total : 0;
                                $percentual = $maxDia > 0 ? ($total / $maxDia) * 100 : 0;
                            @endphp
                            <div class="flex items-center gap-3">
                                <span class="w-20 text-sm font-bold text-gray-600 text-right">{{ $nomesDias[$diaSemana] }}</span>
                                <div class="flex-1 bg-gray-100 rounded-full h-5 overflow-hidden">
                                    <div class="{{ $percentual == 100 ? 'bg-orange-500' : 'bg-blue-400' }} h-full rounded-full transition-all" style="width: {{ $percentual }}%"></div>
                                </div>
                                <span class="w-8 text-sm text-gray-500 font-medium">{{ $total }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-center py-4">Nenhum agendamento encontrado.</p>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>