<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex items-center gap-2">
                <span class="text-[#7B19E5] text-xl">✧</span>
                <h2 class="font-title text-xl text-[#1A002B]">
                    {{ __('Gerenciamento de Produtos') }}
                </h2>
            </div>
            <a href="{{ route('admin.produtos.criar') }}" class="bg-gradient-to-r from-[#7B19E5] to-[#FF2EB6] text-white px-5 py-2 text-sm rounded-full font-medium btn-primary shadow-md hover:shadow-lg transition-all">
                + Novo Produto
            </a>
        </div>
    </x-slot>

    <div class="py-12 relative">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Filtro -->
            <div class="glass-card rounded-2xl shadow-xl overflow-hidden mb-6">
                <div class="p-4 bg-white/70 backdrop-blur-sm border border-white/40">
                    <form action="{{ route('admin.produtos.index') }}" method="GET" class="flex flex-col md:flex-row gap-4">
                        <div class="flex-1">
                            <input type="text" name="search" placeholder="Buscar produto..." value="{{ request('search') }}"
                                class="w-full px-4 py-2.5 bg-white/50 border border-[#FFD6F4] rounded-lg focus:outline-none focus:border-[#7B19E5] focus:ring-2 focus:ring-[#7B19E5]/20 transition-all" />
                        </div>
                        <div class="w-full md:w-48">
                            <select name="tipo" 
                                class="w-full px-4 py-2.5 bg-white/50 border border-[#FFD6F4] rounded-lg focus:outline-none focus:border-[#7B19E5] focus:ring-2 focus:ring-[#7B19E5]/20 transition-all">
                                <option value="">Todos os Tipos</option>
                                <option value="acessorios" {{ request('tipo') == 'acessorios' ? 'selected' : '' }}>Acessórios</option>
                                <option value="kits" {{ request('tipo') == 'kits' ? 'selected' : '' }}>Kits</option>
                                <option value="cosmeticos" {{ request('tipo') == 'cosmeticos' ? 'selected' : '' }}>Cosméticos</option>
                                <option value="cabelo" {{ request('tipo') == 'cabelo' ? 'selected' : '' }}>Cabelo</option>
                            </select>
                        </div>
                        <button type="submit" class="bg-gradient-to-r from-[#7B19E5] to-[#FF2EB6] text-white px-6 py-2 text-sm rounded-full font-medium btn-primary shadow-md hover:shadow-lg transition-all">
                            Filtrar
                        </button>
                    </form>
                </div>
            </div>

            <!-- Tabela -->
            <div class="glass-card rounded-2xl shadow-xl overflow-hidden">
                <div class="p-0 bg-white/70 backdrop-blur-sm border border-white/40">
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead>
                                <tr class="border-b border-[#FFD6F4] bg-white/50">
                                    <th class="px-6 py-4 text-left text-xs font-medium text-[#4A00B9] uppercase tracking-wider">Nome</th>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-[#4A00B9] uppercase tracking-wider">Tipo</th>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-[#4A00B9] uppercase tracking-wider">Preço</th>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-[#4A00B9] uppercase tracking-wider">Estoque</th>
                                    <th class="px-6 py-4 text-right text-xs font-medium text-[#4A00B9] uppercase tracking-wider">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#FFD6F4]">
                                @forelse($produtos as $produto)
                                    <tr class="hover:bg-white/50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-[#1A002B] font-medium">
                                            {{ $produto->nome }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                {{ $produto->tipo == 'acessorios' ? 'bg-purple-100 text-purple-700 border border-purple-200' : 
                                                ($produto->tipo == 'kits' ? 'bg-pink-100 text-pink-700 border border-pink-200' : 
                                                ($produto->tipo == 'cosmeticos' ? 'bg-green-100 text-green-700 border border-green-200' : 
                                                'bg-blue-100 text-blue-700 border border-blue-200')) }}">
                                                {{ ucfirst($produto->tipo) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap font-medium text-[#7B19E5]">
                                            R$ {{ number_format($produto->valor_unitario, 2, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="{{ $produto->quantidade_estoque <= 5 ? 'text-red-600 font-bold' : 'text-gray-600' }}">
                                                {{ $produto->quantidade_estoque }} un.
                                            </span>
                                            @if($produto->quantidade_estoque <= 5)
                                                <span class="ml-2 text-[10px] bg-red-100 text-red-600 px-2 py-0.5 rounded-full">Baixo</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex justify-end gap-3">
                                                <a href="{{ route('admin.produtos.editar', $produto->id_produto) }}" class="text-[#7B19E5] hover:text-[#FF2EB6] transition-colors">
                                                    Editar
                                                </a>
                                                <form action="{{ route('admin.produtos.deletar', $produto->id_produto) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir este produto?');" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-[#FF2EB6] hover:text-red-500 transition-colors">
                                                        Excluir
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                            ✧ Nenhum produto encontrado.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    @if($produtos->hasPages())
                        <div class="px-6 py-4 border-t border-[#FFD6F4] bg-white/30">
                            {{ $produtos->links() }}
                        </div>
                    @endif
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