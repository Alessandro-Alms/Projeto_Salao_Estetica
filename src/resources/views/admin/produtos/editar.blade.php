<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Editar Produto: ') }} {{ $produto->nome }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                
                <form method="POST" action="{{ route('admin.produtos.atualizar', $produto->id_produto) }}">
                    @csrf
                    @method('PUT') {{-- Essencial para o Laravel entender que é uma edição --}}

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        {{-- Nome --}}
                        <div class="md:col-span-2">
                            <x-input-label for="nome" :value="__('Nome do Produto')" />
                            <x-text-input id="nome" class="block mt-1 w-full" type="text" name="nome" :value="old('nome', $produto->nome)" required />
                            <x-input-error :messages="$errors->get('nome')" class="mt-2" />
                        </div>

                        {{-- Categoria --}}
                        <div>
                            <x-input-label for="tipo" :value="__('Categoria')" />
                            <select name="tipo" id="tipo" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full">
                                <option value="acessorios" {{ old('tipo', $produto->tipo) == 'acessorios' ? 'selected' : '' }}>Acessórios</option>
                                <option value="kits" {{ old('tipo', $produto->tipo) == 'kits' ? 'selected' : '' }}>Kits</option>
                                <option value="cosmeticos" {{ old('tipo', $produto->tipo) == 'cosmeticos' ? 'selected' : '' }}>Cosméticos</option>
                                <option value="cabelo" {{ old('tipo', $produto->tipo) == 'cabelo' ? 'selected' : '' }}>Cabelo</option>
                            </select>
                            <x-input-error :messages="$errors->get('tipo')" class="mt-2" />
                        </div>

                        {{-- Preço --}}
                        <div>
                            <x-input-label for="valor_unitario" :value="__('Preço de Venda (R$)')" />
                            <x-text-input id="valor_unitario" class="block mt-1 w-full" type="number" step="0.01" name="valor_unitario" :value="old('valor_unitario', $produto->valor_unitario)" required />
                            <x-input-error :messages="$errors->get('valor_unitario')" class="mt-2" />
                        </div>

                        {{-- Estoque --}}
                        <div>
                            <x-input-label for="quantidade_estoque" :value="__('Quantidade em Estoque')" />
                            <x-text-input id="quantidade_estoque" class="block mt-1 w-full" type="number" name="quantidade_estoque" :value="old('quantidade_estoque', $produto->quantidade_estoque)" required />
                            <x-input-error :messages="$errors->get('quantidade_estoque')" class="mt-2" />
                        </div>

                        {{-- Descrição --}}
                        <div class="md:col-span-2">
                            <x-input-label for="descricao" :value="__('Descrição (Opcional)')" />
                            <textarea id="descricao" name="descricao" rows="3" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full">{{ old('descricao', $produto->descricao) }}</textarea>
                            <x-input-error :messages="$errors->get('descricao')" class="mt-2" />
                        </div>
                    </div>

                    <div class="flex items-center justify-end mt-8 pt-4 border-t gap-4">
                        <a href="{{ route('admin.produtos.index') }}" class="text-sm text-gray-600 underline hover:text-gray-900">
                            {{ __('Cancelar') }}
                        </a>
                        <x-primary-button>
                            {{ __('Atualizar Produto') }}
                        </x-primary-button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>