<div class="glass-card rounded-2xl shadow-xl overflow-hidden mb-6">
    <div class="p-6 bg-white/70 backdrop-blur-sm border border-white/40">
        <div class="flex items-center gap-2 mb-5">
            <span class="text-[#7B19E5] text-xl">✦</span>
            <h3 class="text-lg font-title text-[#4A00B9]">Menu Gerencial</h3>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            {{-- Agenda --}}
            <a href="{{ route('admin.agenda.index') }}" class="group flex items-center gap-4 p-4 rounded-xl bg-white/50 hover:bg-white/80 transition-all hover:shadow-lg hover:-translate-y-1 border border-[#FFD6F4]">
                <div class="w-12 h-12 bg-gradient-to-br from-[#7B19E5] to-[#A855F7] rounded-xl flex items-center justify-center"><span class="text-white text-xl">📅</span></div>
                <div><h4 class="font-title text-[#4A00B9]">Agenda</h4><p class="text-xs text-gray-500">Ver calendário geral</p></div>
            </a>
            
            {{-- Financeiro - Fechamento --}}
            <a href="{{ route('admin.financeiro.fechamento') }}" class="group flex items-center gap-4 p-4 rounded-xl bg-white/50 hover:bg-white/80 transition-all hover:shadow-lg hover:-translate-y-1 border border-[#FFD6F4]">
                <div class="w-12 h-12 bg-gradient-to-br from-[#10B981] to-[#059669] rounded-xl flex items-center justify-center"><span class="text-white text-xl">💰</span></div>
                <div><h4 class="font-title text-[#4A00B9]">Caixa do Dia</h4><p class="text-xs text-gray-500">Lucro e Fechamento</p></div>
            </a>

            {{-- Financeiro - Comissões --}}
            <a href="{{ route('admin.financeiro.comissoes') }}" class="group flex items-center gap-4 p-4 rounded-xl bg-white/50 hover:bg-white/80 transition-all hover:shadow-lg hover:-translate-y-1 border border-[#FFD6F4]">
                <div class="w-12 h-12 bg-gradient-to-br from-[#F59E0B] to-[#D97706] rounded-xl flex items-center justify-center"><span class="text-white text-xl">📊</span></div>
                <div><h4 class="font-title text-[#4A00B9]">Comissões</h4><p class="text-xs text-gray-500">Pagar profissionais</p></div>
            </a>

            {{-- Usuários e Clientes --}}
            <a href="{{ route('admin.usuarios.index') }}" class="group flex items-center gap-4 p-4 rounded-xl bg-white/50 hover:bg-white/80 transition-all hover:shadow-lg hover:-translate-y-1 border border-[#FFD6F4]">
                <div class="w-12 h-12 bg-gradient-to-br from-[#FF2EB6] to-[#FF69B4] rounded-xl flex items-center justify-center"><span class="text-white text-xl">👥</span></div>
                <div><h4 class="font-title text-[#4A00B9]">Usuários</h4><p class="text-xs text-gray-500">Equipe e Clientes</p></div>
            </a>

            {{-- Serviços --}}
            <a href="{{ route('admin.servicos.index') }}" class="group flex items-center gap-4 p-4 rounded-xl bg-white/50 hover:bg-white/80 transition-all hover:shadow-lg hover:-translate-y-1 border border-[#FFD6F4]">
                <div class="w-12 h-12 bg-gradient-to-br from-[#3B82F6] to-[#2563EB] rounded-xl flex items-center justify-center"><span class="text-white text-xl">✂️</span></div>
                <div><h4 class="font-title text-[#4A00B9]">Serviços</h4><p class="text-xs text-gray-500">Tabela de preços</p></div>
            </a>

            {{-- Produtos --}}
            <a href="{{ route('admin.produtos.index') }}" class="group flex items-center gap-4 p-4 rounded-xl bg-white/50 hover:bg-white/80 transition-all hover:shadow-lg hover:-translate-y-1 border border-[#FFD6F4]">
                <div class="w-12 h-12 bg-gradient-to-br from-[#EC4899] to-[#BE185D] rounded-xl flex items-center justify-center"><span class="text-white text-xl">🛍️</span></div>
                <div><h4 class="font-title text-[#4A00B9]">Produtos</h4><p class="text-xs text-gray-500">Estoque de venda</p></div>
            </a>
            
            {{-- Pacotes --}}
            <a href="{{ route('admin.pacotes.index') }}" class="group flex items-center gap-4 p-4 rounded-xl bg-white/50 hover:bg-white/80 transition-all hover:shadow-lg hover:-translate-y-1 border border-[#FFD6F4]">
                <div class="w-12 h-12 bg-gradient-to-br from-[#8B5CF6] to-[#6D28D9] rounded-xl flex items-center justify-center"><span class="text-white text-xl">🎁</span></div>
                <div><h4 class="font-title text-[#4A00B9]">Pacotes</h4><p class="text-xs text-gray-500">Criar pacotes</p></div>
            </a>
            <a href="{{ route('admin.bloqueios.index') }}" class="glass-card p-5 rounded-2xl shadow-lg border border-white/40 bg-white/70 backdrop-blur-sm flex items-center gap-4 hover:-translate-y-1 transition-transform cursor-pointer group">
                <div class="bg-gradient-to-br from-orange-400 to-red-500 w-12 h-12 rounded-xl flex items-center justify-center text-white shadow-md group-hover:shadow-lg transition-all">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="font-title text-lg text-[#1A002B] font-bold">Bloquear Horários</h3>
                    <p class="text-sm text-gray-600">Gerenciar folgas e pausas</p>
                </div>
            </a>
            <a href="{{ route('admin.relatorios.index') }}" class="glass-card p-5 rounded-2xl shadow-lg border border-white/40 bg-white/70 backdrop-blur-sm flex items-center gap-4 hover:-translate-y-1 transition-transform cursor-pointer group">
                <div class="bg-gradient-to-br from-blue-400 to-purple-500 w-12 h-12 rounded-xl flex items-center justify-center text-white shadow-md group-hover:shadow-lg transition-all">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-2m3 0v-2m-3 8V9a6 6 0 0112 0v8a6 6 0 01-12 0z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="font-title text-lg text-[#1A002B] font-bold">Relatórios</h3>
                    <p class="text-sm text-gray-600">Visualizar e exportar dados</p>
                </div>
            </a>
        </div>
    </div>
</div>