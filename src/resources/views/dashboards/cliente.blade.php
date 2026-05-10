@if(($contatoMensagensLidasCliente ?? collect())->count() > 0 || $mensagemProximaVisita || $avaliacoesPendentes > 0 || $pacotesVencendo->count() > 0)
    <div class="glass-card rounded-2xl shadow-xl overflow-hidden">
        <div class="p-6 bg-white/70 backdrop-blur-sm border border-white/40">
            <div class="flex items-center gap-2 mb-4">
                <span class="text-[#7B19E5] text-xl">✧</span>
                <h3 class="text-lg font-title text-[#4A00B9]">Notificações</h3>
            </div>

            <div class="space-y-3">
                @foreach(($contatoMensagensLidasCliente ?? collect()) as $mensagemContato)
                    <div x-data="{ visivel: true }" x-show="visivel" class="p-4 rounded-xl bg-[#7B19E5]/10 border border-[#FFD6F4] text-sm">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div>
                                <p class="font-semibold text-[#4A00B9]">Sua mensagem foi lida</p>
                                <p class="text-gray-600 mt-1">
                                    A equipe já visualizou sua mensagem sobre "{{ $mensagemContato->assunto }}".
                                </p>
                            </div>
                            <button type="button" @click="visivel = false" class="rounded-full bg-white/80 px-4 py-2 text-xs font-bold text-[#7B19E5] hover:bg-[#7B19E5] hover:text-white transition-colors">
                                OK
                            </button>
                        </div>
                    </div>
                @endforeach

                @if($mensagemProximaVisita)
                    <div class="p-4 rounded-xl bg-[#7B19E5]/10 border border-[#FFD6F4] text-sm">
                        <p class="font-semibold text-[#4A00B9]">Próxima visita</p>
                        <p class="text-gray-600 mt-1">
                            {{ $mensagemProximaVisita }}
                            <span class="font-semibold text-[#7B19E5]">
                                {{ \Carbon\Carbon::parse($agendamentoProximo->data_hora_inicio)->format('d/m/Y H:i') }}
                            </span>
                        </p>
                        <p class="text-xs text-gray-500 mt-2">
                            Cancelamentos com menos de 24h geram multa de 5% do valor do serviço.
                        </p>
                    </div>
                @endif

                @if($avaliacoesPendentes > 0)
                    <div class="p-4 rounded-xl bg-[#FF2EB6]/10 border border-[#FFD6F4] text-sm">
                        <p class="font-semibold text-[#FF2EB6]">★ Avaliação pendente</p>
                        <p class="text-gray-600 mt-1">
                            Você tem {{ $avaliacoesPendentes }} atendimento(s) sem avaliação. Sua opinião é importante!
                        </p>
                        <a href="{{ route('cliente.index') }}" class="inline-block mt-2 text-[#FF2EB6] font-semibold hover:underline">
                            Avaliar agora
                        </a>
                    </div>
                @endif

                @if($pacotesVencendo->count() > 0)
                    <div class="p-4 rounded-xl bg-[#F59E0B]/10 border border-[#FFD6F4] text-sm">
                        <p class="font-semibold text-[#F59E0B]">Pacotes a vencer</p>
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

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mt-6">
    <a href="{{ route('cliente.agendar.novo') }}" class="group min-h-28 rounded-2xl bg-gradient-to-br from-[#7B19E5] to-[#FF2EB6] p-5 text-white shadow-lg hover:shadow-xl hover:-translate-y-1 transition-all">
        <span class="block text-2xl mb-3">✧</span>
        <span class="block font-title text-lg">Agendar horário</span>
        <span class="block text-xs text-white/80 mt-1">Escolha serviço, profissional e data.</span>
    </a>
    <a href="{{ route('cliente.produtos.index') }}" class="group min-h-28 rounded-2xl bg-white/80 border border-[#FFD6F4] p-5 text-[#1A002B] shadow-md hover:shadow-lg hover:-translate-y-1 transition-all">
        <span class="block text-2xl text-[#FF2EB6] mb-3">✧</span>
        <span class="block font-title text-lg text-[#4A00B9]">Comprar Produtos</span>
        <span class="block text-xs text-gray-500 mt-1">Veja produtos disponíveis no estoque.</span>
    </a>
    <a href="{{ route('cliente.pacotes.index') }}" class="group min-h-28 rounded-2xl bg-white/80 border border-[#FFD6F4] p-5 text-[#1A002B] shadow-md hover:shadow-lg hover:-translate-y-1 transition-all">
        <span class="block text-2xl text-[#7B19E5] mb-3">✧</span>
        <span class="block font-title text-lg text-[#4A00B9]">Comprar Pacotes</span>
        <span class="block text-xs text-gray-500 mt-1">Use sessões em agendamentos futuros.</span>
    </a>
    <a href="{{ route('cliente.index') }}" class="group min-h-28 rounded-2xl bg-white/80 border border-[#FFD6F4] p-5 text-[#1A002B] shadow-md hover:shadow-lg hover:-translate-y-1 transition-all">
        <span class="block text-2xl text-[#FF2EB6] mb-3">✧</span>
        <span class="block font-title text-lg text-[#4A00B9]">Meus agendamentos</span>
        <span class="block text-xs text-gray-500 mt-1">Acompanhe horários e avaliações.</span>
    </a>
</div>  
