<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('REL010: Sazonalidade de Serviços') }}
            </h2>
            <a href="{{ route('admin.relatorios.index') ?? '#' }}" class="text-sm text-blue-600 hover:underline">⬅ Voltar</a>
        </div>
    </x-slot>

    <div class="container mx-auto px-4 py-6">
        <div class="bg-white p-4 rounded-xl shadow mb-6">
            <form method="GET" action="{{ route('admin.relatorios.sazonalidade') }}" class="flex flex-wrap items-end gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Início</label>
                    <input type="date" name="data_inicio" value="{{ $dataInicio }}" class="border border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fim</label>
                    <input type="date" name="data_fim" value="{{ $dataFim }}" class="border border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <button type="submit" class="bg-indigo-600 text-white px-5 py-2.5 rounded-lg font-bold hover:bg-indigo-700 transition shadow-sm">
                    Ver Sazonalidade
                </button>
            </form>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-gradient-to-br from-indigo-500 to-blue-700 p-6 rounded-xl shadow text-white flex flex-col justify-center relative overflow-hidden">
                <div class="absolute right-4 top-4 opacity-20 text-5xl">🔥</div>
                <h3 class="text-sm font-bold opacity-90 uppercase tracking-wider mb-2">Dia Mais Forte (Pico)</h3>
                @if($diaMaisMovimentado && $diaMaisMovimentado->total_servicos > 0)
                    <p class="text-3xl font-black mb-1">{{ $diaMaisMovimentado->dia_nome }}</p>
                    <p class="text-sm bg-white/20 px-3 py-1 rounded-full inline-block self-start mt-1">
                        {{ $diaMaisMovimentado->total_servicos }} serviços realizados
                    </p>
                @else
                    <p class="text-xl font-bold">Sem dados</p>
                @endif
            </div>

            <div class="bg-white border-l-4 border-orange-400 p-6 rounded-xl shadow flex flex-col justify-center relative">
                <div class="absolute right-4 top-4 text-3xl opacity-50">📉</div>
                <h3 class="text-gray-500 font-bold uppercase text-sm mb-2">Dia Mais Fraco (Atenção)</h3>
                @if($diaMenosMovimentado)
                    <p class="text-3xl font-black text-gray-800">{{ $diaMenosMovimentado->dia_nome }}</p>
                    <p class="text-sm text-orange-600 font-bold mt-1">Ótimo dia para promoções</p>
                @else
                    <p class="text-xl font-bold text-gray-400">Sem dados</p>
                @endif
            </div>

            <div class="bg-white border-l-4 border-green-500 p-6 rounded-xl shadow flex flex-col justify-center">
                <h3 class="text-gray-500 font-bold uppercase text-sm mb-2">Total de Serviços Analisados</h3>
                <p class="text-4xl font-black text-green-600">{{ $totalAgendamentos }}</p>
                <p class="text-sm text-gray-500 mt-2">Agendamentos concluídos no período</p>
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow">
            <h3 class="font-bold mb-6 text-gray-800 text-lg flex items-center gap-2 border-b pb-2">
                📊 Volume de Serviços por Dia da Semana
            </h3>
            
            <div class="space-y-6">
                @php
                    $maxServicos = $diaMaisMovimentado ? $diaMaisMovimentado->total_servicos : 1;
                    if ($maxServicos == 0) $maxServicos = 1; // Evitar divisão por zero
                @endphp

                @foreach($sazonalidadeCompleta as $index => $dia)
                    @php
                        $percentual = ($dia->total_servicos / $maxServicos) * 100;
                    @endphp
                    <div>
                        <div class="flex justify-between items-end mb-1">
                            <span class="text-sm font-bold {{ $index == 0 && $dia->total_servicos > 0 ? 'text-indigo-600' : 'text-gray-700' }}">
                                @if($index == 0 && $dia->total_servicos > 0) 🏆 @endif
                                {{ $dia->dia_nome }}
                            </span>
                            <div class="text-right">
                                <span class="text-lg font-black text-gray-800">{{ $dia->total_servicos }} <span class="text-xs font-normal text-gray-500">serviços</span></span>
                                <span class="ml-2 text-sm text-green-600 font-medium">(R$ {{ number_format($dia->receita_gerada, 2, ',', '.') }})</span>
                            </div>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-4">
                            <div class="{{ $index == 0 ? 'bg-indigo-500' : 'bg-blue-400' }} h-4 rounded-full transition-all duration-500" style="width: {{ $percentual }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>