<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex items-center gap-2">
                <span class="text-[#7B19E5] text-xl">✧</span>
                <h2 class="font-title text-xl text-[#1A002B]">
                    {{ __('REL009: Estoque de Produtos') }}
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
            <!-- Cards de resumo -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <!-- Alertas de Reposição -->
                <div class="glass-card rounded-2xl shadow-xl overflow-hidden">
                    <div class="p-6 {{ $totalAlertas > 0 ? 'bg-gradient-to-br from-[#FF2EB6] to-[#FF69B4]' : 'bg-gradient-to-br from-green-500 to-emerald-600' }} text-white">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="text-sm font-bold opacity-80 uppercase tracking-wider mb-2">Alertas de Reposição</h3>
                                <p class="text-4xl font-black mb-1">{{ $totalAlertas }}</p>
                                <p class="text-sm bg-white/20 px-3 py-1 rounded-full inline-block mt-1">
                                    Produtos com estoque baixo
                                </p>
                            </div>
                            <div class="opacity-30 text-3xl">⚠️</div>
                        </div>
                    </div>
                </div>

                <!-- Volume Total -->
                <div class="glass-card rounded-2xl shadow-xl overflow-hidden">
                    <div class="p-6 bg-white/70 backdrop-blur-sm border border-white/40">
                        <h3 class="text-gray-500 font-bold uppercase text-sm mb-2">Volume Total (Unidades)</h3>
                        <p class="text-4xl font-black text-[#7B19E5]">{{ $totalItens }}</p>
                        <p class="text-sm text-gray-500 mt-2">Itens físicos na prateleira</p>
                    </div>
                </div>

                <!-- Capital em Estoque -->
                <div class="glass-card rounded-2xl shadow-xl overflow-hidden">
                    <div class="p-6 bg-white/70 backdrop-blur-sm border border-white/40">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="text-gray-500 font-bold uppercase text-sm mb-2">Capital em Estoque</h3>
                                <p class="text-3xl font-black text-[#1A002B]">R$ {{ number_format($valorInvestido, 2, ',', '.') }}</p>
                                <p class="text-sm text-[#FF2EB6] font-bold mt-1">Valor de custo/venda parado</p>
                            </div>
                            <div class="opacity-30 text-2xl">✧</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabela de Estoque -->
            <div class="glass-card rounded-2xl shadow-xl overflow-hidden">
                <div class="p-6 bg-white/70 backdrop-blur-sm border border-white/40">
                    <div class="flex justify-between items-center mb-4">
                        <div class="flex items-center gap-2">
                            <span class="text-[#7B19E5] text-xl">✧</span>
                            <h3 class="font-title text-[#4A00B9] text-lg">Posição Atual do Estoque e Sugestões</h3>
                        </div>
                        <span class="text-xs text-gray-500 bg-white/50 px-3 py-1 rounded-full border border-[#FFD6F4]">
                            Ordenado por nível crítico
                        </span>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse whitespace-nowrap">
                            <thead>
                                <tr class="bg-[#7B19E5]/10 border-b border-[#FFD6F4]">
                                    <th class="p-3 rounded-tl-lg text-[#4A00B9] text-xs font-medium uppercase">Produto</th>
                                    <th class="p-3 text-center text-[#4A00B9] text-xs font-medium uppercase">Tipo</th>
                                    <th class="p-3 text-right text-[#4A00B9] text-xs font-medium uppercase">Valor Unit.</th>
                                    <th class="p-3 text-center text-[#4A00B9] text-xs font-medium uppercase">Saldo em Estoque</th>
                                    <th class="p-3 text-center rounded-tr-lg text-[#4A00B9] text-xs font-medium uppercase">Sugestão de Compra</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($produtos as $produto)
                                    @php 
                                        $critico = $produto->quantidade_estoque <= 5; 
                                        $esgotado = $produto->quantidade_estoque == 0;
                                    @endphp
                                    <tr class="border-b border-[#FFD6F4] transition {{ $esgotado ? 'bg-red-50/30' : ($critico ? 'hover:bg-[#FF2EB6]/5' : 'hover:bg-white/50') }}">
                                        <td class="p-3">
                                            <p class="font-bold {{ $esgotado ? 'text-red-600' : 'text-[#1A002B]' }}">{{ $produto->nome }}</p>
                                        </td>
                                        <td class="p-3 text-center">
                                            <span class="bg-white/50 text-gray-600 px-2 py-1 rounded text-xs font-bold uppercase border border-[#FFD6F4]">
                                                {{ $produto->tipo }}
                                            </span>
                                        </td>
                                        <td class="p-3 text-right text-gray-500 font-medium">
                                            R$ {{ number_format($produto->valor_unitario, 2, ',', '.') }}
                                        </td>
                                        <td class="p-3 text-center">
                                            @if($esgotado)
                                                <span class="bg-gradient-to-r from-[#FF2EB6] to-[#FF69B4] text-white px-3 py-1 rounded-full font-bold text-sm shadow-sm inline-flex items-center gap-1">
                                                    0 (Esgotado)
                                                </span>
                                            @elseif($critico)
                                                <span class="bg-[#FF2EB6]/20 text-[#FF2EB6] border border-[#FFD6F4] px-3 py-1 rounded-full font-bold text-sm inline-flex items-center gap-1">
                                                    ⚠️ {{ $produto->quantidade_estoque }}
                                                </span>
                                            @else
                                                <span class="bg-[#7B19E5]/10 text-[#7B19E5] px-3 py-1 rounded-full font-bold text-sm">
                                                    ✔️ {{ $produto->quantidade_estoque }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="p-3 text-center">
                                            @if($esgotado)
                                                <span class="text-[#FF2EB6] font-bold text-sm">Urgentíssimo! Pedir hoje.</span>
                                            @elseif($critico)
                                                <span class="text-[#7B19E5] font-bold text-sm">Comprar reposição em breve.</span>
                                            @else
                                                <span class="text-gray-400 text-sm">Estoque saudável.</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="p-8 text-center text-gray-500">
                                            ✧ Nenhum produto registado na base de dados.
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