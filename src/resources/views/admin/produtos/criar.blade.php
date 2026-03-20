<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Novo Produto</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                <form method="POST" action="{{ route('admin.produtos.salvar') }}">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <x-input-label for="nome" :value="__('Nome do Produto')" />
                            <x-text-input id="nome" class="block mt-1 w-full" type="text" name="nome" required autofocus />
                        </div>

                        <div>
                            <x-input-label for="tipo" :value="__('Categoria')" />
                            <select name="tipo" id="tipo" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full">
                                <option value="acessorios">Acessórios</option>
                                <option value="kits">Kits</option>
                                <option value="cosmeticos">Cosméticos</option>
                                <option value="cabelo">Cabelo</option>
                            </select>
                        </div>

                        <div>
                            <x-input-label for="valor_unitario" :value="__('Preço de Venda (R$)')" />
                            <x-text-input id="valor_unitario" class="block mt-1 w-full" type="number" step="0.01" name="valor_unitario" required />
                        </div>

                        <div>
                            <x-input-label for="quantidade_estoque" :value="__('Estoque Inicial')" />
                            <x-text-input id="quantidade_estoque" class="block mt-1 w-full" type="number" name="quantidade_estoque" required />
                        </div>

                        <div class="md:col-span-2">
                            <x-input-label for="descricao" :value="__('Descrição (Opcional)')" />
                            <textarea id="descricao" name="descricao" rows="3" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full"></textarea>
                        </div>
                    </div>

                    <div class="flex items-center justify-end mt-6 pt-4 border-t">
                        <x-primary-button>{{ __('Salvar Produto') }}</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>