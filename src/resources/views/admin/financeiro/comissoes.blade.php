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
            
            <!-- FILTRO DE BUSCA -->
            <div class="glass-card rounded-2xl shadow-xl overflow-hidden mb-6">
                <div class="p-6 bg-white/70 backdrop-blur-sm border border-white/40">
                    <div class="flex items-center gap-2 mb-4">
                        <span class="text-[#7B19E5] text-xl">✧</span>
                        <h2 class="text-xl font-title text-[#4A00B9]">Filtrar Extrato</h2>
                    </div>
                    <form action="{{ route('admin.financeiro.comissoes') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                        <div>
                            <label class="block text-xs font-medium text-[#4A00B9] uppercase mb-1">Profissional</label>
                            <select name="profissional_id" class="w-full px-4 py-2.5 bg-white/50 border border-[#FFD6F4] rounded-lg focus:outline-none focus:border-[#7B19E5] focus:ring-2 focus:ring-[#7B19E5]/20 transition-all text-sm">
                                <option value="">Selecione um profissional</option>
                                @foreach($profissionais as $p)
                                    @php 
                                        $id = $p->id_profissional ?? $p->id; 
                                        $nome = $p->nome ?? $p->name ?? 'Profissional sem nome';
                                    @endphp
                                    <option value="{{ $id }}" {{ request('profissional_id') == $id ? 'selected' : '' }}>
                                        {{ $nome }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-[#4A00B9] uppercase mb-1">Mês</label>
                            <select name="mes" class="w-full px-4 py-2.5 bg-white/50 border border-[#FFD6F4] rounded-lg focus:outline-none focus:border-[#7B19E5] focus:ring-2 focus:ring-[#7B19E5]/20 transition-all text-sm">
                                <option value="01" {{ request('mes', date('m')) == '01' ? 'selected' : '' }}>Janeiro</option>
                                <option value="02" {{ request('mes', date('m')) == '02' ? 'selected' : '' }}>Fevereiro</option>
                                <option value="03" {{ request('mes', date('m')) == '03' ? 'selected' : '' }}>Março</option>
                                <option value="04" {{ request('mes', date('m')) == '04' ? 'selected' : '' }}>Abril</option>
                                <option value="05" {{ request('mes', date('m')) == '05' ? 'selected' : '' }}>Maio</option>
                                <option value="06" {{ request('mes', date('m')) == '06' ? 'selected' : '' }}>Junho</option>
                                <option value="07" {{ request('mes', date('m')) == '07' ? 'selected' : '' }}>Julho</option>
                                <option value="08" {{ request('mes', date('m')) == '08' ? 'selected' : '' }}>Agosto</option>
                                <option value="09" {{ request('mes', date('m')) == '09' ? 'selected' : '' }}>Setembro</option>
                                <option value="10" {{ request('mes', date('m')) == '10' ? 'selected' : '' }}>Outubro</option>
                                <option value="11" {{ request('mes', date('m')) == '11' ? 'selected' : '' }}>Novembro</option>
                                <option value="12" {{ request('mes', date('m')) == '12' ? 'selected' : '' }}>Dezembro</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-[#4A00B9] uppercase mb-1">Ano</label>
                            <select name="ano" class="w-full px-4 py-2.5 bg-white/50 border border-[#FFD6F4] rounded-lg focus:outline-none focus:border-[#7B19E5] focus:ring-2 focus:ring-[#7B19E5]/20 transition-all text-sm">
                                <option value="2026" selected>2026</option>
                                <option value="2025">2025</option>
                            </select>
                        </div>

                        <button type="submit" class="bg-gradient-to-r from-[#7B19E5] to-[#FF2EB6] text-white px-6 py-2.5 text-sm rounded-full font-medium btn-primary shadow-md hover:shadow-lg transition-all flex items-center justify-center gap-2">
                            Gerar Extrato
                        </button>
                    </form>
                </div>
            </div>

            @if(request('profissional_id'))
                <!-- RESUMO FINANCEIRO DO PROFISSIONAL -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div class="glass-card rounded-2xl shadow-xl overflow-hidden">
                        <div class="p-6 bg-gradient-to-br from-[#7B19E5] to-[#A855F7] text-white">
                            <p class="text-white/80 text-xs font-medium uppercase">Total Produzido (Bruto)</p>
                            <h3 class="text-3xl font-title mt-2">R$ {{ number_format(collect($comissoes)->sum('valor_total'), 2, ',', '.') }}</h3>
                        </div>
                    </div>
                    <div class="glass-card rounded-2xl shadow-xl overflow-hidden">
                        <div class="p-6 bg-gradient-to-br from-[#FF2EB6] to-[#FF69B4] text-white">
                            <p class="text-white/80 text-xs font-medium uppercase">Comissão a Pagar (Líquido)</p>
                            <h3 class="text-3xl font-title mt-2">R$ {{ number_format($totalComissao, 2, ',', '.') }}</h3>
                        </div>
                    </div>
                </div>

                <!-- TABELA DETALHADA -->
                <div class="glass-card rounded-2xl shadow-xl overflow-hidden">
                    <div class="p-0 bg-white/70 backdrop-blur-sm border border-white/40">
                        <div class="overflow-x-auto">
                            <table class="min-w-full">
                                <thead>
                                    <tr class="border-b border-[#FFD6F4] bg-white/50">
                                        <th class="px-6 py-4 text-left text-xs font-medium text-[#4A00B9] uppercase tracking-wider">Data</th>
                                        <th class="px-6 py-4 text-left text-xs font-medium text-[#4A00B9] uppercase tracking-wider">Serviço</th>
                                        <th class="px-6 py-4 text-left text-xs font-medium text-[#4A00B9] uppercase tracking-wider">Valor do Serviço</th>
                                        <th class="px-6 py-4 text-right text-xs font-medium text-[#4A00B9] uppercase tracking-wider">Sua Comissão</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-[#FFD6F4]">
                                    @forelse($comissoes as $c)
                                        <tr class="hover:bg-white/50 transition-colors">
                                            <td class="px-6 py-4 text-sm text-gray-500">{{ $c['data'] }}</td>
                                            <td class="px-6 py-4 text-sm font-medium text-[#1A002B]">{{ $c['descricao'] }}</td>
                                            <td class="px-6 py-4 text-sm text-[#7B19E5] font-medium">R$ {{ number_format($c['valor_total'], 2, ',', '.') }}</td>
                                            <td class="px-6 py-4 text-sm text-right font-bold text-[#FF2EB6]">R$ {{ number_format($c['valor_comissao'], 2, ',', '.') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                                                ✧ Nenhum serviço executado por este profissional no período selecionado.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @else
                <div class="glass-card rounded-2xl shadow-xl overflow-hidden">
                    <div class="p-8 bg-white/70 backdrop-blur-sm border border-white/40 text-center">
                        <span class="text-[#7B19E5] text-3xl block mb-2">✧</span>
                        <p class="text-gray-500">Selecione um profissional acima para visualizar o extrato de comissões.</p>
                    </div>
                </div>
            @endif

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