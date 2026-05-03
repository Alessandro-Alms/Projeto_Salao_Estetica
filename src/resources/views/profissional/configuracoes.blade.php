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
            
            @if(session('success'))
                <div class="mb-6 p-4 rounded-lg bg-green-50/80 border border-green-200 text-green-700">
                    ✧ {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('profissional.servicos.atualizar') }}">
                @csrf
                @method('PUT')

                <!-- Meus Serviços e Especialidades -->
                <div class="glass-card rounded-2xl shadow-xl overflow-hidden mb-6">
                    <div class="p-6 bg-white/70 backdrop-blur-sm border border-white/40">
                        <div class="flex items-center gap-2 mb-5">
                            <span class="text-[#7B19E5] text-xl">✧</span>
                            <h3 class="text-lg font-title text-[#4A00B9]">Meus Serviços e Especialidades</h3>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($servicos as $servico)
                                @php $vinculo = $usuario->servicos->find($servico->id_servico); @endphp
                                <div class="flex flex-wrap items-center gap-3 p-4 rounded-xl transition-all {{ $vinculo ? 'bg-[#FF2EB6]/10 border border-[#FF2EB6]/30' : 'bg-white/50 border border-[#FFD6F4]' }}">
                                    <input type="checkbox" name="servicos[{{ $servico->id_servico }}][ativo]" {{ $vinculo ? 'checked' : '' }} class="rounded text-[#7B19E5] focus:ring-[#FF2EB6] w-5 h-5 border-[#FFD6F4]">
                                    <div class="flex-1">
                                        <span class="block font-title text-[#4A00B9]">{{ $servico->nome }}</span>
                                        <span class="text-xs text-gray-500">Padrão: {{ $servico->duracao }} min</span>
                                    </div>
                                    <div>
                                        <label class="block text-[10px] uppercase text-gray-400 font-medium mb-1">Tempo (min)</label>
                                        <input type="number" name="servicos[{{ $servico->id_servico }}][duracao]" value="{{ $vinculo ? $vinculo->pivot->duracao_customizada : $servico->duracao }}" class="w-20 px-2 py-1 bg-white/50 border border-[#FFD6F4] rounded-lg focus:outline-none focus:border-[#7B19E5] focus:ring-2 focus:ring-[#7B19E5]/20 transition-all text-sm">
                                        <p class="text-[10px] text-gray-500 mt-1">💰 Comissão: 50% (fixa)</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Minha Grade de Horários -->
                <div class="glass-card rounded-2xl shadow-xl overflow-hidden mb-6">
                    <div class="p-6 bg-white/70 backdrop-blur-sm border border-white/40">
                        <div class="flex items-center gap-2 mb-5">
                            <span class="text-[#FF2EB6] text-xl">✦</span>
                            <h3 class="text-lg font-title text-[#4A00B9]">Minha Grade de Horários</h3>
                        </div>
                        <div class="space-y-3">
                            @php
                                $dias = [1 => 'Segunda-feira', 2 => 'Terça-feira', 3 => 'Quarta-feira', 4 => 'Quinta-feira', 5 => 'Sexta-feira', 6 => 'Sábado', 0 => 'Domingo'];
                            @endphp
                            @foreach($dias as $num => $nome)
                                @php $h = $usuario->horariosTrabalho->where('dia_semana', $num)->first(); @endphp
                                <div class="flex flex-wrap items-center gap-3 p-3 rounded-xl hover:bg-white/30 transition border border-[#FFD6F4]">
                                    <div class="w-32 font-semibold text-[#1A002B]">{{ $nome }}</div>
                                    <div class="flex items-center gap-2">
                                        <input type="time" name="horarios[{{ $num }}][inicio]" value="{{ $h->hora_inicio ?? '08:00' }}" class="px-3 py-2 bg-white/50 border border-[#FFD6F4] rounded-lg focus:outline-none focus:border-[#7B19E5] focus:ring-2 focus:ring-[#7B19E5]/20 transition-all text-sm">
                                        <span class="text-gray-400">às</span>
                                        <input type="time" name="horarios[{{ $num }}][fim]" value="{{ $h->hora_fim ?? '18:00' }}" class="px-3 py-2 bg-white/50 border border-[#FFD6F4] rounded-lg focus:outline-none focus:border-[#7B19E5] focus:ring-2 focus:ring-[#7B19E5]/20 transition-all text-sm">
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <input type="checkbox" name="horarios[{{ $num }}][trabalha]" value="1" {{ ($h->trabalha ?? true) ? 'checked' : '' }} class="rounded text-[#7B19E5] focus:ring-[#FF2EB6] border-[#FFD6F4]">
                                        <span class="text-xs font-medium text-gray-500 uppercase">Ativo</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <div>
                                            <label class="text-xs text-gray-500">Início Almoço</label>
                                            <input type="time" name="horarios[{{ $num }}][almoco_inicio]" value="{{ $h->almoco_inicio ?? '12:00' }}" class="px-2 py-1 bg-white/50 border border-[#FFD6F4] rounded-lg focus:outline-none focus:border-[#7B19E5] focus:ring-2 focus:ring-[#7B19E5]/20 transition-all text-sm">
                                        </div>
                                        <div>
                                            <label class="text-xs text-gray-500">Fim Almoço</label>
                                            <input type="time" name="horarios[{{ $num }}][almoco_fim]" value="{{ $h->almoco_fim ?? '13:00' }}" class="px-2 py-1 bg-white/50 border border-[#FFD6F4] rounded-lg focus:outline-none focus:border-[#7B19E5] focus:ring-2 focus:ring-[#7B19E5]/20 transition-all text-sm">
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Botão Atualizar -->
                <div class="flex justify-end mb-4">
                    <button type="submit" class="bg-gradient-to-r from-[#7B19E5] to-[#FF2EB6] text-white px-10 py-3 text-sm rounded-full font-medium btn-primary shadow-lg hover:shadow-xl transition-all">
                        Atualizar Meu Perfil
                    </button>
                </div>
            </form>
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