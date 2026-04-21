<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('REL003: Desempenho por Profissional') }}
            </h2>
            <a href="{{ route('admin.relatorios.index') ?? '#' }}" class="text-sm text-blue-600 hover:underline">⬅ Voltar</a>
        </div>
    </x-slot>

    <div class="container mx-auto px-4 py-6">
        <div class="bg-white p-4 rounded-xl shadow mb-6">
            <form method="GET" action="{{ route('admin.relatorios.desempenho') }}" class="flex flex-wrap items-end gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Início</label>
                    <input type="date" name="data_inicio" value="{{ $dataInicio }}" class="border border-gray-300 rounded-lg shadow-sm focus:border-amber-500 focus:ring-amber-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fim</label>
                    <input type="date" name="data_fim" value="{{ $dataFim }}" class="border border-gray-300 rounded-lg shadow-sm focus:border-amber-500 focus:ring-amber-500">
                </div>
                <button type="submit" class="bg-amber-500 text-white px-5 py-2.5 rounded-lg font-bold hover:bg-amber-600 transition shadow-sm">
                    Analisar Equipa
                </button>
            </form>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div class="bg-gradient-to-br from-amber-400 to-amber-600 p-6 rounded-xl shadow text-white flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold opacity-90 uppercase tracking-wider mb-1">Profissional Mais Rentável</h3>
                    @if($campeaoFaturamento && $campeaoFaturamento->receita_gerada > 0)
                        <p class="text-3xl font-black mb-1">{{ $campeaoFaturamento->name }}</p>
                        <p class="text-sm bg-white/20 px-3 py-1 rounded-full inline-block">
                            Gerou R$ {{ number_format($campeaoFaturamento->receita_gerada, 2, ',', '.') }}
                        </p>
                    @else
                        <p class="text-xl font-bold">Sem dados no período</p>
                    @endif
                </div>
                <div class="text-6xl opacity-30">💰</div>
            </div>

            <div class="bg-white border-l-4 border-yellow-400 p-6 rounded-xl shadow flex items-center justify-between">
                <div>
                    <h3 class="text-gray-500 font-bold uppercase text-sm mb-1">Melhor Avaliado (Estrelas)</h3>
                    @if($campeaoAvaliacao)
                        <p class="text-3xl font-black text-gray-800 mb-1">{{ $campeaoAvaliacao->name }}</p>
                        <div class="flex items-center gap-2">
                            <span class="text-yellow-400 text-xl">★</span>
                            <span class="font-bold text-gray-700">{{ number_format($campeaoAvaliacao->media_nota, 1) }}</span>
                            <span class="text-xs text-gray-400">({{ $campeaoAvaliacao->total_avaliacoes }} opiniões)</span>
                        </div>
                    @else
                        <p class="text-xl font-bold text-gray-400">Nenhuma avaliação ainda</p>
                    @endif
                </div>
                <div class="text-6xl opacity-10">⭐</div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow overflow-x-auto">
            <h3 class="font-bold mb-4 text-gray-800 text-lg">Detalhamento da Equipa</h3>
            
            <table class="w-full text-left border-collapse whitespace-nowrap">
                <thead>
                    <tr class="bg-gray-50 text-gray-600 border-b border-gray-200">
                        <th class="p-3 rounded-tl-lg">Profissional</th>
                        <th class="p-3 text-center">Avaliação Média</th>
                        <th class="p-3 text-center">Serviços Executados</th>
                        <th class="p-3 text-right">Receita Gerada para o Salão</th>
                        <th class="p-3 text-right rounded-tr-lg">Comissões a Receber</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($profissionais as $prof)
                        <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                            <td class="p-3 font-bold text-gray-800">{{ $prof->name }}</td>
                            
                            <td class="p-3 text-center">
                                @if($prof->media_nota)
                                    <div class="inline-flex items-center gap-1 bg-yellow-50 text-yellow-700 px-2 py-1 rounded-full font-bold text-sm border border-yellow-200">
                                        ⭐ {{ number_format($prof->media_nota, 1) }}
                                    </div>
                                @else
                                    <span class="text-xs text-gray-400">Sem nota</span>
                                @endif
                            </td>
                            
                            <td class="p-3 text-center">
                                <span class="bg-gray-100 text-gray-700 px-3 py-1 rounded-full font-bold">
                                    {{ $prof->total_servicos }}
                                </span>
                            </td>
                            
                            <td class="p-3 text-right font-medium text-green-600">
                                R$ {{ number_format($prof->receita_gerada ?? 0, 2, ',', '.') }}
                            </td>

                            <td class="p-3 text-right font-bold text-amber-600">
                                R$ {{ number_format($prof->comissao_total ?? 0, 2, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center text-gray-500">
                                Nenhum profissional cadastrado no sistema.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>