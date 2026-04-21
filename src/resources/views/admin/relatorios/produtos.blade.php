<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('REL004: Produtos Mais Vendidos') }}
            </h2>
            <a href="{{ route('admin.relatorios.index') ?? '#' }}" class="text-sm text-blue-600 hover:underline">⬅ Voltar</a>
        </div>
    </x-slot>

    <div class="container mx-auto px-4 py-6">
        <div class="bg-white p-4 rounded-xl shadow mb-6">
            <form method="GET" action="{{ route('admin.relatorios.produtos') }}" class="flex flex-wrap items-end gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Início</label>
                    <input type="date" name="data_inicio" value="{{ $dataInicio }}" class="border border-gray-300 rounded-lg shadow-sm focus:border-pink-500 focus:ring-pink-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fim</label>
                    <input type="date" name="data_fim" value="{{ $dataFim }}" class="border border-gray-300 rounded-lg shadow-sm focus:border-pink-500 focus:ring-pink-500">
                </div>
                <button type="submit" class="bg-pink-600 text-white px-5 py-2.5 rounded-lg font-bold hover:bg-pink-700 transition shadow-sm">
                    Filtrar Vendas
                </button>
            </form>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-gradient-to-br from-pink-500 to-rose-600 p-6 rounded-xl shadow text-white flex flex-col justify-center relative overflow-hidden">
                <div class="absolute right-4 top-4 opacity-20 text-5xl">🏆</div>
                <h3 class="text-sm font-bold opacity-80 uppercase tracking-wider mb-2">Produto Campeão (Giro)</h3>
                @if($campeao)
                    <p class="text-2xl font-black mb-1 truncate">{{ $campeao->nome }}</p>
                    <p class="text-sm bg-white/20 px-3 py-1 rounded-full inline-block self-start mt-1">
                        {{ $campeao->total_vendido }} unidades vendidas
                    </p>
                @else
                    <p class="text-xl font-bold">Sem vendas</p>
                @endif
            </div>

            <div class="bg-white border-l-4 border-blue-500 p-6 rounded-xl shadow flex flex-col justify-center">
                <h3 class="text-gray-500 font-bold uppercase text-sm mb-2">Giro Total (Unidades)</h3>
                <div class="flex items-end gap-2">
                    <p class="text-4xl font-black text-gray-800">{{ $totalUnidadesVendidas }}</p>
                    <p class="text-sm text-gray-500 mb-1">itens saíram</p>
                </div>
            </div>

            <div class="bg-white border-l-4 border-green-500 p-6 rounded-xl shadow flex flex-col justify-center">
                <h3 class="text-gray-500 font-bold uppercase text-sm mb-2">Receita com Produtos</h3>
                <div class="flex items-end gap-2">
                    <p class="text-4xl font-black text-green-600">R$ {{ number_format($receitaTotal, 2, ',', '.') }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow overflow-x-auto">
            <h3 class="font-bold mb-4 text-gray-800 text-lg flex items-center gap-2">
                📦 Ranking, Giro e Estoque Atual
            </h3>
            
            <table class="w-full text-left border-collapse whitespace-nowrap">
                <thead>
                    <tr class="bg-gray-50 text-gray-600 border-b border-gray-200">
                        <th class="p-3 rounded-tl-lg">#</th>
                        <th class="p-3">Produto</th>
                        <th class="p-3 text-center">Giro (Qtd Vendida)</th>
                        <th class="p-3 text-center">Estoque Atual</th>
                        <th class="p-3 text-right rounded-tr-lg">Receita Gerada</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($produtosVendidos as $index => $produto)
                        <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                            <td class="p-3 font-bold text-gray-400">{{ $index + 1 }}º</td>
                            <td class="p-3 font-bold text-gray-800">{{ $produto->nome }}</td>
                            
                            <td class="p-3 text-center">
                                <span class="bg-blue-50 text-blue-700 px-3 py-1 rounded-full font-bold">
                                    {{ $produto->total_vendido }}
                                </span>
                            </td>
                            
                            <td class="p-3 text-center">
                                @if($produto->quantidade_estoque <= 5)
                                    <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full font-bold text-sm flex items-center justify-center gap-1 w-max mx-auto" title="Estoque Baixo! Risco de faltar.">
                                        ⚠️ {{ $produto->quantidade_estoque }}
                                    </span>
                                @else
                                    <span class="text-gray-600 font-medium">
                                        {{ $produto->quantidade_estoque }}
                                    </span>
                                @endif
                            </td>
                            
                            <td class="p-3 text-right font-bold text-green-600">
                                R$ {{ number_format($produto->receita_gerada, 2, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center text-gray-500">
                                Nenhuma venda de produto registada neste período.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>