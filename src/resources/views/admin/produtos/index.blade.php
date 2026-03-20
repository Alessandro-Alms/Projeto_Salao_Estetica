<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Gerenciamento de Produtos') }}
            </h2>
            <a href="{{ route('admin.produtos.criar') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                + Novo Produto
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="bg-white p-6 rounded-t-lg border border-gray-200 shadow-sm mb-4">
                <form action="{{ route('admin.produtos.index') }}" method="GET" class="flex flex-col md:flex-row gap-4">
                    <div class="flex-1">
                        <x-text-input name="search" placeholder="Buscar produto..." class="w-full" value="{{ request('search') }}" />
                    </div>
                    <div class="w-full md:w-48">
                        <select name="tipo" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full">
                            <option value="">Todos os Tipos</option>
                            <option value="acessorios" {{ request('tipo') == 'acessorios' ? 'selected' : '' }}>Acessórios</option>
                            <option value="kits" {{ request('tipo') == 'kits' ? 'selected' : '' }}>Kits</option>
                            <option value="cosmeticos" {{ request('tipo') == 'cosmeticos' ? 'selected' : '' }}>Cosméticos</option>
                            <option value="cabelo" {{ request('tipo') == 'cabelo' ? 'selected' : '' }}>Cabelo</option>
                        </select>
                    </div>
                    <x-primary-button type="submit">Filtrar</x-primary-button>
                </form>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nome</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Preço</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estoque</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($produtos as $produto)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $produto->nome }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        {{ ucfirst($produto->tipo) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap font-medium">
                                    R$ {{ number_format($produto->valor_unitario, 2, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="{{ $produto->quantidade_estoque <= 5 ? 'text-red-600 font-bold' : 'text-gray-900' }}">
                                        {{ $produto->quantidade_estoque }} un.
                                    </span>
                                    @if($produto->quantidade_estoque <= 5)
                                        <span class="ml-2 text-[10px] bg-red-100 text-red-600 px-1 rounded uppercase">Baixo</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="{{ route('admin.produtos.editar', $produto->id_produto) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Editar</a>
                                    <form action="{{ route('admin.produtos.deletar', $produto->id_produto) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir este produto?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">Excluir</button>
                                </form>
                            </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-gray-500">Nenhum produto encontrado.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="px-6 py-4 border-t">
                    {{ $produtos->links() }} {{-- Note: aqui use $produtos->links() no seu código --}}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>