<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Editar Usuário: ') }} {{ $usuario->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                <div class="p-6 text-gray-900">
                    
                    <form method="POST" action="{{ route('admin.usuarios.atualizar', ['user' => $usuario->id]) }}">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            
                            <div>
                                <x-input-label for="name" :value="__('Nome Completo')" />
                                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $usuario->name)" required autofocus />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="email" :value="__('E-mail')" />
                                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $usuario->email)" required />
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="cpf" :value="__('CPF')" />
                                <x-text-input id="cpf" class="block mt-1 w-full" type="text" name="cpf" :value="old('cpf', $usuario->cpf)" required />
                                <x-input-error :messages="$errors->get('cpf')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="telefone" :value="__('Telefone')" />
                                <x-text-input id="telefone" class="block mt-1 w-full" type="text" name="telefone" :value="old('telefone', $usuario->telefone)" required />
                                <x-input-error :messages="$errors->get('telefone')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="cargo" :value="__('Cargo / Função')" />
                                @if(auth()->user()->cargo === 'gerente')
                                <select name="cargo" id="cargo" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full text-gray-700">
                                    <option value="cliente" {{ old('cargo', $usuario->cargo) == 'cliente' ? 'selected' : '' }}>Cliente</option>
                                    <option value="profissional" {{ old('cargo', $usuario->cargo) == 'profissional' ? 'selected' : '' }}>Profissional</option>
                                    <option value="recepcionista" {{ old('cargo', $usuario->cargo) == 'recepcionista' ? 'selected' : '' }}>Recepcionista</option>
                                    <option value="gerente" {{ old('cargo', $usuario->cargo) == 'gerente' ? 'selected' : '' }}>Gerente (Admin)</option>
                                </select>
                                @else
                                <input type="text" value="Cliente" class="block mt-1 w-full bg-gray-100 border-gray-300 rounded-md text-gray-500" readonly>
                                <input type="hidden" name="cargo" value="cliente">
                                @endif  
                                <x-input-error :messages="$errors->get('cargo')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="password" :value="__('Nova Senha')" />
                                <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" placeholder="Deixe em branco para não alterar" />
                                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-8 border-t pt-4">
                            <a href="{{ route('admin.usuarios.index') }}" class="mr-4 text-sm text-gray-600 underline hover:text-gray-900 transition-colors">
                                {{ __('Cancelar e Voltar') }}
                            </a>
                            <x-primary-button>
                                {{ __('SALVAR ALTERAÇÕES') }}
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
        <script src="https://unpkg.com/imask"></script>
    <script>
        var cpfMask = IMask(document.getElementById('cpf'), {
            mask: '000.000.000-00'
        });
        var phoneMask = IMask(document.getElementById('telefone'), {
            mask: [,
                { mask: '(00) 00000-0000' }
            ]
        });
        // No seu script da máscara, adicione isso ao evento de submit do formulário:
        document.querySelector('form').addEventListener('submit', function() {
            // Remove a máscara antes de enviar para o servidor não ver os pontos
            const cpfInput = document.getElementById('cpf');
            cpfInput.value = cpfInput.value.replace(/\D/g, ''); // Deixa só números

            const telInput = document.getElementById('telefone');
            telInput.value = telInput.value.replace(/\D/g, ''); // Deixa só números
        });
    </script>
</x-app-layout>