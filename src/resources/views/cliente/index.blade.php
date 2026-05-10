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
            
            {{-- Alertas de Feedback --}}
            @if(session('success'))
                <div class="mb-6 p-4 rounded-lg bg-green-50/80 border border-green-200 text-green-700">
                    ✧ {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 p-4 rounded-lg bg-red-50/80 border border-red-200 text-red-700">
                    @foreach ($errors->all() as $error)
                        <p>✧ {{ $error }}</p>
                    @endforeach
                </div>
            @endif

            {{-- Seção de pacotes --}}
            <div class="mb-12">
                <div class="flex items-center gap-2 mb-6">
                    <span class="text-[#7B19E5] text-xl">✧</span>
                    <h2 class="text-2xl font-title text-[#4A00B9]">Meus Pacotes Ativos</h2>
                </div>

                <div class="mb-6">
                    <a href="{{ route('cliente.pacotes.index') }}" class="inline-flex items-center justify-center bg-white text-[#FF2EB6] border-2 border-[#FF2EB6] px-5 py-2 rounded-full text-sm font-bold hover:bg-[#FF2EB6] hover:text-white transition-all">
                        Comprar pacote
                    </a>
                </div>

                @if(isset($pacotes) && $pacotes->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($pacotes as $meuPacote)
                            <div class="glass-card rounded-2xl shadow-xl overflow-hidden hover-lift">
                                <div class="p-5 bg-white/70 backdrop-blur-sm border border-white/40">
                                    <div class="flex justify-between items-start mb-3">
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gradient-to-r from-green-500/20 to-green-600/20 text-green-700 border border-green-200">
                                            ✧ Ativo
                                        </span>
                                    </div>
                                    <h3 class="text-lg font-title text-[#4A00B9] mt-2 mb-1">{{ $meuPacote->pacote->nome }}</h3>
                                    <p class="text-xs text-[#7B19E5] mb-2">
                                        {{ $meuPacote->pacote->servicos->pluck('nome')->join(', ') ?: ($meuPacote->pacote->servico->nome ?? 'Serviço removido') }}
                                    </p>
                                    <p class="text-sm text-[#7B19E5] mb-4">
                                        Válido até: <span class="font-bold">{{ \Carbon\Carbon::parse($meuPacote->data_validade)->format('d/m/Y') }}</span>
                                    </p>
                                    <div class="bg-white/50 rounded-lg p-3 border border-[#FFD6F4] flex justify-between items-center">
                                        <span class="text-gray-600 text-sm font-medium">Sessões Restantes:</span>
                                        <span class="text-2xl font-black text-[#7B19E5]">{{ $meuPacote->sessoes_restantes }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="glass-card rounded-2xl shadow-xl overflow-hidden">
                        <div class="p-6 bg-white/70 backdrop-blur-sm border border-white/40 text-center">
                            <p class="text-gray-500">✧ Não possuis nenhum pacote ativo.</p>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Seção de agendamentos --}}
            <div class="flex justify-between items-center mb-6">
                <div class="flex items-center gap-2">
                    <span class="text-[#7B19E5] text-xl">✧</span>
                    <h2 class="text-2xl font-title text-[#4A00B9]">Meus Agendamentos</h2>
                </div>
                <a href="{{ route('cliente.agendar.novo') }}" class="bg-gradient-to-r from-[#7B19E5] to-[#FF2EB6] text-white px-6 py-2.5 text-sm rounded-full font-medium btn-primary shadow-md hover:shadow-lg transition-all">
                    + Novo Agendamento
                </a>
            </div>

            {{-- FILTRO DE PERÍODO --}}
            <div class="mb-6 flex gap-3 flex-wrap">
                <form method="GET" action="{{ route('cliente.index') }}" class="flex gap-2">
                    <input type="hidden" name="filtro" id="filtro_input" value="{{ request('filtro', '7') }}">
                    
                    <button type="button" onclick="filtrarAgendamentos(7)" 
                            class="px-4 py-2 rounded-full text-sm font-medium transition-all {{ request('filtro', '7') == '7' ? 'bg-gradient-to-r from-[#7B19E5] to-[#FF2EB6] text-white shadow-md' : 'bg-white/70 border border-[#FFD6F4] text-[#4A00B9] hover:bg-white/90' }}">
                        Calendário Próximos 7 dias
                    </button>

                    <button type="button" onclick="filtrarAgendamentos(30)" 
                            class="px-4 py-2 rounded-full text-sm font-medium transition-all {{ request('filtro') == '30' ? 'bg-gradient-to-r from-[#7B19E5] to-[#FF2EB6] text-white shadow-md' : 'bg-white/70 border border-[#FFD6F4] text-[#4A00B9] hover:bg-white/90' }}">
                        Calendário Próximos 30 dias
                    </button>

                    <button type="button" onclick="filtrarAgendamentos('todos')" 
                            class="px-4 py-2 rounded-full text-sm font-medium transition-all {{ request('filtro') == 'todos' ? 'bg-gradient-to-r from-[#7B19E5] to-[#FF2EB6] text-white shadow-md' : 'bg-white/70 border border-[#FFD6F4] text-[#4A00B9] hover:bg-white/90' }}">
                        Calendário Todos os agendamentos
                    </button>
                </form>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($agendamentos as $agenda)
                    <div class="glass-card rounded-2xl shadow-xl overflow-hidden hover-lift">
                        <div class="p-5 bg-white/70 backdrop-blur-sm border border-white/40 flex flex-col h-full">
                            <div class="flex justify-between items-start mb-3">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-500/20 text-blue-700 border border-blue-200">
                                    ✧ Fidelidade: {{ auth()->user()->contador_fidelidade ?? 0 }}/5
                                </span>

                                <span class="px-2 py-1 text-xs font-bold rounded-full 
                                    @if($agenda->status == 'executado') bg-blue-100 text-blue-700 
                                    @elseif($agenda->status == 'cancelado') bg-red-100 text-red-700 
                                    @else bg-green-100 text-green-700 @endif">
                                    {{ ucfirst($agenda->status) }}
                                </span>
                            </div>

                            <div class="mb-4">
                                <p class="text-sm text-[#7B19E5] font-semibold uppercase">
                                    {{ \Carbon\Carbon::parse($agenda->data_hora_inicio)->translatedFormat('d \d\e F') }}
                                </p>
                                <h3 class="text-lg font-title text-[#4A00B9]">
                                    @php
                                        $servicos = $agenda->servicos->pluck('nome')->toArray();
                                        if (empty($servicos) && $agenda->servico) {
                                            $servicos = [$agenda->servico->nome];
                                        }
                                        echo implode(', ', $servicos);
                                    @endphp
                                </h3>
                            </div>

                            <div class="space-y-2 text-sm text-[#1A002B] mb-4">
                                <p><strong class="text-[#7B19E5]">Horário:</strong> {{ \Carbon\Carbon::parse($agenda->data_hora_inicio)->format('H:i') }}</p>
                                <p><strong class="text-[#7B19E5]">Profissional:</strong> {{ $agenda->profissional->name }}</p>
                                <p><strong class="text-[#7B19E5]">Valor:</strong> R$ {{ number_format($agenda->valor_total, 2, ',', '.') }}</p>
                            </div>

                            <div class="mt-auto pt-4 border-t border-[#FFD6F4]">
                                @if($agenda->status == 'executado')
                                    @if(!$agenda->avaliacao)
                                        @php
                                            $nomesServicos = $agenda->servicos->pluck('nome')->toArray();
                                            if (empty($nomesServicos) && $agenda->servico) {
                                                $nomesServicos = [$agenda->servico->nome];
                                            }
                                            $servicosStr = implode(', ', $nomesServicos);
                                        @endphp
                                        <button onclick="abrirModalAvaliacao({{ $agenda->id_agendamento }}, @js($servicosStr))" 
                                                class="w-full bg-gradient-to-r from-yellow-500 to-orange-500 text-white py-2 rounded-full font-medium btn-primary shadow-md hover:shadow-lg transition-all flex items-center justify-center gap-2">
                                            ★ Avaliar Atendimento
                                        </button>
                                    @else
                                        <div class="text-center p-2 bg-green-50/50 rounded-lg border border-green-200">
                                            <span class="text-green-700 text-sm font-bold flex items-center justify-center gap-1">
                                                ✓ Avaliado: {{ $agenda->avaliacao->nota }} / 5 estrelas
                                            </span>
                                        </div>
                                    @endif
                                @elseif($agenda->status == 'confirmado')
                                    @php
                                        $multaValor = $agenda->valor_total * 0.05;
                                    @endphp
                                    <form action="{{ route('cliente.agendamento.cancelar', ['id' => $agenda->id_agendamento]) }}" method="POST" onsubmit="return confirmarCancelamento('{{ number_format($multaValor, 2, ',', '.') }}')">
                                        @csrf
                                        <button type="submit" class="w-full text-[#FF2EB6] border border-[#FF2EB6] py-2 rounded-full text-sm font-bold hover:bg-[#FF2EB6] hover:text-white transition-all">
                                                Cancelar agendamento
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="glass-card rounded-2xl shadow-xl overflow-hidden col-span-full">
                        <div class="p-10 bg-white/70 backdrop-blur-sm border border-white/40 text-center">
                            <p class="text-gray-500">✧ Ainda não tens agendamentos.</p>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Modal de avaliação --}}
    <div id="modalAvaliacao" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black/50 backdrop-blur-sm"></div>

            <div class="relative glass-card rounded-2xl shadow-xl max-w-lg w-full p-6 transition-all transform bg-white/95 backdrop-blur-sm">
                <form action="{{ route('cliente.avaliar.salvar') }}" method="POST">
                    @csrf
                    <input type="hidden" name="agendamento_id" id="modal_agendamento_id">
                    
                    <div class="flex items-center gap-2 mb-2">
                        <span class="text-[#7B19E5] text-xl">✧</span>
                        <h3 class="text-xl font-title text-[#4A00B9]">Como foi o atendimento?</h3>
                    </div>
                    <p class="text-sm text-gray-500 mb-6" id="modal_servico_nome"></p>

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-[#4A00B9] mb-4 text-center">Seleciona a tua nota:</label>
                        
                        <div class="star-rating flex flex-row-reverse justify-center gap-2">
                            <input type="radio" id="star5" name="nota" value="5" class="hidden peer" required />
                            <label for="star5" class="cursor-pointer text-4xl text-gray-300 hover:text-yellow-400 peer-checked:text-yellow-400">★</label>
                            
                            <input type="radio" id="star4" name="nota" value="4" class="hidden peer" />
                            <label for="star4" class="cursor-pointer text-4xl text-gray-300 hover:text-yellow-400 peer-checked:text-yellow-400">★</label>
                            
                            <input type="radio" id="star3" name="nota" value="3" class="hidden peer" />
                            <label for="star3" class="cursor-pointer text-4xl text-gray-300 hover:text-yellow-400 peer-checked:text-yellow-400">★</label>
                            
                            <input type="radio" id="star2" name="nota" value="2" class="hidden peer" />
                            <label for="star2" class="cursor-pointer text-4xl text-gray-300 hover:text-yellow-400 peer-checked:text-yellow-400">★</label>
                            
                            <input type="radio" id="star1" name="nota" value="1" class="hidden peer" />
                            <label for="star1" class="cursor-pointer text-4xl text-gray-300 hover:text-yellow-400 peer-checked:text-yellow-400">★</label>
                        </div>

                        <style>
                            .star-rating label:hover ~ label,
                            .star-rating input:checked ~ label {
                                color: #FACC15;
                            }
                        </style>
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-[#4A00B9] mb-2">Comentário (opcional):</label>
                        <textarea name="comentario" rows="3" class="w-full px-4 py-3 bg-white/50 border border-[#FFD6F4] rounded-lg focus:outline-none focus:border-[#7B19E5] focus:ring-2 focus:ring-[#7B19E5]/20 transition-all" placeholder="O que achaste do resultado?"></textarea>
                    </div>

                    <div class="flex flex-col sm:flex-row-reverse gap-3">
                        <button type="submit" class="bg-gradient-to-r from-[#7B19E5] to-[#FF2EB6] text-white py-2 rounded-full font-medium btn-primary shadow-md hover:shadow-lg transition-all px-6">
                            Enviar
                        </button>
                        <button type="button" onclick="fecharModalAvaliacao()" class="bg-gray-100 text-gray-700 py-2 rounded-full font-medium hover:bg-gray-200 transition-all px-6">
                            Cancelar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function filtrarAgendamentos(periodo) {
            const url = new URL(window.location);
            if (periodo === 'todos') {
                url.searchParams.set('filtro', 'todos');
            } else {
                url.searchParams.set('filtro', periodo);
            }
            window.location.href = url.toString();
        }

        function abrirModalAvaliacao(id, servico) {
            document.getElementById('modal_agendamento_id').value = id;
            document.getElementById('modal_servico_nome').innerText = "Serviço: " + servico;
            document.getElementById('modalAvaliacao').classList.remove('hidden');
        }

        function fecharModalAvaliacao() {
            document.getElementById('modalAvaliacao').classList.add('hidden');
        }

        function confirmarCancelamento(multa) {
            return confirm(
                `Ao cancelar com menos de 24h, será cobrada uma multa de 5% do serviço.\n` +
                `Valor estimado da multa: R$ ${multa}.\n\n` +
                `Deseja mesmo cancelar?`
            );
        }
    </script>
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
    
    .hover-lift {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .hover-lift:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 25px -12px rgba(123, 25, 229, 0.25);
    }
</style>
