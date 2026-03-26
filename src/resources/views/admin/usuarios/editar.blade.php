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
                    
                    <form method="POST" action="{{ route('admin.usuarios.atualizar', $usuario) }}">
                        @csrf
                        @method('PUT')
                            @if($usuario->cargo === 'recepcionista' || $usuario->cargo === 'gerente')
                            <div class="mt-6 p-4 bg-amber-50 rounded-lg border border-amber-200 text-amber-700 text-sm">
                                <strong>Aviso:</strong> Este usuário possui acesso administrativo ao sistema. Certifique-se de manter o e-mail atualizado.
                            </div>
                            @endif
                            @if($usuario->cargo === 'cliente')
                            <div class="mt-6 p-6 bg-blue-50 rounded-xl border border-blue-200">
                                <h3 class="text-lg font-bold text-blue-800 mb-4 flex items-center">
                                    <span class="mr-2">👤</span> Perfil do Cliente
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <x-input-label for="d_nasc" :value="__('Data de Nascimento')" />
                                        <x-text-input id="d_nasc" name="d_nasc" type="date" class="block mt-1 w-full" :value="old('d_nasc', $usuario->d_nasc)" required />
                                    </div>
                                    <div>
                                        <x-input-label for="endereco" :value="__('Endereço')" />
                                        <x-text-input id="endereco" name="endereco" class="block mt-1 w-full" :value="old('endereco', $usuario->endereco)" />
                                    </div>
                                </div>
                            </div>
                            @endif
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
                        @if($usuario->cargo === 'profissional')
                        <div class="mt-8 p-6 bg-pink-50 rounded-xl border border-pink-200">
                            {{-- Bloco de Horários (Preparação para Demanda 4) --}}
                            <div class="mt-6 p-6 bg-gray-50 rounded-xl border border-gray-200">
                                <h3 class="text-lg font-bold text-gray-700 mb-4 flex items-center">
                                    <span class="mr-2">📅</span> Horário de Atendimento
                                </h3>
                                <p class="text-sm text-gray-500">O horário padrão é das 08:00 às 18:00. <br>Exceções e turnos específicos serão configurados na próxima etapa.</p>
                            </div>
                            <h3 class="text-lg font-bold text-pink-800 mb-4 flex items-center">
                                <span class="mr-2">✂️</span> Serviços e Comissões do Profissional
                            </h3>
                            
                            <div class="space-y-3">
                                @foreach($servicos as $servico)
                                    @php 
                                        // Busca se já existe o relacionamento no banco
                                        $vinculo = $usuario->servicos->find($servico->id_servico); 
                                    @endphp
                                    <div class="flex items-center bg-white p-4 rounded-lg shadow-sm border border-gray-100">
                                        <input type="checkbox" name="servicos[{{ $servico->id_servico }}][ativo]" {{ $vinculo ? 'checked' : '' }} class="rounded text-pink-600 focus:ring-pink-500 h-5 w-5">
                                        
                                        <div class="ml-4 flex-1">
                                            <span class="font-bold text-gray-700">{{ $servico->nome }}</span>
                                            <span class="text-xs text-gray-400 block">Duração padrão: {{ $servico->duracao }} min</span>
                                        </div>

                                        <div class="flex gap-4">
                                            <div class="w-28">
                                                <label class="block text-[10px] uppercase font-bold text-gray-400">Comissão %</label>
                                                <input type="number" step="0.01" 
                                                    name="servicos[{{ $servico->id_servico }}][comissao]" 
                                                    {{-- ACESSO AO PIVOT AQUI --}}
                                                    value="{{ $vinculo ? $vinculo->pivot->comissao_percentual : '50.00' }}" 
                                                    class="w-full border-gray-200 rounded text-sm focus:border-pink-500">
                                            </div>
                                            <div class="w-28">
                                                <label class="block text-[10px] uppercase font-bold text-gray-400">Tempo (min)</label>
                                                <input type="number" 
                                                    name="servicos[{{ $servico->id_servico }}][duracao]" 
                                                    {{-- ACESSO AO PIVOT AQUI --}}
                                                    value="{{ $vinculo ? $vinculo->pivot->duracao_customizada : $servico->duracao }}" 
                                                    class="w-full border-gray-200 rounded text-sm focus:border-pink-500">
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
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