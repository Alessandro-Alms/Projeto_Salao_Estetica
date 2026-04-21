<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('REL001: Faturamento por Período') }}
            </h2>
            <a href="{{ route('admin.relatorios.index') ?? '#' }}" class="text-sm text-blue-600 hover:underline">⬅ Voltar</a>
        </div>
    </x-slot>

    <div class="container mx-auto px-4 py-6">
        <div class="bg-white p-4 rounded-xl shadow mb-6">
            <form method="GET" action="{{ route('admin.relatorios.faturamento') }}" class="flex flex-wrap items-end gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Início</label>
                    <input type="date" name="data_inicio" value="{{ $dataInicio }}" class="border border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fim</label>
                    <input type="date" name="data_fim" value="{{ $dataFim }}" class="border border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500">
                </div>
                <button type="submit" class="bg-green-600 text-white px-5 py-2.5 rounded-lg font-bold hover:bg-green-700 transition shadow-sm">
                    Filtrar Período
                </button>
            </form>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-gradient-to-br from-green-500 to-emerald-700 p-6 rounded-xl shadow text-white relative overflow-hidden">
                <div class="absolute right-4 top-4 opacity-20 text-5xl">💰</div>
                <h3 class="text-sm font-bold opacity-80 uppercase tracking-wider mb-2">Faturamento Total</h3>
                <p class="text-4xl font-black mb-1">R$ {{ number_format($faturamentoTotal, 2, ',', '.') }}</p>
                <p class="text-sm bg-white/20 px-3 py-1 rounded-full inline-block mt-2">
                    {{ $qtdTransacoes }} transações realizadas
                </p>
            </div>

            <div class="bg-white border-l-4 border-blue-500 p-6 rounded-xl shadow flex flex-col justify-center">
                <h3 class="text-gray-500 font-bold uppercase text-sm mb-2">Ticket Médio</h3>
                <div class="flex items-end gap-2">
                    <p class="text-4xl font-black text-gray-800">R$ {{ number_format($ticketMedio, 2, ',', '.') }}</p>
                </div>
                <p class="text-sm text-gray-500 mt-2">Gasto médio por cliente/transação</p>
            </div>

            <div class="bg-white border-l-4 {{ $crescimento >= 0 ? 'border-green-500' : 'border-red-500' }} p-6 rounded-xl shadow flex flex-col justify-center">
                <h3 class="text-gray-500 font-bold uppercase text-sm mb-2">Comparativo (Período Anterior)</h3>
                <div class="flex items-center gap-3">
                    <span class="text-4xl font-black {{ $crescimento >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ $crescimento > 0 ? '+' : ''}}{{ number_format($crescimento, 1, ',', '.') }}%
                    </span>
                    <span class="text-2xl {{ $crescimento >= 0 ? 'text-green-500' : 'text-red-500' }}">
                        {{ $crescimento >= 0 ? '↗' : '↘' }}
                    </span>
                </div>
                <p class="text-sm text-gray-500 mt-2">
                    Anterior: R$ {{ number_format($faturamentoAnterior, 2, ',', '.') }}
                </p>
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow">
            <h3 class="font-bold mb-4 text-gray-800 text-lg">Composição do Faturamento</h3>
            
            @php
                $pctServicos = $faturamentoTotal > 0 ? ($receitaServicos / $faturamentoTotal) * 100 : 0;
                $pctVendas = $faturamentoTotal > 0 ? ($receitaVendas / $faturamentoTotal) * 100 : 0;
            @endphp

            <div class="mb-4 flex justify-between text-sm font-bold text-gray-600">
                <span>Serviços ({{ number_format($pctServicos, 1) }}%)</span>
                <span>Produtos/Avulsos ({{ number_format($pctVendas, 1) }}%)</span>
            </div>

            <div class="w-full bg-gray-200 rounded-full h-6 flex overflow-hidden mb-6">
                <div class="bg-blue-500 h-6" style="width: {{ $pctServicos }}%"></div>
                <div class="bg-purple-500 h-6" style="width: {{ $pctVendas }}%"></div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="p-4 border border-blue-100 bg-blue-50 rounded-lg flex justify-between items-center">
                    <div>
                        <p class="text-sm font-bold text-blue-800 uppercase">✂️ Serviços</p>
                        <p class="text-xs text-blue-600">Agendamentos Executados</p>
                    </div>
                    <p class="text-xl font-black text-blue-700">R$ {{ number_format($receitaServicos, 2, ',', '.') }}</p>
                </div>

                <div class="p-4 border border-purple-100 bg-purple-50 rounded-lg flex justify-between items-center">
                    <div>
                        <p class="text-sm font-bold text-purple-800 uppercase">🛍️ Vendas</p>
                        <p class="text-xs text-purple-600">Produtos ou Pacotes</p>
                    </div>
                    <p class="text-xl font-black text-purple-700">R$ {{ number_format($receitaVendas, 2, ',', '.') }}</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>