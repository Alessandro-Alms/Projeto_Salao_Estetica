<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('REL008: Comissões a Pagar') }}
            </h2>
            <a href="{{ route('admin.relatorios.index') ?? '#' }}" class="text-sm text-blue-600 hover:underline">⬅ Voltar</a>
        </div>
    </x-slot>

    <div class="container mx-auto px-4 py-6">
        <div class="bg-white p-4 rounded-xl shadow mb-6">
            <form method="GET" action="{{ route('admin.relatorios.comissoes') }}" class="flex flex-wrap items-end gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Início</label>
                    <input type="date" name="data_inicio" value="{{ $dataInicio }}" class="border border-gray-300 rounded-lg shadow-sm focus:border-amber-500 focus:ring-amber-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fim</label>
                    <input type="date" name="data_fim" value="{{ $dataFim }}" class="border border-gray-300 rounded-lg shadow-sm focus:border-amber-500 focus:ring-amber-500">
                </div>
                <button type="submit" class="bg-amber-600 text-white px-5 py-2.5 rounded-lg font-bold hover:bg-amber-700 transition shadow-sm">
                    Calcular Comissões
                </button>
            </form>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-gradient-to-br from-red-500 to-rose-700 p-6 rounded-xl shadow text-white flex flex-col justify-center relative overflow-hidden">
                <div class="absolute right-4 top-4 opacity-20 text-5xl">💸</div>
                <h3 class="text-sm font-bold opacity-90 uppercase tracking-wider mb-2">Total a Pagar (Saída)</h3>
                <p class="text-4xl font-black mb-1">R$ {{ number_format($totalGeralComissoes, 2, ',', '.') }}</p>
                <p class="text-sm bg-white/20 px-3 py-1 rounded-full inline-block self-start mt-1">
                    Para {{ $comissoes->count() }} profissionais
                </p>
            </div>

            <div class="bg-white border-l-4 border-blue-500 p-6 rounded-xl shadow flex flex-col justify-center">
                <h3 class="text-gray-500 font-bold uppercase text-sm mb-2">Serviços Comissionados</h3>
                <p class="text-4xl font-black text-blue-600">{{ $totalServicosRealizados }}</p>
                <p class="text-sm text-gray-500 mt-2">Atendimentos concluídos</p>
            </div>

            <div class="bg-white border-l-4 border-amber-400 p-6 rounded-xl shadow flex flex-col justify-center relative">
                <div class="absolute right-4 top-4 text-3xl opacity-50">⭐</div>
                <h3 class="text-gray-500 font-bold uppercase text-sm mb-2">Maior Cheque (Destaque)</h3>
                @if($maiorComissao)
                    <p class="text-2xl font-black text-gray-800 truncate" title="{{ $maiorComissao->name }}">{{ $maiorComissao->name }}</p>
                    <p class="text-sm font-bold text-amber-600 mt-1">Recebe: R$ {{ number_format($maiorComissao->comissao_a_pagar, 2, ',', '.') }}</p>
                @else
                    <p class="text-xl font-bold text-gray-400">Sem dados</p>
                @endif
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow overflow-x-auto">
            <h3 class="font-bold mb-4 text-gray-800 text-lg flex items-center gap-2">
                📋 Folha de Pagamento Detalhada
            </h3>
            
            <table class="w-full text-left border-collapse whitespace-nowrap">
                <thead>
                    <tr class="bg-gray-50 text-gray-600 border-b border-gray-200">
                        <th class="p-3 rounded-tl-lg">Profissional</th>
                        <th class="p-3 text-center">Qtd. Serviços</th>
                        <th class="p-3 text-right">Receita Bruta Gerada</th>
                        <th class="p-3 text-right">Valor a Pagar (Líquido)</th>
                        <th class="p-3 text-center rounded-tr-lg">Notificar</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($comissoes as $item)
                        <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                            <td class="p-3">
                                <p class="font-bold text-gray-800">{{ $item->name }}</p>
                                <p class="text-xs text-gray-500">{{ $item->telefone ?: 'Sem contacto' }}</p>
                            </td>
                            
                            <td class="p-3 text-center">
                                <span class="bg-gray-100 text-gray-700 px-3 py-1 rounded-full font-bold">
                                    {{ $item->total_servicos }}
                                </span>
                            </td>

                            <td class="p-3 text-right text-gray-500 font-medium">
                                R$ {{ number_format($item->receita_gerada, 2, ',', '.') }}
                            </td>
                            
                            <td class="p-3 text-right font-black text-red-600 text-lg">
                                R$ {{ number_format($item->comissao_a_pagar, 2, ',', '.') }}
                            </td>

                            <td class="p-3 text-center">
                                @if($item->telefone)
                                    @php
                                        $msg = urlencode("Olá {$item->name}! O teu fechamento deste período foi concluído. O teu valor a receber é de R$ " . number_format($item->comissao_a_pagar, 2, ',', '.') . ". Obrigado pelo excelente trabalho!");
                                        $zap = preg_replace('/[^0-9]/', '', $item->telefone);
                                    @endphp
                                    <a href="https://wa.me/55{{ $zap }}?text={{ $msg }}" target="_blank" class="inline-flex items-center gap-1 bg-green-500 text-white px-3 py-1.5 rounded-lg text-xs font-bold hover:bg-green-600 transition shadow-sm">
                                        Enviar Recibo
                                    </a>
                                @else
                                    <span class="text-xs text-gray-400">Sem telefone</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center text-gray-500">
                                Nenhuma comissão gerada neste período.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>