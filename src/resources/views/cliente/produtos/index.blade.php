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
                    <h1 class="text-4xl font-title text-[#4A00B9] mt-2">Comprar Produtos</h1>
                    <p class="text-gray-600 mt-2 max-w-2xl">
                        Escolha os produtos disponíveis em estoque e confirme a compra direto na sua conta.
                    </p>
                </div>

                <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center px-5 py-3 rounded-full bg-white/80 border border-[#FFD6F4] text-[#7B19E5] font-bold hover:bg-[#7B19E5] hover:text-white transition-all">
                    Voltar ao painel
                </a>
            </div>

            <section>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse($produtos as $produto)
                        <article class="glass-card rounded-2xl overflow-hidden border border-white/40 hover-lift">
                            <div class="p-6 bg-white/75 h-full flex flex-col">
                                <div class="flex items-center justify-between gap-3 mb-4">
                                    <span class="px-3 py-1 rounded-full bg-[#7B19E5]/10 text-[#7B19E5] text-xs font-bold">
                                        {{ ucfirst($produto->tipo) }}
                                    </span>
                                </div>

                                <h2 class="text-2xl font-title text-[#4A00B9]">{{ $produto->nome }}</h2>
                                @if($produto->descricao)
                                    <p class="text-sm text-gray-600 mt-2">{{ $produto->descricao }}</p>
                                @endif

                                <div class="mt-6 mb-6">
                                    <p class="text-sm text-gray-500 font-semibold">Valor unitario</p>
                                    <p class="text-4xl font-black text-[#FF2EB6]">R$ {{ number_format($produto->valor_unitario, 2, ',', '.') }}</p>
                                </div>

                                <div class="mb-6 p-4 rounded-xl bg-[#7B19E5]/10 border border-[#FFD6F4] flex items-center justify-between">
                                    <span class="text-sm text-[#4A00B9] font-bold uppercase">Estoque disponivel</span>
                                    <span class="text-3xl font-black text-[#7B19E5]">{{ $produto->quantidade_estoque }}</span>
                                </div>

                                <form action="{{ route('cliente.produtos.comprar') }}" method="POST" class="mt-auto space-y-4" onsubmit="return confirm('Confirmar compra deste produto para sua conta?');">
                                    @csrf
                                    <input type="hidden" name="produto_id" value="{{ $produto->id_produto }}">

                                    <div>
                                        <label class="block text-sm font-medium text-[#4A00B9] mb-2">Quantidade</label>
                                        <input type="number" name="quantidade" value="1" min="1" max="{{ $produto->quantidade_estoque }}" required
                                            class="w-full px-4 py-3 bg-white/60 border border-[#FFD6F4] rounded-lg focus:outline-none focus:border-[#7B19E5] focus:ring-2 focus:ring-[#7B19E5]/20 transition-all">
                                    </div>

                                    <button type="submit" class="w-full bg-gradient-to-r from-[#7B19E5] to-[#FF2EB6] text-white px-6 py-3 rounded-full font-bold btn-primary shadow-lg hover:shadow-xl transition-all">
                                        Comprar para mim
                                    </button>
                                </form>
                            </div>
                        </article>
                    @empty
                        <div class="glass-card rounded-2xl p-8 text-center col-span-full border border-white/40">
                            <p class="text-gray-500">Nenhum produto disponivel em estoque no momento.</p>
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
