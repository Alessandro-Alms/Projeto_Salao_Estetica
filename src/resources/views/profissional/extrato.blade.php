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
            
            <div class="flex flex-wrap justify-between items-center mb-8 gap-4">
                <div class="flex items-center gap-2">
                    <span class="text-[#7B19E5] text-2xl">✧</span>
                    <h2 class="text-2xl font-title text-[#4A00B9]">Meu Extrato Financeiro</h2>
                </div>
                
                <form action="{{ route('profissional.extrato') }}" method="GET" class="flex gap-2 items-center">
                    <select name="mes" class="px-4 py-2 bg-white/50 border border-[#FFD6F4] rounded-lg focus:outline-none focus:border-[#7B19E5] focus:ring-2 focus:ring-[#7B19E5]/20 transition-all text-sm">
                        <option value="1" {{ $mes == 1 ? 'selected' : '' }}>Janeiro</option>
                        <option value="2" {{ $mes == 2 ? 'selected' : '' }}>Fevereiro</option>
                        <option value="3" {{ $mes == 3 ? 'selected' : '' }}>Março</option>
                        <option value="4" {{ $mes == 4 ? 'selected' : '' }}>Abril</option>
                        <option value="5" {{ $mes == 5 ? 'selected' : '' }}>Maio</option>
                        <option value="6" {{ $mes == 6 ? 'selected' : '' }}>Junho</option>
                        <option value="7" {{ $mes == 7 ? 'selected' : '' }}>Julho</option>
                        <option value="8" {{ $mes == 8 ? 'selected' : '' }}>Agosto</option>
                        <option value="9" {{ $mes == 9 ? 'selected' : '' }}>Setembro</option>
                        <option value="10" {{ $mes == 10 ? 'selected' : '' }}>Outubro</option>
                        <option value="11" {{ $mes == 11 ? 'selected' : '' }}>Novembro</option>
                        <option value="12" {{ $mes == 12 ? 'selected' : '' }}>Dezembro</option>
                    </select>
                    <button type="submit" class="bg-gradient-to-r from-[#7B19E5] to-[#FF2EB6] text-white px-6 py-2 text-sm rounded-full font-medium btn-primary shadow-md hover:shadow-lg transition-all">
                        Filtrar
                    </button>
                </form>
            </div>

            <!-- Cards de Resumo -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="glass-card rounded-2xl shadow-xl overflow-hidden">
                    <div class="p-6 bg-white/70 backdrop-blur-sm border border-white/40">
                        <p class="text-sm font-medium text-[#7B19E5] uppercase tracking-wider">Comissão Serviços</p>
                        <p class="text-3xl font-title text-[#4A00B9]">R$ {{ number_format($totalComissaoServicos, 2, ',', '.') }}</p>
                    </div>
                </div>

                <div class="glass-card rounded-2xl shadow-xl overflow-hidden">
                    <div class="p-6 bg-white/70 backdrop-blur-sm border border-white/40">
                        <p class="text-sm font-medium text-[#FF2EB6] uppercase tracking-wider">Comissão Produtos (10%)</p>
                        <p class="text-3xl font-title text-[#4A00B9]">R$ {{ number_format($totalComissaoProdutos, 2, ',', '.') }}</p>
                    </div>
                </div>

                <div class="glass-card rounded-2xl shadow-xl overflow-hidden">
                    <div class="p-6 bg-gradient-to-br from-[#7B19E5] to-[#FF2EB6] text-white">
                        <p class="text-sm font-medium uppercase tracking-wider opacity-90">Total a Receber</p>
                        <p class="text-4xl font-title">R$ {{ number_format($totalComissaoServicos + $totalComissaoProdutos, 2, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            <!-- Tabela de Detalhamento -->
            <div class="glass-card rounded-2xl shadow-xl overflow-hidden">
                <div class="p-5 bg-white/70 backdrop-blur-sm border-b border-[#FFD6F4]">
                    <div class="flex items-center gap-2">
                        <span class="text-[#7B19E5] text-lg">✦</span>
                        <h3 class="font-title text-[#4A00B9]">Detalhamento dos Serviços</h3>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-white/50 border-b border-[#FFD6F4]">
                            <tr>
                                <th class="px-6 py-4 text-xs font-medium text-[#4A00B9] uppercase tracking-wider">Data</th>
                                <th class="px-6 py-4 text-xs font-medium text-[#4A00B9] uppercase tracking-wider">Serviço</th>
                                <th class="px-6 py-4 text-xs font-medium text-[#4A00B9] uppercase tracking-wider text-center">% Comis.</th>
                                <th class="px-6 py-4 text-xs font-medium text-[#4A00B9] uppercase tracking-wider text-right">Ganho Neto</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#FFD6F4]">
                            @forelse($agendamentos as $agenda)
                                <tr class="hover:bg-white/50 transition">
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        {{ \Carbon\Carbon::parse($agenda->data_hora_inicio)->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="font-medium text-[#1A002B]">{{ $agenda->servico->nome }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="bg-[#7B19E5]/10 text-[#7B19E5] px-2 py-1 rounded-full text-xs font-medium">
                                            {{ number_format($agenda->comissao_paga_percentual, 0) }}%
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right font-bold text-[#FF2EB6]">
                                        R$ {{ number_format($agenda->valor_comissao, 2, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-10 text-center text-gray-400">
                                        ✧ Nenhum atendimento realizado neste período.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
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