<x-app-layout>
    <x-slot name="header">
        <div class="hidden"></div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- HEADER -->
            <div class="glass-card rounded-2xl shadow-xl overflow-hidden mb-6">
                <div class="p-8 bg-white/70 backdrop-blur-sm border border-white/40">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-[#7B19E5] to-[#FF2EB6] rounded-xl flex items-center justify-center shadow-md">
                                <span class="text-white text-lg">📅</span>
                            </div>
                            <h1 class="text-3xl font-title text-[#4A00B9]">Calendário de Agendamentos</h1>
                        </div>
                        <a href="{{ route('admin.agendar.cliente') }}" class="px-6 py-3 bg-gradient-to-r from-[#7B19E5] to-[#A855F7] text-white rounded-lg font-bold hover:shadow-lg transition-all inline-block">
                            ➕ Novo Agendamento
                        </a>
                    </div>

                    @if(session('status'))
                        <div class="mb-6 p-4 rounded-lg bg-green-50/80 border border-green-200 text-green-700">
                            ✧ {{ session('status') }}
                        </div>
                    @endif

                    <!-- FILTRO DE DATAS -->
                    <form method="GET" class="flex gap-3 flex-wrap items-end">
                        <div>
                            <label class="block text-sm font-medium text-[#4A00B9] mb-2">📅 Data Início</label>
                            <input type="date" name="data_inicio" value="{{ $dataInicio }}" 
                                class="px-4 py-2 bg-white/50 border border-[#FFD6F4] rounded-lg focus:outline-none focus:border-[#7B19E5]">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-[#4A00B9] mb-2">📅 Data Fim</label>
                            <input type="date" name="data_fim" value="{{ $dataFim }}" 
                                class="px-4 py-2 bg-white/50 border border-[#FFD6F4] rounded-lg focus:outline-none focus:border-[#7B19E5]">
                        </div>
                        <button type="submit" class="px-6 py-2 bg-[#7B19E5] text-white rounded-lg font-bold hover:bg-[#6012c8] transition">
                            🔍 Filtrar
                        </button>
                    </form>
                </div>
            </div>

            <!-- ESTATÍSTICAS -->
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
                <div class="glass-card p-4 bg-blue-50/70 border border-blue-200">
                    <p class="text-gray-600 text-sm">Total</p>
                    <p class="text-3xl font-bold text-blue-600">{{ $totalAgendamentos }}</p>
                </div>
                <div class="glass-card p-4 bg-green-50/70 border border-green-200">
                    <p class="text-gray-600 text-sm">✓ Executados</p>
                    <p class="text-3xl font-bold text-green-600">{{ $executados }}</p>
                </div>
                <div class="glass-card p-4 bg-yellow-50/70 border border-yellow-200">
                    <p class="text-gray-600 text-sm">⏳ Confirmados</p>
                    <p class="text-3xl font-bold text-yellow-600">{{ $confirmados }}</p>
                </div>
                <div class="glass-card p-4 bg-red-50/70 border border-red-200">
                    <p class="text-gray-600 text-sm">✗ Cancelados</p>
                    <p class="text-3xl font-bold text-red-600">{{ $cancelados }}</p>
                </div>
                <div class="glass-card p-4 bg-purple-50/70 border border-purple-200">
                    <p class="text-gray-600 text-sm">❌ Faltas</p>
                    <p class="text-3xl font-bold text-purple-600">{{ $faltas }}</p>
                </div>
            </div>

            <!-- CONTEÚDO PRINCIPAL: ABAS -->
            <div class="glass-card rounded-2xl shadow-xl overflow-hidden">
                <div class="bg-white/70 backdrop-blur-sm border border-white/40">
                    <!-- ABAS -->
                    <div class="flex border-b border-[#FFD6F4]">
                        <button onclick="mostrarAba('calendario')" id="aba-calendario" class="aba-btn flex-1 py-4 px-6 text-center font-bold text-[#4A00B9] border-b-2 border-[#7B19E5] bg-white/50 transition">
                            📅 Calendário
                        </button>
                        <button onclick="mostrarAba('lista')" id="aba-lista" class="aba-btn flex-1 py-4 px-6 text-center font-bold text-gray-600 hover:text-[#4A00B9] transition">
                            📋 Lista de Agendamentos
                        </button>
                    </div>

                    <!-- CONTEÚDO DAS ABAS -->
                    <div class="p-8">
                        <!-- ABA 1: CALENDÁRIO -->
                        <div id="conteudo-calendario" class="aba-conteudo">
                            <div class="space-y-4">
                                @forelse($agendamentosPorData as $data => $agendamentosData)
                                    <div class="bg-white/50 p-4 rounded-lg border border-[#FFD6F4]">
                                        <h3 class="text-lg font-bold text-[#4A00B9] mb-3">
                                            📅 {{ \Carbon\Carbon::parse($data)->format('d/m/Y - l') }}
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
                                                            🕒 {{ $agendamento->data_hora_inicio->format('H:i') }} - {{ $agendamento->servico->nome }}
                                                        </p>
                                                        <p class="text-sm text-gray-600">👤 {{ $agendamento->cliente->name }}</p>
                                                        <p class="text-sm text-gray-600">💼 {{ $agendamento->profissional->name }}</p>
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
                                        <p class="text-gray-500 text-lg">📭 Nenhum agendamento no período selecionado</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        <!-- ABA 2: LISTA DE AGENDAMENTOS -->
                        <div id="conteudo-lista" class="aba-conteudo hidden">
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm">
                                    <thead class="bg-white/50 border-b border-[#FFD6F4]">
                                        <tr>
                                            <th class="px-6 py-3 text-left font-bold text-[#4A00B9]">Data/Hora</th>
                                            <th class="px-6 py-3 text-left font-bold text-[#4A00B9]">Cliente</th>
                                            <th class="px-6 py-3 text-left font-bold text-[#4A00B9]">Profissional</th>
                                            <th class="px-6 py-3 text-left font-bold text-[#4A00B9]">Serviço</th>
                                            <th class="px-6 py-3 text-left font-bold text-[#4A00B9]">Valor</th>
                                            <th class="px-6 py-3 text-left font-bold text-[#4A00B9]">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-[#FFD6F4]">
                                        @forelse($agendamentos as $agendamento)
                                            <tr class="hover:bg-white/50 transition">
                                                <td class="px-6 py-3">{{ $agendamento->data_hora_inicio->format('d/m/Y H:i') }}</td>
                                                <td class="px-6 py-3">{{ $agendamento->cliente->name }}</td>
                                                <td class="px-6 py-3">{{ $agendamento->profissional->name }}</td>
                                                <td class="px-6 py-3">{{ $agendamento->servico->nome }}</td>
                                                <td class="px-6 py-3 font-bold text-[#4A00B9]">R$ {{ number_format($agendamento->valor_total, 2, ',', '.') }}</td>
                                                <td class="px-6 py-3">
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
                                                <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                                    📭 Nenhum agendamento no período selecionado
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
            // Ocultar todas as abas
            document.querySelectorAll('.aba-conteudo').forEach(el => el.classList.add('hidden'));
            document.querySelectorAll('.aba-btn').forEach(el => {
                el.classList.remove('border-b-2', 'border-[#7B19E5]', 'bg-white/50');
                el.classList.add('text-gray-600');
            });

            // Mostrar a aba selecionada
            document.getElementById('conteudo-' + aba).classList.remove('hidden');
            document.getElementById('aba-' + aba).classList.add('border-b-2', 'border-[#7B19E5]', 'bg-white/50', 'text-[#4A00B9]');
            document.getElementById('aba-' + aba).classList.remove('text-gray-600');
        }
    </script>
</x-app-layout>