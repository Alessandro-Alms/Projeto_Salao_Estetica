<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Alertas de Feedback --}}
            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 shadow-sm rounded-r">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 shadow-sm rounded-r">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            {{-- SECÇÃO DE PACOTES --}}
            <div class="mb-12">
                <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center gap-2">
                    🎟️ Meus Pacotes Ativos
                </h2>

                @if($pacotes->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($pacotes as $meuPacote)
                            <div class="bg-gradient-to-br from-blue-50 to-blue-100 border border-blue-200 rounded-xl p-5 shadow-sm relative overflow-hidden">
                                <div class="relative z-10">
                                    <span class="bg-blue-600 text-white text-xs font-bold px-2 py-1 rounded uppercase tracking-wide">Ativo</span>
                                    <h3 class="text-lg font-black text-blue-900 mt-3 mb-1">{{ $meuPacote->pacote->nome }}</h3>
                                    <p class="text-sm text-blue-700 mb-4">
                                        Válido até: <span class="font-bold">{{ \Carbon\Carbon::parse($meuPacote->data_validade)->format('d/m/Y') }}</span>
                                    </p>
                                    <div class="bg-white rounded-lg p-3 border border-blue-100 shadow-inner flex justify-between items-center">
                                        <span class="text-gray-600 text-sm font-medium">Sessões Restantes:</span>
                                        <span class="text-2xl font-black text-blue-600">{{ $meuPacote->sessoes_restantes }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="bg-white border border-gray-200 rounded-xl p-6 text-center shadow-sm">
                        <p class="text-gray-500">Não possuis nenhum pacote ativo.</p>
                    </div>
                @endif
            </div>

            <hr class="border-gray-200 mb-8">

            {{-- SECÇÃO DE AGENDAMENTOS --}}
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800">Meus Agendamentos</h2>
                <a href="{{ route('cliente.agendar.novo') }}" class="bg-pink-600 text-white px-4 py-2 rounded-lg font-bold hover:bg-pink-700 transition shadow-sm">
                    + Novo Agendamento
                </a>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($agendamentos as $agenda)
                    <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-200 p-5 flex flex-col justify-between">
                        <div>
                            <div class="flex justify-between items-start mb-3">
                                <span class="bg-blue-100 text-blue-800 text-xs font-bold px-2 py-1 rounded">
                                    Fidelidade: {{ auth()->user()->contador_fidelidade }}/5
                                </span>

                                <span class="px-2 py-1 text-xs font-bold rounded-full 
                                    @if($agenda->status == 'executado') bg-blue-100 text-blue-700 
                                    @elseif($agenda->status == 'cancelado') bg-red-100 text-red-700 
                                    @else bg-green-100 text-green-700 @endif">
                                    {{ ucfirst($agenda->status) }}
                                </span>
                            </div>

                            <div class="mb-4">
                                <p class="text-sm text-gray-500 font-semibold uppercase">
                                    {{ \Carbon\Carbon::parse($agenda->data_hora_inicio)->translatedFormat('d \d\e F') }}
                                </p>
                                <h3 class="text-lg font-bold text-gray-900">{{ $agenda->servico->nome }}</h3>
                            </div>

                            <div class="space-y-2 text-sm text-gray-600 mb-4">
                                <p><strong>🕒 Horário:</strong> {{ \Carbon\Carbon::parse($agenda->data_hora_inicio)->format('H:i') }}</p>
                                <p><strong>👤 Profissional:</strong> {{ $agenda->profissional->name }}</p>
                                <p><strong>💰 Valor:</strong> R$ {{ number_format($agenda->valor_total, 2, ',', '.') }}</p>
                            </div>
                        </div>

                        {{-- BOTÃO DE AVALIAÇÃO OU STATUS FINAL --}}
                        <div class="mt-4 pt-4 border-t border-gray-100">
                            @if($agenda->status == 'executado')
                                @if(!$agenda->avaliacao)
                                    <button onclick="abrirModalAvaliacao({{ $agenda->id_agendamento }}, '{{ $agenda->servico->nome }}')" 
                                            class="w-full bg-yellow-500 text-white py-2 rounded-lg font-bold hover:bg-yellow-600 transition flex items-center justify-center gap-2">
                                        ⭐ Avaliar Atendimento
                                    </button>
                                @else
                                    <div class="text-center p-2 bg-green-50 rounded-lg border border-green-100">
                                        <span class="text-green-700 text-sm font-bold flex items-center justify-center gap-1">
                                            ✅ Avaliado: {{ $agenda->avaliacao->nota }} / 5 estrelas
                                        </span>
                                    </div>
                                @endif
                            @elseif($agenda->status == 'confirmado')
                                <form action="{{ route('cliente.agendamento.cancelar', ['id' => $agenda->id_agendamento]) }}" method="POST" onsubmit="return confirm('Cancelar este horário?')">
                                    @csrf
                                    <button type="submit" class="w-full text-red-600 border border-red-600 py-2 rounded-lg hover:bg-red-50 text-sm font-bold transition">
                                        ❌ Cancelar Agendamento
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="col-span-full bg-white p-10 text-center rounded-xl shadow border border-gray-200">
                        <p class="text-gray-500">Ainda não tens agendamentos.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- MODAL DE AVALIAÇÃO --}}
    <div id="modalAvaliacao" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-500 opacity-75"></div>

            <div class="relative bg-white rounded-xl shadow-xl max-w-lg w-full p-6 transition-all transform">
                <form action="{{ route('cliente.avaliar.salvar') }}" method="POST">
                    @csrf
                    <input type="hidden" name="agendamento_id" id="modal_agendamento_id">
                    
                    <h3 class="text-xl font-bold text-gray-900 mb-1">Como foi o atendimento?</h3>
                    <p class="text-sm text-gray-500 mb-6" id="modal_servico_nome"></p>

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-4 text-center">Seleciona a tua nota:</label>
                        
                        {{-- Logica das Estrelas --}}
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
                            /* Faz o preenchimento da esquerda para a direita */
                            .star-rating label:hover ~ label,
                            .star-rating input:checked ~ label {
                                color: #FACC15;
                            }
                        </style>
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Comentário (opcional):</label>
                        <textarea name="comentario" rows="3" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-pink-500 focus:border-pink-500" placeholder="O que achaste do resultado?"></textarea>
                    </div>

                    <div class="flex flex-col sm:flex-row-reverse gap-3">
                        <button type="submit" class="w-full bg-pink-600 text-white py-2 rounded-lg font-bold hover:bg-pink-700">Enviar</button>
                        <button type="button" onclick="fecharModalAvaliacao()" class="w-full bg-gray-100 text-gray-700 py-2 rounded-lg font-bold hover:bg-gray-200">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function abrirModalAvaliacao(id, servico) {
            document.getElementById('modal_agendamento_id').value = id;
            document.getElementById('modal_servico_nome').innerText = "Serviço: " + servico;
            document.getElementById('modalAvaliacao').classList.remove('hidden');
        }

        function fecharModalAvaliacao() {
            document.getElementById('modalAvaliacao').classList.add('hidden');
        }
    </script>
</x-app-layout>