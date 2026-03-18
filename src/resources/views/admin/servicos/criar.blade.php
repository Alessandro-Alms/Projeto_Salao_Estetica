<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Cadastrar Novo Serviço') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('admin.servicos.salvar') }}">
                    @csrf

                    <div>
                        <x-input-label for="nome" :value="__('Nome do Serviço')" />
                        <x-text-input id="nome" name="nome" type="text" class="mt-1 block w-full" :value="old('nome')" required />
                        <x-input-error :messages="$errors->get('nome')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input-label for="preco" :value="__('Preço (R$)')" />
                        <x-text-input id="preco" name="preco" type="number" step="0.01" class="mt-1 block w-full" :value="old('preco')" required />
                        <x-input-error :messages="$errors->get('preco')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input-label for="duracao" :value="__('Duração (minutos)')" />
                        <x-text-input id="duracao" name="duracao" type="number" class="mt-1 block w-full" :value="old('duracao')" placeholder="Ex: 60" required />
                        <x-input-error :messages="$errors->get('duracao')" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-end mt-6">
                        <a href="{{ route('admin.servicos.index') }}" class="mr-4 text-sm text-gray-600 hover:underline">Cancelar</a>
                        <x-primary-button>
                            {{ __('Salvar Serviço') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>