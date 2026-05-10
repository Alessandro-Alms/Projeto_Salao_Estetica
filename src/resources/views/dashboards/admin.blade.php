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