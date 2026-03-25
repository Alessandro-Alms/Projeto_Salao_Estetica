<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex items-center gap-2">
                <span class="text-[#7B19E5] text-xl">✧</span>
                <h2 class="font-title text-xl text-[#1A002B]">
                    {{ __('Gerenciar Serviços') }}
                </h2>
            </div>
            @if(auth()->user()->cargo === 'gerente')
                <a href="{{ route('admin.servicos.criar') }}" class="bg-gradient-to-r from-[#7B19E5] to-[#FF2EB6] text-white px-5 py-2 text-sm rounded-full font-medium btn-primary shadow-md hover:shadow-lg transition-all">
                    + Novo Serviço
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-12 relative">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Filtro de Busca -->
            <div class="glass-card rounded-2xl shadow-xl overflow-hidden mb-6">
                <div class="p-4 bg-white/70 backdrop-blur-sm border border-white/40">
                    <form action="{{ route('admin.servicos.index') }}" method="GET" class="flex flex-col md:flex-row gap-4">
                        <div class="flex-1">
                            <input type="text" name="search" placeholder="Buscar serviço..." value="{{ request('search') }}"
                                class="w-full px-4 py-2.5 bg-white/50 border border-[#FFD6F4] rounded-lg focus:outline-none focus:border-[#7B19E5] focus:ring-2 focus:ring-[#7B19E5]/20 transition-all" />
                        </div>
                        <div class="flex gap-2">
                            <button type="submit" class="bg-gradient-to-r from-[#7B19E5] to-[#FF2EB6] text-white px-6 py-2 text-sm rounded-full font-medium btn-primary shadow-md hover:shadow-lg transition-all">
                                Filtrar
                            </button>
                            @if(request('search'))
                                <a href="{{ route('admin.servicos.index') }}" class="px-4 py-2 text-sm text-gray-500 hover:text-[#7B19E5] transition-colors">
                                    Limpar
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            <div class="glass-card rounded-2xl shadow-xl overflow-hidden">
                <div class="p-6 bg-white/70 backdrop-blur-sm border border-white/40">
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead>
                                <tr class="border-b border-[#FFD6F4]">
                                    <th class="px-6 py-4 text-left text-xs font-medium text-[#4A00B9] uppercase tracking-wider">Nome</th>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-[#4A00B9] uppercase tracking-wider">Preço</th>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-[#4A00B9] uppercase tracking-wider">Duração</th>
                                    <th class="px-6 py-4 text-right text-xs font-medium text-[#4A00B9] uppercase tracking-wider">Ações</th>
                                 </tr>
                            </thead>
                            <tbody class="divide-y divide-[#FFD6F4]">
                                @forelse($servicos as $servico)
                                    <tr class="hover:bg-white/50 transition-colors">
                                        <td class="px-6 py-4 text-sm text-[#1A002B] font-medium">{{ $servico->nome }}</td>
                                        <td class="px-6 py-4 text-sm text-[#7B19E5]">R$ {{ number_format($servico->preco, 2, ',', '.') }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-500">{{ $servico->duracao }} min</td>
                                        <td class="px-6 py-4 text-right text-sm font-medium space-x-3">
                                            <a href="{{ route('admin.servicos.editar', $servico->id_servico) }}" class="text-[#7B19E5] hover:text-[#FF2EB6] transition-colors">
                                                Editar
                                            </a>
                                            
                                            @if(auth()->user()->cargo === 'gerente')
                                                <form action="{{ route('admin.servicos.deletar', $servico->id_servico) }}" method="POST" class="inline">
                                                    @csrf 
                                                    @method('DELETE')
                                                    <button type="submit" class="text-[#FF2EB6] hover:text-red-500 transition-colors" onclick="return confirm('Excluir este serviço?')">
                                                        Excluir
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                                            ✧ Nenhum serviço encontrado.
                                            @if(request('search'))
                                                <br><span class="text-xs">Tente outra busca ou <a href="{{ route('admin.servicos.index') }}" class="text-[#7B19E5]">limpe o filtro</a></span>
                                            @else
                                                <br><span class="text-xs">Clique em "Novo Serviço" para começar</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
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
    
    .glass-card {
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        box-shadow: 0 8px 32px rgba(123, 25, 229, 0.1);
    }
    
    .btn-primary {
        position: relative;
        overflow: hidden;
        transition: all 0.3s ease;
        z-index: 1;
    }
    
    .btn-primary::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.3);
        transform: translate(-50%, -50%);
        transition: width 0.6s ease, height 0.6s ease;
        z-index: -1;
    }
    
    .btn-primary:hover::before {
        width: 300px;
        height: 300px;
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
    }
</style>