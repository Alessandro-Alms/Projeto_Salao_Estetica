<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('REL005: Fidelização e Clientes VIP') }}
            </h2>
            <a href="{{ route('admin.relatorios.index') ?? '#' }}" class="text-sm text-blue-600 hover:underline">⬅ Voltar</a>
        </div>
    </x-slot>

    <div class="container mx-auto px-4 py-6">
        <div class="bg-white p-4 rounded-xl shadow mb-6">
            <form method="GET" action="{{ route('admin.relatorios.fidelizacao') }}" class="flex flex-wrap items-end gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Início</label>
                    <input type="date" name="data_inicio" value="{{ $dataInicio }}" class="border border-gray-300 rounded-lg shadow-sm focus:border-purple-500 focus:ring-purple-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fim</label>
                    <input type="date" name="data_fim" value="{{ $dataFim }}" class="border border-gray-300 rounded-lg shadow-sm focus:border-purple-500 focus:ring-purple-500">
                </div>
                <button type="submit" class="bg-purple-600 text-white px-5 py-2.5 rounded-lg font-bold hover:bg-purple-700 transition shadow-sm">
                    Analisar Fidelização
                </button>
            </form>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-gradient-to-br from-indigo-500 to-purple-600 p-6 rounded-xl shadow text-white flex flex-col justify-center relative overflow-hidden">
                <div class="absolute right-4 top-4 opacity-20 text-5xl">🔁</div>
                <h3 class="text-sm font-bold opacity-80 uppercase tracking-wider mb-1">Taxa de Retorno</h3>
                <p class="text-4xl font-black mb-1">{{ number_format($taxaRetorno, 1) }}%</p>
                <p class="text-sm mt-1 bg-white/20 px-3 py-1 rounded-full self-start">
                    {{ $clientesRetornaram }} de {{ $totalClientesAtendidos }} clientes voltaram
                </p>
            </div>

            <div class="bg-white border-l-4 border-yellow-400 p-6 rounded-xl shadow flex flex-col justify-center relative">
                <div class="absolute right-4 top-4 text-3xl opacity-50">👑</div>
                <h3 class="text-gray-500 font-bold uppercase text-sm mb-2">Maior Comprador (VIP 1)</h3>
                @if($clienteTop1)
                    <p class="text-2xl font-black text-gray-800 truncate" title="{{ $clienteTop1->name }}">{{ $clienteTop1->name }}</p>
                    <p class="text-sm font-bold text-green-600 mt-1">R$ {{ number_format($clienteTop1->valor_gasto_total, 2, ',', '.') }} investidos</p>
                @else
                    <p class="text-xl font-bold text-gray-400">Sem clientes</p>
                @endif
            </div>

            <div class="bg-white border-l-4 border-blue-500 p-6 rounded-xl shadow flex flex-col justify-center">
                <h3 class="text-gray-500 font-bold uppercase text-sm mb-2">Clientes Únicos Atendidos</h3>
                <p class="text-4xl font-black text-blue-600">{{ $totalClientesAtendidos }}</p>
                <p class="text-sm text-gray-500 mt-2">No período selecionado</p>
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow overflow-x-auto">
            <h3 class="font-bold mb-4 text-gray-800 text-lg flex items-center gap-2">
                🌟 Ranking de Clientes VIP
            </h3>
            <p class="text-sm text-gray-500 mb-6">Lista de clientes ordenada pelo valor total investido no salão neste período.</p>
            
            <table class="w-full text-left border-collapse whitespace-nowrap">
                <thead>
                    <tr class="bg-purple-50 text-purple-800 border-b border-purple-100">
                        <th class="p-3 rounded-tl-lg">Posição</th>
                        <th class="p-3">Cliente / Telefone</th>
                        <th class="p-3 text-center">Pontos (Fidelidade)</th>
                        <th class="p-3 text-center">Visitas (Período)</th>
                        <th class="p-3 text-center">Última Visita</th>
                        <th class="p-3 text-right rounded-tr-lg">Total Gasto</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($clientes as $index => $cliente)
                        <tr class="border-b border-gray-100 hover:bg-gray-50 transition {{ $index < 3 ? 'bg-yellow-50/30' : '' }}">
                            <td class="p-3 font-bold text-gray-500 text-lg">
                                @if($index == 0) 🥇
                                @elseif($index == 1) 🥈
                                @elseif($index == 2) 🥉
                                @else #{{ $index + 1 }}
                                @endif
                            </td>
                            
                            <td class="p-3">
                                <p class="font-bold text-gray-800">{{ $cliente->name }}</p>
                                <p class="text-xs text-gray-500">{{ $cliente->telefone ?: 'Sem contacto' }}</p>
                            </td>
                            
                            <td class="p-3 text-center">
                                <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs font-bold">
                                    {{ $cliente->contador_fidelidade }} pts
                                </span>
                            </td>
                            
                            <td class="p-3 text-center">
                                <span class="bg-gray-100 text-gray-700 px-3 py-1 rounded-full font-bold">
                                    {{ $cliente->total_visitas }}
                                </span>
                            </td>

                            <td class="p-3 text-center text-sm text-gray-600">
                                {{ \Carbon\Carbon::parse($cliente->ultima_visita)->format('d/m/Y') }}
                            </td>
                            
                            <td class="p-3 text-right font-black {{ $index < 3 ? 'text-green-600' : 'text-gray-700' }}">
                                R$ {{ number_format($cliente->valor_gasto_total, 2, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-gray-500">
                                Nenhum cliente atendido neste período.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>