<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex items-center gap-2">
                <span class="text-[#7B19E5] text-xl">✧</span>
                <h2 class="font-title text-xl text-[#1A002B]">
                    {{ __('Gerenciar Usuários') }}
                </h2>
            </div>
            <a href="{{ route('admin.usuarios.criar') }}" class="bg-gradient-to-r from-[#7B19E5] to-[#FF2EB6] text-white px-5 py-2 text-sm rounded-full font-medium btn-primary shadow-md hover:shadow-lg transition-all">
                + Novo Usuário
            </a>
        </div>
    </x-slot>

    <div class="py-12 relative">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if (session('status'))
                <div class="mb-4 text-sm text-green-600 bg-green-50/80 backdrop-blur-sm p-4 rounded-xl border border-green-200 shadow-sm">
                    ✧ {{ session('status') }}
                </div>
            @endif

            <!-- Filtro de Busca -->
            <div class="glass-card rounded-2xl shadow-xl overflow-hidden mb-6">
                <div class="p-4 bg-white/70 backdrop-blur-sm border border-white/40">
                    <form action="{{ route('admin.usuarios.index') }}" method="GET" class="flex flex-col md:flex-row gap-4">
                        <div class="flex-1">
                            <input type="text" name="search" placeholder="Buscar por nome, e-mail ou CPF..." value="{{ request('search') }}"
                                class="w-full px-4 py-2.5 bg-white/50 border border-[#FFD6F4] rounded-lg focus:outline-none focus:border-[#7B19E5] focus:ring-2 focus:ring-[#7B19E5]/20 transition-all" />
                        </div>
                        <div class="w-full md:w-48">
                            <select name="cargo" 
                                class="w-full px-4 py-2.5 bg-white/50 border border-[#FFD6F4] rounded-lg focus:outline-none focus:border-[#7B19E5] focus:ring-2 focus:ring-[#7B19E5]/20 transition-all">
                                <option value="">Todos os Cargos</option>
                                <option value="cliente" {{ request('cargo') == 'cliente' ? 'selected' : '' }}>Cliente</option>
                                <option value="profissional" {{ request('cargo') == 'profissional' ? 'selected' : '' }}>Profissional</option>
                                <option value="recepcionista" {{ request('cargo') == 'recepcionista' ? 'selected' : '' }}>Recepcionista</option>
                                <option value="gerente" {{ request('cargo') == 'gerente' ? 'selected' : '' }}>Gerente</option>
                            </select>
                        </div>
                        <div class="flex gap-2">
                            <button type="submit" class="bg-gradient-to-r from-[#7B19E5] to-[#FF2EB6] text-white px-6 py-2 text-sm rounded-full font-medium btn-primary shadow-md hover:shadow-lg transition-all">
                                Filtrar
                            </button>
                            @if(request('search') || request('cargo'))
                                <a href="{{ route('admin.usuarios.index') }}" class="px-4 py-2 text-sm text-gray-500 hover:text-[#7B19E5] transition-colors">
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
                                    <th class="px-6 py-4 text-left text-xs font-medium text-[#4A00B9] uppercase tracking-wider">Cargo</th>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-[#4A00B9] uppercase tracking-wider">Contato</th>
                                    <th class="px-6 py-4 text-right text-xs font-medium text-[#4A00B9] uppercase tracking-wider">Ações</th>
                                  </tr>
                            </thead>
                            <tbody class="divide-y divide-[#FFD6F4]">
                                @forelse ($usuarios as $usuario)
                                    <tr class="hover:bg-white/50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-[#1A002B]">{{ $usuario->name }}</div>
                                            <div class="text-xs text-gray-500 mt-1">
                                                CPF: {{ substr($usuario->cpf, 0, 3) . '.' . substr($usuario->cpf, 3, 3) . '.' . substr($usuario->cpf, 6, 3) . '-' . substr($usuario->cpf, 9, 2) }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                {{ $usuario->cargo == 'gerente' ? 'bg-gradient-to-r from-red-500/20 to-red-600/20 text-red-700 border border-red-200' : 
                                                ($usuario->cargo == 'recepcionista' ? 'bg-gradient-to-r from-blue-500/20 to-blue-600/20 text-blue-700 border border-blue-200' : 
                                                ($usuario->cargo == 'profissional' ? 'bg-gradient-to-r from-purple-500/20 to-purple-600/20 text-purple-700 border border-purple-200' : 
                                                'bg-gradient-to-r from-green-500/20 to-green-600/20 text-green-700 border border-green-200')) }}">
                                                {{ ucfirst($usuario->cargo) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-[#1A002B]">{{ $usuario->email }}</div>
                                            <div class="text-xs text-[#7B19E5] mt-1">
                                                {{ '(' . substr($usuario->telefone, 0, 2) . ') ' . substr($usuario->telefone, 2, 5) . '-' . substr($usuario->telefone, 7) }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex justify-end gap-3">
                                                <a href="{{ route('admin.usuarios.editar', $usuario->id) }}" class="text-[#7B19E5] hover:text-[#FF2EB6] transition-colors">
                                                    Editar
                                                </a>

                                                @if(auth()->user()->cargo === 'gerente' && $usuario->id !== auth()->user()->id)
                                                    <form action="{{ route('admin.usuarios.deletar', $usuario->id) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir este usuário?')" class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-[#FF2EB6] hover:text-red-500 transition-colors">
                                                            Excluir
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                                            ✧ Nenhum usuário encontrado.
                                            @if(request('search') || request('cargo'))
                                                <br><span class="text-xs">Tente outra busca ou <a href="{{ route('admin.usuarios.index') }}" class="text-[#7B19E5]">limpe o filtro</a></span>
                                            @else
                                                <br><span class="text-xs">Clique em "Novo Usuário" para começar</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($usuarios->hasPages())
                        <div class="mt-6 pt-4 border-t border-[#FFD6F4]">
                            {{ $usuarios->links() }}
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


select:focus {
    outline: none;
    box-shadow: 0 0 0 2px rgba(123, 25, 229, 0.3);
    border-color: #7B19E5;
}

input:focus, select:focus, textarea:focus {
    outline: none;
    box-shadow: 0 0 0 2px rgba(123, 25, 229, 0.3);
    border-color: #7B19E5;
}

</style>