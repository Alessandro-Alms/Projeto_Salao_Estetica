<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Gerenciar Usuários') }}
            </h2>
            <a href="{{ route('admin.usuarios.criar') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                + Novo Usuário
            </a>
        </div>
        <div class="mb-4 border-b border-gray-200">
            <ul class="flex flex-wrap -mb-px text-sm font-medium text-center">
                <li class="mr-2">
                    <a href="{{ route('admin.usuarios.index') }}" 
                    class="inline-block p-4 border-b-2 rounded-t-lg {{ !request('cargo') ? 'text-pink-600 border-pink-600 active' : 'border-transparent hover:text-gray-600 hover:border-gray-300' }}">
                        Todos os Usuários
                    </a>
                </li>
                <li class="mr-2">
                    <a href="{{ route('admin.usuarios.index', ['cargo' => 'cliente']) }}" 
                    class="inline-block p-4 border-b-2 rounded-t-lg {{ request('cargo') === 'cliente' ? 'text-pink-600 border-pink-600 active' : 'border-transparent hover:text-gray-600 hover:border-gray-300' }}">
                        Clientes
                    </a>
                </li>
                <li class="mr-2">
                    <a href="{{ route('admin.usuarios.index', ['cargo' => 'profissional']) }}" 
                    class="inline-block p-4 border-b-2 rounded-t-lg {{ request('cargo') === 'profissional' ? 'text-pink-600 border-pink-600 active' : 'border-transparent hover:text-gray-600 hover:border-gray-300' }}">
                        Profissionais
                    </a>
                </li>
                <li class="mr-2">
                    <a href="{{ route('admin.usuarios.index', ['cargo' => 'recepcionista']) }}" 
                    class="inline-block p-4 border-b-2 rounded-t-lg {{ request('cargo') === 'recepcionista' ? 'text-pink-600 border-pink-600 active' : 'border-transparent hover:text-gray-600 hover:border-gray-300' }}">
                        Recepcionistas
                    </a>
                </li>
            </ul>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if (session('status'))
                <div class="mb-4 font-medium text-sm text-green-600 bg-green-100 p-4 rounded-lg border border-green-200 shadow-sm">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                <div class="p-6 text-gray-900">
                    {{-- Barra de Busca e Filtros --}}
                    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 mb-6">
                        <form action="{{ route('admin.usuarios.index') }}" method="GET" class="flex flex-col md:flex-row gap-4">
                            {{-- Mantém o cargo fixo se vier de um link específico --}}
                            <input type="hidden" name="cargo" value="{{ request('cargo') }}">
                            
                            <div class="flex-1">
                                <x-text-input name="search" placeholder="Buscar por nome, e-mail ou CPF..." class="w-full" value="{{ request('search') }}" />
                            </div>
                            
                            <x-primary-button class="bg-amber-500 hover:bg-amber-600">
                                🔍 Buscar
                            </x-primary-button>

                            @if(request('search') || request('cargo'))
                                <a href="{{ route('admin.usuarios.index') }}" class="text-sm text-gray-500 flex items-center underline">Limpar Filtros</a>
                            @endif
                        </form>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nome</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cargo</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contato</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                {{-- Usando a variável $usuarios que você definiu no Controller --}}
                                @forelse ($usuarios as $usuario)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $usuario->name }}</div>
                                            <div class="text-sm text-gray-500">CPF: {{ substr($usuario->cpf, 0, 3) . '.' . substr($usuario->cpf, 3, 3) . '.' . substr($usuario->cpf, 6, 3) . '-' . substr($usuario->cpf, 9, 2) }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                {{ $usuario->cargo == 'gerente' ? 'bg-red-100 text-red-800' : 
                                                ($usuario->cargo == 'recepcionista' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800') }}">
                                                {{ ucfirst($usuario->cargo) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $usuario->email }}</div>
                                            <div class="text-sm text-gray-500">{{'('. substr($usuario->telefone, 0, 2) . ') ' . substr($usuario->telefone, 2, 5) . '-' . substr($usuario->telefone, 7)}}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex justify-end gap-3">
                                                <a href="{{ route('admin.usuarios.editar', $usuario->id) }}" class="text-indigo-600 hover:text-indigo-900 font-bold">
                                                    Editar
                                                </a>

                                            @if(auth()->user()->cargo === 'gerente')
                                                <form action="{{ route('admin.usuarios.deletar', $usuario->id) }}" method="POST" onsubmit="return confirm('Tem certeza?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600">Excluir</button>
                                                </form>
                                            @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 text-center text-gray-500 italic">
                                            Nenhum usuário encontrado.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $usuarios->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>