<x-app-layout>
    <x-slot name="header">
        <div class="hidden"></div>
    </x-slot>

    <div class="py-12 relative">
        <div class="fixed inset-0 -z-10 overflow-hidden">
            <div class="absolute top-0 -left-20 w-[600px] h-[600px] bg-[#7B19E5]/20 rounded-full blur-3xl"></div>
            <div class="absolute bottom-0 -right-20 w-[700px] h-[700px] bg-[#FF2EB6]/20 rounded-full blur-3xl"></div>
            <div class="absolute inset-0 bg-gradient-to-br from-[#7B19E5]/5 via-white/30 to-[#FF2EB6]/5"></div>
        </div>

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-6 p-4 rounded-lg bg-green-50/80 border border-green-200 text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 p-4 rounded-lg bg-red-50/80 border border-red-200 text-red-700">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 mb-8">
                <div>
                    <p class="text-sm uppercase tracking-[0.25em] text-[#FF2EB6] font-bold">autoatendimento</p>
                    <h1 class="text-4xl font-title text-[#4A00B9] mt-2">Comprar Pacotes</h1>
                    <p class="text-gray-600 mt-2 max-w-2xl">
                        Escolha um pacote e ele fica disponível imediatamente para usar ao finalizar atendimentos do serviço correspondente.
                    </p>
                </div>

                <a href="{{ route('cliente.index') }}" class="inline-flex items-center justify-center px-5 py-3 rounded-full bg-white/80 border border-[#FFD6F4] text-[#7B19E5] font-bold hover:bg-[#7B19E5] hover:text-white transition-all">
                    Ver meus agendamentos
                </a>
            </div>

            <section class="mb-12">
                <div class="flex items-center gap-2 mb-5">
                    <span class="text-[#7B19E5] text-xl">✧</span>
                    <h2 class="text-2xl font-title text-[#4A00B9]">Meus pacotes ativos</h2>
                </div>

                @if($meusPacotes->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                        @foreach($meusPacotes as $meuPacote)
                            <article class="glass-card rounded-2xl p-5 border border-white/40">
                                <div class="flex items-center justify-between gap-3">
                                    <span class="px-3 py-1 rounded-full bg-green-100 text-green-700 text-xs font-bold">Ativo</span>
                                    <span class="text-xs text-gray-500">vence {{ \Carbon\Carbon::parse($meuPacote->data_validade)->format('d/m/Y') }}</span>
                                </div>
                                <h3 class="text-xl font-title text-[#4A00B9] mt-4">{{ $meuPacote->pacote->nome }}</h3>
                                <p class="text-sm text-[#7B19E5] mt-1">
                                    {{ $meuPacote->pacote->servicos->pluck('nome')->join(', ') ?: ($meuPacote->pacote->servico->nome ?? 'Serviço removido') }}
                                </p>
                                <div class="mt-4 p-4 rounded-xl bg-white/60 border border-[#FFD6F4] flex items-center justify-between">
                                    <span class="text-sm text-gray-600 font-semibold">Sessões restantes</span>
                                    <span class="text-3xl font-black text-[#7B19E5]">{{ $meuPacote->sessoes_restantes }}</span>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @else
                    <div class="glass-card rounded-2xl p-6 text-center border border-white/40">
                        <p class="text-gray-500">Você ainda não tem pacote ativo.</p>
                    </div>
                @endif
            </section>

            <section>
                <div class="flex items-center gap-2 mb-5">
                    <span class="text-[#FF2EB6] text-xl">✧</span>
                    <h2 class="text-2xl font-title text-[#4A00B9]">Pacotes disponíveis</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse($pacotesDisponiveis as $pacote)
                        <article class="glass-card rounded-3xl overflow-hidden border border-white/40 hover-lift">
                            <div class="p-6 bg-white/75 h-full flex flex-col">
                                <div class="flex items-center justify-between gap-3 mb-4">
                                    <span class="px-3 py-1 rounded-full bg-[#7B19E5]/10 text-[#7B19E5] text-xs font-bold">
                                        {{ $pacote->quantidade_sessoes }} sessões
                                    </span>
                                    <span class="text-xs text-gray-500">{{ $pacote->validade_dias }} dias</span>
                                </div>

                                <h3 class="text-2xl font-title text-[#4A00B9]">{{ $pacote->nome }}</h3>
                                <p class="text-sm text-gray-600 mt-2">
                                    {{ $pacote->servicos->pluck('nome')->join(', ') ?: ($pacote->servico->nome ?? 'Serviço removido') }}
                                </p>

                                <div class="mt-6 mb-6">
                                    <p class="text-sm text-gray-500 font-semibold">Valor do pacote</p>
                                    <p class="text-4xl font-black text-[#FF2EB6]">R$ {{ number_format($pacote->valor_total, 2, ',', '.') }}</p>
                                </div>

                                <form action="{{ route('cliente.pacotes.comprar') }}" method="POST" class="mt-auto" onsubmit="return confirm('Confirmar compra deste pacote para sua conta?');">
                                    @csrf
                                    <input type="hidden" name="pacote_id" value="{{ $pacote->id_pacote }}">
                                    <button type="submit" class="w-full bg-gradient-to-r from-[#7B19E5] to-[#FF2EB6] text-white px-6 py-3 rounded-full font-bold btn-primary shadow-lg hover:shadow-xl transition-all">
                                        Comprar para mim
                                    </button>
                                </form>
                            </div>
                        </article>
                    @empty
                        <div class="glass-card rounded-2xl p-8 text-center col-span-full border border-white/40">
                            <p class="text-gray-500">Nenhum pacote ativo disponivel no momento.</p>
                        </div>
                    @endforelse
                </div>
            </section>
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
        background: rgba(255, 255, 255, 0.72);
        backdrop-filter: blur(12px);
        box-shadow: 0 18px 45px rgba(123, 25, 229, 0.10);
    }

    .hover-lift {
        transition: transform 0.28s ease, box-shadow 0.28s ease;
    }

    .hover-lift:hover {
        transform: translateY(-5px);
        box-shadow: 0 24px 55px rgba(255, 46, 182, 0.18);
    }

    .btn-primary {
        position: relative;
        overflow: hidden;
    }
</style>
