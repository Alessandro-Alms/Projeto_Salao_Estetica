<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <span class="text-[#7B19E5] text-xl">✧</span>
            <h2 class="font-title text-xl text-[#1A002B]">
                {{ __('Calendário de Agendamentos') }}
            </h2>
        </div>
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
            @if(session('status'))
                <div class="mb-4 text-sm text-green-600 bg-green-50/80 backdrop-blur-sm p-4 rounded-xl border border-green-200 shadow-sm">
                    <strong>✧ Sucesso!</strong> {{ session('status') }}
                </div>
            @endif

            <!-- Bloco de filtro e botão novo -->
            <div class="glass-card rounded-2xl shadow-xl overflow-hidden mb-6">
                <div class="p-4 bg-white/70 backdrop-blur-sm border border-white/40 flex flex-col lg:flex-row justify-between items-center gap-4">
                    
                    <form method="GET" class="flex flex-col md:flex-row gap-4 w-full lg:flex-1">
                        <div>
                            <label class="block text-sm font-medium text-[#4A00B9] mb-1">Data Início</label>
                            <input type="date" name="data_inicio" value="{{ $dataInicio }}" 
                                class="px-4 py-2.5 bg-white/50 border border-[#FFD6F4] rounded-lg focus:outline-none focus:border-[#7B19E5] focus:ring-2 focus:ring-[#7B19E5]/20 transition-all" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-[#4A00B9] mb-1">Data Fim</label>
                            <input type="date" name="data_fim" value="{{ $dataFim }}" 
                                class="px-4 py-2.5 bg-white/50 border border-[#FFD6F4] rounded-lg focus:outline-none focus:border-[#7B19E5] focus:ring-2 focus:ring-[#7B19E5]/20 transition-all" />
                        </div>
                        <div class="flex gap-2 items-end">
                            <button type="submit" class="bg-gradient-to-r from-[#7B19E5] to-[#FF2EB6] text-white px-6 py-2.5 text-sm rounded-lg font-medium btn-primary shadow-md hover:shadow-lg transition-all">
                                Filtrar
                            </button>
                        </div>
                    </form>

                    <div class="w-full lg:w-auto flex justify-end shrink-0 border-t lg:border-t-0 lg:border-l border-[#FFD6F4] pt-4 lg:pt-0 lg:pl-4">
                        <a href="{{ route('admin.agendar.cliente') }}" class="flex items-center gap-2 bg-white text-[#7B19E5] border-2 border-[#7B19E5] px-6 py-2.5 rounded-full font-bold hover:bg-[#7B19E5] hover:text-white transition-all shadow-sm whitespace-nowrap">
                            <span>+</span> Novo Agendamento
                        </a>
                    </div>

                </div>
            </div>

            <!-- ESTATÍSTICAS -->
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
                <div class="glass-card p-4 bg-blue-50/70 border border-blue-200 rounded-xl">
                    <p class="text-gray-600 text-sm">Total</p>
                    <p class="text-2xl font-bold text-blue-600">{{ $totalAgendamentos }}</p>
                </div>
                <div class="glass-card p-4 bg-green-50/70 border border-green-200 rounded-xl">
                    <p class="text-gray-600 text-sm"> Executados</p>
                    <p class="text-2xl font-bold text-green-600">{{ $executados }}</p>
                </div>
                <div class="glass-card p-4 bg-yellow-50/70 border border-yellow-200 rounded-xl">
                    <p class="text-gray-600 text-sm"> Confirmados</p>
                    <p class="text-2xl font-bold text-yellow-600">{{ $confirmados }}</p>
                </div>
                <div class="glass-card p-4 bg-red-50/70 border border-red-200 rounded-xl">
                    <p class="text-gray-600 text-sm"> Cancelados</p>
                    <p class="text-2xl font-bold text-red-600">{{ $cancelados }}</p>
                </div>
                <div class="glass-card p-4 bg-purple-50/70 border border-purple-200 rounded-xl">
                    <p class="text-gray-600 text-sm"> Faltas</p>
                    <p class="text-2xl font-bold text-purple-600">{{ $faltas }}</p>
                </div>
            </div>

            <!-- CONTEÚDO PRINCIPAL: ABAS -->
            <div class="glass-card rounded-2xl shadow-xl overflow-hidden">
                <div class="p-0 bg-white/70 backdrop-blur-sm border border-white/40">
                    <!-- ABAS -->
                    <div class="flex border-b border-[#FFD6F4]">
                        <button onclick="mostrarAba('calendario')" id="aba-calendario" class="aba-btn flex-1 py-4 px-6 text-center font-bold text-[#4A00B9] border-b-2 border-[#7B19E5] bg-white/50 transition">
                            ✧ Calendário
                        </button>
                        <button onclick="mostrarAba('lista')" id="aba-lista" class="aba-btn flex-1 py-4 px-6 text-center font-bold text-gray-600 hover:text-[#4A00B9] transition">
                            ✦ Lista de Agendamentos
                        </button>
                    </div>

                    <!-- CONTEÚDO DAS ABAS -->
                    <div class="p-6">
                        <!-- ABA 1: CALENDÁRIO -->
                        <div id="conteudo-calendario" class="aba-conteudo">
                            <div class="space-y-4">
                                @forelse($agendamentosPorData as $data => $agendamentosData)
                                    <div class="bg-white/50 p-4 rounded-lg border border-[#FFD6F4]">
                                        <h3 class="text-lg font-bold text-[#4A00B9] mb-3">
                                            ✧ {{ \Carbon\Carbon::parse($data)->format('d/m/Y - l') }}
                                        </h3>
                                        <div class="space-y-2">
                                            @foreach($agendamentosData as $agendamento)
                                                <div class="flex gap-4 p-3 bg-white/70 rounded-lg border-l-4 
                                                    @if($agendamento->status == 'executado') border-green-500
                                                    @elseif($agendamento->status == 'confirmado') border-yellow-500
                                                    @elseif($agendamento->status == 'cancelado') border-red-500
                                                    @else border-purple-500 @endif">
                                                    
                                                    <div class="flex-1">
                                                        <p class="font-bold text-[#4A00B9]">
                                                            ✧ {{ $agendamento->data_hora_inicio->format('H:i') }} - {{ $agendamento->servico->nome }}
                                                        </p>
                                                        <p class="text-sm text-gray-600">✦ {{ $agendamento->cliente->name }}</p>
                                                        <p class="text-sm text-gray-600">✧ {{ $agendamento->profissional->name }}</p>
                                                    </div>
                                                    
                                                    <div class="flex flex-col gap-2">
                                                        <span class="px-3 py-1 text-xs font-bold rounded-full 
                                                            @if($agendamento->status == 'executado') bg-green-100 text-green-700
                                                            @elseif($agendamento->status == 'confirmado') bg-yellow-100 text-yellow-700
                                                            @elseif($agendamento->status == 'cancelado') bg-red-100 text-red-700
                                                            @else bg-purple-100 text-purple-700 @endif">
                                                            {{ ucfirst($agendamento->status) }}
                                                        </span>
                                                        <span class="text-sm font-bold text-[#4A00B9]">R$ {{ number_format($agendamento->valor_total, 2, ',', '.') }}</span>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-12">
                                        <p class="text-gray-500">✧ Nenhum agendamento no período selecionado</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        <!-- ABA 2: LISTA DE AGENDAMENTOS -->
                        <div id="conteudo-lista" class="aba-conteudo hidden">
                            <div class="overflow-x-auto">
                                <table class="min-w-full">
                                    <thead>
                                        <tr class="border-b border-[#FFD6F4] bg-white/50">
                                            <th class="px-6 py-4 text-left text-xs font-medium text-[#4A00B9] uppercase tracking-wider">Data/Hora</th>
                                            <th class="px-6 py-4 text-left text-xs font-medium text-[#4A00B9] uppercase tracking-wider">Cliente</th>
                                            <th class="px-6 py-4 text-left text-xs font-medium text-[#4A00B9] uppercase tracking-wider">Profissional</th>
                                            <th class="px-6 py-4 text-left text-xs font-medium text-[#4A00B9] uppercase tracking-wider">Serviço</th>
                                            <th class="px-6 py-4 text-left text-xs font-medium text-[#4A00B9] uppercase tracking-wider">Valor</th>
                                            <th class="px-6 py-4 text-left text-xs font-medium text-[#4A00B9] uppercase tracking-wider">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-[#FFD6F4]">
                                        @forelse($agendamentos as $agendamento)
                                            <tr class="hover:bg-white/50 transition-colors">
                                                <td class="px-6 py-4 text-sm text-gray-500">{{ $agendamento->data_hora_inicio->format('d/m/Y H:i') }}</td>
                                                <td class="px-6 py-4 text-sm text-[#1A002B]">{{ $agendamento->cliente->name }}</td>
                                                <td class="px-6 py-4 text-sm text-[#1A002B]">{{ $agendamento->profissional->name }}</td>
                                                <td class="px-6 py-4 text-sm text-[#1A002B]">{{ $agendamento->servico->nome }}</td>
                                                <td class="px-6 py-4 text-sm font-medium text-[#7B19E5]">R$ {{ number_format($agendamento->valor_total, 2, ',', '.') }}</td>
                                                <td class="px-6 py-4">
                                                    <span class="px-3 py-1 text-xs font-bold rounded-full 
                                                        @if($agendamento->status == 'executado') bg-green-100 text-green-700
                                                        @elseif($agendamento->status == 'confirmado') bg-yellow-100 text-yellow-700
                                                        @elseif($agendamento->status == 'cancelado') bg-red-100 text-red-700
                                                        @else bg-purple-100 text-purple-700 @endif">
                                                        {{ ucfirst($agendamento->status) }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                                    ✧ Nenhum agendamento no período selecionado
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function mostrarAba(aba) {
            document.querySelectorAll('.aba-conteudo').forEach(el => el.classList.add('hidden'));
            document.querySelectorAll('.aba-btn').forEach(el => {
                el.classList.remove('border-b-2', 'border-[#7B19E5]', 'bg-white/50');
                el.classList.add('text-gray-600');
            });

            document.getElementById('conteudo-' + aba).classList.remove('hidden');
            document.getElementById('aba-' + aba).classList.add('border-b-2', 'border-[#7B19E5]', 'bg-white/50', 'text-[#4A00B9]');
            document.getElementById('aba-' + aba).classList.remove('text-gray-600');
        }
    </script>
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
