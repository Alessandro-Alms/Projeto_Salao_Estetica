<x-app-layout>
    <x-slot name="header">
        <div class="hidden"></div>
    </x-slot>

    <div class="py-12 relative">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="glass-card rounded-2xl shadow-xl overflow-hidden">
                <div class="p-6 bg-white/70 backdrop-blur-sm border border-white/40">
                    <!-- Cabeçalho -->
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 bg-gradient-to-br from-[#7B19E5] to-[#FF2EB6] rounded-xl flex items-center justify-center shadow-md">
                            <span class="text-white text-lg">✧</span>
                        </div>
                        <h2 class="text-xl font-title text-[#4A00B9]">
                            {{ __('Cadastrar Novo Usuário') }}
                        </h2>
                    </div>

                    <form method="POST" action="{{ route('admin.usuarios.salvar') }}">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Nome Completo -->
                            <div>
                                <label for="name" class="block text-sm font-medium text-[#4A00B9] mb-2">
                                    {{ __('Nome Completo') }}
                                </label>
                                <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                                    class="w-full px-4 py-3 bg-white/50 border border-[#FFD6F4] rounded-lg focus:outline-none focus:border-[#7B19E5] focus:ring-2 focus:ring-[#7B19E5]/20 transition-all" />
                                <x-input-error :messages="$errors->get('name')" class="mt-2 text-red-500 text-xs" />
                            </div>

                            <!-- E-mail -->
                            <div>
                                <label for="email" class="block text-sm font-medium text-[#4A00B9] mb-2">
                                    {{ __('E-mail') }}
                                </label>
                                <input id="email" type="email" name="email" value="{{ old('email') }}" required
                                    class="w-full px-4 py-3 bg-white/50 border border-[#FFD6F4] rounded-lg focus:outline-none focus:border-[#7B19E5] focus:ring-2 focus:ring-[#7B19E5]/20 transition-all" />
                                <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-500 text-xs" />
                            </div>

                            <!-- CPF -->
                            <div>
                                <label for="cpf" class="block text-sm font-medium text-[#4A00B9] mb-2">
                                    {{ __('CPF') }}
                                </label>
                                <input id="cpf" type="text" name="cpf" value="{{ old('cpf') }}" required
                                    class="w-full px-4 py-3 bg-white/50 border border-[#FFD6F4] rounded-lg focus:outline-none focus:border-[#7B19E5] focus:ring-2 focus:ring-[#7B19E5]/20 transition-all" />
                                <x-input-error :messages="$errors->get('cpf')" class="mt-2 text-red-500 text-xs" />
                            </div>

                            <!-- Telefone -->
                            <div>
                                <label for="telefone" class="block text-sm font-medium text-[#4A00B9] mb-2">
                                    {{ __('Telefone') }}
                                </label>
                                <input id="telefone" type="text" name="telefone" value="{{ old('telefone') }}" required
                                    class="w-full px-4 py-3 bg-white/50 border border-[#FFD6F4] rounded-lg focus:outline-none focus:border-[#7B19E5] focus:ring-2 focus:ring-[#7B19E5]/20 transition-all" />
                                <x-input-error :messages="$errors->get('telefone')" class="mt-2 text-red-500 text-xs" />
                            </div>

                            <!-- Cargo -->
                            <div>
                                <label for="cargo" class="block text-sm font-medium text-[#4A00B9] mb-2">
                                    {{ __('Cargo / Função') }}
                                </label>
                                
                                @if(auth()->user()->isGerente())
                                    <select name="cargo" id="cargo" 
                                        class="w-full px-4 py-3 bg-white/50 border border-[#FFD6F4] rounded-lg focus:outline-none focus:border-[#7B19E5] focus:ring-2 focus:ring-[#7B19E5]/20 transition-all">
                                        @foreach($cargosPermitidos as $cargo)
                                            <option value="{{ $cargo }}" {{ old('cargo', request('cargo', 'cliente')) == $cargo ? 'selected' : '' }}>
                                                {{ $cargo === 'gerente' ? 'Gerente (Admin)' : ucfirst($cargo) }}
                                            </option>
                                        @endforeach
                                    </select>
                                @else
                                    <select disabled 
                                        class="w-full px-4 py-3 bg-gray-100/50 border border-[#FFD6F4] rounded-lg text-gray-500 cursor-not-allowed">
                                        <option selected>Cliente</option>
                                    </select>
                                    <input type="hidden" name="cargo" value="cliente">
                                @endif
                                
                                <x-input-error :messages="$errors->get('cargo')" class="mt-2 text-red-500 text-xs" />
                            </div>

                            <!-- Senha -->
                            <div>
                                <label for="password" class="block text-sm font-medium text-[#4A00B9] mb-2">
                                    {{ __('Senha') }}
                                </label>
                                <input id="password" type="password" name="password" required autocomplete="new-password"
                                    class="w-full px-4 py-3 bg-white/50 border border-[#FFD6F4] rounded-lg focus:outline-none focus:border-[#7B19E5] focus:ring-2 focus:ring-[#7B19E5]/20 transition-all" />
                                <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-500 text-xs" />
                            </div>
                        </div>

                        <!-- Botões -->
                        <div class="flex items-center justify-end gap-3 mt-8 pt-4 border-t border-[#FFD6F4]">
                            <a href="{{ route('admin.usuarios.index') }}" class="px-6 py-2.5 text-sm text-gray-500 hover:text-[#7B19E5] transition-colors">
                                {{ __('Cancelar') }}
                            </a>
                            <button type="submit" class="bg-gradient-to-r from-[#7B19E5] to-[#FF2EB6] text-white px-8 py-2.5 text-sm rounded-full font-medium btn-primary shadow-md hover:shadow-lg transition-all">
                                {{ __('CADASTRAR USUÁRIO') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/imask"></script>
    <script>
        // Máscara CPF
        var cpfMask = IMask(document.getElementById('cpf'), {
            mask: '000.000.000-00'
        });
        
        // Máscara Telefone
        var phoneMask = IMask(document.getElementById('telefone'), {
            mask: '(00) 00000-0000'
        });
        
        // Remove máscara antes de enviar
        document.querySelector('form').addEventListener('submit', function() {
            const cpfInput = document.getElementById('cpf');
            if (cpfInput) cpfInput.value = cpfInput.value.replace(/\D/g, '');
            
            const telInput = document.getElementById('telefone');
            if (telInput) telInput.value = telInput.value.replace(/\D/g, '');
        });
    </script>
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
