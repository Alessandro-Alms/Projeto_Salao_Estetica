<div class="glass-card rounded-2xl shadow-xl overflow-hidden mb-6">
    <div class="p-6 bg-white/70 backdrop-blur-sm border border-white/40">
        <div class="flex items-center gap-2 mb-5">
            <span class="text-[#7B19E5] text-xl">✧</span>
            <h3 class="text-lg font-title text-[#4A00B9]">Minha Área</h3>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="{{ route('profissional.agenda') }}" class="group flex items-center gap-4 p-4 rounded-xl bg-white/50 hover:bg-white/80 transition-all hover:shadow-lg hover:-translate-y-1 border border-[#FFD6F4]">
                <div class="w-12 h-12 bg-gradient-to-br from-[#7B19E5] to-[#A855F7] rounded-xl flex items-center justify-center shadow-md">
                    <span class="text-white text-xl">Calendário</span>
                </div>
                <div>
                    <h4 class="font-title text-[#4A00B9]">Minha Agenda</h4>
                    <p class="text-xs text-gray-500">Meus clientes do dia</p>
                </div>
            </a>

            <a href="{{ route('profissional.extrato') }}" class="group flex items-center gap-4 p-4 rounded-xl bg-white/50 hover:bg-white/80 transition-all hover:shadow-lg hover:-translate-y-1 border border-[#FFD6F4]">
                <div class="w-12 h-12 bg-gradient-to-br from-[#FF2EB6] to-[#FF69B4] rounded-xl flex items-center justify-center shadow-md">
                    <span class="text-white text-xl">✧</span>
                </div>
                <div>
                    <h4 class="font-title text-[#4A00B9]">Meu Extrato</h4>
                    <p class="text-xs text-gray-500">Ver comissões</p>
                </div>
            </a>

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
    <div class="glass-card rounded-2xl shadow-xl overflow-hidden mb-6">
        <div class="p-6 bg-white/70 backdrop-blur-sm border border-white/40">
            <div class="flex items-center gap-2 mb-4">
                <span class="text-[#FF2EB6] text-xl">!</span>
                <h3 class="text-lg font-title text-[#4A00B9]">Avisos de Agenda e Valores Especiais</h3>
            </div>

            <div class="space-y-3 text-sm text-[#1A002B]">
                <p>Quando você bloquear um dia, sua agenda fica indisponível nesse período mesmo que o cliente aceite pagar mais.</p>
                <p>Atendimentos aos sábados e domingos têm acréscimo de 25% e aumentam sua comissão. Almoço padrão, feriados e bloqueios gerais também podem gerar acréscimos; quando acontecem juntos, os percentuais são somados.</p>
            </div>

            <div class="mt-4 space-y-2">
                @forelse($bloqueiosProfissionalFuturos as $bloqueio)
                    <div class="flex flex-wrap justify-between gap-2 p-3 rounded-xl bg-white/50 border border-[#FFD6F4]">
                        <span class="font-semibold text-[#4A00B9]">{{ \Carbon\Carbon::parse($bloqueio->data_hora_inicio)->format('d/m/Y') }}</span>
                        <span class="text-gray-600">{{ $bloqueio->motivo ?? 'Indisponibilidade' }}</span>
                    </div>
                @empty
                    <div class="p-3 rounded-xl bg-white/50 border border-[#FFD6F4] text-gray-500">
                        Nenhum bloqueio futuro informado por você.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="glass-card rounded-2xl shadow-xl overflow-hidden mb-6">
        <div class="p-6 bg-white/70 backdrop-blur-sm border border-white/40">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 bg-gradient-to-br from-[#7B19E5] to-[#FF2EB6] rounded-xl flex items-center justify-center shadow-md">
                    <span class="text-white text-lg">✦</span>
                </div>
                <div>
                    <h3 class="text-lg font-title text-[#4A00B9]">REL011: Avaliações e Reputação</h3>
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
                            <p class="text-sm opacity-90">Sem avaliações registradas ainda.</p>
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
