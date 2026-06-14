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

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-6">
                <div class="flex items-center gap-2">
                    <span class="text-[#7B19E5] text-2xl">✧</span>
                    <h1 class="text-2xl font-title text-[#4A00B9]">Editar Pacote: {{ $pacote->nome }}</h1>
                </div>
                <a href="{{ route('admin.pacotes.index') }}" class="text-[#7B19E5] hover:text-[#FF2EB6] transition-colors inline-flex items-center gap-1">
                    <i class="fa-solid fa-arrow-left text-xs"></i> Voltar para a lista
                </a>
            </div>

            <div class="glass-card rounded-2xl shadow-xl overflow-hidden mb-8">
                <div class="p-6 bg-white/70 backdrop-blur-sm border border-white/40">
                    <form action="{{ route('admin.pacotes.update', $pacote->id_pacote) }}" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-5">
                        @csrf
                        @method('PUT')
                        
                        <!-- Nome do Pacote -->
                        <div class="col-span-1 md:col-span-2">
<label class="block text-sm font-medium text-[#4A00B9] mb-2">Nome do Pacote <span class="text-[#FF2EB6]">*</span></label>
                            <input type="text" name="nome" value="{{ $pacote->nome }}" required 
                                class="w-full px-4 py-3 bg-white/50 border border-[#FFD6F4] rounded-lg focus:outline-none focus:border-[#7B19E5] focus:ring-2 focus:ring-[#7B19E5]/20 transition-all">
                        </div>

                        <!-- Servicos inclusos -->
                        <div>
<label class="block text-sm font-medium text-[#4A00B9] mb-2">Servicos inclusos <span class="text-[#FF2EB6]">*</span></label>
                            @php
                                $servicosSelecionados = $pacote->servicos->pluck('id_servico')->push($pacote->servico_id)->filter()->unique();
                            @endphp
                            <div class="max-h-44 overflow-y-auto space-y-2 bg-white/50 border border-[#FFD6F4] rounded-lg p-3">
                                @foreach($servicos as $servico)
                                    <label class="flex items-center gap-2 text-sm text-[#1A002B]">
                                        <input type="checkbox" name="servicos_ids[]" value="{{ $servico->id_servico }}" class="rounded border-[#FFD6F4] text-[#7B19E5] focus:ring-[#7B19E5]" @checked($servicosSelecionados->contains($servico->id_servico))>
                                        <span>{{ $servico->nome }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- Qtd. de Sessões -->
                        <div>
<label class="block text-sm font-medium text-[#4A00B9] mb-2">Qtd. de Sessões <span class="text-[#FF2EB6]">*</span></label>
                            <input type="number" name="quantidade_sessoes" value="{{ $pacote->quantidade_sessoes }}" min="2" required 
                                class="w-full px-4 py-3 bg-white/50 border border-[#FFD6F4] rounded-lg focus:outline-none focus:border-[#7B19E5] focus:ring-2 focus:ring-[#7B19E5]/20 transition-all">
                        </div>

                        <!-- Valor Total -->
                        <div>
<label class="block text-sm font-medium text-[#4A00B9] mb-2">Valor Total (R$) <span class="text-[#FF2EB6]">*</span></label>
                            <input type="number" name="valor_total" value="{{ $pacote->valor_total }}" step="0.01" required 
                                class="w-full px-4 py-3 bg-white/50 border border-[#FFD6F4] rounded-lg focus:outline-none focus:border-[#7B19E5] focus:ring-2 focus:ring-[#7B19E5]/20 transition-all">
                        </div>

                        <!-- Validade em dias -->
                        <div>
<label class="block text-sm font-medium text-[#4A00B9] mb-2">Validade (em dias) <span class="text-[#FF2EB6]">*</span></label>
                            <input type="number" name="validade_dias" value="{{ $pacote->validade_dias }}" required 
                                class="w-full px-4 py-3 bg-white/50 border border-[#FFD6F4] rounded-lg focus:outline-none focus:border-[#7B19E5] focus:ring-2 focus:ring-[#7B19E5]/20 transition-all">
                        </div>

                        <!-- Botão -->
                        <div class="col-span-1 md:col-span-3 mt-4">
                            <button type="submit" class="bg-gradient-to-r from-[#7B19E5] to-[#FF2EB6] text-white px-8 py-3 text-sm rounded-full font-medium btn-primary shadow-lg hover:shadow-xl transition-all">
                                Atualizar Pacote
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
