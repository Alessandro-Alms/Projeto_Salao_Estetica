<x-app-layout>
    <x-slot name="header">
        <div class="hidden"></div>
    </x-slot>

    <div class="py-10 relative">
        <div class="fixed inset-0 -z-10 overflow-hidden">
            <div class="absolute top-0 -left-20 w-[600px] h-[600px] bg-[#7B19E5]/20 rounded-full blur-3xl"></div>
            <div class="absolute bottom-0 -right-20 w-[700px] h-[700px] bg-[#FF2EB6]/20 rounded-full blur-3xl"></div>
            <div class="absolute inset-0 bg-gradient-to-br from-[#7B19E5]/5 via-white/30 to-[#FF2EB6]/5"></div>
        </div>

        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
                <div>
                    <p class="text-sm tracking-[0.25em] text-[#FF2EB6] font-bold uppercase">Contato</p>
                    <h1 class="text-3xl font-title text-[#4A00B9] mt-1">Mensagens do site</h1>
                </div>
                <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center px-5 py-3 rounded-full bg-white/80 border border-[#FFD6F4] text-[#7B19E5] font-bold hover:bg-[#7B19E5] hover:text-white transition-all">
                    Voltar ao painel
                </a>
            </div>

            @if(session('status'))
                <div class="mb-6 rounded-2xl border border-[#FFD6F4] bg-white/80 px-5 py-4 text-[#4A00B9] shadow-md">
                    {{ session('status') }}
                </div>
            @endif

            <div class="space-y-4">
                @forelse($mensagens as $mensagem)
                    <div class="glass-card rounded-2xl shadow-lg overflow-hidden">
                        <div class="p-5 bg-white/75 backdrop-blur-sm border border-white/40">
                            <div class="flex flex-wrap items-start justify-between gap-4">
                                <div>
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h2 class="text-xl font-title text-[#4A00B9]">{{ $mensagem->assunto }}</h2>
                                        @if(!$mensagem->lida_at)
                                            <span class="rounded-full bg-[#FF2EB6]/10 px-3 py-1 text-xs font-bold text-[#FF2EB6]">Nova</span>
                                        @endif
                                    </div>
                                    <p class="mt-1 text-sm text-gray-500">
                                        {{ $mensagem->nome }} · {{ $mensagem->email }} · {{ \Carbon\Carbon::parse($mensagem->created_at)->format('d/m/Y H:i') }}
                                    </p>
                                </div>

                                @if(!$mensagem->lida_at)
                                    <form method="POST" action="{{ route('admin.contatos.lida', $mensagem->id) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="rounded-full bg-[#7B19E5] px-4 py-2 text-sm font-bold text-white hover:bg-[#FF2EB6] transition-colors">
                                            Marcar como lida
                                        </button>
                                    </form>
                                @endif
                            </div>

                            <p class="mt-4 whitespace-pre-line text-[#1A002B] leading-relaxed">{{ $mensagem->mensagem }}</p>
                        </div>
                    </div>
                @empty
                    <div class="glass-card rounded-2xl p-8 text-center text-gray-500">
                        Nenhuma mensagem recebida pelo site ainda.
                    </div>
                @endforelse
            </div>

            @if(method_exists($mensagens, 'links'))
                <div class="mt-6">
                    {{ $mensagens->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
