<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex items-center gap-2">
                <span class="text-[#7B19E5] text-xl">✧</span>
                <h2 class="font-title text-xl text-[#1A002B]">
                    {{ __('REL005: Fidelização e Clientes VIP') }}
                </h2>
            </div>
            <a href="{{ route('admin.relatorios.index') ?? '#' }}" class="text-sm text-[#7B19E5] hover:text-[#FF2EB6] transition-colors inline-flex items-center gap-1">
                <i class="fa-solid fa-arrow-left text-xs"></i> Voltar
            </a>
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

        <div class="container mx-auto px-4">
            <!-- Filtro -->
            <div class="glass-card rounded-2xl shadow-xl overflow-hidden mb-6">
                <div class="p-4 bg-white/70 backdrop-blur-sm border border-white/40">
                    <form method="GET" action="{{ route('admin.relatorios.fidelizacao') }}" class="flex flex-wrap items-end gap-4">
                        <div>
                            <label class="block text-sm font-medium text-[#4A00B9] mb-1">Início</label>
                            <input type="date" name="data_inicio" value="{{ $dataInicio }}" 
                                class="px-4 py-2.5 bg-white/50 border border-[#FFD6F4] rounded-lg focus:outline-none focus:border-[#7B19E5] focus:ring-2 focus:ring-[#7B19E5]/20 transition-all">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-[#4A00B9] mb-1">Fim</label>
                            <input type="date" name="data_fim" value="{{ $dataFim }}" 
                                class="px-4 py-2.5 bg-white/50 border border-[#FFD6F4] rounded-lg focus:outline-none focus:border-[#7B19E5] focus:ring-2 focus:ring-[#7B19E5]/20 transition-all">
                        </div>
                        <button type="submit" class="bg-gradient-to-r from-[#7B19E5] to-[#FF2EB6] text-white px-6 py-2.5 text-sm rounded-full font-medium btn-primary shadow-md hover:shadow-lg transition-all">
                            Analisar Fidelização
                        </button>
                    </form>
                </div>
            </div>

            <!-- Downloads -->
            <div class="flex gap-2 mb-6 flex-wrap">
                <a href="{{ route('admin.relatorios.fidelizacao.download-excel', ['data_inicio' => $dataInicio, 'data_fim' => $dataFim]) }}" class="flex items-center gap-2 bg-white/50 text-[#00B050] border border-[#FFD6F4] px-4 py-2 rounded-lg hover:bg-white/80 transition font-medium">
                    📊 Exportar Excel
                </a>
            </div>

            <!-- Cards de resumo -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <!-- Taxa de Retorno -->
                <div class="glass-card rounded-2xl shadow-xl overflow-hidden">
                    <div class="p-6 bg-gradient-to-br from-[#7B19E5] to-[#A855F7] text-white">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="text-sm font-bold opacity-80 uppercase tracking-wider mb-1">Taxa de Retorno</h3>
                                <p class="text-4xl font-black mb-1">{{ number_format($taxaRetorno, 1) }}%</p>
                                <p class="text-sm mt-1 bg-white/20 px-3 py-1 rounded-full self-start">
                                    {{ $clientesRetornaram }} de {{ $totalClientesAtendidos }} clientes voltaram
                                </p>
                            </div>
                            <div class="opacity-30 text-4xl">✧</div>
                        </div>
                    </div>
                </div>

                <!-- Maior Comprador -->
                <div class="glass-card rounded-2xl shadow-xl overflow-hidden">
                    <div class="p-6 bg-white/70 backdrop-blur-sm border border-white/40">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="text-gray-500 font-bold uppercase text-sm mb-2">Maior Comprador (VIP 1)</h3>
                                @if($clienteTop1)
                                    <p class="text-xl font-black text-[#1A002B] truncate" title="{{ $clienteTop1->name }}">{{ $clienteTop1->name }}</p>
                                    <p class="text-sm font-bold text-[#7B19E5] mt-1">R$ {{ number_format($clienteTop1->valor_gasto_total, 2, ',', '.') }} investidos</p>
                                @else
                                    <p class="text-xl font-bold text-gray-400">Sem clientes</p>
                                @endif
                            </div>
                            <div class="opacity-30 text-3xl">✧</div>
                        </div>
                    </div>
                </div>

                <!-- Clientes Únicos -->
                <div class="glass-card rounded-2xl shadow-xl overflow-hidden">
                    <div class="p-6 bg-white/70 backdrop-blur-sm border border-white/40">
                        <h3 class="text-gray-500 font-bold uppercase text-sm mb-2">Clientes Únicos Atendidos</h3>
                        <p class="text-4xl font-black text-[#FF2EB6]">{{ $totalClientesAtendidos }}</p>
                        <p class="text-sm text-gray-500 mt-2">No período selecionado</p>
                    </div>
                </div>
            </div>

            <!-- Ranking de Clientes VIP -->
            <div class="glass-card rounded-2xl shadow-xl overflow-hidden">
                <div class="p-6 bg-white/70 backdrop-blur-sm border border-white/40">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="text-[#7B19E5] text-xl">✧</span>
                        <h3 class="font-title text-[#4A00B9] text-lg">Ranking de Clientes VIP</h3>
                    </div>
                    <p class="text-sm text-gray-500 mb-6">Lista de clientes ordenada pelo valor total investido no salão neste período.</p>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse whitespace-nowrap">
                            <thead>
                                <tr class="bg-[#7B19E5]/10 border-b border-[#FFD6F4]">
                                    <th class="p-3 rounded-tl-lg text-[#4A00B9] text-xs font-medium uppercase">Posição</th>
                                    <th class="p-3 text-[#4A00B9] text-xs font-medium uppercase">Cliente / Telefone</th>
                                    <th class="p-3 text-center text-[#4A00B9] text-xs font-medium uppercase">Pontos (Fidelidade)</th>
                                    <th class="p-3 text-center text-[#4A00B9] text-xs font-medium uppercase">Visitas (Período)</th>
                                    <th class="p-3 text-center text-[#4A00B9] text-xs font-medium uppercase">Última Visita</th>
                                    <th class="p-3 text-right rounded-tr-lg text-[#4A00B9] text-xs font-medium uppercase">Total Gasto</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($clientes as $index => $cliente)
                                    <tr class="border-b border-[#FFD6F4] hover:bg-white/50 transition {{ $index < 3 ? 'bg-[#FF2EB6]/5' : '' }}">
                                        <td class="p-3 font-bold text-gray-500 text-lg">
                                            @if($index == 0) ✧
                                            @elseif($index == 1) ✦
                                            @elseif($index == 2) ✧
                                            @else #{{ $index + 1 }}
                                            @endif
                                        </td>
                                        <td class="p-3">
                                            <p class="font-bold text-[#1A002B]">{{ $cliente->name }}</p>
                                            <p class="text-xs text-gray-500">{{ $cliente->telefone ?: 'Sem contacto' }}</p>
                                        <td>
                                        <td class="p-3 text-center">
                                            <span class="bg-[#7B19E5]/10 text-[#7B19E5] px-2 py-1 rounded-full text-xs font-bold">
                                                {{ $cliente->contador_fidelidade }} pts
                                            </span>
                                        </td>
                                        <td class="p-3 text-center">
                                            <span class="bg-white/50 text-gray-600 px-3 py-1 rounded-full font-bold text-xs border border-[#FFD6F4]">
                                                {{ $cliente->total_visitas }}
                                            </span>
                                        </td>
                                        <td class="p-3 text-center text-sm text-gray-500">
                                            {{ \Carbon\Carbon::parse($cliente->ultima_visita)->format('d/m/Y') }}
                                        </td>
                                        <td class="p-3 text-right font-black {{ $index < 3 ? 'text-[#7B19E5]' : 'text-gray-700' }}">
                                            R$ {{ number_format($cliente->valor_gasto_total, 2, ',', '.') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="p-8 text-center text-gray-500">
                                            ✧ Nenhum cliente atendido neste período.
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