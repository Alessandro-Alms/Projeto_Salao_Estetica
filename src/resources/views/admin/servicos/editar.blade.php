<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Editar Serviço: ') }} {{ $servico->nome }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                {{-- Note o route 'atualizar' e o método @method('PUT') --}}
                <form method="POST" action="{{ route('admin.servicos.atualizar', $servico->id_servico) }}">
                    @csrf
                    @method('PUT')

                    <div>
                        <x-input-label for="nome" :value="__('Nome do Serviço')" />
                        @if(auth()->user()->cargo === 'gerente')
                            <x-text-input id="nome" name="nome" type="text" class="mt-1 block w-full" 
                                          :value="old('nome', $servico->nome)" required />
                        @else
                            <x-text-input :value="$servico->nome" class="mt-1 block w-full bg-gray-100" readonly />
                            <input type="hidden" name="nome" value="{{ $servico->nome }}">
                        @endif
                        <x-input-error :messages="$errors->get('nome')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input-label for="preco" :value="__('Preço (R$)')" />
                        @if(auth()->user()->cargo === 'gerente')
                            <x-text-input id="preco" name="preco" type="number" step="0.01" class="mt-1 block w-full" 
                                          :value="old('preco', $servico->preco)" required />
                        @else
                            <x-text-input value="R$ {{ number_format($servico->preco, 2, ',', '.') }}" 
                                          class="mt-1 block w-full bg-gray-100" readonly />
                            <input type="hidden" name="preco" value="{{ $servico->preco }}">
                        @endif
                        <x-input-error :messages="$errors->get('preco')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input-label for="duracao" :value="__('Duração (minutos)')" />
                        @if(auth()->user()->cargo === 'gerente')
                        <x-text-input id="duracao" name="duracao" type="number" class="mt-1 block w-full" :value="old('duracao', $servico->duracao)" placeholder="Ex: 60" required />
                        @endif
                    </div>

                    <div class="flex items-center justify-end mt-6">
                        <a href="{{ route('admin.servicos.index') }}" class="mr-4 text-sm text-gray-600 hover:underline">
                            {{ __('Voltar') }}
                        </a>
                        
                        @if(auth()->user()->cargo === 'gerente')
                            <x-primary-button>
                                {{ __('Atualizar Serviço') }}
                            </x-primary-button>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>