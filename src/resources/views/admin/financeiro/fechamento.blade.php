<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-8">
                <h1 class="text-3xl font-bold text-gray-800">💰 Fechamento de Caixa</h1>
                <form action="{{ route('admin.financeiro.fechamento') }}" method="GET" class="flex items-center gap-2 bg-white p-2 rounded-lg shadow-sm border">
                    <label for="data" class="text-sm font-bold text-gray-600 ml-2">Mudar data:</label>
                    <input type="date" name="data" id="data" 
                           value="{{ $dataSelecionada }}" 
                           class="border-gray-300 rounded-md shadow-sm text-sm focus:ring-pink-500">
                    <button type="submit" class="bg-pink-600 text-white px-4 py-2 rounded-md hover:bg-pink-700 font-bold text-sm transition">
                        🔍 Filtrar
                    </button>
                </form>
            </div>

            {{-- CARDS DE RESUMO --}}

                <div class="bg-white p-6 rounded-xl shadow border-l-8 border-red-500">
                    <p class="text-sm text-gray-500 uppercase font-bold">Total Comissões (Saída)</p>
                    <h2 class="text-3xl font-black text-red-600">- R$ {{ number_format($totalComissoes, 2, ',', '.') }}</h2>
                </div>

                <div class="bg-white p-6 rounded-xl shadow border-l-8 border-green-500">
                    <p class="text-sm text-gray-500 uppercase font-bold">Lucro Líquido Salão</p>
                    <h2 class="text-3xl font-black text-green-600">R$ {{ number_format($lucroLiquido, 2, ',', '.') }}</h2>
                </div>
            </div>

            {{-- DETALHAMENTO --}}
            <div class="bg-white rounded-xl shadow overflow-hidden">
                <div class="p-6 border-b border-gray-100 bg-gray-50">
                    <h3 class="font-bold text-gray-700">Detalhamento do Dia</h3>
                </div>
                <div class="p-6">
                    <div class="flex justify-between py-3 border-b">
                        <span class="text-gray-600 font-medium">Serviços Executados (Total Bruto)</span>
                        <span class="font-bold">R$ {{ number_format($totalServicos, 2, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between py-3 border-b">
                        <span class="text-gray-600 font-medium">Produtos Vendidos (Total Bruto)</span>
                        <span class="font-bold">R$ {{ number_format($totalProdutos, 2, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between py-3 text-red-500">
                        <span class="font-medium italic">Estimativa de Comissões a Pagar</span>
                        <span class="font-bold">- R$ {{ number_format($totalComissoes, 2, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <p class="mt-4 text-sm text-gray-400 text-center italic">
                * Valores calculados com base em 50% de comissão padrão para serviços e 10% para produtos.
            </p>
        </div>
    </div>
</x-app-layout>