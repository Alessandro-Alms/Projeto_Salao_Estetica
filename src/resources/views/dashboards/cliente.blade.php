@if($mensagemProximaVisita || $avaliacoesPendentes > 0 || $pacotesVencendo->count() > 0)
    <div class="glass-card rounded-2xl shadow-xl overflow-hidden">
        <div class="p-6 bg-white/70 backdrop-blur-sm border border-white/40">
            <div class="flex items-center gap-2 mb-4">
                <span class="text-[#7B19E5] text-xl">✧</span>
                <h3 class="text-lg font-title text-[#4A00B9]">Notificacoes</h3>
            </div>

            <div class="space-y-3">
                @if($mensagemProximaVisita)
                    <div class="p-4 rounded-xl bg-[#7B19E5]/10 border border-[#FFD6F4] text-sm">
                        <p class="font-semibold text-[#4A00B9]">📅 Proxima visita</p>
                        <p class="text-gray-600 mt-1">
                            {{ $mensagemProximaVisita }}
                            <span class="font-semibold text-[#7B19E5]">
                                {{ \Carbon\Carbon::parse($agendamentoProximo->data_hora_inicio)->format('d/m/Y H:i') }}
                            </span>
                        </p>
                        <p class="text-xs text-gray-500 mt-2">
                            Cancelamentos com menos de 24h geram multa de 5% do valor do servico.
                        </p>
                    </div>
                @endif

                @if($avaliacoesPendentes > 0)
                    <div class="p-4 rounded-xl bg-[#FF2EB6]/10 border border-[#FFD6F4] text-sm">
                        <p class="font-semibold text-[#FF2EB6]">⭐ Avaliacao pendente</p>
                        <p class="text-gray-600 mt-1">
                            Voce tem {{ $avaliacoesPendentes }} atendimento(s) sem avaliacao. Sua opiniao e importante!
                        </p>
                        <a href="{{ route('cliente.index') }}" class="inline-block mt-2 text-[#FF2EB6] font-semibold hover:underline">
                            Avaliar agora
                        </a>
                    </div>
                @endif

                @if($pacotesVencendo->count() > 0)
                    <div class="p-4 rounded-xl bg-[#F59E0B]/10 border border-[#FFD6F4] text-sm">
                        <p class="font-semibold text-[#F59E0B]">🎁 Pacotes a vencer</p>
                        <ul class="mt-2 space-y-1 text-gray-600">
                            @foreach($pacotesVencendo as $pacote)
                                <li>
                                    {{ $pacote->pacote->nome }} vence em {{ \Carbon\Carbon::parse($pacote->data_validade)->format('d/m/Y') }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endif

<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
    <a href="{{ route('cliente.agendar.novo') }}" class="flex items-center justify-center p-6 bg-gradient-to-r from-[#7B19E5] to-[#FF2EB6] text-white rounded-full font-medium btn-primary shadow-lg hover:shadow-xl transition-all text-center">
        Agendar Novo Horário
    </a>
    <a href="{{ route('cliente.index') }}" class="flex items-center justify-center p-6 bg-white text-[#7B19E5] border-2 border-[#7B19E5] rounded-full font-medium hover:bg-[#7B19E5] hover:text-white transition-all text-center">
        Meus Agendamentos
    </a>
</div>  
