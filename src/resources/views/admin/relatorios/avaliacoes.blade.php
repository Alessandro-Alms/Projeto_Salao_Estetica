<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('REL011: Avaliações e Reputação') }}
            </h2>
            <a href="{{ route('admin.relatorios.index') ?? '#' }}" class="text-sm text-blue-600 hover:underline">⬅ Voltar</a>
        </div>
    </x-slot>

    <div class="container mx-auto px-4 py-6">
        <div class="bg-white p-4 rounded-xl shadow mb-6">
            <form method="GET" action="{{ route('admin.relatorios.avaliacoes') }}" class="flex flex-wrap items-end gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Início</label>
                    <input type="date" name="data_inicio" value="{{ $dataInicio }}" class="border border-gray-300 rounded-lg shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fim</label>
                    <input type="date" name="data_fim" value="{{ $dataFim }}" class="border border-gray-300 rounded-lg shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                </div>
                <button type="submit" class="bg-yellow-500 text-white px-5 py-2.5 rounded-lg font-bold hover:bg-yellow-600 transition shadow-sm">
                    Analisar Reputação
                </button>
            </form>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-gradient-to-br from-yellow-400 to-yellow-600 p-6 rounded-xl shadow text-white flex flex-col justify-center relative overflow-hidden">
                <div class="absolute right-4 top-4 opacity-30 text-5xl">⭐</div>
                <h3 class="text-sm font-bold opacity-90 uppercase tracking-wider mb-2">Média Geral</h3>
                <div class="flex items-end gap-2 mb-1">
                    <p class="text-5xl font-black">{{ number_format($mediaGeral, 1) }}</p>
                    <p class="text-xl font-bold opacity-80 mb-1">/ 5.0</p>
                </div>
                <p class="text-sm bg-white/20 px-3 py-1 rounded-full inline-block self-start mt-1">
                    {{ $totalAvaliacoes }} avaliações recebidas
                </p>
            </div>

            <div class="bg-white border-l-4 border-green-500 p-6 rounded-xl shadow flex flex-col justify-center">
                <h3 class="text-gray-500 font-bold uppercase text-sm mb-2">Taxa de Aprovação</h3>
                <p class="text-4xl font-black text-green-600">{{ number_format($percentualAprovacao, 1) }}%</p>
                <p class="text-sm text-gray-500 mt-2">Clientes que deram 4 ou 5 estrelas</p>
            </div>

            <div class="bg-white p-4 rounded-xl shadow flex flex-col justify-center">
                <h3 class="text-gray-500 font-bold uppercase text-xs mb-3 text-center border-b pb-2">Resumo das Estrelas</h3>
                <div class="space-y-2">
                    @foreach([5, 4, 3, 2, 1] as $estrela)
                        @php
                            $qtd = $distribuicao[$estrela];
                            $pct = $totalAvaliacoes > 0 ? ($qtd / $totalAvaliacoes) * 100 : 0;
                            // Cores diferentes dependendo da nota
                            $cor = $estrela >= 4 ? 'bg-green-500' : ($estrela == 3 ? 'bg-yellow-400' : 'bg-red-500');
                        @endphp
                        <div class="flex items-center text-xs">
                            <span class="w-12 flex items-center gap-1 font-bold text-gray-600">{{ $estrela }} ⭐</span>
                            <div class="flex-1 bg-gray-100 rounded-full h-2 mx-2">
                                <div class="{{ $cor }} h-2 rounded-full" style="width: {{ $pct }}%"></div>
                            </div>
                            <span class="w-8 text-right text-gray-500">{{ $qtd }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="bg-white p-6 rounded-xl shadow lg:col-span-1">
                <h3 class="font-bold mb-4 text-gray-800 border-b pb-2 flex items-center gap-2">
                    🏆 Top Profissionais
                </h3>
                @if($rankingProfissionais->count() > 0)
                    <ul class="space-y-4 mt-4">
                        @foreach($rankingProfissionais as $index => $prof)
                            <li class="flex justify-between items-center bg-gray-50 p-3 rounded-lg border border-gray-100">
                                <div>
                                    <p class="font-bold text-gray-800 flex items-center gap-2">
                                        @if($index == 0) 🥇 
                                        @elseif($index == 1) 🥈 
                                        @elseif($index == 2) 🥉 
                                        @else <span class="w-5 text-center text-gray-400">#{{ $index + 1 }}</span>
                                        @endif
                                        {{ $prof->nome }}
                                    </p>
                                    <p class="text-xs text-gray-500 mt-1">{{ $prof->total_avaliacoes }} avaliações</p>
                                </div>
                                <div class="text-right">
                                    <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-sm font-black flex items-center gap-1">
                                        {{ number_format($prof->media, 1) }} ⭐
                                    </span>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-gray-500 text-sm py-4">Sem dados suficientes para o ranking.</p>
                @endif
            </div>

            <div class="bg-white p-6 rounded-xl shadow lg:col-span-2 border border-yellow-50">
                <h3 class="font-bold mb-4 text-gray-800 text-lg flex items-center gap-2">
                    💬 Feed de Comentários Recentes
                </h3>
                
                <div class="space-y-4 max-h-[500px] overflow-y-auto pr-2">
                    @forelse($avaliacoes->whereNotNull('comentario')->where('comentario', '!=', '')->take(15) as $av)
                        <div class="bg-gray-50 p-4 rounded-xl border-l-4 {{ $av->nota >= 4 ? 'border-green-500' : ($av->nota == 3 ? 'border-yellow-400' : 'border-red-500') }}">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <span class="font-bold text-gray-800">{{ $av->cliente_nome }}</span>
                                    <span class="text-xs text-gray-500 ml-2">avaliou <strong>{{ $av->profissional_nome }}</strong></span>
                                </div>
                                <div class="flex gap-1 text-sm">
                                    @for($i = 1; $i <= 5; $i++)
                                        <span class="{{ $i <= $av->nota ? 'text-yellow-400' : 'text-gray-300' }}">★</span>
                                    @endfor
                                </div>
                            </div>
                            <p class="text-gray-700 text-sm italic">"{{ $av->comentario }}"</p>
                            <p class="text-xs text-gray-400 mt-2 text-right">
                                {{ \Carbon\Carbon::parse($av->created_at)->format('d/m/Y H:i') }}
                            </p>
                        </div>
                    @empty
                        <div class="text-center p-8 bg-gray-50 rounded-lg text-gray-500">
                            <p class="text-4xl mb-3">📭</p>
                            Nenhum comentário por escrito recebido neste período.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>