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
            <div class="flex justify-between items-center mb-8 flex-wrap gap-4">
                <div class="flex items-center gap-2">
                    <span class="text-[#7B19E5] text-2xl">✧</span>
                    <h1 class="text-3xl font-title text-[#4A00B9]">Fechamento de Caixa</h1>
                </div>
                <form action="{{ route('admin.financeiro.fechamento') }}" method="GET" class="glass-card rounded-2xl overflow-hidden">
                    <div class="flex items-center gap-2 p-2 bg-white/70 backdrop-blur-sm border border-white/40">
                        <label for="data" class="text-sm font-medium text-[#4A00B9] ml-2">
                            <i class="fa-regular fa-calendar mr-1"></i> Mudar data:
                        </label>
                        <input type="date" name="data" id="data" 
                               value="{{ $dataSelecionada }}" 
                               class="px-3 py-1.5 bg-white/50 border border-[#FFD6F4] rounded-lg focus:outline-none focus:border-[#7B19E5] focus:ring-2 focus:ring-[#7B19E5]/20 transition-all text-sm">
                        <button type="submit" class="bg-gradient-to-r from-[#7B19E5] to-[#FF2EB6] text-white px-4 py-1.5 rounded-full font-medium btn-primary shadow-md hover:shadow-lg transition-all text-sm">
                            <i class="fa-regular fa-magnifying-glass mr-1"></i> Filtrar
                        </button>
                    </div>
                </form>
            </div>

            <!-- CARDS DE RESUMO -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Total Comissões -->
                <div class="glass-card rounded-2xl shadow-xl overflow-hidden">
                    <div class="p-6 bg-white/70 backdrop-blur-sm border border-white/40">
                        <div class="flex items-center gap-2 mb-2">
                            <i class="fa-regular fa-arrow-down text-red-500"></i>
                            <p class="text-xs text-gray-500 uppercase font-medium">Total Comissões (Saída)</p>
                        </div>
                        <h2 class="text-3xl font-title text-red-500">- R$ {{ number_format($totalComissoes, 2, ',', '.') }}</h2>
                    </div>
                </div>

                <!-- Lucro Líquido -->
                <div class="glass-card rounded-2xl shadow-xl overflow-hidden">
                    <div class="p-6 bg-white/70 backdrop-blur-sm border border-white/40">
                        <div class="flex items-center gap-2 mb-2">
                            <i class="fa-regular fa-arrow-up text-green-500"></i>
                            <p class="text-xs text-gray-500 uppercase font-medium">Lucro Líquido Salão</p>
                        </div>
                        <h2 class="text-3xl font-title text-green-600">R$ {{ number_format($lucroLiquido, 2, ',', '.') }}</h2>
                    </div>
                </div>
            </div>

            <!-- DETALHAMENTO -->
            <div class="glass-card rounded-2xl shadow-xl overflow-hidden">
                <div class="p-5 bg-white/70 backdrop-blur-sm border-b border-[#FFD6F4]">
                    <div class="flex items-center gap-2">
                        <i class="fa-regular fa-list-ul text-[#7B19E5]"></i>
                        <h3 class="font-title text-[#4A00B9]">Detalhamento do Dia</h3>
                    </div>
                </div>
                <div class="p-6 bg-white/70 backdrop-blur-sm border border-white/40">
                    <div class="flex justify-between py-3 border-b border-[#FFD6F4]">
                        <span class="text-gray-600 font-medium">
                            <i class="fa-regular fa-spa mr-2 text-[#7B19E5]"></i> Serviços Executados (Total Bruto)
                        </span>
                        <span class="font-bold text-[#1A002B]">R$ {{ number_format($totalServicos, 2, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between py-3 border-b border-[#FFD6F4]">
                        <span class="text-gray-600 font-medium">
                            <i class="fa-regular fa-box mr-2 text-[#7B19E5]"></i> Produtos Vendidos (Total Bruto)
                        </span>
                        <span class="font-bold text-[#1A002B]">R$ {{ number_format($totalProdutos, 2, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between py-3 text-red-500">
                        <span class="font-medium italic">
                            <i class="fa-regular fa-percent mr-2"></i> Estimativa de Comissões a Pagar
                        </span>
                        <span class="font-bold">- R$ {{ number_format($totalComissoes, 2, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <p class="mt-6 text-sm text-gray-400 text-center italic">
                <i class="fa-regular fa-circle-info mr-1"></i> Os valores de comissão refletem a taxa exata configurada para cada profissional no momento do atendimento.
            </p>
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