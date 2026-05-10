<x-app-layout>
    <x-slot name="header">
        <div class="hidden"></div>
    </x-slot>

    <div class="py-8 md:py-10 relative">
        <div class="fixed inset-0 -z-10 overflow-hidden">
            <div class="absolute top-0 -left-20 w-[600px] h-[600px] bg-[#7B19E5]/20 rounded-full blur-3xl"></div>
            <div class="absolute bottom-0 -right-20 w-[700px] h-[700px] bg-[#FF2EB6]/20 rounded-full blur-3xl"></div>
            <div class="absolute top-1/3 left-1/3 w-[400px] h-[400px] bg-[#A955D3]/15 rounded-full blur-3xl"></div>
            <div class="absolute inset-0 bg-gradient-to-br from-[#7B19E5]/5 via-white/30 to-[#FF2EB6]/5"></div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="glass-card rounded-2xl shadow-xl overflow-hidden mb-6">
                <div class="p-5 md:p-6 bg-white/75 backdrop-blur-sm border border-white/40">
                    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-gradient-to-br from-[#7B19E5] to-[#FF2EB6] rounded-xl flex items-center justify-center shadow-lg shrink-0">
                            <span class="text-2xl text-white">✧</span>
                            </div>
                            <div>
                                <h3 class="text-xl md:text-2xl font-title text-[#4A00B9]">Olá, {{ auth()->user()->name }}!</h3>
                                <p class="text-sm text-[#7B19E5] font-body">Bem-vinda ao seu painel</p>
                            </div>
                        </div>
                        <div class="inline-flex items-center gap-2 bg-[#FF2EB6]/10 px-4 py-2 rounded-full self-start md:self-auto">
                            <span class="text-xs font-bold uppercase tracking-wide text-[#FF2EB6]">{{ ucfirst(auth()->user()->cargo) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Lógica de exibição por Cargo (Sua organização perfeita!) --}}
            @if(auth()->user()->cargo === 'gerente')
                @include('dashboards.admin')
            
            @elseif(auth()->user()->cargo === 'recepcionista')
                @include('dashboards.recepcionista')
            
            @elseif(auth()->user()->cargo === 'profissional')
                @include('dashboards.profissional')
            
            @else
                @include('dashboards.cliente')
            @endif

        </div>
    </div>
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
    
    .font-body {
        font-family: 'Space Grotesk', sans-serif;
    }
    
    .glass-card {
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        box-shadow: 0 8px 32px rgba(123, 25, 229, 0.1);
    }
</style>
