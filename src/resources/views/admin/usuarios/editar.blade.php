<x-app-layout>
    <x-slot name="header">
        <div class="hidden"></div>
    </x-slot>

    <div class="py-12 relative">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="glass-card rounded-2xl shadow-xl overflow-hidden">
                <div class="p-6 bg-white/70 backdrop-blur-sm border border-white/40">
                    
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 bg-gradient-to-br from-[#7B19E5] to-[#FF2EB6] rounded-xl flex items-center justify-center shadow-md">
                            <span class="text-white text-lg">✧</span>
                        </div>
                        <h2 class="text-xl font-title text-[#4A00B9]">
                            {{ __('Editar Usuário: ') }} <span class="text-[#FF2EB6]">{{ $usuario->name }}</span>
                        </h2>
                    </div>

                    <form method="POST" action="{{ route('admin.usuarios.atualizar', $usuario) }}">
                        @csrf
                        @method('PUT')

                        @if($usuario->cargo === 'recepcionista' || $usuario->cargo === 'gerente')
                        <div class="mb-6 p-4 bg-amber-50/80 backdrop-blur-sm rounded-lg border border-amber-200 text-amber-700 text-sm shadow-sm">
                            <strong>✧ Aviso:</strong> Este usuário possui acesso administrativo ao sistema. Certifique-se de manter o e-mail atualizado.
                        </div>
                        @endif

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="name" class="block text-sm font-medium text-[#4A00B9] mb-2">
{{ __('Nome Completo') }} <span class="text-[#FF2EB6]">*</span>
                                </label>
                                <input id="name" type="text" name="name" value="{{ old('name', $usuario->name) }}" required autofocus
                                    class="w-full px-4 py-3 bg-white/50 border border-[#FFD6F4] rounded-lg focus:outline-none focus:border-[#7B19E5] focus:ring-2 focus:ring-[#7B19E5]/20 transition-all" />
                                <x-input-error :messages="$errors->get('name')" class="mt-2 text-red-500 text-xs" />
                            </div>

                            <div>
                                <label for="email" class="block text-sm font-medium text-[#4A00B9] mb-2">
{{ __('E-mail') }} <span class="text-[#FF2EB6]">*</span>
                                </label>
                                <input id="email" type="email" name="email" value="{{ old('email', $usuario->email) }}" required
                                    class="w-full px-4 py-3 bg-white/50 border border-[#FFD6F4] rounded-lg focus:outline-none focus:border-[#7B19E5] focus:ring-2 focus:ring-[#7B19E5]/20 transition-all" />
                                <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-500 text-xs" />
                            </div>

                            <div>
                                <label for="cpf" class="block text-sm font-medium text-[#4A00B9] mb-2">
{{ __('CPF') }} <span class="text-[#FF2EB6]">*</span>
                                </label>
<input id="cpf" type="text" name="cpf" value="{{ old('cpf', $usuario->cpf) }}" required data-mask="cpf"
                                    class="w-full px-4 py-3 bg-white/50 border border-[#FFD6F4] rounded-lg focus:outline-none focus:border-[#7B19E5] focus:ring-2 focus:ring-[#7B19E5]/20 transition-all" />
                                <x-input-error :messages="$errors->get('cpf')" class="mt-2 text-red-500 text-xs" />
                            </div>

                            <div>
                                <label for="telefone" class="block text-sm font-medium text-[#4A00B9] mb-2">
{{ __('Telefone') }} <span class="text-[#FF2EB6]">*</span>
                                </label>
                                <input id="telefone" type="text" name="telefone" value="{{ old('telefone', $usuario->telefone) }}" required data-mask="telefone"
                                    class="w-full px-4 py-3 bg-white/50 border border-[#FFD6F4] rounded-lg focus:outline-none focus:border-[#7B19E5] focus:ring-2 focus:ring-[#7B19E5]/20 transition-all" />
                                <x-input-error :messages="$errors->get('telefone')" class="mt-2 text-red-500 text-xs" />
                            </div>

                            <div>
                                <label for="cargo" class="block text-sm font-medium text-[#4A00B9] mb-2">
{{ __('Cargo / Função') }} <span class="text-[#FF2EB6]">*</span>
                                </label>
                                @if(auth()->user()->cargo === 'gerente')
                                    <select name="cargo" id="cargo" 
                                        class="w-full px-4 py-3 bg-white/50 border border-[#FFD6F4] rounded-lg focus:outline-none focus:border-[#7B19E5] focus:ring-2 focus:ring-[#7B19E5]/20 transition-all">
                                        <option value="cliente" {{ old('cargo', $usuario->cargo) == 'cliente' ? 'selected' : '' }}>Cliente</option>
                                        <option value="profissional" {{ old('cargo', $usuario->cargo) == 'profissional' ? 'selected' : '' }}>Profissional</option>
                                        <option value="recepcionista" {{ old('cargo', $usuario->cargo) == 'recepcionista' ? 'selected' : '' }}>Recepcionista</option>
                                        <option value="gerente" {{ old('cargo', $usuario->cargo) == 'gerente' ? 'selected' : '' }}>Gerente (Admin)</option>
                                    </select>
                                @else
                                    <input type="text" value="Cliente" 
                                        class="w-full px-4 py-3 bg-gray-50/50 border border-[#FFD6F4] rounded-lg text-gray-500 cursor-not-allowed" readonly />
                                    <input type="hidden" name="cargo" value="cliente">
                                @endif  
                                <x-input-error :messages="$errors->get('cargo')" class="mt-2 text-red-500 text-xs" />
                            </div>

                            <div>
                                <label for="password" class="block text-sm font-medium text-[#4A00B9] mb-2">
                                    {{ __('Nova Senha') }}
                                </label>
                                <input id="password" type="password" name="password" 
                                    class="w-full px-4 py-3 bg-white/50 border border-[#FFD6F4] rounded-lg focus:outline-none focus:border-[#7B19E5] focus:ring-2 focus:ring-[#7B19E5]/20 transition-all"
                                    placeholder="Deixe em branco para não alterar" />
                                <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-500 text-xs" />
                            </div>
                        </div>

                        <div class="mt-8 p-6 bg-white/40 rounded-xl border border-[#FFD6F4]">
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="d_nasc" class="block text-sm font-medium text-[#4A00B9] mb-2">
                                        {{ __('Data de Nascimento') }}
                                    </label>
                                    <input id="d_nasc" type="date" name="d_nasc" value="{{ old('d_nasc', $usuario->d_nasc) }}" required
                                        class="w-full px-4 py-3 bg-white/50 border border-[#FFD6F4] rounded-lg focus:outline-none focus:border-[#7B19E5] focus:ring-2 focus:ring-[#7B19E5]/20 transition-all" />
                                </div>
                                <div>
                                    <label for="endereco" class="block text-sm font-medium text-[#4A00B9] mb-2">
                                        {{ __('Endereço') }}
                                    </label>
                                    <input id="endereco" type="text" name="endereco" value="{{ old('endereco', $usuario->endereco) }}"
                                        class="w-full px-4 py-3 bg-white/50 border border-[#FFD6F4] rounded-lg focus:outline-none focus:border-[#7B19E5] focus:ring-2 focus:ring-[#7B19E5]/20 transition-all" />
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center justify-end gap-3 mt-8 pt-4 border-t border-[#FFD6F4]">
                            <a href="{{ route('admin.usuarios.index') }}" class="px-6 py-2.5 text-sm font-medium text-gray-500 hover:text-[#7B19E5] transition-colors">
                                {{ __('Cancelar') }}
                            </a>
                            <button type="submit" class="bg-gradient-to-r from-[#7B19E5] to-[#FF2EB6] text-white px-8 py-2.5 text-sm rounded-full font-medium btn-primary shadow-md hover:shadow-lg transition-all">
                                {{ __('SALVAR ALTERAÇÃ•ES') }}
                            </button>
                        </div>
                    </form>
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
