{{-- Recepcao --}}
@if(($chegadasPendentes ?? collect())->isNotEmpty() || ($saidasPendentes ?? collect())->isNotEmpty())
    <div class="glass-card rounded-2xl shadow-xl overflow-hidden mb-6">
        <div class="p-6 bg-white/70 backdrop-blur-sm border border-white/40">
            <div class="flex items-center gap-2 mb-5">
                <span class="text-[#FF2EB6] text-xl">!</span>
                <h3 class="text-lg font-title text-[#4A00B9]">Fluxo de Atendimento</h3>
            </div>

            @if(($chegadasPendentes ?? collect())->isNotEmpty())
                <div class="mb-6">
                    <h4 class="text-sm font-bold uppercase text-[#4A00B9] mb-3">Clientes para registrar chegada</h4>
                    <div class="space-y-3">
                        @foreach($chegadasPendentes as $agendamento)
                            @php
                                $inicioAgendamento = \Carbon\Carbon::parse($agendamento->data_hora_inicio);
                                $limiteChegada = \Carbon\Carbon::parse($agendamento->data_hora_inicio)->addMinutes(20);
                                $estaAdiantado = now()->lt($inicioAgendamento);
                            @endphp
                            <div class="p-4 rounded-xl bg-white/60 border border-[#FFD6F4] flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
                                <div>
                                    <p class="font-bold text-[#1A002B]">{{ $agendamento->cliente->name }} <span class="text-sm text-gray-500">com {{ $agendamento->profissional->name }}</span></p>
                                    <p class="text-sm text-gray-600">{{ $agendamento->servico->nome }} - {{ $inicioAgendamento->format('H:i') }}</p>
                                    @if($estaAdiantado)
                                        <p class="text-sm font-bold text-[#4A00B9]">
                                            Agendamento futuro. Pode registrar chegada antecipada.
                                        </p>
                                    @else
                                        <p class="text-sm font-bold text-[#FF2EB6]">
                                            Chegada em ate <span data-countdown="{{ $limiteChegada->toIso8601String() }}">--:--</span>
                                        </p>
                                    @endif
                                </div>
                                <div class="flex flex-wrap gap-2">
                                    <form action="{{ route('admin.agendamento.presenca', $agendamento->id_agendamento) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="px-4 py-2 rounded-full bg-gradient-to-r from-[#7B19E5] to-[#A855F7] text-white font-bold text-sm">Registrar chegada</button>
                                    </form>
                                    <form action="{{ route('admin.agendamentos.falta', $agendamento->id_agendamento) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="px-4 py-2 rounded-full border border-[#FF2EB6] text-[#FF2EB6] font-bold text-sm">Marcar falta</button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if(($saidasPendentes ?? collect())->isNotEmpty())
                <div>
                    <h4 class="text-sm font-bold uppercase text-[#4A00B9] mb-3">Atendimentos aguardando pagamento e saida</h4>
                    <div class="space-y-4">
                        @foreach($saidasPendentes as $agendamento)
                            @php
                                $valorProdutos = $agendamento->vendas->where('status_pagamento', 'pendente')->sum('valor_venda');
                                $valorTotalSaida = (float) $agendamento->valor_total + (float) $valorProdutos;
                            @endphp
                            <div class="p-4 rounded-xl bg-white/60 border border-[#FFD6F4]">
                                <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                                    <div>
                                        <p class="font-bold text-[#1A002B]">{{ $agendamento->cliente->name }}</p>
                                        <p class="text-sm text-gray-600">{{ $agendamento->servico->nome }} com {{ $agendamento->profissional->name }}</p>
                                        <p class="text-sm text-[#4A00B9] font-bold mt-1">Total: R$ {{ number_format($valorTotalSaida, 2, ',', '.') }}</p>
                                        @if($valorProdutos > 0)
                                            <p class="text-xs text-gray-500">Inclui produtos usados: R$ {{ number_format($valorProdutos, 2, ',', '.') }}</p>
                                        @endif
                                    </div>
                                    <form action="{{ route('admin.agendamento.saida', $agendamento->id_agendamento) }}" method="POST" class="w-full lg:max-w-md">
                                        @csrf
                                        <x-payment-fields
                                            :formas-pagamento="['dinheiro', 'pix', 'cartao_debito', 'cartao_credito']"
                                            :total="$valorTotalSaida"
                                        />
                                        <button type="submit" class="mt-3 w-full px-4 py-2 rounded-full bg-gradient-to-r from-green-500 to-green-600 text-white font-bold text-sm">Registrar saida e pagamento</button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    <script>
        document.querySelectorAll('[data-countdown]').forEach((item) => {
            const tick = () => {
                const diff = new Date(item.dataset.countdown).getTime() - Date.now();
                if (diff <= 0) {
                    item.textContent = '00:00';
                    if (!item.dataset.expired) {
                        item.dataset.expired = 'true';
                        setTimeout(() => window.location.reload(), 1000);
                    }
                    return;
                }

                const totalSeconds = Math.floor(diff / 1000);
                const minutes = String(Math.floor(totalSeconds / 60)).padStart(2, '0');
                const seconds = String(totalSeconds % 60).padStart(2, '0');
                item.textContent = `${minutes}:${seconds}`;
            };

            tick();
            setInterval(tick, 1000);
        });
    </script>
@endif

<div class="glass-card rounded-2xl shadow-xl overflow-hidden mb-6">
    <div class="p-6 bg-white/70 backdrop-blur-sm border border-white/40">
        <div class="flex items-center gap-2 mb-5">
            <span class="text-[#7B19E5] text-xl">✧</span>
            <h3 class="text-lg font-title text-[#4A00B9]">Recepção</h3>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            {{-- 1. Agendar Cliente (Roxo + ✧) --}}
            <a href="{{ route('admin.agendar.cliente') }}" class="group flex items-center gap-4 p-4 rounded-xl bg-white/50 hover:bg-white/80 transition-all hover:shadow-lg hover:-translate-y-1 border border-[#FFD6F4]">
                <div class="w-12 h-12 bg-gradient-to-br from-[#7B19E5] to-[#A855F7] rounded-xl flex items-center justify-center shadow-md">
                    <span class="text-white text-xl">✧</span>
                </div>
                <div>
                    <h4 class="font-title text-[#4A00B9]">Agendar Cliente</h4>
                    <p class="text-xs text-gray-500">Marcar para um cliente</p>
                </div>
            </a>

            {{-- 2. Calendário (Rosa + ✦) --}}
            <a href="{{ route('admin.agenda.index') }}" class="group flex items-center gap-4 p-4 rounded-xl bg-white/50 hover:bg-white/80 transition-all hover:shadow-lg hover:-translate-y-1 border border-[#FFD6F4]">
                <div class="w-12 h-12 bg-gradient-to-br from-[#FF2EB6] to-[#FF69B4] rounded-xl flex items-center justify-center shadow-md">
                    <span class="text-white text-xl">✦</span>
                </div>
                <div>
                    <h4 class="font-title text-[#4A00B9]">Calendário</h4>
                    <p class="text-xs text-gray-500">Gerenciar agendamentos</p>
                </div>
            </a>

            <a href="{{ route('disponibilidade.profissionais') }}" class="group flex items-center gap-4 p-4 rounded-xl bg-white/50 hover:bg-white/80 transition-all hover:shadow-lg hover:-translate-y-1 border border-[#FFD6F4]">
                <div class="w-12 h-12 bg-gradient-to-br from-[#7B19E5] to-[#A855F7] rounded-xl flex items-center justify-center shadow-md">
                    <span class="text-white text-xl">+</span>
                </div>
                <div>
                    <h4 class="font-title text-[#4A00B9]">Disponibilidade</h4>
                    <p class="text-xs text-gray-500">Ver profissionais livres</p>
                </div>
            </a>

            {{-- 3. Vender Pacote (Roxo + ✧) --}}
            <a href="{{ route('admin.venda.create') }}" class="group flex items-center gap-4 p-4 rounded-xl bg-white/50 hover:bg-white/80 transition-all hover:shadow-lg hover:-translate-y-1 border border-[#FFD6F4]">
                <div class="w-12 h-12 bg-gradient-to-br from-[#7B19E5] to-[#A855F7] rounded-xl flex items-center justify-center shadow-md">
                    <span class="text-white text-xl">✧</span>
                </div>
                <div>
                    <h4 class="font-title text-[#4A00B9]">Vender Pacote</h4>
                    <p class="text-xs text-gray-500">Vincular pacote a cliente</p>
                </div>
            </a>

            {{-- 4. Vender Produto (Rosa + ✦) --}}
            <a href="{{ route('admin.vendas.produtos.create') }}" class="group flex items-center gap-4 p-4 rounded-xl bg-white/50 hover:bg-white/80 transition-all hover:shadow-lg hover:-translate-y-1 border border-[#FFD6F4]">
                <div class="w-12 h-12 bg-gradient-to-br from-[#FF2EB6] to-[#FF69B4] rounded-xl flex items-center justify-center shadow-md">
                    <span class="text-white text-xl">✦</span>
                </div>
                <div>
                    <h4 class="font-title text-[#4A00B9]">Vender Produto</h4>
                    <p class="text-xs text-gray-500">Registrar venda no balcão</p>
                </div>
            </a>

            <a href="{{ route('admin.vendas.pendentes') }}" class="group flex items-center gap-4 p-4 rounded-xl bg-white/50 hover:bg-white/80 transition-all hover:shadow-lg hover:-translate-y-1 border border-[#FFD6F4]">
                <div class="w-12 h-12 bg-gradient-to-br from-[#7B19E5] to-[#A855F7] rounded-xl flex items-center justify-center shadow-md">
                    <span class="text-white text-xl">✧</span>
                </div>
                <div>
                    <h4 class="font-title text-[#4A00B9]">Compras Pendentes</h4>
                    <p class="text-xs text-gray-500">Confirmar pagamento</p>
                </div>
            </a>

            {{-- 5. Novo Cliente (Roxo + ✧) --}}
            <a href="{{ route('admin.usuarios.criar') }}" class="group flex items-center gap-4 p-4 rounded-xl bg-white/50 hover:bg-white/80 transition-all hover:shadow-lg hover:-translate-y-1 border border-[#FFD6F4]">
                <div class="w-12 h-12 bg-gradient-to-br from-[#7B19E5] to-[#A855F7] rounded-xl flex items-center justify-center shadow-md">
                    <span class="text-white text-xl">✧</span>
                </div>
                <div>
                    <h4 class="font-title text-[#4A00B9]">Novo Cliente</h4>
                    <p class="text-xs text-gray-500">Cadastrar no sistema</p>
                </div>
            </a>
        </div>
    </div>
</div>

{{-- Caixa (Financeiro) --}}
<div class="glass-card rounded-2xl shadow-xl overflow-hidden mb-6">
    <div class="p-6 bg-white/70 backdrop-blur-sm border border-white/40">
        <div class="flex items-center gap-2 mb-5">
            <span class="text-[#7B19E5] text-xl">✧</span>
            <h3 class="text-lg font-title text-[#4A00B9]">Caixa</h3>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            {{-- Fechamento de Caixa (Rosa + ✦) --}}
            <a href="{{ route('admin.financeiro.fechamento') }}" class="group flex items-center gap-4 p-4 rounded-xl bg-white/50 hover:bg-white/80 transition-all hover:shadow-lg hover:-translate-y-1 border border-[#FFD6F4]">
                <div class="w-12 h-12 bg-gradient-to-br from-[#FF2EB6] to-[#FF69B4] rounded-xl flex items-center justify-center shadow-md">
                    <span class="text-white text-xl">✦</span>
                </div>
                <div>
                    <h4 class="font-title text-[#4A00B9]">Fechamento de Caixa</h4>
                    <p class="text-xs text-gray-500">Lucro e fechamento do dia</p>
                </div>
            </a>
        </div>
    </div>
</div>
