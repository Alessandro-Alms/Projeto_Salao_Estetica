<x-app-layout>
    <x-slot name="header">
        <div class="hidden"></div>
    </x-slot>

    <div class="py-12 relative">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="glass-card rounded-2xl shadow-xl overflow-hidden">
                <div class="p-6 bg-white/70 backdrop-blur-sm border border-white/40">
                    <!-- Cabeçalho -->
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 bg-gradient-to-br from-[#7B19E5] to-[#FF2EB6] rounded-xl flex items-center justify-center shadow-md">
                            <span class="text-white text-lg">✧</span>
                        </div>
                        <h2 class="text-xl font-title text-[#4A00B9]">
                            {{ __('Cadastrar Novo Serviço') }}
                        </h2>
                    </div>

                    <form method="POST" action="{{ route('admin.servicos.salvar') }}">
                        @csrf

                        <!-- Nome do Serviço -->
                        <div class="mb-4">
                            <label for="nome" class="block text-sm font-medium text-[#4A00B9] mb-2">
                                {{ __('Nome do Serviço') }}
                            </label>
                            <input
                                id="nome"
                                name="nome"
                                type="text"
                                class="w-full px-4 py-3 bg-white/50 border border-[#FFD6F4] rounded-lg focus:outline-none focus:border-[#7B19E5] focus:ring-2 focus:ring-[#7B19E5]/20 transition-all"
                                value="{{ old('nome') }}"
                                required
                            />
                            <x-input-error :messages="$errors->get('nome')" class="mt-2 text-red-500 text-xs" />
                        </div>

                        <!-- Preço -->
                        <div class="mb-4">
                            <label for="preco" class="block text-sm font-medium text-[#4A00B9] mb-2">
                                {{ __('Preço (R$)') }}
                            </label>
                            <input
                                id="preco"
                                name="preco"
                                type="number"
                                step="0.01"
                                class="w-full px-4 py-3 bg-white/50 border border-[#FFD6F4] rounded-lg focus:outline-none focus:border-[#7B19E5] focus:ring-2 focus:ring-[#7B19E5]/20 transition-all"
                                value="{{ old('preco') }}"
                                required
                            />
                            <x-input-error :messages="$errors->get('preco')" class="mt-2 text-red-500 text-xs" />
                        </div>

                        <!-- Duração -->
                        <div class="mb-6">
                            <label for="duracao" class="block text-sm font-medium text-[#4A00B9] mb-2">
                                {{ __('Duração (minutos)') }}
                            </label>
                            <input
                                id="duracao"
                                name="duracao"
                                type="number"
                                class="w-full px-4 py-3 bg-white/50 border border-[#FFD6F4] rounded-lg focus:outline-none focus:border-[#7B19E5] focus:ring-2 focus:ring-[#7B19E5]/20 transition-all"
                                value="{{ old('duracao') }}"
                                placeholder="Ex: 60"
                                required
                            />
                            <x-input-error :messages="$errors->get('duracao')" class="mt-2 text-red-500 text-xs" />
                        </div>

                        <!-- Botões -->
                        <div class="flex items-center justify-end gap-3">
                            <a href="{{ route('admin.servicos.index') }}" class="px-6 py-2.5 text-sm text-gray-500 hover:text-[#7B19E5] transition-colors">
                                {{ __('Cancelar') }}
                            </a>
                            <button type="submit" class="bg-gradient-to-r from-[#7B19E5] to-[#FF2EB6] text-white px-8 py-2.5 text-sm rounded-full font-medium btn-primary shadow-md hover:shadow-lg transition-all">
                                {{ __('SALVAR SERVIÇO') }}
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