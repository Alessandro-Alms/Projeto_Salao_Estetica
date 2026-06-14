{{-- Recepcao --}}
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
