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
            <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 mb-8">
                <div>
                    <p class="text-sm uppercase tracking-[0.25em] text-[#FF2EB6] font-bold">caixa</p>
                    <h1 class="text-3xl font-title text-[#4A00B9] mt-2">Compras Pendentes</h1>
                    <p class="text-gray-600 mt-2">Confirme PIX de pacotes e produtos retirados/pagos presencialmente.</p>
                </div>

                <a href="{{ route('admin.financeiro.fechamento') }}" class="inline-flex items-center justify-center px-5 py-3 rounded-full bg-white/80 border border-[#FFD6F4] text-[#7B19E5] font-bold hover:bg-[#7B19E5] hover:text-white transition-all">
                    Ver fechamento
                </a>
            </div>

            @if(session('status'))
                <div class="mb-6 p-4 rounded-lg bg-green-50/80 border border-green-200 text-green-700">
                    {{ session('status') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 p-4 rounded-lg bg-red-50/80 border border-red-200 text-red-700">
                    @foreach($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
                <section class="glass-card rounded-2xl overflow-hidden" data-pending-section>
                    <div class="p-5 bg-white/70 border-b border-[#FFD6F4]">
                        <h2 class="text-xl font-title text-[#4A00B9]">Produtos</h2>
                    </div>
                    <div class="p-5 bg-white/70 space-y-4">
                        @forelse($vendasPendentes as $venda)
                            <article class="p-4 rounded-xl bg-white/70 border border-[#FFD6F4]" data-pending-item>
                                <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-3">
                                    <div>
                                        <p class="font-bold text-[#4A00B9]">{{ $venda->produto->nome ?? 'Produto removido' }}</p>
                                        <p class="text-sm text-gray-600">Cliente: {{ $venda->vendedor->name ?? 'Cliente removido' }}</p>
                                        <p class="text-sm text-gray-600">Qtd: {{ $venda->quantidade }} | R$ {{ number_format($venda->valor_venda, 2, ',', '.') }}</p>
                                    </div>
                                    <form action="{{ route('admin.vendas.cancelar-pendente', $venda) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button class="px-4 py-2 rounded-full bg-red-50 text-red-600 font-bold text-sm border border-red-200" onclick="return confirm('Cancelar este pedido?');">Cancelar</button>
                                    </form>
                                </div>
                                <form action="{{ route('admin.vendas.confirmar-pagamento', $venda) }}" method="POST" class="mt-4 flex flex-col md:flex-row gap-3">
                                    @csrf
                                    @method('PATCH')
                                    <select name="forma_pagamento" required class="flex-1 px-4 py-3 bg-white/80 border border-[#FFD6F4] rounded-lg focus:outline-none focus:border-[#7B19E5]">
                                        <option value="">Forma de pagamento...</option>
                                        @foreach($formasPagamento as $formaPagamento)
                                            <option value="{{ $formaPagamento }}" @selected($venda->forma_pagamento === $formaPagamento)>{{ ucfirst(str_replace('_', ' ', $formaPagamento)) }}</option>
                                        @endforeach
                                    </select>
                                    <button class="px-5 py-3 rounded-full bg-gradient-to-r from-[#7B19E5] to-[#FF2EB6] text-white font-bold">Confirmar pagamento</button>
                                </form>
                            </article>
                        @empty
                            <p class="text-gray-500 text-center py-8">Nenhum produto pendente.</p>
                        @endforelse
                        <div class="pt-3 flex flex-wrap justify-center gap-2" data-pending-pagination></div>
                    </div>
                </section>

                <section class="glass-card rounded-2xl overflow-hidden" data-pending-section>
                    <div class="p-5 bg-white/70 border-b border-[#FFD6F4]">
                        <h2 class="text-xl font-title text-[#4A00B9]">Pacotes</h2>
                    </div>
                    <div class="p-5 bg-white/70 space-y-4">
                        @forelse($pacotesPendentes as $clientePacote)
                            <article class="p-4 rounded-xl bg-white/70 border border-[#FFD6F4]" data-pending-item>
                                <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-3">
                                    <div>
                                        <p class="font-bold text-[#4A00B9]">{{ $clientePacote->pacote->nome ?? 'Pacote removido' }}</p>
                                        <p class="text-sm text-gray-600">Cliente: {{ $clientePacote->cliente->name ?? 'Cliente removido' }}</p>
                                        <p class="text-sm text-gray-600">R$ {{ number_format($clientePacote->pacote->valor_total ?? 0, 2, ',', '.') }}</p>
                                        <p class="text-xs font-bold {{ $clientePacote->status_pagamento === 'aguardando_confirmacao' ? 'text-amber-600' : 'text-[#7B19E5]' }}">
                                            {{ $clientePacote->status_pagamento === 'aguardando_confirmacao' ? 'Cliente informou que pagou via PIX' : 'Aguardando PIX do cliente' }}
                                        </p>
                                    </div>
                                    <form action="{{ route('admin.cliente-pacotes.cancelar-pendente', $clientePacote) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button class="px-4 py-2 rounded-full bg-red-50 text-red-600 font-bold text-sm border border-red-200" onclick="return confirm('Cancelar este pedido?');">Cancelar</button>
                                    </form>
                                </div>
                                <form action="{{ route('admin.cliente-pacotes.confirmar-pagamento', $clientePacote) }}" method="POST" class="mt-4 flex flex-col md:flex-row gap-3">
                                    @csrf
                                    @method('PATCH')
                                    @if($clientePacote->status_pagamento === 'aguardando_confirmacao' && $clientePacote->forma_pagamento === 'pix')
                                        <input type="hidden" name="forma_pagamento" value="pix">
                                        <div class="flex-1 px-4 py-3 bg-[#7B19E5]/10 border border-[#FFD6F4] rounded-lg text-[#4A00B9] font-bold">
                                            Forma de pagamento: PIX
                                        </div>
                                    @else
                                        <select name="forma_pagamento" required class="flex-1 px-4 py-3 bg-white/80 border border-[#FFD6F4] rounded-lg focus:outline-none focus:border-[#7B19E5]">
                                            <option value="">Forma de pagamento...</option>
                                            @foreach($formasPagamento as $formaPagamento)
                                                <option value="{{ $formaPagamento }}" @selected(($clientePacote->forma_pagamento ?? 'pix') === $formaPagamento)>{{ ucfirst(str_replace('_', ' ', $formaPagamento)) }}</option>
                                            @endforeach
                                        </select>
                                    @endif
                                    <button class="px-5 py-3 rounded-full bg-gradient-to-r from-[#7B19E5] to-[#FF2EB6] text-white font-bold">Confirmar pagamento</button>
                                </form>
                            </article>
                        @empty
                            <p class="text-gray-500 text-center py-8">Nenhum pacote pendente.</p>
                        @endforelse
                        <div class="pt-3 flex flex-wrap justify-center gap-2" data-pending-pagination></div>
                    </div>
                </section>
            </div>
        </div>
    </div>
</x-app-layout>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&display=swap');

    .font-title {
        font-family: 'Playfair Display', serif;
        font-weight: 700;
    }

    .glass-card {
        background: rgba(255, 255, 255, 0.72);
        backdrop-filter: blur(12px);
        box-shadow: 0 18px 45px rgba(123, 25, 229, 0.10);
    }
</style>

<script>
    (() => {
        const perPage = 10;

        document.querySelectorAll('[data-pending-section]').forEach((section) => {
            const items = Array.from(section.querySelectorAll('[data-pending-item]'));
            const pagination = section.querySelector('[data-pending-pagination]');
            let currentPage = 1;

            if (!pagination || items.length <= perPage) {
                return;
            }

            const render = () => {
                const totalPages = Math.ceil(items.length / perPage);

                items.forEach((item, index) => {
                    item.classList.toggle('hidden', !(index >= (currentPage - 1) * perPage && index < currentPage * perPage));
                });

                pagination.innerHTML = '';

                for (let page = 1; page <= totalPages; page++) {
                    const button = document.createElement('button');
                    button.type = 'button';
                    button.textContent = page;
                    button.className = `w-10 h-10 rounded-full font-bold transition-all ${page === currentPage ? 'bg-[#7B19E5] text-white' : 'bg-white border border-[#FFD6F4] text-[#7B19E5]'}`;
                    button.addEventListener('click', () => {
                        currentPage = page;
                        render();
                        section.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    });
                    pagination.appendChild(button);
                }
            };

            render();
        });
    })();
</script>
