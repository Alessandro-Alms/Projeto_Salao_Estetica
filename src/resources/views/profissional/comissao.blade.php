<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-bold text-gray-800">Meu Extrato de Comissões</h1>
                
                {{-- Filtro de Mês/Ano (Opcional, se o seu controller aceitar request) --}}
                <form method="GET" class="flex gap-2">
                    <select name="mes" class="rounded border-gray-300 shadow-sm text-sm">
                        <option value="04">Abril</option>
                        <option value="05">Maio</option>
                        </select>
                    <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded text-sm font-bold hover:bg-gray-700">
                        Filtrar
                    </button>
                </form>
            </div>

            {{-- Card de Total --}}
            <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl shadow-lg p-6 mb-8 text-white flex justify-between items-center">
                <div>
                    <p class="text-green-100 text-sm font-bold uppercase tracking-wider mb-1">Total a Receber no Período</p>
                    <h2 class="text-4xl font-black">R$ {{ number_format($totalComissao, 2, ',', '.') }}</h2>
                </div>
                <div class="bg-white/20 p-4 rounded-full">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>

            {{-- Tabela de Extrato Detalhado --}}
            <div class="bg-white rounded-xl shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="font-bold text-gray-700 text-lg">Histórico de Atendimentos e Vendas</h3>
                </div>
                
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Data</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Descrição</th>
                            <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Valor Cobrado</th>
                            <th class="px-6 py-3 text-right text-xs font-bold text-green-600 uppercase tracking-wider">Sua Comissão</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($comissoes as $item)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-medium">
                                    {{ $item['data'] }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ $item['descricao'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right">
                                    R$ {{ number_format($item['valor_total'], 2, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-green-600 text-right">
                                    + R$ {{ number_format($item['comissao'], 2, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-gray-500 text-sm">
                                    Nenhuma comissão registrada neste período.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</x-app-layout>