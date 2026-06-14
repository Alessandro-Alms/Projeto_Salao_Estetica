@if(($contatoMensagens ?? collect())->count() > 0)
    <div class="glass-card rounded-2xl shadow-xl overflow-hidden mb-6">
        <div class="p-6 bg-white/70 backdrop-blur-sm border border-white/40">
            <div class="flex flex-wrap items-center justify-between gap-3 mb-5">
                <div class="flex items-center gap-2">
                    <span class="text-[#7B19E5] text-xl">✧</span>
                    <h3 class="text-lg font-title text-[#4A00B9]">Mensagens do site</h3>
                </div>
                <a href="{{ route('admin.contatos.index') }}" class="text-sm font-bold text-[#7B19E5] hover:text-[#FF2EB6] transition-colors">
                    Ver todas
                </a>
            </div>

            @if(($contatoMensagensNaoLidas ?? 0) > 0)
                <div class="mb-4 rounded-xl border border-[#FFD6F4] bg-[#FF2EB6]/10 px-4 py-3 text-sm font-semibold text-[#FF2EB6]">
                    {{ $contatoMensagensNaoLidas }} mensagem(ns) nova(s) aguardando leitura.
                </div>
            @endif

            <div class="space-y-3">
                @foreach($contatoMensagens as $mensagem)
                    <div class="rounded-xl border border-[#FFD6F4] bg-white/60 p-4">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div>
                                <p class="font-title text-[#4A00B9]">{{ $mensagem->assunto }}</p>
                                <p class="text-xs text-gray-500">
                                    {{ $mensagem->nome }} · {{ $mensagem->email }}
                                </p>
                            </div>
                            @if(!$mensagem->lida_at)
                                <span class="rounded-full bg-[#FF2EB6]/10 px-3 py-1 text-xs font-bold text-[#FF2EB6]">Nova</span>
                            @endif
                        </div>
                        <p class="mt-2 text-sm text-[#1A002B] line-clamp-2">{{ $mensagem->mensagem }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endif

@if(($pagamentosPendentesProdutos ?? 0) + ($pagamentosPendentesPacotes ?? 0) > 0)
    <div class="glass-card rounded-2xl shadow-xl overflow-hidden mb-6">
        <div class="p-6 bg-amber-50/80 backdrop-blur-sm border border-amber-200">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <span class="text-amber-600 text-xl">✧</span>
                        <h3 class="text-lg font-title text-[#4A00B9]">Pagamentos para confirmar</h3>
                    </div>
                    <p class="text-sm text-gray-600">
                        {{ $pagamentosPendentesPacotes ?? 0 }} pacote(s) via PIX e {{ $pagamentosPendentesProdutos ?? 0 }} produto(s) reservado(s) aguardando conferência.
                    </p>
                </div>
                <a href="{{ route('admin.vendas.pendentes') }}" class="inline-flex items-center justify-center px-5 py-3 rounded-full bg-gradient-to-r from-[#7B19E5] to-[#FF2EB6] text-white font-bold">
                    Conferir agora
                </a>
            </div>
        </div>
    </div>
@endif

<div class="glass-card rounded-2xl shadow-xl overflow-hidden mb-6">
    <div class="p-6 bg-white/70 backdrop-blur-sm border border-white/40">
        <div class="flex items-center gap-2 mb-5">
            <span class="text-[#7B19E5] text-xl">✧</span>
            <h3 class="text-lg font-title text-[#4A00B9]">Menu Gerencial</h3>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            {{-- Agenda (roxo) --}}
            <a href="{{ route('admin.agenda.index') }}" class="group flex items-center gap-4 p-4 rounded-xl bg-white/50 hover:bg-white/80 transition-all hover:shadow-lg hover:-translate-y-1 border border-[#FFD6F4]">
                <div class="w-12 h-12 bg-gradient-to-br from-[#7B19E5] to-[#A855F7] rounded-xl flex items-center justify-center shadow-md">
                    <span class="text-white text-xl">✧</span>
                </div>
                <div>
                    <h4 class="font-title text-[#4A00B9]">Agenda</h4>
                    <p class="text-xs text-gray-500">Ver calendário geral</p>
                </div>
            </a>
            
            {{-- Agendar Cliente (rosa) --}}
            <a href="{{ route('admin.agendar.cliente') }}" class="group flex items-center gap-4 p-4 rounded-xl bg-white/50 hover:bg-white/80 transition-all hover:shadow-lg hover:-translate-y-1 border border-[#FFD6F4]">
                <div class="w-12 h-12 bg-gradient-to-br from-[#FF2EB6] to-[#FF69B4] rounded-xl flex items-center justify-center shadow-md">
                    <span class="text-white text-xl">✦</span>
                </div>
                <div>
                    <h4 class="font-title text-[#4A00B9]">Agendar Cliente</h4>
                    <p class="text-xs text-gray-500">Marcar para um cliente</p>
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

            {{-- Caixa do Dia (roxo) --}}
            <a href="{{ route('admin.financeiro.fechamento') }}" class="group flex items-center gap-4 p-4 rounded-xl bg-white/50 hover:bg-white/80 transition-all hover:shadow-lg hover:-translate-y-1 border border-[#FFD6F4]">
                <div class="w-12 h-12 bg-gradient-to-br from-[#7B19E5] to-[#A855F7] rounded-xl flex items-center justify-center shadow-md">
                    <span class="text-white text-xl">✧</span>
                </div>
                <div>
                    <h4 class="font-title text-[#4A00B9]">Caixa do Dia</h4>
                    <p class="text-xs text-gray-500">Lucro e Fechamento</p>
                </div>
            </a>

            {{-- Comissões (rosa) --}}
            <a href="{{ route('admin.financeiro.comissoes') }}" class="group flex items-center gap-4 p-4 rounded-xl bg-white/50 hover:bg-white/80 transition-all hover:shadow-lg hover:-translate-y-1 border border-[#FFD6F4]">
                <div class="w-12 h-12 bg-gradient-to-br from-[#FF2EB6] to-[#FF69B4] rounded-xl flex items-center justify-center shadow-md">
                    <span class="text-white text-xl">✦</span>
                </div>
                <div>
                    <h4 class="font-title text-[#4A00B9]">Comissões</h4>
                    <p class="text-xs text-gray-500">Pagar profissionais</p>
                </div>
            </a>

            {{-- Usuários (roxo) --}}
            <a href="{{ route('admin.usuarios.index') }}" class="group flex items-center gap-4 p-4 rounded-xl bg-white/50 hover:bg-white/80 transition-all hover:shadow-lg hover:-translate-y-1 border border-[#FFD6F4]">
                <div class="w-12 h-12 bg-gradient-to-br from-[#7B19E5] to-[#A855F7] rounded-xl flex items-center justify-center shadow-md">
                    <span class="text-white text-xl">✧</span>
                </div>
                <div>
                    <h4 class="font-title text-[#4A00B9]">Usuários</h4>
                    <p class="text-xs text-gray-500">Equipe e Clientes</p>
                </div>
            </a>

            {{-- Serviços (rosa) --}}
            <a href="{{ route('admin.servicos.index') }}" class="group flex items-center gap-4 p-4 rounded-xl bg-white/50 hover:bg-white/80 transition-all hover:shadow-lg hover:-translate-y-1 border border-[#FFD6F4]">
                <div class="w-12 h-12 bg-gradient-to-br from-[#FF2EB6] to-[#FF69B4] rounded-xl flex items-center justify-center shadow-md">
                    <span class="text-white text-xl">✦</span>
                </div>
                <div>
                    <h4 class="font-title text-[#4A00B9]">Serviços</h4>
                    <p class="text-xs text-gray-500">Tabela de preços</p>
                </div>
            </a>

            {{-- Produtos (roxo) --}}
            <a href="{{ route('admin.produtos.index') }}" class="group flex items-center gap-4 p-4 rounded-xl bg-white/50 hover:bg-white/80 transition-all hover:shadow-lg hover:-translate-y-1 border border-[#FFD6F4]">
                <div class="w-12 h-12 bg-gradient-to-br from-[#7B19E5] to-[#A855F7] rounded-xl flex items-center justify-center shadow-md">
                    <span class="text-white text-xl">✧</span>
                </div>
                <div>
                    <h4 class="font-title text-[#4A00B9]">Produtos</h4>
                    <p class="text-xs text-gray-500">Estoque de venda</p>
                </div>
            </a>

            {{-- Vender Produto (rosa) --}}
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
            
            <a href="{{ route('admin.contatos.index') }}" class="group flex items-center gap-4 p-4 rounded-xl bg-white/50 hover:bg-white/80 transition-all hover:shadow-lg hover:-translate-y-1 border border-[#FFD6F4]">
                <div class="w-12 h-12 bg-gradient-to-br from-[#FF2EB6] to-[#FF69B4] rounded-xl flex items-center justify-center text-white text-xl shadow-md">
                    ✧
                </div>
                <div>
                    <h4 class="font-title text-[#4A00B9]">Mensagens do site</h4>
                    <p class="text-xs text-gray-500">
                        {{ ($contatoMensagensNaoLidas ?? 0) > 0 ? ($contatoMensagensNaoLidas . ' nova(s) para ler') : 'Ver contatos recebidos' }}
                    </p>
                </div>
            </a>

            {{-- Pacotes (roxo) --}}
            <a href="{{ route('admin.pacotes.index') }}" class="group flex items-center gap-4 p-4 rounded-xl bg-white/50 hover:bg-white/80 transition-all hover:shadow-lg hover:-translate-y-1 border border-[#FFD6F4]">
                <div class="w-12 h-12 bg-gradient-to-br from-[#7B19E5] to-[#A855F7] rounded-xl flex items-center justify-center shadow-md">
                    <span class="text-white text-xl">✧</span>
                </div>
                <div>
                    <h4 class="font-title text-[#4A00B9]">Pacotes</h4>
                    <p class="text-xs text-gray-500">Criar pacotes</p>
                </div>
            </a>

            {{-- Bloquear Horários (rosa) --}}
            <a href="{{ route('admin.bloqueios.index') }}" class="group flex items-center gap-4 p-4 rounded-xl bg-white/50 hover:bg-white/80 transition-all hover:shadow-lg hover:-translate-y-1 border border-[#FFD6F4]">
                <div class="w-12 h-12 bg-gradient-to-br from-[#FF2EB6] to-[#FF69B4] rounded-xl flex items-center justify-center shadow-md">
                    <span class="text-white text-xl">✦</span>
                </div>
                <div>
                    <h4 class="font-title text-[#4A00B9]">Bloquear Horários</h4>
                    <p class="text-xs text-gray-500">Gerenciar folgas e pausas</p>
                </div>
            </a>

            {{-- Relatórios (roxo) --}}
            <a href="{{ route('admin.relatorios.index') }}" class="group flex items-center gap-4 p-4 rounded-xl bg-white/50 hover:bg-white/80 transition-all hover:shadow-lg hover:-translate-y-1 border border-[#FFD6F4]">
                <div class="w-12 h-12 bg-gradient-to-br from-[#7B19E5] to-[#A855F7] rounded-xl flex items-center justify-center shadow-md">
                    <span class="text-white text-xl">✧</span>
                </div>
                <div>
                    <h4 class="font-title text-[#4A00B9]">Relatórios</h4>
                    <p class="text-xs text-gray-500">Visualizar e exportar dados</p>
                </div>
            </a>
        </div>
    </div>
</div>
