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
                            {{ __('Editar Produto: ') }} <span class="text-[#FF2EB6]">{{ $produto->nome }}</span>
                        </h2>
                    </div>

                    <form method="POST" action="{{ route('admin.produtos.atualizar', $produto->id_produto) }}">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Nome -->
                            <div class="md:col-span-2">
                                <label for="nome" class="block text-sm font-medium text-[#4A00B9] mb-2">
{{ __('Nome do Produto') }} <span class="text-[#FF2EB6]">*</span>
                                </label>
                                <input id="nome" type="text" name="nome" value="{{ old('nome', $produto->nome) }}" required
                                    class="w-full px-4 py-3 bg-white/50 border border-[#FFD6F4] rounded-lg focus:outline-none focus:border-[#7B19E5] focus:ring-2 focus:ring-[#7B19E5]/20 transition-all" />
                                <x-input-error :messages="$errors->get('nome')" class="mt-2 text-red-500 text-xs" />
                            </div>

                            <!-- Categoria -->
                            <div>
                                <label for="tipo" class="block text-sm font-medium text-[#4A00B9] mb-2">
{{ __('Categoria') }} <span class="text-[#FF2EB6]">*</span>
                                </label>
                                <select name="tipo" id="tipo" 
                                    class="w-full px-4 py-3 bg-white/50 border border-[#FFD6F4] rounded-lg focus:outline-none focus:border-[#7B19E5] focus:ring-2 focus:ring-[#7B19E5]/20 transition-all">
                                    <option value="acessorios" {{ old('tipo', $produto->tipo) == 'acessorios' ? 'selected' : '' }}>Acessórios</option>
                                    <option value="kits" {{ old('tipo', $produto->tipo) == 'kits' ? 'selected' : '' }}>Kits</option>
                                    <option value="cosmeticos" {{ old('tipo', $produto->tipo) == 'cosmeticos' ? 'selected' : '' }}>Cosméticos</option>
                                    <option value="cabelo" {{ old('tipo', $produto->tipo) == 'cabelo' ? 'selected' : '' }}>Cabelo</option>
                                </select>
                                <x-input-error :messages="$errors->get('tipo')" class="mt-2 text-red-500 text-xs" />
                            </div>

                            <!-- Preço -->
                            <div>
                                <label for="valor_unitario" class="block text-sm font-medium text-[#4A00B9] mb-2">
{{ __('Preço de Venda (R$)') }} <span class="text-[#FF2EB6]">*</span>
                                </label>
                                <input id="valor_unitario" type="number" step="0.01" name="valor_unitario" value="{{ old('valor_unitario', $produto->valor_unitario) }}" required
                                    class="w-full px-4 py-3 bg-white/50 border border-[#FFD6F4] rounded-lg focus:outline-none focus:border-[#7B19E5] focus:ring-2 focus:ring-[#7B19E5]/20 transition-all" />
                                <x-input-error :messages="$errors->get('valor_unitario')" class="mt-2 text-red-500 text-xs" />
                            </div>

                            <!-- Estoque -->
                            <div>
                                <label for="quantidade_estoque" class="block text-sm font-medium text-[#4A00B9] mb-2">
{{ __('Quantidade em Estoque') }} <span class="text-[#FF2EB6]">*</span>
                                </label>
                                <input id="quantidade_estoque" type="number" name="quantidade_estoque" value="{{ old('quantidade_estoque', $produto->quantidade_estoque) }}" required
                                    class="w-full px-4 py-3 bg-white/50 border border-[#FFD6F4] rounded-lg focus:outline-none focus:border-[#7B19E5] focus:ring-2 focus:ring-[#7B19E5]/20 transition-all" />
                                <x-input-error :messages="$errors->get('quantidade_estoque')" class="mt-2 text-red-500 text-xs" />
                            </div>

                            <!-- Descrição -->
                            <div class="md:col-span-2">
                                <label for="descricao" class="block text-sm font-medium text-[#4A00B9] mb-2">
                                    {{ __('Descrição (Opcional)') }}
                                </label>
                                <textarea id="descricao" name="descricao" rows="3"
                                    class="w-full px-4 py-3 bg-white/50 border border-[#FFD6F4] rounded-lg focus:outline-none focus:border-[#7B19E5] focus:ring-2 focus:ring-[#7B19E5]/20 transition-all resize-none">{{ old('descricao', $produto->descricao) }}</textarea>
                                <x-input-error :messages="$errors->get('descricao')" class="mt-2 text-red-500 text-xs" />
                            </div>
                        </div>

                        <!-- Botões -->
                        <div class="flex items-center justify-end gap-3 mt-8 pt-4 border-t border-[#FFD6F4]">
                            <a href="{{ route('admin.produtos.index') }}" class="px-6 py-2.5 text-sm text-gray-500 hover:text-[#7B19E5] transition-colors">
                                {{ __('Cancelar') }}
                            </a>
                            <button type="submit" class="bg-gradient-to-r from-[#7B19E5] to-[#FF2EB6] text-white px-8 py-2.5 text-sm rounded-full font-medium btn-primary shadow-md hover:shadow-lg transition-all">
                                {{ __('ATUALIZAR PRODUTO') }}
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