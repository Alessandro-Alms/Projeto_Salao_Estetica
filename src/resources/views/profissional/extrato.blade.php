<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="flex justify-between items-center mb-8">
                <h2 class="text-2xl font-bold text-gray-800">📊 Meu Extrato Financeiro</h2>
                
                <form action="{{ route('profissional.extrato') }}" method="GET" class="flex gap-2">
                    <select name="mes" class="rounded-lg border-gray-300 text-sm">
                        @for($i=1; $i<=12; $i++)
                            <option value="{{ $i }}" {{ $mes == $i ? 'selected' : '' }}>
                                {{ Carbon\Carbon::create()->month($i)->translatedFormat('F') }}
                            </option>
                        @endfor
                    </select>
                    <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition">Filtrar</button>
                </form>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <p class="text-sm font-semibold text-gray-400 uppercase tracking-wider">Comissão Serviços</p>
                    <p class="text-3xl font-bold text-blue-600">R$ {{ number_format($totalComissaoServicos, 2, ',', '.') }}</p>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <p class="text-sm font-semibold text-gray-400 uppercase tracking-wider">Comissão Produtos (10%)</p>
                    <p class="text-3xl font-bold text-green-600">R$ {{ number_format($totalComissaoProdutos, 2, ',', '.') }}</p>
                </div>

                <div class="bg-indigo-600 p-6 rounded-2xl shadow-lg text-white">
                    <p class="text-sm font-semibold uppercase tracking-wider opacity-80">Total a Receber</p>
                    <p class="text-4xl font-black">R$ {{ number_format($totalComissaoServicos + $totalComissaoProdutos, 2, ',', '.') }}</p>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100">
                    <h3 class="font-bold text-gray-700">Detalhamento dos Serviços</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50 border-b border-gray-100">
                            <tr>
                                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Data</th>
                                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Serviço</th>
                                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase text-center">% Comis.</th>
                                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase text-right">Ganho Neto</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($agendamentos as $agenda)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        {{ \Carbon\Carbon::parse($agenda->data_hora_inicio)->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="font-semibold text-gray-800">{{ $agenda->servico->nome }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="bg-blue-50 text-blue-700 px-2 py-1 rounded text-xs font-bold">
                                            {{ number_format($agenda->comissao_paga_percentual, 0) }}%
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right font-bold text-gray-900">
                                        R$ {{ number_format($agenda->valor_comissao, 2, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-10 text-center text-gray-400">
                                        Nenhum atendimento realizado neste período.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>