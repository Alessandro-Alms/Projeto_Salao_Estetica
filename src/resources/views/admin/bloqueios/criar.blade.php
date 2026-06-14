<x-app-layout>
    <x-slot name="header">
        <div class="hidden"></div>
    </x-slot>

    <div class="py-12 relative">
        <!-- Fundo -->
        <div class="fixed inset-0 -z-10 overflow-hidden">
            <div class="absolute top-0 -left-20 w-[600px] h-[600px] bg-[#7B19E5]/20 rounded-full blur-3xl"></div>
            <div class="absolute bottom-0 -right-20 w-[700px] h-[700px] bg-[#FF2EB6]/20 rounded-full blur-3xl"></div>
            <div class="absolute top-1/3 left-1/3 w-[400px] h-[400px] bg-[#A955D3]/15 rounded-full blur-3xl"></div>
            <div class="absolute inset-0 bg-gradient-to-br from-[#7B19E5]/5 via-white/30 to-[#FF2EB6]/5"></div>
        </div>

        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="glass-card rounded-2xl shadow-xl overflow-hidden">
                <div class="p-8 bg-white/70 backdrop-blur-sm border border-white/40">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 bg-gradient-to-br from-[#7B19E5] to-[#FF2EB6] rounded-xl flex items-center justify-center shadow-md">
                            <span class="text-white text-lg">✧</span>
                        </div>
                        <h1 class="text-2xl font-title text-[#4A00B9]">Cadastrar Novo Bloqueio</h1>
                    </div>

                    <form action="{{ route('admin.bloqueios.store') }}" method="POST">
                        @csrf

                        <!-- Profissional -->
                        <div class="mb-5">
                            <label class="block text-sm font-medium text-[#4A00B9] mb-2">Profissional <span class="text-[#FF2EB6]">*</span></label>
                            <select name="profissional_id" required
                                data-searchable-select
                                data-searchable-placeholder="Digite o nome do profissional...">
                                
                                <option value="">Selecione...</option>
                                class="w-full px-4 py-3 bg-white/50 border border-[#FFD6F4] rounded-lg focus:outline-none focus:border-[#7B19E5] focus:ring-2 focus:ring-[#7B19E5]/20 transition-all">

                                @foreach($profissionais as $p)
                                    <option value="{{ $p->id }}">{{ $p->name }}</option>
                                @endforeach
                            </select>

                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                            <!-- Início do Bloqueio -->
                            <div>
                                <label class="block text-sm font-medium text-[#4A00B9] mb-2">Início do Bloqueio</label>
                                <input type="datetime-local" name="data_hora_inicio" 
                                    class="w-full px-4 py-3 bg-white/50 border border-[#FFD6F4] rounded-lg focus:outline-none focus:border-[#7B19E5] focus:ring-2 focus:ring-[#7B19E5]/20 transition-all @error('data_hora_inicio') border-red-500 @enderror" 
                                    required>
                                @error('data_hora_inicio') 
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p> 
                                @enderror
                            </div>

                            <!-- Fim do Bloqueio -->
                            <div>
                                <label class="block text-sm font-medium text-[#4A00B9] mb-2">Fim do Bloqueio</label>
                                <input type="datetime-local" name="data_hora_fim" 
                                    class="w-full px-4 py-3 bg-white/50 border border-[#FFD6F4] rounded-lg focus:outline-none focus:border-[#7B19E5] focus:ring-2 focus:ring-[#7B19E5]/20 transition-all @error('data_hora_fim') border-red-500 @enderror" 
                                    required>
                                @error('data_hora_fim') 
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p> 
                                @enderror
                            </div>
                        </div>

                        <!-- Motivo -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-[#4A00B9] mb-2">Motivo / Descrição</label>
                            <input type="text" name="motivo" 
                                class="w-full px-4 py-3 bg-white/50 border border-[#FFD6F4] rounded-lg focus:outline-none focus:border-[#7B19E5] focus:ring-2 focus:ring-[#7B19E5]/20 transition-all" 
                                placeholder="Ex: Feriado de Páscoa, Consulta Médica, Manutenção...">
                        </div>

                        <!-- Botões -->
                        <div class="flex justify-between gap-3 mt-6 pt-4 border-t border-[#FFD6F4]">
                            <a href="{{ route('admin.bloqueios.index') }}" class="px-6 py-2.5 text-sm text-gray-500 hover:text-[#7B19E5] transition-colors">
                                Cancelar
                            </a>
                            <button type="submit" class="bg-gradient-to-r from-[#7B19E5] to-[#FF2EB6] text-white px-8 py-2.5 text-sm rounded-full font-medium btn-primary shadow-md hover:shadow-lg transition-all">
                                Salvar Bloqueio
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
