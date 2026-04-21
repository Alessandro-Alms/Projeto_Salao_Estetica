<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('REL007: Financeiro Detalhado') }}
            </h2>
            <a href="{{ route('admin.relatorios.index') ?? '#' }}" class="text-sm text-blue-600 hover:underline">⬅ Voltar</a>
        </div>
    </x-slot>

    <div class="container mx-auto px-4 py-6">
        <div class="bg-white p-4 rounded-xl shadow mb-6">
            <form method="GET" action="{{ route('admin.relatorios.financeiro') }}" class="flex flex-wrap items-end gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Início</label>
                    <input type="date" name="data_inicio" value="{{ $dataInicio }}" class="border border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fim</label>
                    <input type="date" name="data_fim" value="{{ $dataFim }}" class="border border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500">
                </div>
                <button type="submit" class="bg-green-600 text-white px-5 py-2.5 rounded-lg font-bold hover:bg-green-700 transition shadow-sm">
                    Gerar Balanço
                </button>
            </form>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white border-l-4 border-blue-500 p-6 rounded-xl shadow flex flex-col justify-center">
                <h3 class="text-gray-500 font-bold uppercase text-sm mb-2">Total de Entradas (Bruto)</h3>
                <p class="text-3xl font-black text-blue-600">R$ {{ number_format($totalEntradas, 2, ',', '.') }}</p>
                <p class="text-sm text-gray-500 mt-1">Soma de tudo o que os clientes pagaram</p>
            </div>

            <div class="bg-white border-l-4 border-red-500 p-6 rounded-xl shadow flex flex-col justify-center">
                <h3 class="text-gray-500 font-bold uppercase text-sm mb-2">Total de Saídas (Despesas)</h3>
                <p class="text-3xl font-black text-red-600">R$ {{ number_format($totalSaidas, 2, ',', '.') }}</p>
                <p class="text-sm text-gray-500 mt-1">Comissões a pagar no período</p>
            </div>

            <div class="bg-gradient-to-br {{ $saldoLiquido >= 0 ? 'from-green-500 to-emerald-700' : 'from-red-500 to-rose-700' }} p-6 rounded-xl shadow text-white flex flex-col justify-center relative overflow-hidden">
                <div class="absolute right-4 top-4 opacity-20 text-5xl">💵</div>
                <h3 class="text-sm font-bold opacity-80 uppercase tracking-wider mb-2">Saldo Líquido Gerado</h3>
                <p class="text-4xl font-black mb-1">R$ {{ number_format($saldoLiquido, 2, ',', '.') }}</p>
                <p class="text-sm bg-white/20 px-3 py-1 rounded-full inline-block mt-2 self-start">
                    (Entradas - Saídas)
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white p-6 rounded-xl shadow">
                <h3 class="font-bold mb-6 text-gray-800 text-lg border-b pb-2 flex items-center gap-2">
                    📈 Entradas por Categoria
                </h3>

                @php
                    $pctServicos = $totalEntradas > 0 ? ($receitaServicos / $totalEntradas) * 100 : 0;
                    $pctProdutos = $totalEntradas > 0 ? ($receitaProdutos / $totalEntradas) * 100 : 0;
                    $pctPacotes = $totalEntradas > 0 ? ($receitaPacotes / $totalEntradas) * 100 : 0;
                @endphp

                <div class="space-y-6">
                    <div>
                        <div class="flex justify-between items-end mb-1">
                            <span class="text-sm font-bold text-gray-700">✂️ Serviços Executados</span>
                            <span class="text-lg font-black text-blue-600">R$ {{ number_format($receitaServicos, 2, ',', '.') }}</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-3">
                            <div class="bg-blue-500 h-3 rounded-full" style="width: {{ $pctServicos }}%"></div>
                        </div>
                        <p class="text-xs text-gray-400 mt-1 text-right">{{ number_format($pctServicos, 1) }}% do total</p>
                    </div>

                    <div>
                        <div class="flex justify-between items-end mb-1">
                            <span class="text-sm font-bold text-gray-700">🛍️ Venda de Produtos</span>
                            <span class="text-lg font-black text-purple-600">R$ {{ number_format($receitaProdutos, 2, ',', '.') }}</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-3">
                            <div class="bg-purple-500 h-3 rounded-full" style="width: {{ $pctProdutos }}%"></div>
                        </div>
                        <p class="text-xs text-gray-400 mt-1 text-right">{{ number_format($pctProdutos, 1) }}% do total</p>
                    </div>

                    <div>
                        <div class="flex justify-between items-end mb-1">
                            <span class="text-sm font-bold text-gray-700">🎁 Venda de Pacotes</span>
                            <span class="text-lg font-black text-orange-500">R$ {{ number_format($receitaPacotes, 2, ',', '.') }}</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-3">
                            <div class="bg-orange-400 h-3 rounded-full" style="width: {{ $pctPacotes }}%"></div>
                        </div>
                        <p class="text-xs text-gray-400 mt-1 text-right">{{ number_format($pctPacotes, 1) }}% do total</p>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl shadow">
                <h3 class="font-bold mb-6 text-gray-800 text-lg border-b pb-2 flex items-center gap-2">
                    📉 Despesas Registadas
                </h3>

                <div class="bg-red-50 border border-red-100 p-4 rounded-lg flex items-center justify-between mb-4">
                    <div>
                        <p class="font-bold text-red-800">Pagamento de Comissões</p>
                        <p class="text-sm text-red-600">Repasse aos profissionais parceiros</p>
                    </div>
                    <p class="text-xl font-black text-red-700">R$ {{ number_format($despesaComissoes, 2, ',', '.') }}</p>
                </div>

                <div class="bg-gray-50 border border-gray-200 p-4 rounded-lg flex items-center justify-between opacity-60">
                    <div>
                        <p class="font-bold text-gray-600">Despesas Fixas (Água, Luz, etc.)</p>
                        <p class="text-sm text-gray-500">Módulo ainda não integrado</p>
                    </div>
                    <p class="text-xl font-bold text-gray-400">R$ 0,00</p>
                </div>
                
                <div class="mt-6 p-4 bg-blue-50 text-blue-800 text-sm rounded-lg border border-blue-100">
                    <strong>💡 Nota:</strong> Atualmente o sistema calcula automaticamente as despesas geradas pelas comissões de serviços executados. Para calcular o lucro real e total do salão, futuramente podes adicionar um módulo de contas a pagar (fornecedores, impostos e custos fixos).
                </div>
            </div>
        </div>
    </div>
</x-app-layout>