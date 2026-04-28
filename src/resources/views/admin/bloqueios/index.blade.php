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
            <div class="glass-card rounded-2xl shadow-xl overflow-hidden">
                <div class="p-6 bg-white/70 backdrop-blur-sm border border-white/40">
                    <div class="flex justify-between items-center mb-6">
                        <div class="flex items-center gap-2">
                            <span class="text-[#7B19E5] text-xl">✧</span>
                            <h2 class="text-xl font-title text-[#4A00B9]">Bloqueios de Agenda (Folgas e Feriados)</h2>
                        </div>
                        <a href="{{ route('admin.bloqueios.create') }}" class="bg-gradient-to-r from-[#7B19E5] to-[#FF2EB6] text-white px-5 py-2 text-sm rounded-full font-medium btn-primary shadow-md hover:shadow-lg transition-all">
                            + Novo Bloqueio
                        </a>
                    </div>

                    @if(session('success'))
                        <div class="mb-6 p-4 rounded-lg bg-green-50/80 border border-green-200 text-green-700">
                            ✧ {{ session('success') }}
                        </div>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead>
                                <tr class="border-b border-[#FFD6F4]">
                                    <th class="px-6 py-4 text-left text-xs font-medium text-[#4A00B9] uppercase tracking-wider">Profissional</th>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-[#4A00B9] uppercase tracking-wider">Início</th>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-[#4A00B9] uppercase tracking-wider">Fim</th>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-[#4A00B9] uppercase tracking-wider">Motivo</th>
                                    <th class="px-6 py-4 text-right text-xs font-medium text-[#4A00B9] uppercase tracking-wider">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#FFD6F4]">
                                @forelse($bloqueios as $bloqueio)
                                    <tr class="hover:bg-white/50 transition-colors">
                                        <td class="px-6 py-4 text-sm text-[#1A002B] font-medium">
                                            {{ $bloqueio->profissional->name ?? 'TODOS (Feriado/Geral)' }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500">
                                            {{ \Carbon\Carbon::parse($bloqueio->data_hora_inicio)->format('d/m/Y H:i') }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500">
                                            {{ \Carbon\Carbon::parse($bloqueio->data_hora_fim)->format('d/m/Y H:i') }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-[#1A002B]">
                                            {{ $bloqueio->motivo }}
                                        </td>
                                        <td class="px-6 py-4 text-right text-sm font-medium">
                                            <form action="{{ route('admin.bloqueios.destroy', $bloqueio->id_bloqueio) }}" method="POST" onsubmit="return confirm('Remover este bloqueio?')" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-[#FF2EB6] hover:text-red-500 transition-colors">
                                                    Excluir
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="hover:bg-white/50 transition-colors">
                                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                            ✧ Nenhum bloqueio registrado.
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