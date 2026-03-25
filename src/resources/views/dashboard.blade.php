<x-app-layout>
    <x-slot name="header">
        <div class="hidden"></div>
    </x-slot>

    <div class="py-12 relative">
        <!-- Fundo igual ao site -->
        <div class="fixed inset-0 -z-10 overflow-hidden">
            <div class="absolute top-0 -left-20 w-[600px] h-[600px] bg-[#7B19E5]/20 rounded-full blur-3xl"></div>
            <div class="absolute bottom-0 -right-20 w-[700px] h-[700px] bg-[#FF2EB6]/20 rounded-full blur-3xl"></div>
            <div class="absolute top-1/3 left-1/3 w-[400px] h-[400px] bg-[#A955D3]/15 rounded-full blur-3xl"></div>
            <div class="absolute inset-0 bg-gradient-to-br from-[#7B19E5]/5 via-white/30 to-[#FF2EB6]/5"></div>
        </div>

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Card de boas-vindas -->
            <div class="glass-card rounded-2xl shadow-xl overflow-hidden mb-8">
                <div class="p-6 bg-white/70 backdrop-blur-sm border border-white/40">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-[#7B19E5] to-[#FF2EB6] rounded-xl flex items-center justify-center shadow-lg">
                            <span class="text-2xl text-white">✧</span>
                        </div>
                        <div>
                            <h3 class="text-xl font-title text-[#4A00B9]">Olá, {{ auth()->user()->name }}!</h3>
                            <p class="text-sm text-[#7B19E5] font-body">Bem-vinda ao seu painel</p>
                        </div>
                    </div>
                    
                    <p class="text-[#1A002B] font-body leading-relaxed">
                        {{ __("Você está logada!") }}
                    </p>

                    <!-- Cargo do usuário -->
                    <div class="mt-3 inline-flex items-center gap-2 bg-[#FF2EB6]/10 px-3 py-1 rounded-full">
                        <span class="text-xs font-medium text-[#FF2EB6]">✦ {{ ucfirst(auth()->user()->cargo) }}</span>
                    </div>
                </div>
            </div>

            {{-- Atalhos para o Recepcionista --}}
            @if(auth()->user()->cargo === 'recepcionista')
                <div class="glass-card rounded-2xl shadow-xl overflow-hidden">
                    <div class="p-6 bg-white/70 backdrop-blur-sm border border-white/40">
                        <div class="flex items-center gap-2 mb-5">
                            <span class="text-[#7B19E5] text-xl">✧</span>
                            <h3 class="text-lg font-title text-[#4A00B9]">Atalhos rápidos</h3>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <a href="{{ route('admin.usuarios.criar') }}" 
                               class="group flex items-center gap-4 p-4 rounded-xl bg-white/50 hover:bg-white/80 transition-all hover:shadow-lg hover:-translate-y-1 border border-[#FFD6F4]">
                                <div class="w-12 h-12 bg-gradient-to-br from-[#7B19E5] to-[#A855F7] rounded-xl flex items-center justify-center shadow-md group-hover:scale-110 transition-transform">
                                    <span class="text-white text-xl">✧</span>
                                </div>
                                <div>
                                    <h4 class="font-title text-[#4A00B9] group-hover:text-[#7B19E5] transition-colors">Cadastrar Cliente</h4>
                                    <p class="text-xs text-gray-500">Adicione um novo cliente ao sistema</p>
                                </div>
                            </a>

                            <a href="{{ route('admin.usuarios.index') }}" 
                               class="group flex items-center gap-4 p-4 rounded-xl bg-white/50 hover:bg-white/80 transition-all hover:shadow-lg hover:-translate-y-1 border border-[#FFD6F4]">
                                <div class="w-12 h-12 bg-gradient-to-br from-[#FF2EB6] to-[#FF69B4] rounded-xl flex items-center justify-center shadow-md group-hover:scale-110 transition-transform">
                                    <span class="text-white text-xl">✦</span>
                                </div>
                                <div>
                                    <h4 class="font-title text-[#4A00B9] group-hover:text-[#FF2EB6] transition-colors">Lista de Clientes</h4>
                                    <p class="text-xs text-gray-500">Visualize e gerencie seus clientes</p>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Se for gerente --}}
            @if(auth()->user()->cargo === 'gerente')
                <div class="glass-card rounded-2xl shadow-xl overflow-hidden">
                    <div class="p-6 bg-white/70 backdrop-blur-sm border border-white/40">
                        <div class="flex items-center gap-2 mb-5">
                            <span class="text-[#7B19E5] text-xl">✦</span>
                            <h3 class="text-lg font-title text-[#4A00B9]">Painel do Gerente</h3>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <a href="{{ route('admin.usuarios.index') }}" 
                               class="group flex items-center gap-4 p-4 rounded-xl bg-white/50 hover:bg-white/80 transition-all hover:shadow-lg hover:-translate-y-1 border border-[#FFD6F4]">
                                <div class="w-12 h-12 bg-gradient-to-br from-[#7B19E5] to-[#A855F7] rounded-xl flex items-center justify-center">
                                    <span class="text-white text-xl">✦</span>
                                </div>
                                <div>
                                    <h4 class="font-title text-[#4A00B9]">Gerenciar Usuários</h4>
                                    <p class="text-xs text-gray-500">Todos os colaboradores do sistema</p>
                                </div>
                            </a>

                            <a href="{{ route('admin.servicos.index') }}" 
                               class="group flex items-center gap-4 p-4 rounded-xl bg-white/50 hover:bg-white/80 transition-all hover:shadow-lg hover:-translate-y-1 border border-[#FFD6F4]">
                                <div class="w-12 h-12 bg-gradient-to-br from-[#FF2EB6] to-[#FF69B4] rounded-xl flex items-center justify-center">
                                    <span class="text-white text-xl">✧</span>
                                </div>
                                <div>
                                    <h4 class="font-title text-[#4A00B9]">Gerenciar Serviços</h4>
                                    <p class="text-xs text-gray-500">Edite os serviços oferecidos</p>
                                </div>
                            </a>

                            <a href="{{ route('admin.produtos.index') }}" 
                               class="group flex items-center gap-4 p-4 rounded-xl bg-white/50 hover:bg-white/80 transition-all hover:shadow-lg hover:-translate-y-1 border border-[#FFD6F4]">
                                <div class="w-12 h-12 bg-gradient-to-br from-[#7B19E5] to-[#FF2EB6] rounded-xl flex items-center justify-center">
                                    <span class="text-white text-xl">✦</span>
                                </div>
                                <div>
                                    <h4 class="font-title text-[#4A00B9]">Gerenciar Produtos</h4>
                                    <p class="text-xs text-gray-500">Controle de estoque e vendas</p>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Se for profissional --}}
            @if(auth()->user()->cargo === 'profissional')
                <div class="glass-card rounded-2xl shadow-xl overflow-hidden">
                    <div class="p-6 bg-white/70 backdrop-blur-sm border border-white/40">
                        <div class="flex items-center gap-2 mb-5">
                            <span class="text-[#7B19E5] text-xl">✧</span>
                            <h3 class="text-lg font-title text-[#4A00B9]">Meus Atendimentos</h3>
                        </div>
                        
                        <div class="text-center py-8">
                            <div class="w-16 h-16 bg-gradient-to-br from-[#7B19E5] to-[#FF2EB6] rounded-full flex items-center justify-center mx-auto mb-4">
                                <span class="text-2xl text-white">✦</span>
                            </div>
                            <p class="text-[#1A002B]">Em breve você poderá ver seus agendamentos aqui!</p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Se for cliente comum --}}
            @if(auth()->user()->cargo === 'cliente')
                <div class="glass-card rounded-2xl shadow-xl overflow-hidden">
                    <div class="p-6 bg-white/70 backdrop-blur-sm border border-white/40">
                        <div class="flex items-center gap-2 mb-5">
                            <span class="text-[#7B19E5] text-xl">✧</span>
                            <h3 class="text-lg font-title text-[#4A00B9]">Meus agendamentos</h3>
                        </div>
                        
                        <div class="text-center py-8">
                            <div class="w-16 h-16 bg-gradient-to-br from-[#7B19E5] to-[#FF2EB6] rounded-full flex items-center justify-center mx-auto mb-4">
                                <span class="text-2xl text-white">✧</span>
                            </div>
                            <p class="text-[#1A002B]">Em breve você poderá ver seus agendamentos aqui!</p>
                            <a href="/" class="inline-block mt-4 text-sm text-[#7B19E5] hover:text-[#FF2EB6] transition-colors">
                                Voltar para o site →
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700;800&family=Playfair+Display:wght@700&family=Space+Grotesk:wght@300;400;500;600;700&display=swap');
    
::-webkit-scrollbar { width: 8px; background: #f8f0ff; }
            ::-webkit-scrollbar-thumb { background: linear-gradient(135deg, #7B19E5, #FF2EB6); border-radius: 10px; }

    .font-title {
        font-family: 'Playfair Display', serif;
        font-weight: 700;
        letter-spacing: -0.02em;
    }
    
    .font-body {
        font-family: 'Space Grotesk', sans-serif;
    }
    
    .glass-card {
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        box-shadow: 0 8px 32px rgba(123, 25, 229, 0.1);
    }
</style>