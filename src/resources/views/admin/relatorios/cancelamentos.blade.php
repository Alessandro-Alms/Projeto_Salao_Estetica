<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('REL006: Análise de Cancelamentos e Faltas') }}
            </h2>
            <a href="{{ route('admin.relatorios.index') ?? '#' }}" class="text-sm text-blue-600 hover:underline">⬅ Voltar</a>
        </div>
    </x-slot>

    <div class="container mx-auto px-4 py-6">
        <div class="bg-white p-4 rounded-xl shadow mb-6">
            <form method="GET" action="{{ route('admin.relatorios.cancelamentos') }}" class="flex flex-wrap items-end gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Início</label>
                    <input type="date" name="data_inicio" value="{{ $dataInicio }}" class="border border-gray-300 rounded-lg shadow-sm focus:border-red-500 focus:ring-red-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fim</label>
                    <input type="date" name="data_fim" value="{{ $dataFim }}" class="border border-gray-300 rounded-lg shadow-sm focus:border-red-500 focus:ring-red-500">
                </div>
                <button type="submit" class="bg-red-600 text-white px-5 py-2.5 rounded-lg font-bold hover:bg-red-700 transition shadow-sm">
                    ⚠️ Analisar Evasão
                </button>
            </form>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white border-l-4 border-orange-500 p-6 rounded-xl shadow flex flex-col justify-center">
                <h3 class="text-gray-500 font-bold uppercase text-sm mb-2">Taxa de Evasão Global</h3>
                <div class="flex items-end gap-2">
                    <p class="text-4xl font-black text-orange-600">{{ number_format($taxaEvasao, 1) }}%</p>
                </div>
                <p class="text-sm text-gray-500 mt-2">{{ $totalEvasoes }} faltas em {{ $totalGeral }} agendamentos totais</p>
            </div>

            <div class="bg-gradient-to-br from-red-500 to-red-700 p-6 rounded-xl shadow text-white flex flex-col justify-center relative overflow-hidden">
                <div class="absolute right-4 top-4 opacity-20 text-5xl">📉</div>
                <h3 class="text-sm font-bold opacity-80 uppercase tracking-wider mb-2">Prejuízo Estimado</h3>
                <p class="text-3xl font-black mb-1">R$ {{ number_format($prejuizoTotal, 2, ',', '.') }}</p>
                <p class="text-sm bg-white/20 px-3 py-1 rounded-full inline-block mt-2 self-start">
                    Valor de serviços que não ocorreram
                </p>
            </div>

            @php $piorHora = $horariosCriticos->first(); @endphp
            <div class="bg-white border-l-4 border-red-800 p-6 rounded-xl shadow flex flex-col justify-center">
                <h3 class="text-gray-500 font-bold uppercase text-sm mb-2">Pior Horário (Maior Fuga)</h3>
                @if($piorHora)
                    <p class="text-4xl font-black text-gray-800">{{ str_pad($piorHora->hora, 2, '0', STR_PAD_LEFT) }}:00</p>
                    <p class="text-sm text-red-600 font-bold mt-2">Teve {{ $piorHora->total }} cancelamentos</p>
                @else
                    <p class="text-xl font-bold text-gray-400">Sem dados</p>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="bg-white p-6 rounded-xl shadow lg:col-span-1">
                <h3 class="font-bold mb-4 text-gray-800 border-b pb-2">⏰ Top 5 Horários Críticos</h3>
                @if($horariosCriticos->count() > 0)
                    <ul class="space-y-3">
                        @foreach($horariosCriticos as $hc)
                            <li class="flex justify-between items-center">
                                <span class="font-bold text-gray-700">{{ str_pad($hc->hora, 2, '0', STR_PAD_LEFT) }}:00</span>
                                <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-sm font-bold">
                                    {{ $hc->total }} faltas
                                </span>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-gray-500 text-sm py-4">Excelente! Ninguém cancelou neste período.</p>
                @endif

                <h3 class="font-bold mt-8 mb-4 text-gray-800 border-b pb-2">💬 Principais Motivos</h3>
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

            <div class="bg-white p-6 rounded-xl shadow lg:col-span-2 overflow-x-auto border border-red-100">
                <h3 class="font-bold mb-2 text-gray-800 text-lg flex items-center gap-2">
                    🚨 Ranking de Ofensores (Top 10)
                </h3>
                <p class="text-sm text-gray-500 mb-4">Clientes que mais desmarcaram ou faltaram no período.</p>

                <table class="w-full text-left border-collapse whitespace-nowrap">
                    <thead>
                        <tr class="bg-red-50 text-red-800 border-b border-red-100 text-sm">
                            <th class="p-3 rounded-tl-lg">Cliente</th>
                            <th class="p-3 text-center">Faltas/Cancelamentos</th>
                            <th class="p-3 text-right">Potencial Prejuízo</th>
                            <th class="p-3 text-center rounded-tr-lg">Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ofensores as $ofensor)
                            <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                                <td class="p-3">
                                    <p class="font-bold text-gray-800">{{ $ofensor->nome }}</p>
                                    <p class="text-xs text-gray-500">{{ $ofensor->telefone ?: 'Sem contacto' }}</p>
                                </td>
                                
                                <td class="p-3 text-center">
                                    <span class="bg-red-600 text-white px-3 py-1 rounded-full font-bold shadow-sm">
                                        {{ $ofensor->total_falhas }}
                                    </span>
                                </td>
                                
                                <td class="p-3 text-right font-medium text-gray-600">
                                    R$ {{ number_format($ofensor->prejuizo, 2, ',', '.') }}
                                </td>

                                <td class="p-3 text-center">
                                    @if($ofensor->telefone)
                                        <a href="https://wa.me/55{{ preg_replace('/[^0-9]/', '', $ofensor->telefone) }}" target="_blank" class="inline-block bg-green-500 text-white px-3 py-1.5 rounded text-xs font-bold hover:bg-green-600 transition shadow-sm">
                                            WhatsApp
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="p-8 text-center text-gray-500">
                                    Nenhum cliente ofensor encontrado neste período.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>