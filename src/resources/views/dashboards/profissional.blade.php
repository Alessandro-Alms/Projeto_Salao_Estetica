<div class="glass-card rounded-2xl shadow-xl overflow-hidden mb-6">
    <div class="p-6 bg-white/70 backdrop-blur-sm border border-white/40">
        <div class="flex items-center gap-2 mb-5">
            <span class="text-[#7B19E5] text-xl">✧</span>
            <h3 class="text-lg font-title text-[#4A00B9]">Minha Área</h3>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            {{-- Minha Agenda (roxo + ✧) --}}
            <a href="{{ route('profissional.agenda') }}" class="group flex items-center gap-4 p-4 rounded-xl bg-white/50 hover:bg-white/80 transition-all hover:shadow-lg hover:-translate-y-1 border border-[#FFD6F4]">
                <div class="w-12 h-12 bg-gradient-to-br from-[#7B19E5] to-[#A855F7] rounded-xl flex items-center justify-center shadow-md">
                    <span class="text-white text-xl">✧</span>
                </div>
                <div>
                    <h4 class="font-title text-[#4A00B9]">Minha Agenda</h4>
                    <p class="text-xs text-gray-500">Meus clientes do dia</p>
                </div>
            </a>

            {{-- Meu Extrato (rosa + ✦) --}}
            <a href="{{ route('profissional.extrato') }}" class="group flex items-center gap-4 p-4 rounded-xl bg-white/50 hover:bg-white/80 transition-all hover:shadow-lg hover:-translate-y-1 border border-[#FFD6F4]">
                <div class="w-12 h-12 bg-gradient-to-br from-[#FF2EB6] to-[#FF69B4] rounded-xl flex items-center justify-center shadow-md">
                    <span class="text-white text-xl">✦</span>
                </div>
                <div>
                    <h4 class="font-title text-[#4A00B9]">Meu Extrato</h4>
                    <p class="text-xs text-gray-500">Ver comissões</p>
                </div>
            </a>

            {{-- Configurações (roxo + ✧) --}}
            <a href="{{ route('profissional.servicos.editar') }}" class="group flex items-center gap-4 p-4 rounded-xl bg-white/50 hover:bg-white/80 transition-all hover:shadow-lg hover:-translate-y-1 border border-[#FFD6F4]">
                <div class="w-12 h-12 bg-gradient-to-br from-[#7B19E5] to-[#A855F7] rounded-xl flex items-center justify-center shadow-md">
                    <span class="text-white text-xl">✧</span>
                </div>
                <div>
                    <h4 class="font-title text-[#4A00B9]">Configurações</h4>
                    <p class="text-xs text-gray-500">Ajustar serviços</p>
                </div>
            </a>
        </div>
    </div>
</div>

@if($user->cargo === 'profissional')
    @if(($clientesAguardandoProfissional ?? collect())->isNotEmpty())
        <div class="glass-card rounded-2xl shadow-xl overflow-hidden mb-6">
            <div class="p-6 bg-white/70 backdrop-blur-sm border border-white/40">
                <div class="flex items-center gap-2 mb-4">
                    <span class="text-[#FF2EB6] text-xl">!</span>
                    <h3 class="text-lg font-title text-[#4A00B9]">Clientes aguardando atendimento</h3>
                </div>

                <div class="space-y-3">
                    @foreach($clientesAguardandoProfissional as $agendamento)
                        <div class="p-4 rounded-xl bg-white/60 border border-[#FFD6F4]">
                            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                            <div>
                                <p class="font-bold text-[#1A002B]">{{ $agendamento->cliente->name }}</p>
                                <p class="text-sm text-gray-600">{{ $agendamento->servico->nome }} - chegou {{ optional($agendamento->chegada_em)->format('H:i') }}</p>
                                <p class="text-xs font-bold {{ $agendamento->status === 'em_atendimento' ? 'text-green-700' : 'text-[#FF2EB6]' }}">
                                    {{ $agendamento->status === 'em_atendimento' ? 'Atendimento em andamento' : 'Aguardando inicio' }}
                                </p>
                            </div>
                            @if($agendamento->status === 'presente')
                                <form action="{{ route('profissional.agendamento.iniciar', $agendamento->id_agendamento) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="px-4 py-2 rounded-full bg-gradient-to-r from-green-500 to-green-600 text-white font-bold text-sm">Iniciar atendimento</button>
                                </form>
                            @endif
                            </div>

                            @if($agendamento->status === 'em_atendimento')
                                <form action="{{ route('profissional.agendamento.executado', $agendamento->id_agendamento) }}" method="POST" class="mt-4 space-y-3">
                                    @csrf
                                    <textarea name="observacao" rows="2" class="w-full px-4 py-3 bg-white/70 border border-[#FFD6F4] rounded-lg focus:outline-none focus:border-[#7B19E5] focus:ring-2 focus:ring-[#7B19E5]/20 transition-all text-sm" placeholder="Observacoes do atendimento"></textarea>

                                    <div data-dashboard-products-list class="space-y-2">
                                        <div class="flex flex-col sm:flex-row gap-2">
                                            <select name="produtos[0][id]" class="flex-1 px-4 py-2 bg-white/70 border border-[#FFD6F4] rounded-lg focus:outline-none focus:border-[#7B19E5] focus:ring-2 focus:ring-[#7B19E5]/20 transition-all text-sm">
                                                <option value="">Produto usado...</option>
                                                @foreach(($produtosAtendimento ?? collect()) as $produto)
                                                    <option value="{{ $produto->id_produto }}">{{ $produto->nome }} (Disp: {{ $produto->quantidade_estoque }})</option>
                                                @endforeach
                                            </select>
                                            <input type="number" name="produtos[0][quantidade]" value="1" min="1" class="sm:w-24 px-3 py-2 bg-white/70 border border-[#FFD6F4] rounded-lg focus:outline-none focus:border-[#7B19E5] focus:ring-2 focus:ring-[#7B19E5]/20 transition-all text-sm">
                                        </div>
                                    </div>

                                    <div class="flex flex-col sm:flex-row gap-2 sm:items-center sm:justify-between">
                                        <button type="button" data-dashboard-add-product class="text-xs bg-[#FF2EB6]/20 text-[#FF2EB6] px-3 py-2 rounded-full hover:bg-[#FF2EB6] hover:text-white transition">
                                            + Adicionar produto usado
                                        </button>
                                        <button type="submit" class="px-4 py-2 rounded-full bg-gradient-to-r from-green-500 to-green-600 text-white font-bold text-sm">
                                            Finalizar atendimento
                                        </button>
                                    </div>
                                    <p class="text-xs text-gray-500">Produtos usados serao cobrados na recepcao por 10% do valor do produto.</p>
                                </form>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        <script>
            let dashboardProdutoIndex = 1;
            document.addEventListener('click', (event) => {
                const button = event.target.closest('[data-dashboard-add-product]');
                if (!button) {
                    return;
                }

                const form = button.closest('form');
                const list = form?.querySelector('[data-dashboard-products-list]');
                const first = list?.firstElementChild;
                if (!list || !first) {
                    return;
                }

                const clone = first.cloneNode(true);
                const select = clone.querySelector('select');
                const input = clone.querySelector('input');
                select.name = `produtos[${dashboardProdutoIndex}][id]`;
                select.value = '';
                input.name = `produtos[${dashboardProdutoIndex}][quantidade]`;
                input.value = 1;
                list.appendChild(clone);
                dashboardProdutoIndex++;
            });
        </script>
    @endif

    <div class="glass-card rounded-2xl shadow-xl overflow-hidden mb-6">
        <div class="p-6 bg-white/70 backdrop-blur-sm border border-white/40">
            <div class="flex items-center gap-2 mb-4">
                <span class="text-[#FF2EB6] text-xl">✦</span>
                <h3 class="text-lg font-title text-[#4A00B9]">Avisos de Agenda e Valores Especiais</h3>
            </div>

            <div class="space-y-3 text-sm text-[#1A002B]">
                <p>Quando você bloquear um dia, sua agenda fica indisponível nesse período mesmo que o cliente aceite pagar mais.</p>
                <p>Atendimentos aos sábados, domingos, almoço padrão, feriados e bloqueios gerais podem gerar acréscimos para o cliente. Sua comissão permanece calculada sobre o valor base do serviço.</p>
            </div>

            <div class="mt-4 space-y-2">
                @forelse($bloqueiosProfissionalFuturos as $bloqueio)
                    <div class="flex flex-wrap justify-between gap-2 p-3 rounded-xl bg-white/50 border border-[#FFD6F4]">
                        <span class="font-semibold text-[#4A00B9]">{{ \Carbon\Carbon::parse($bloqueio->data_hora_inicio)->format('d/m/Y') }}</span>
                        <span class="text-gray-600">{{ $bloqueio->motivo ?? 'Indisponibilidade' }}</span>
                    </div>
                @empty
                    <div class="p-3 rounded-xl bg-white/50 border border-[#FFD6F4] text-gray-500">
                        ✧ Nenhum bloqueio futuro informado por você.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    @php
        $temAvaliacoes = 
            (($mediaAvaliacao ?? 0) > 0) ||
            (($totalAvaliacoes ?? 0) > 0) ||
            (isset($comentariosAvaliacao) && $comentariosAvaliacao->count() > 0);
    @endphp

    @if($temAvaliacoes)

    <div class="glass-card rounded-2xl shadow-xl overflow-hidden mb-6">
        <div class="p-6 bg-white/70 backdrop-blur-sm border border-white/40">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 bg-gradient-to-br from-[#7B19E5] to-[#FF2EB6] rounded-xl flex items-center justify-center shadow-md">
                    <span class="text-white text-lg">✧</span>
                </div>
                <div>
                    <h3 class="text-lg font-title text-[#4A00B9]">Avaliações e Reputação</h3>
                    <p class="text-xs text-gray-500">Média de estrelas e comentários dos seus clientes</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="glass-card rounded-2xl overflow-hidden">
                    <div class="p-5 bg-gradient-to-br from-[#7B19E5] to-[#A855F7] text-white">
                        <h4 class="text-xs font-bold uppercase tracking-wider mb-3 opacity-90">Média Geral</h4>
                        @if($mediaAvaliacao)
                            <div class="flex items-end gap-2">
                                <span class="text-4xl font-black">{{ number_format($mediaAvaliacao, 1) }}</span>
                                <span class="text-lg font-bold opacity-80">/ 5.0</span>
                            </div>
                            <div class="flex gap-1 mt-2">
                                @for($i = 1; $i <= 5; $i++)
                                    <span class="{{ $i <= round($mediaAvaliacao) ? 'text-white' : 'text-white/40' }}">★</span>
                                @endfor
                            </div>
                            <p class="text-xs mt-3 bg-white/20 px-3 py-1 rounded-full inline-block">{{ $totalAvaliacoes }} avaliações</p>
                        @else
                            <p class="text-sm opacity-90">✧ Sem avaliações registradas ainda.</p>
                        @endif
                    </div>
                </div>

                <div class="lg:col-span-2">
                    <h4 class="text-sm font-bold text-[#4A00B9] mb-3">Comentários Recentes</h4>
                    <div class="space-y-4 max-h-[320px] overflow-y-auto pr-2">
                        @forelse($comentariosAvaliacao as $comentario)
                            <div class="bg-white/50 p-4 rounded-xl border-l-4 {{ $comentario->nota >= 4 ? 'border-[#7B19E5]' : ($comentario->nota == 3 ? 'border-[#FF2EB6]' : 'border-gray-400') }}">
                                <div class="flex justify-between items-start mb-2">
                                    <div>
                                        <span class="font-bold text-[#1A002B]">{{ $comentario->cliente_nome }}</span>
                                    </div>
                                    <div class="flex gap-1 text-sm">
                                        @for($i = 1; $i <= 5; $i++)
                                            <span class="{{ $i <= $comentario->nota ? 'text-[#FF2EB6]' : 'text-gray-300' }}">★</span>
                                        @endfor
                                    </div>
                                </div>
                                <p class="text-gray-700 text-sm italic">"{{ $comentario->comentario }}"</p>
                                <p class="text-xs text-gray-400 mt-2 text-right">
                                    {{ \Carbon\Carbon::parse($comentario->created_at)->format('d/m/Y H:i') }}
                                </p>
                            </div>
                        @empty
                            <div class="text-center p-6 bg-white/30 rounded-lg text-gray-500">
                                <p class="text-2xl mb-2">✧</p>
                                Nenhum comentário por escrito recebido.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
@endif
