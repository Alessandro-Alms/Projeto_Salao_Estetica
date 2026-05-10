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
                <h1 class="text-2xl font-title text-[#4A00B9]">Gestão de Pacotes Promocionais</h1>
            </div>

            {{-- Mensagem de Sucesso --}}
            @if(session('success'))
                <div class="mb-6 p-4 rounded-lg bg-green-50/80 border border-green-200 text-green-700">
                    ✧ {{ session('success') }}
                </div>
            @endif

            {{-- Formulário de Criação --}}
            <div class="glass-card rounded-2xl shadow-xl overflow-hidden mb-8">
                <div class="p-6 bg-white/70 backdrop-blur-sm border border-white/40">
                    <div class="flex items-center gap-2 mb-4">
                        <span class="text-[#FF2EB6] text-lg">✦</span>
                        <h2 class="text-lg font-title text-[#4A00B9]">Criar Novo Pacote</h2>
                    </div>
                    <form action="{{ route('admin.pacotes.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @csrf
                        
                        <div class="col-span-1 md:col-span-2">
                            <label class="block text-sm font-medium text-[#4A00B9] mb-2">Nome do Pacote (Ex: Combo Verão Laser)</label>
                            <input type="text" name="nome" required 
                                class="w-full px-4 py-3 bg-white/50 border border-[#FFD6F4] rounded-lg focus:outline-none focus:border-[#7B19E5] focus:ring-2 focus:ring-[#7B19E5]/20 transition-all">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-[#4A00B9] mb-2">Servicos inclusos</label>
                            <div class="max-h-44 overflow-y-auto space-y-2 bg-white/50 border border-[#FFD6F4] rounded-lg p-3">
                                @foreach($servicos as $servico)
                                    <label class="flex items-center gap-2 text-sm text-[#1A002B]">
                                        <input type="checkbox" name="servicos_ids[]" value="{{ $servico->id_servico }}" class="rounded border-[#FFD6F4] text-[#7B19E5] focus:ring-[#7B19E5]">
                                        <span>{{ $servico->nome }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-[#4A00B9] mb-2">Qtd. de Sessões</label>
                            <input type="number" name="quantidade_sessoes" min="2" required 
                                class="w-full px-4 py-3 bg-white/50 border border-[#FFD6F4] rounded-lg focus:outline-none focus:border-[#7B19E5] focus:ring-2 focus:ring-[#7B19E5]/20 transition-all">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-[#4A00B9] mb-2">Valor Total (R$)</label>
                            <input type="number" name="valor_total" step="0.01" required 
                                class="w-full px-4 py-3 bg-white/50 border border-[#FFD6F4] rounded-lg focus:outline-none focus:border-[#7B19E5] focus:ring-2 focus:ring-[#7B19E5]/20 transition-all">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-[#4A00B9] mb-2">Validade (em dias)</label>
                            <input type="number" name="validade_dias" value="90" required 
                                class="w-full px-4 py-3 bg-white/50 border border-[#FFD6F4] rounded-lg focus:outline-none focus:border-[#7B19E5] focus:ring-2 focus:ring-[#7B19E5]/20 transition-all">
                        </div>

                        <div class="col-span-1 md:col-span-3 mt-4">
                            <button type="submit" class="bg-gradient-to-r from-[#7B19E5] to-[#FF2EB6] text-white px-8 py-3 text-sm rounded-full font-medium btn-primary shadow-lg hover:shadow-xl transition-all">
                                Salvar Pacote
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Lista de Pacotes --}}
            <div class="glass-card rounded-2xl shadow-xl overflow-hidden">
                <div class="p-0 bg-white/70 backdrop-blur-sm border border-white/40">
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead>
                                <tr class="border-b border-[#FFD6F4] bg-white/50">
                                    <th class="px-6 py-4 text-left text-xs font-medium text-[#4A00B9] uppercase tracking-wider">Nome / Serviço</th>
                                    <th class="px-6 py-4 text-center text-xs font-medium text-[#4A00B9] uppercase tracking-wider">Sessões</th>
                                    <th class="px-6 py-4 text-center text-xs font-medium text-[#4A00B9] uppercase tracking-wider">Valor</th>
                                    <th class="px-6 py-4 text-center text-xs font-medium text-[#4A00B9] uppercase tracking-wider">Validade</th>
                                    <th class="px-6 py-4 text-center text-xs font-medium text-[#4A00B9] uppercase tracking-wider">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#FFD6F4]">
                                @foreach($pacotes as $pacote)
                                    <tr class="hover:bg-white/50 transition-colors">
                                        <td class="px-6 py-4">
                                            <div class="font-medium text-[#1A002B]">{{ $pacote->nome }}</div>
                                            <div class="text-sm text-[#7B19E5]">
                                                {{ $pacote->servicos->pluck('nome')->join(', ') ?: ($pacote->servico->nome ?? 'Serviço removido') }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-center text-sm text-[#1A002B]">{{ $pacote->quantidade_sessoes }}</td>
                                        <td class="px-6 py-4 text-center text-sm font-medium text-[#7B19E5]">R$ {{ number_format($pacote->valor_total, 2, ',', '.') }}</td>
                                        <td class="px-6 py-4 text-center text-sm text-gray-500">{{ $pacote->validade_dias }} dias</td>
                                        <td class="px-6 py-4 text-center text-sm font-medium">
                                            <a href="{{ route('admin.pacotes.edit', $pacote->id_pacote) }}" class="text-[#7B19E5] hover:text-[#FF2EB6] transition-colors mr-3">Editar</a>

                                            <form action="{{ route('admin.pacotes.destroy', $pacote->id_pacote) }}" method="POST" class="inline-block" onsubmit="return confirm('Tem certeza que deseja excluir este pacote?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-[#FF2EB6] hover:text-red-500 transition-colors">Excluir</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
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
