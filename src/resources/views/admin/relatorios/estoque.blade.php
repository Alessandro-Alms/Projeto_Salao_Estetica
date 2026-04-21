<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('REL009: Estoque de Produtos') }}
            </h2>
            <a href="{{ route('admin.relatorios.index') ?? '#' }}" class="text-sm text-blue-600 hover:underline">⬅ Voltar</a>
        </div>
    </x-slot>

    <div class="container mx-auto px-4 py-6">
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-gradient-to-br {{ $totalAlertas > 0 ? 'from-red-500 to-rose-700' : 'from-green-500 to-emerald-600' }} p-6 rounded-xl shadow text-white flex flex-col justify-center relative overflow-hidden">
                <div class="absolute right-4 top-4 opacity-20 text-5xl">⚠️</div>
                <h3 class="text-sm font-bold opacity-90 uppercase tracking-wider mb-2">Alertas de Reposição</h3>
                <p class="text-4xl font-black mb-1">{{ $totalAlertas }}</p>
                <p class="text-sm bg-white/20 px-3 py-1 rounded-full inline-block self-start mt-1">
                    Produtos com estoque baixo
                </p>
            </div>

            <div class="bg-white border-l-4 border-blue-500 p-6 rounded-xl shadow flex flex-col justify-center">
                <h3 class="text-gray-500 font-bold uppercase text-sm mb-2">Volume Total (Unidades)</h3>
                <p class="text-4xl font-black text-blue-600">{{ $totalItens }}</p>
                <p class="text-sm text-gray-500 mt-2">Itens físicos na prateleira</p>
            </div>

            <div class="bg-white border-l-4 border-purple-500 p-6 rounded-xl shadow flex flex-col justify-center relative">
                <div class="absolute right-4 top-4 text-3xl opacity-50">💰</div>
                <h3 class="text-gray-500 font-bold uppercase text-sm mb-2">Capital em Estoque</h3>
                <p class="text-3xl font-black text-gray-800">R$ {{ number_format($valorInvestido, 2, ',', '.') }}</p>
                <p class="text-sm text-purple-600 font-bold mt-1">Valor de custo/venda parado</p>
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow overflow-x-auto">
            <div class="flex justify-between items-center mb-4">
                <h3 class="font-bold text-gray-800 text-lg flex items-center gap-2">
                    📦 Posição Atual do Estoque e Sugestões
                </h3>
                <span class="text-xs text-gray-500 bg-gray-100 px-3 py-1 rounded-full">
                    Ordenado por nível crítico
                </span>
            </div>
            
            <table class="w-full text-left border-collapse whitespace-nowrap">
                <thead>
                    <tr class="bg-gray-50 text-gray-600 border-b border-gray-200">
                        <th class="p-3 rounded-tl-lg">Produto</th>
                        <th class="p-3 text-center">Tipo</th>
                        <th class="p-3 text-right">Valor Unit.</th>
                        <th class="p-3 text-center">Saldo em Estoque</th>
                        <th class="p-3 text-center rounded-tr-lg">Sugestão de Compra</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($produtos as $produto)
                        @php 
                            $critico = $produto->quantidade_estoque <= 5; 
                            $esgotado = $produto->quantidade_estoque == 0;
                        @endphp
                        <tr class="border-b border-gray-100 transition {{ $esgotado ? 'bg-red-50/50' : ($critico ? 'hover:bg-orange-50/30' : 'hover:bg-gray-50') }}">
                            <td class="p-3">
                                <p class="font-bold {{ $esgotado ? 'text-red-700' : 'text-gray-800' }}">{{ $produto->nome }}</p>
                            </td>
                            
                            <td class="p-3 text-center">
                                <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded text-xs font-bold uppercase">
                                    {{ $produto->tipo }}
                                </span>
                            </td>

                            <td class="p-3 text-right text-gray-500 font-medium">
                                R$ {{ number_format($produto->valor_unitario, 2, ',', '.') }}
                            </td>
                            
                            <td class="p-3 text-center">
                                @if($esgotado)
                                    <span class="bg-red-600 text-white px-3 py-1 rounded-full font-bold text-sm shadow-sm inline-flex items-center gap-1">
                                        ❌ 0 (Esgotado)
                                    </span>
                                @elseif($critico)
                                    <span class="bg-orange-100 text-orange-800 border border-orange-200 px-3 py-1 rounded-full font-bold text-sm inline-flex items-center gap-1">
                                        ⚠️ {{ $produto->quantidade_estoque }}
                                    </span>
                                @else
                                    <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full font-bold text-sm">
                                        ✔️ {{ $produto->quantidade_estoque }}
                                    </span>
                                @endif
                            </td>

                            <td class="p-3 text-center">
                                @if($esgotado)
                                    <span class="text-red-600 font-bold text-sm">Urgentíssimo! Pedir hoje.</span>
                                @elseif($critico)
                                    <span class="text-orange-600 font-bold text-sm">Comprar reposição em breve.</span>
                                @else
                                    <span class="text-gray-400 text-sm">Estoque saudável.</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center text-gray-500">
                                Nenhum produto registado na base de dados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>