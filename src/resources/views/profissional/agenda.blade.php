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
            <div class="flex items-center gap-2 mb-6">
                <span class="text-[#7B19E5] text-2xl">✧</span>
                <h1 class="text-2xl font-title text-[#4A00B9]">Minha Agenda de Atendimentos</h1>
            </div>

            @if(session('status'))
                <div class="mb-6 p-4 rounded-lg bg-green-50/80 border border-green-200 text-green-700">
                    ✧ {{ session('status') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 p-4 rounded-lg bg-red-50/80 border border-red-200 text-red-700">
                    ✧ {{ session('error') }}
                </div>
            @endif

            <!-- FILTRO DE PERÍODO -->
            <div class="mb-6 flex gap-3 flex-wrap">
                <form method="GET" action="{{ route('profissional.agenda') }}" class="flex gap-2">
                    <input type="hidden" name="filtro" id="filtro_input" value="{{ request('filtro', '7') }}">
                    
                    <button type="button" onclick="filtrarAgendamentos(7)" 
                            class="px-4 py-2 rounded-full text-sm font-medium transition-all {{ request('filtro', '7') == '7' ? 'bg-gradient-to-r from-[#7B19E5] to-[#FF2EB6] text-white shadow-md' : 'bg-white/70 border border-[#FFD6F4] text-[#4A00B9] hover:bg-gradient-to-r hover:from-[#7B19E5] hover:to-[#FF2EB6] hover:text-white' }}">
                        ✧ Próximos 7 dias
                    </button>

                    <button type="button" onclick="filtrarAgendamentos(30)" 
                            class="px-4 py-2 rounded-full text-sm font-medium transition-all {{ request('filtro') == '30' ? 'bg-gradient-to-r from-[#7B19E5] to-[#FF2EB6] text-white shadow-md' : 'bg-white/70 border border-[#FFD6F4] text-[#4A00B9] hover:bg-gradient-to-r hover:from-[#7B19E5] hover:to-[#FF2EB6] hover:text-white' }}">
                        ✦ Próximos 30 dias
                    </button>

                    <button type="button" onclick="filtrarAgendamentos('todos')" 
                            class="px-4 py-2 rounded-full text-sm font-medium transition-all {{ request('filtro') == 'todos' ? 'bg-gradient-to-r from-[#7B19E5] to-[#FF2EB6] text-white shadow-md' : 'bg-white/70 border border-[#FFD6F4] text-[#4A00B9] hover:bg-gradient-to-r hover:from-[#7B19E5] hover:to-[#FF2EB6] hover:text-white' }}">
                        ✧ Todos os agendamentos
                    </button>
                </form>
            </div>

            @forelse($agendamentos as $dia => $itens)
                <div class="mb-8">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="text-[#FF2EB6] text-xl">✦</span>
                        <h2 class="text-lg font-title text-[#4A00B9]">
                            {{ $dia }}
                        </h2>
                    </div>
                    
                    <div class="grid gap-4">
                        @foreach($itens as $agenda)
                            <div class="glass-card rounded-2xl shadow-xl overflow-hidden hover-lift">
                                <div class="p-6 bg-white/70 backdrop-blur-sm border border-white/40">
                                    
                                    @if($agenda->cliente->contador_fidelidade == 5)
                                        <div class="mb-4 inline-block px-3 py-1 text-xs font-semibold rounded-full bg-gradient-to-r from-yellow-500/20 to-orange-500/20 text-yellow-700 border border-yellow-200">
                                            ✧ PRÓXIMO SERVIÇO COM 50% OFF!
                                        </div>
                                    @else
                                        <div class="mb-4 inline-block px-3 py-1 text-xs font-semibold rounded-full bg-blue-500/20 text-blue-700 border border-blue-200">
                                            ✧ Fidelidade: {{ $agenda->cliente->contador_fidelidade }}/5
                                        </div>
                                    @endif

                                    <div class="flex flex-wrap justify-between items-start mb-4">
                                        <div>
                                            <span class="text-xl font-title text-[#7B19E5]">
                                                {{ \Carbon\Carbon::parse($agenda->data_hora_inicio)->format('H:i') }}
                                            </span>
                                            <span class="ml-4 font-bold text-[#1A002B]">{{ $agenda->servico->nome }}</span>
                                            <p class="text-sm text-gray-500 mt-1">Cliente: {{ $agenda->cliente->name }}</p>
                                            @if(($agenda->acrescimo_especial ?? 0) > 0)
                                                <div class="mt-2 inline-flex flex-wrap items-center gap-2 rounded-xl border border-yellow-200 bg-yellow-50 px-3 py-2 text-xs font-semibold text-yellow-800">
                                                    <span>Atendimento com acréscimo:</span>
                                                    <span>{{ $agenda->motivo_acrescimo }}</span>
                                                    <span class="text-green-700">+ R$ {{ number_format($agenda->acrescimo_especial, 2, ',', '.') }}</span>
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <span class="text-xs font-bold uppercase px-3 py-1 rounded-full bg-gray-100/80 text-gray-600 border border-gray-200">
                                                Status: {{ $agenda->status }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="mt-4 pt-4 border-t border-[#FFD6F4]">
                                        
                                        @if($agenda->status == 'confirmado' || $agenda->status == 'pendente')
                                            <div class="flex flex-col sm:flex-row gap-3">
                                                <form action="{{ route('agendamentos.falta', $agenda->id_agendamento) }}" method="POST" class="flex-1">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="w-full text-[#FF2EB6] border border-[#FF2EB6] hover:bg-[#FF2EB6] hover:text-white font-medium py-2 px-4 rounded-full transition text-sm">
                                                        ✧ Marcar falta
                                                    </button>
                                                </form>
                                                <form action="{{ route('agendamento.presenca', $agenda->id_agendamento) }}" method="POST" class="flex-1">
                                                    @csrf
                                                    <button type="submit" class="w-full bg-gradient-to-r from-[#7B19E5] to-[#A855F7] hover:from-[#FF2EB6] hover:to-[#FF69B4] text-white font-medium py-2 px-4 rounded-full transition text-sm">
                                                        ✧ Confirmar presença
                                                    </button>
                                                </form>
                                            </div>

                                        @elseif($agenda->status == 'presente')
                                            <form action="{{ route('profissional.agendamento.executado', $agenda->id_agendamento) }}" method="POST" class="space-y-4">
                                                @csrf
                                                
                                                <div>
                                                    <label class="block text-xs font-medium text-[#4A00B9] uppercase mb-1">Observações do Atendimento:</label>
                                                    <textarea name="observacao" rows="2" class="w-full px-4 py-3 bg-white/50 border border-[#FFD6F4] rounded-lg focus:outline-none focus:border-[#7B19E5] focus:ring-2 focus:ring-[#7B19E5]/20 transition-all" placeholder="Ex: Cabelo seco, usado shampoo hidratante..."></textarea>
                                                </div>

                                                <div class="bg-white/50 rounded-xl p-4 border border-[#FFD6F4]">
                                                    <h4 class="font-title text-[#4A00B9] text-sm mb-2">Produtos Vendidos (Opcional)</h4>
                                                    
                                                    <div id="lista-produtos">
                                                        <div class="flex gap-2 mb-2 items-center">
                                                            <select name="produtos[0][id]" data-searchable-select data-searchable-compact="true" data-searchable-placeholder="Digite o nome do produto..." class="flex-1 px-4 py-2 bg-white/50 border border-[#FFD6F4] rounded-lg focus:outline-none focus:border-[#7B19E5] focus:ring-2 focus:ring-[#7B19E5]/20 transition-all text-sm">
                                                                <option value="">Selecione um produto...</option>
                                                                @foreach($produtos as $p)
                                                                    <option value="{{ $p->id_produto }}">{{ $p->nome }} (Disp: {{ $p->quantidade_estoque }})</option>
                                                                @endforeach
                                                            </select>
                                                            <input type="number" name="produtos[0][quantidade]" value="1" min="1" class="w-20 px-3 py-2 bg-white/50 border border-[#FFD6F4] rounded-lg focus:outline-none focus:border-[#7B19E5] focus:ring-2 focus:ring-[#7B19E5]/20 transition-all text-sm">
                                                        </div>
                                                    </div>

                                                    <button type="button" onclick="addProduto()" class="text-xs bg-[#FF2EB6]/20 text-[#FF2EB6] px-3 py-1 rounded-full hover:bg-[#FF2EB6] hover:text-white transition">
                                                        + Adicionar outro produto
                                                    </button>
                                                </div>

                                                @php
                                                    $porcentagemComissao = 50;
                                                    $taxaMatematica = $porcentagemComissao / 100;
                                                    $valorBaseServico = $agenda->valor_base ?? ($agenda->servico->preco ?? 0);
                                                    $acrescimoServico = $agenda->acrescimo_especial ?? 0;
                                                    $valorServico = $valorBaseServico + $acrescimoServico;
                                                    $valorReceber = $valorServico * $taxaMatematica;
                                                @endphp

                                                <div class="bg-white/50 rounded-xl p-4 border border-[#FFD6F4]">
                                                    <h4 class="text-sm font-title text-[#4A00B9] mb-2">Resumo Financeiro</h4>
                                                    
                                                    <div class="flex justify-between text-sm mb-1">
                                                        <span class="text-gray-600">Valor base do serviço:</span>
                                                        <span class="font-semibold text-[#1A002B]">R$ {{ number_format($valorBaseServico, 2, ',', '.') }}</span>
                                                    </div>

                                                    @if($acrescimoServico > 0)
                                                        <div class="flex justify-between text-sm mb-1">
                                                            <span class="text-yellow-700 font-semibold">Acréscimo:</span>
                                                            <span class="font-semibold text-yellow-700">+ R$ {{ number_format($acrescimoServico, 2, ',', '.') }}</span>
                                                        </div>
                                                        <p class="text-xs text-yellow-700 mb-2">
                                                            {{ $agenda->motivo_acrescimo }}. Sua comissão também considera este valor maior.
                                                        </p>
                                                    @endif

                                                    <div class="border-t border-[#FFD6F4] my-2"></div>

                                                    <div class="flex justify-between items-center text-sm">
                                                        <span class="text-[#7B19E5] font-bold">Sua Comissão ({{ $porcentagemComissao }}%):</span>
                                                        <span class="font-bold text-lg text-green-600">
                                                            + R$ {{ number_format($valorReceber, 2, ',', '.') }}
                                                        </span>
                                                    </div>
                                                    
                                                    <p class="text-xs text-gray-400 mt-2 italic">
                                                        *Se adicionar produtos, ganhará mais comissão!
                                                    </p>
                                                </div>
                                                
                                                @php
                                                    $pacoteDisponivel = $agenda->cliente->pacotesAtivos->first(function ($clientePacote) use ($agenda) {
                                                        return $clientePacote->pacote && $clientePacote->pacote->aceitaServico((int) $agenda->servico_id);
                                                    });
                                                @endphp

                                                @if($pacoteDisponivel)
                                                    <div class="bg-gradient-to-r from-blue-500/10 to-blue-600/10 rounded-xl p-4 border border-blue-200">
                                                        <div class="flex items-center gap-3">
                                                            <span class="text-2xl">✧</span>
                                                            <div>
                                                                <h3 class="text-md font-title text-blue-700">Pacote Disponível!</h3>
                                                                <p class="text-sm text-blue-600">Este cliente possui <strong>{{ $pacoteDisponivel->sessoes_restantes }} sessões</strong> do pacote "{{ $pacoteDisponivel->pacote->nome }}".</p>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="mt-3 ml-10">
                                                            <label class="inline-flex items-center gap-2 cursor-pointer">
                                                                <input type="checkbox" name="usar_pacote" value="{{ $pacoteDisponivel->id }}" class="w-4 h-4 text-[#7B19E5] rounded border-[#FFD6F4] focus:ring-[#7B19E5]">
                                                                <span class="text-sm text-gray-700">Abater 1 sessão deste pacote (O valor do serviço será R$ 0,00)</span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                @endif

                                                <div class="bg-white/50 rounded-xl p-4 border border-[#FFD6F4]">
                                                    <label class="block text-xs font-medium text-[#4A00B9] uppercase mb-2">Forma de pagamento do serviço</label>
                                                    <select name="forma_pagamento" data-forma-pagamento required class="w-full px-4 py-3 bg-white/70 border border-[#FFD6F4] rounded-lg focus:outline-none focus:border-[#7B19E5] focus:ring-2 focus:ring-[#7B19E5]/20 transition-all">
                                                        <option value="">Selecione como foi pago...</option>
                                                        <option value="dinheiro">Dinheiro</option>
                                                        <option value="pix">PIX</option>
                                                        <option value="cartao_debito">Cartão de débito</option>
                                                        <option value="cartao_credito">Cartão de crédito</option>
                                                    </select>
                                                    <p class="text-xs text-gray-500 mt-2">O valor do serviço só entra no financeiro depois desta seleção.</p>
                                                </div>

                                                <button type="submit" class="w-full bg-gradient-to-r from-green-500 to-green-600 text-white font-bold py-3 px-4 rounded-full shadow-lg hover:shadow-xl transition-all">
                                                    ✧ Finalizar e Baixar Estoque
                                                </button>
                                            </form>
                                        @else
                                            <p class="text-center text-gray-400 italic text-sm">✧ Atendimento finalizado.</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @empty
                <div class="glass-card rounded-2xl shadow-xl overflow-hidden">
                    <div class="p-10 bg-white/70 backdrop-blur-sm border border-white/40 text-center">
                        <p class="text-gray-500">✧ Você não tem agendamentos marcados.</p>
                    </div>
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
        novoItem.querySelector('[data-searchable-wrapper]')?.remove();
        
        const select = novoItem.querySelector('select');
        select.name = `produtos[${produtoIndex}][id]`;
        select.value = '';
        select.classList.remove('hidden');
        select.dataset.searchableReady = 'false';
        novoItem.querySelector('input').name = `produtos[${produtoIndex}][quantidade]`;
        novoItem.querySelector('input').value = 1;
        
        container.appendChild(novoItem);
        window.iniciarSelectsPesquisaveis?.(novoItem);
        produtoIndex++;
    }

    function filtrarAgendamentos(periodo) {
        const url = new URL(window.location);
        if (periodo === 'todos') {
            url.searchParams.set('filtro', 'todos');
        } else {
            url.searchParams.set('filtro', periodo);
        }
        window.location.href = url.toString();
    }

    document.addEventListener('change', (event) => {
        if (!event.target.matches('input[name="usar_pacote"]')) {
            return;
        }

        const form = event.target.closest('form');
        const select = form?.querySelector('[data-forma-pagamento]');

        if (!select) {
            return;
        }

        select.required = !event.target.checked;
        select.disabled = event.target.checked;
        if (event.target.checked) {
            select.value = '';
        }
    });
</script>

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
    
    .hover-lift {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .hover-lift:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 25px -12px rgba(123, 25, 229, 0.25);
    }
</style>
