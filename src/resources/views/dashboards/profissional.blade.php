<div class="glass-card rounded-2xl shadow-xl overflow-hidden mb-6">
    <div class="p-6 bg-white/70 backdrop-blur-sm border border-white/40">
        <div class="flex items-center gap-2 mb-5">
            <span class="text-[#7B19E5] text-xl">✧</span>
            <h3 class="text-lg font-title text-[#4A00B9]">Minha Área</h3>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="{{ route('profissional.agenda') }}" class="group flex items-center gap-4 p-4 rounded-xl bg-white/50 hover:bg-white/80 transition-all hover:shadow-lg hover:-translate-y-1 border border-[#FFD6F4]">
                <div class="w-12 h-12 bg-gradient-to-br from-[#7B19E5] to-[#A855F7] rounded-xl flex items-center justify-center"><span class="text-white text-xl">📅</span></div>
                <div><h4 class="font-title text-[#4A00B9]">Minha Agenda</h4><p class="text-xs text-gray-500">Meus clientes do dia</p></div>
            </a>

            <a href="{{ route('profissional.extrato') }}" class="group flex items-center gap-4 p-4 rounded-xl bg-white/50 hover:bg-white/80 transition-all hover:shadow-lg hover:-translate-y-1 border border-[#FFD6F4]">
                <div class="w-12 h-12 bg-gradient-to-br from-[#10B981] to-[#059669] rounded-xl flex items-center justify-center"><span class="text-white text-xl">💰</span></div>
                <div><h4 class="font-title text-[#4A00B9]">Meu Extrato</h4><p class="text-xs text-gray-500">Ver comissões</p></div>
            </a>

            <a href="{{ route('profissional.servicos.editar') }}" class="group flex items-center gap-4 p-4 rounded-xl bg-white/50 hover:bg-white/80 transition-all hover:shadow-lg hover:-translate-y-1 border border-[#FFD6F4]">
                <div class="w-12 h-12 bg-gradient-to-br from-[#3B82F6] to-[#2563EB] rounded-xl flex items-center justify-center"><span class="text-white text-xl">⚙️</span></div>
                <div><h4 class="font-title text-[#4A00B9]">Configurações</h4><p class="text-xs text-gray-500">Ajustar serviços</p></div>
            </a>
        </div>
    </div>
</div>