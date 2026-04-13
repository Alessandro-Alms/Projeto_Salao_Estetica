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
                                <div class="flex justify-between items-center mb-4">
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

                                {{-- LÓGICA DE BOTÕES DA DEMANDA 5 --}}
                                <div class="mt-4 pt-4 border-t border-gray-100">
                                    
                                    {{-- PASSO 1: Marcar Presença (UC005) --}}
                                    @if($agenda->status == 'confirmado' || $agenda->status == 'pendente')
                                        <form action="{{ route('agendamentos.falta', $agendamento->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="text-red-600 font-bold">Marcar Falta</button>
                                        </form>
                                        <form action="{{ route('agendamento.presenca', $agenda->id_agendamento) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition">
                                                📍 Confirmar Presença (Check-in)
                                            </button>
                                        </form>

                                    {{-- PASSO 2: Finalizar com Obs e Produto (UC006 e UC007) --}}
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

                                            <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-4 rounded-lg shadow-lg transition">
                                                ✅ Finalizar e Baixar Estoque
                                            </button>
                                        </form>
                                    @else
                                        <p class="text-center text-gray-400 italic text-sm">Atendimento já finalizado.</p>
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
        
        // Atualiza os nomes para produtos[1][id], produtos[2][id]...
        novoItem.querySelector('select').name = `produtos[${produtoIndex}][id]`;
        novoItem.querySelector('input').name = `produtos[${produtoIndex}][quantidade]`;
        novoItem.querySelector('input').value = 1;
        
        container.appendChild(novoItem);
        produtoIndex++;
    }
</script>