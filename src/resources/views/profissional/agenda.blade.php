<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <h1 class="text-2xl font-bold mb-6">Minha Agenda de Atendimentos</h1>

            {{-- Alertas de Sucesso ou Erro --}}
            @if(session('status'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 shadow">
                    {{ session('status') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 shadow">
                    {{ session('error') }}
                </div>
            @endif

            @forelse($agendamentos as $dia => $itens)
                <div class="mb-8">
                    <h2 class="bg-pink-100 text-pink-800 p-2 rounded-lg font-bold inline-block mb-3">
                        📅 Dia: {{ $dia }}
                    </h2>
                    
                    <div class="grid gap-4">
                        @foreach($itens as $agenda)
                            <div class="bg-white p-6 shadow rounded-xl border-l-4 border-pink-500">
                                
                                {{-- AVISO DE FIDELIDADE --}}
                                @if($agenda->cliente->contador_fidelidade == 5)
                                    <span class="bg-yellow-100 text-yellow-800 text-xs font-bold px-2 py-1 rounded border border-yellow-300">
                                        🎁 PRÓXIMO SERVIÇO COM 50% OFF!
                                    </span>
                                @else
                                    <span class="bg-blue-100 text-blue-800 text-xs font-bold px-2 py-1 rounded">
                                        Fidelidade do Cliente: {{ $agenda->cliente->contador_fidelidade }}/5
                                    </span>
                                @endif

                                <div class="flex justify-between items-center mb-4 mt-2">
                                    <div>
                                        <span class="text-lg font-black text-gray-700">
                                            {{ \Carbon\Carbon::parse($agenda->data_hora_inicio)->format('H:i') }}
                                        </span>
                                        <span class="ml-4 font-bold text-gray-600">{{ $agenda->servico->nome }}</span>
                                        <p class="text-sm text-gray-400">Cliente: {{ $agenda->cliente->name }}</p>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-xs font-bold uppercase p-1 bg-gray-100 rounded border border-gray-200">
                                            Status: {{ $agenda->status }}
                                        </span>
                                    </div>
                                </div>

                                {{-- LÓGICA DE BOTÕES --}}
                                <div class="mt-4 pt-4 border-t border-gray-100">
                                    
                                    {{-- PASSO 1: Marcar Presença --}}
                                    @if($agenda->status == 'confirmado' || $agenda->status == 'pendente')
                                        <div class="flex gap-4 items-center">
                                            <form action="{{ route('agendamentos.falta', $agenda->id_agendamento) }}" method="POST" class="w-1/3">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="w-full text-red-600 border border-red-600 hover:bg-red-50 font-bold py-2 px-4 rounded-lg transition">Marcar Falta</button>
                                            </form>
                                            <form action="{{ route('agendamento.presenca', $agenda->id_agendamento) }}" method="POST" class="w-2/3">
                                                @csrf
                                                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition">
                                                    📍 Confirmar Presença (Check-in)
                                                </button>
                                            </form>
                                        </div>

                                    {{-- PASSO 2: Finalizar com Obs e Produto --}}
                                    @elseif($agenda->status == 'presente')
                                        <form action="{{ route('profissional.agendamento.executado', $agenda->id_agendamento) }}" method="POST" class="space-y-4">
                                            @csrf
                                            
                                            {{-- Campo de Observações --}}
                                            <div>
                                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Observações do Atendimento:</label>
                                                <textarea name="observacao" rows="2" class="w-full rounded-md border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 text-sm" placeholder="Ex: Cabelo seco, usado shampoo hidratante..."></textarea>
                                            </div>

                                            {{-- Seleção de Produto para Venda --}}
                                            <div class="bg-white p-4 rounded border">
                                                <h4 class="font-bold text-sm mb-2">Produtos Vendidos (Opcional)</h4>
                                                
                                                <div id="lista-produtos">
                                                    <div class="flex gap-2 mb-2 items-center">
                                                        <select name="produtos[0][id]" class="rounded text-sm border-gray-300 flex-1">
                                                            <option value="">Selecione um produto...</option>
                                                            @foreach($produtos as $p)
                                                                <option value="{{ $p->id_produto }}">{{ $p->nome }} (Disp: {{ $p->quantidade_estoque }})</option>
                                                            @endforeach
                                                        </select>
                                                        <input type="number" name="produtos[0][quantidade]" value="1" min="1" class="w-20 rounded text-sm border-gray-300">
                                                    </div>
                                                </div>

                                                <button type="button" onclick="addProduto()" class="text-xs bg-gray-200 px-2 py-1 rounded hover:bg-gray-300">
                                                    + Adicionar outro produto
                                                </button>
                                            </div>

                                            {{-- Resumo Financeiro --}}
                                            @php
                                                // Busca a comissão específica deste profissional para ESTE serviço na tabela pivot
                                                $pivot = \Illuminate\Support\Facades\DB::table('profissional_servico')
                                                            ->where('profissional_id', auth()->id())
                                                            ->where('servico_id', $agenda->servico_id)
                                                            ->first();

                                                $porcentagemComissao = $pivot ? $pivot->comissao_percentual : 50; 
                                                $taxaMatematica = $porcentagemComissao / 100;
                                                
                                                $valorServico = $agenda->servico->preco ?? 0; 
                                                $valorReceber = $valorServico * $taxaMatematica;
                                            @endphp

                                            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                                                <h4 class="text-sm font-bold text-gray-700 mb-2 uppercase tracking-wide">Resumo Financeiro</h4>
                                                
                                                <div class="flex justify-between text-sm mb-1">
                                                    <span class="text-gray-600">Valor do Serviço:</span>
                                                    <span class="font-semibold text-gray-900">R$ {{ number_format($valorServico, 2, ',', '.') }}</span>
                                                </div>

                                                <div class="border-t border-gray-200 my-2"></div>

                                                <div class="flex justify-between items-center text-sm">
                                                    <span class="text-pink-600 font-bold">Sua Comissão ({{ $porcentagemComissao }}%):</span>
                                                    <span class="font-bold text-lg text-green-600">
                                                        + R$ {{ number_format($valorReceber, 2, ',', '.') }}
                                                    </span>
                                                </div>
                                                
                                                <p class="text-xs text-gray-400 mt-2 italic">
                                                    *Se adicionar produtos, ganhará mais comissão!
                                                </p>
                                            </div>
                                            
                                            {{-- LÓGICA DO PACOTE VERIFICANDO DINAMICAMENTE PARA ESTE CLIENTE DA LISTA --}}
                                            @php
                                                $pacoteDisponivel = $agenda->cliente->pacotesAtivos->firstWhere('pacote.servico_id', $agenda->servico_id);
                                            @endphp

                                            @if($pacoteDisponivel)
                                                <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6 rounded-r shadow-sm">
                                                    <div class="flex items-center">
                                                        <svg class="h-6 w-6 text-blue-500 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                        <div>
                                                            <h3 class="text-lg font-bold text-blue-800">Pacote Disponível!</h3>
                                                            <p class="text-sm text-blue-700">Este cliente possui <strong>{{ $pacoteDisponivel->sessoes_restantes }} sessões</strong> do pacote "{{ $pacoteDisponivel->pacote->nome }}".</p>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="mt-4 ml-9">
                                                        <label class="inline-flex items-center cursor-pointer">
                                                            <input type="checkbox" name="usar_pacote" value="{{ $pacoteDisponivel->id }}" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 h-5 w-5">
                                                            <span class="ml-2 text-gray-700 font-semibold text-md">Abater 1 sessão deste pacote (O valor do serviço será R$ 0,00)</span>
                                                        </label>
                                                    </div>
                                                </div>
                                            @endif

                                            <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-4 rounded-lg shadow-lg transition">
                                                ✅ Finalizar e Baixar Estoque
                                            </button>
                                        </form>
                                    @else
                                        <p class="text-center text-gray-400 italic text-sm">Atendimento finalizado.</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @empty
                <div class="bg-white p-8 text-center rounded-xl shadow">
                    <p class="text-gray-500">Você não tem agendamentos marcados.</p>
                </div>
            @endforelse
        </div>
    </div>  
</x-app-layout>

<script>
    let produtoIndex = 1;
    function addProduto() {
        const container = document.getElementById('lista-produtos');
        const novoItem = container.firstElementChild.cloneNode(true);
        
        novoItem.querySelector('select').name = `produtos[${produtoIndex}][id]`;
        novoItem.querySelector('input').name = `produtos[${produtoIndex}][quantidade]`;
        novoItem.querySelector('input').value = 1;
        
        container.appendChild(novoItem);
        produtoIndex++;
    }
</script>