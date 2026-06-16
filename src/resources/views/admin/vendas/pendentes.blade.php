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

            <form data-pending-filter class="glass-card rounded-2xl p-4 mb-6 bg-white/75 border border-white/40">
                <label class="block text-sm font-bold text-[#4A00B9] mb-2">Buscar pedido</label>
                <div>
                    <input
                        type="text"
                        data-filter-search
                        placeholder="Nome ou CPF do cliente"
                        class="w-full px-4 py-3 bg-white/80 border border-[#FFD6F4] rounded-xl focus:outline-none focus:border-[#7B19E5] focus:ring-2 focus:ring-[#7B19E5]/20"
                    >
                </div>
            </form>

            <div id="pending-root" class="grid grid-cols-1 xl:grid-cols-2 gap-6">
                <section class="glass-card rounded-2xl overflow-hidden" data-pending-section>
                    <div class="p-5 bg-white/70 border-b border-[#FFD6F4]">
                        <h2 class="text-xl font-title text-[#4A00B9]">Produtos</h2>
                    </div>
                    <div class="p-5 bg-white/70 space-y-4">
                        @forelse($comandasPendentes as $comanda)
                            <article
                                class="p-4 rounded-xl bg-white/70 border border-[#FFD6F4]"
                                data-pending-item
                                data-filter-row
                                data-filter-text="{{ $comanda->cliente->name ?? '' }} {{ $comanda->cliente->cpf ?? '' }}"
                            >
                                <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-3">
                                    <div>
                                        <p class="font-bold text-[#4A00B9]">{{ $comanda->cliente->name ?? 'Cliente removido' }}</p>
                                        <p class="text-sm text-gray-600">CPF: {{ $comanda->cliente?->cpf ? preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $comanda->cliente->cpf) : '-' }}</p>
                                        <p class="text-xs text-gray-500">Pedido em {{ $comanda->criada_em?->format('d/m/Y H:i') }}</p>
                                        <p class="text-xs text-gray-500">{{ $comanda->vendas->count() }} item(ns) | R$ {{ number_format($comanda->valor_total, 2, ',', '.') }}</p>
                                    </div>
                                    <button type="button" data-modal-open="comanda-{{ $comanda->codigo_pedido }}" class="px-5 py-3 rounded-full bg-[#7B19E5] text-white font-bold hover:bg-[#FF2EB6] transition-all">
                                        Analisar
                                    </button>
                                </div>
                            </article>

                            <div data-modal="comanda-{{ $comanda->codigo_pedido }}" class="hidden fixed inset-0 z-[100] items-center justify-center p-4 sm:p-6">
                                <div class="absolute inset-0 bg-[#14001F]/75 backdrop-blur-sm" data-modal-close></div>
                                <div class="relative w-full max-w-4xl max-h-[90vh] overflow-hidden rounded-[30px] bg-white shadow-[0_32px_120px_rgba(20,0,31,0.42)] border border-white/80">
                                    <div class="absolute inset-x-0 top-0 h-2 bg-gradient-to-r from-[#7B19E5] via-[#FF2EB6] to-[#FFD6F4]"></div>
                                    <div class="max-h-[90vh] overflow-y-auto p-6 md:p-8">
                                    <div class="flex items-start justify-between gap-4 mb-5">
                                        <div>
                                            <p class="text-xs uppercase tracking-[0.2em] text-[#FF2EB6] font-bold">comanda</p>
                                            <h3 class="text-2xl font-title text-[#4A00B9] mt-1">{{ $comanda->cliente->name ?? 'Cliente removido' }}</h3>
                                            <p class="text-sm text-gray-600">CPF: {{ $comanda->cliente?->cpf ? preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $comanda->cliente->cpf) : '-' }}</p>
                                            <p class="text-sm text-gray-600">Pedido em {{ $comanda->criada_em?->format('d/m/Y H:i') }}</p>
                                        </div>
                                        <button type="button" data-modal-close class="w-10 h-10 rounded-full bg-[#7B19E5]/10 text-[#7B19E5] font-black">x</button>
                                    </div>

                                    <div class="rounded-xl border border-[#FFD6F4] bg-white/70 overflow-hidden">
                                        @foreach($comanda->vendas as $venda)
                                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 px-4 py-3 border-b border-[#FFD6F4] last:border-b-0">
                                                <div>
                                                    <p class="font-bold text-[#4A00B9]">{{ $venda->produto->nome ?? 'Produto removido' }}</p>
                                                    <p class="text-xs text-gray-500">Qtd: {{ $venda->quantidade }}</p>
                                                </div>
                                                <p class="font-black text-[#FF2EB6]">R$ {{ number_format($venda->valor_venda, 2, ',', '.') }}</p>
                                            </div>
                                        @endforeach
                                    </div>

                                    <div class="mt-4 p-4 rounded-xl bg-[#7B19E5]/10 border border-[#FFD6F4] flex items-center justify-between gap-3">
                                        <span class="font-bold text-[#4A00B9]">Total da comanda</span>
                                        <span class="text-2xl font-black text-[#FF2EB6]">R$ {{ number_format($comanda->valor_total, 2, ',', '.') }}</span>
                                    </div>

                                    <form action="{{ route('admin.vendas.comandas.confirmar-pagamento', [$comanda->cliente, $comanda->codigo_pedido]) }}" method="POST" class="mt-4 space-y-3">
                                        @csrf
                                        @method('PATCH')
                                        <x-payment-fields :formas-pagamento="$formasPagamento" :total="$comanda->valor_total" />
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                            <button type="submit" class="px-5 py-3 rounded-full bg-gradient-to-r from-[#7B19E5] to-[#FF2EB6] text-white font-bold">Confirmar pagamento</button>
                                            <button type="submit" form="cancel-comanda-{{ $comanda->codigo_pedido }}" class="px-5 py-3 rounded-full bg-red-50 text-red-600 font-bold border border-red-200" onclick="return confirm('Cancelar toda esta comanda?');">Cancelar comanda</button>
                                        </div>
                                    </form>

                                    <form id="cancel-comanda-{{ $comanda->codigo_pedido }}" action="{{ route('admin.vendas.comandas.cancelar-pendente', [$comanda->cliente, $comanda->codigo_pedido]) }}" method="POST" class="hidden">
                                        @csrf
                                        @method('PATCH')
                                    </form>
                                    </div>
                                </div>
                            </div>
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
                            @php
                                $pixJaInformado = $clientePacote->status_pagamento === 'aguardando_confirmacao' && $clientePacote->forma_pagamento === 'pix';
                            @endphp
                            <article
                                class="p-4 rounded-xl bg-white/70 border border-[#FFD6F4]"
                                data-pending-item
                                data-filter-row
                                data-filter-text="{{ $clientePacote->cliente->name ?? '' }} {{ $clientePacote->cliente->cpf ?? '' }}"
                            >
                                <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-3">
                                    <div>
                                        <p class="font-bold text-[#4A00B9]">{{ $clientePacote->cliente->name ?? 'Cliente removido' }}</p>
                                        <p class="text-sm text-gray-600">CPF: {{ $clientePacote->cliente?->cpf ? preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $clientePacote->cliente->cpf) : '-' }}</p>
                                        <p class="text-xs text-gray-500">Pedido em {{ $clientePacote->created_at->format('d/m/Y H:i') }}</p>
                                        <p class="text-sm text-gray-600">{{ $clientePacote->pacote->nome ?? 'Pacote removido' }} | R$ {{ number_format($clientePacote->pacote->valor_total ?? 0, 2, ',', '.') }}</p>
                                        <p class="text-xs font-bold {{ $clientePacote->status_pagamento === 'aguardando_confirmacao' ? 'text-amber-600' : 'text-[#7B19E5]' }}">
                                            {{ $clientePacote->status_pagamento === 'aguardando_confirmacao' ? 'Cliente informou que pagou via PIX' : 'Aguardando PIX do cliente' }}
                                        </p>
                                    </div>
                                    <button type="button" data-modal-open="pacote-{{ $clientePacote->id }}" class="px-5 py-3 rounded-full bg-[#7B19E5] text-white font-bold hover:bg-[#FF2EB6] transition-all">
                                        Analisar
                                    </button>
                                </div>
                            </article>

                            <div data-modal="pacote-{{ $clientePacote->id }}" class="hidden fixed inset-0 z-[100] items-center justify-center p-4 sm:p-6">
                                <div class="absolute inset-0 bg-[#14001F]/75 backdrop-blur-sm" data-modal-close></div>
                                <div class="relative w-full max-w-4xl max-h-[90vh] overflow-hidden rounded-[30px] bg-white shadow-[0_32px_120px_rgba(20,0,31,0.42)] border border-white/80">
                                    <div class="absolute inset-x-0 top-0 h-2 bg-gradient-to-r from-[#7B19E5] via-[#FF2EB6] to-[#FFD6F4]"></div>
                                    <div class="max-h-[90vh] overflow-y-auto p-6 md:p-8">
                                    <div class="flex items-start justify-between gap-4 mb-5">
                                        <div>
                                            <p class="text-xs uppercase tracking-[0.2em] text-[#FF2EB6] font-bold">pacote</p>
                                            <h3 class="text-2xl font-title text-[#4A00B9] mt-1">{{ $clientePacote->cliente->name ?? 'Cliente removido' }}</h3>
                                            <p class="text-sm text-gray-600">CPF: {{ $clientePacote->cliente?->cpf ? preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $clientePacote->cliente->cpf) : '-' }}</p>
                                            <p class="text-sm text-gray-600">Pedido em {{ $clientePacote->created_at->format('d/m/Y H:i') }}</p>
                                        </div>
                                        <button type="button" data-modal-close class="w-10 h-10 rounded-full bg-[#7B19E5]/10 text-[#7B19E5] font-black">x</button>
                                    </div>

                                    <div class="rounded-xl border border-[#FFD6F4] bg-white/70 p-4">
                                        <p class="font-bold text-[#4A00B9]">{{ $clientePacote->pacote->nome ?? 'Pacote removido' }}</p>
                                        <p class="text-sm text-gray-600 mt-1">
                                            {{ $clientePacote->pacote->servicos->pluck('nome')->join(', ') ?: 'Servicos removidos' }}
                                        </p>
                                        <p class="text-xs font-bold mt-3 {{ $clientePacote->status_pagamento === 'aguardando_confirmacao' ? 'text-amber-600' : 'text-[#7B19E5]' }}">
                                            {{ $clientePacote->status_pagamento === 'aguardando_confirmacao' ? 'Cliente informou que pagou via PIX' : 'Aguardando PIX do cliente' }}
                                        </p>
                                    </div>

                                    <div class="mt-4 p-4 rounded-xl bg-[#7B19E5]/10 border border-[#FFD6F4] flex items-center justify-between gap-3">
                                        <span class="font-bold text-[#4A00B9]">Total do pacote</span>
                                        <span class="text-2xl font-black text-[#FF2EB6]">R$ {{ number_format($clientePacote->pacote->valor_total ?? 0, 2, ',', '.') }}</span>
                                    </div>

                                    <form action="{{ route('admin.cliente-pacotes.confirmar-pagamento', $clientePacote) }}" method="POST" class="mt-4 space-y-3">
                                        @csrf
                                        @method('PATCH')
                                        @if($pixJaInformado)
                                            <input type="hidden" name="forma_pagamento" value="pix">
                                            <div class="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3">
                                                <p class="text-sm font-bold text-amber-700">PIX informado pelo cliente</p>
                                                <p class="text-xs text-amber-700/80 mt-1">
                                                    Confira se o valor entrou na conta. Ao aprovar, o sistema registra o pagamento total como PIX.
                                                </p>
                                            </div>
                                        @else
                                            <x-payment-fields
                                                :formas-pagamento="$formasPagamento"
                                                :total="$clientePacote->pacote->valor_total ?? 0"
                                            />
                                        @endif
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                            <button type="submit" class="px-5 py-3 rounded-full bg-gradient-to-r from-[#7B19E5] to-[#FF2EB6] text-white font-bold">
                                                {{ $pixJaInformado ? 'Aprovar PIX' : 'Confirmar pagamento' }}
                                            </button>
                                            <button type="submit" form="cancel-pacote-{{ $clientePacote->id }}" class="px-5 py-3 rounded-full bg-red-50 text-red-600 font-bold border border-red-200" onclick="return confirm('Cancelar este pedido?');">Cancelar pedido</button>
                                        </div>
                                    </form>

                                    <form id="cancel-pacote-{{ $clientePacote->id }}" action="{{ route('admin.cliente-pacotes.cancelar-pendente', $clientePacote) }}" method="POST" class="hidden">
                                        @csrf
                                        @method('PATCH')
                                    </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500 text-center py-8">Nenhum pacote pendente.</p>
                        @endforelse
                        <div class="pt-3 flex flex-wrap justify-center gap-2" data-pending-pagination></div>
                    </div>
                </section>
                <div class="glass-card rounded-2xl p-8 text-center xl:col-span-2 hidden" data-filter-empty>
                    <p class="text-gray-500">Nenhuma pendencia encontrada para esta busca.</p>
                </div>
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
        const perPage = 5;
        const filter = document.querySelector('[data-pending-filter]');
        const searchInput = filter?.querySelector('[data-filter-search]');
        const emptyState = document.querySelector('[data-filter-empty]');

        const normalizeText = (text) => (text || '')
            .toString()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .toLowerCase();

        const sections = Array.from(document.querySelectorAll('[data-pending-section]')).map((section) => ({
            section,
            items: Array.from(section.querySelectorAll('[data-filter-row]')),
            pagination: section.querySelector('[data-pending-pagination]'),
            page: 1,
        }));

        const render = () => {
            const search = normalizeText(searchInput?.value || '');
            let totalVisible = 0;

            sections.forEach((state) => {
                const matchedItems = state.items.filter((item) => {
                    return !search || normalizeText(item.dataset.filterText).includes(search);
                });

                totalVisible += matchedItems.length;
                const totalPages = Math.max(1, Math.ceil(matchedItems.length / perPage));
                state.page = Math.min(state.page, totalPages);

                state.items.forEach((item) => item.classList.add('hidden'));
                matchedItems
                    .slice((state.page - 1) * perPage, state.page * perPage)
                    .forEach((item) => item.classList.remove('hidden'));

                if (!state.pagination) {
                    return;
                }

                state.pagination.innerHTML = '';

                if (matchedItems.length <= perPage) {
                    return;
                }

                for (let page = 1; page <= totalPages; page++) {
                    const button = document.createElement('button');
                    button.type = 'button';
                    button.textContent = page;
                    button.className = `w-10 h-10 rounded-full font-bold transition-all ${page === state.page ? 'bg-[#7B19E5] text-white' : 'bg-white border border-[#FFD6F4] text-[#7B19E5]'}`;
                    button.addEventListener('click', () => {
                        state.page = page;
                        render();
                        state.section.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    });
                    state.pagination.appendChild(button);
                }
            });

            emptyState?.classList.toggle('hidden', totalVisible > 0);
        };

        filter?.addEventListener('submit', (event) => event.preventDefault());
        searchInput?.addEventListener('input', () => {
            sections.forEach((state) => state.page = 1);
            render();
        });

        document.addEventListener('click', (event) => {
            const openButton = event.target.closest('[data-modal-open]');
            const closeButton = event.target.closest('[data-modal-close]');

            if (openButton) {
                const modal = document.querySelector(`[data-modal="${openButton.dataset.modalOpen}"]`);
                if (modal && modal.parentElement !== document.body) {
                    document.body.appendChild(modal);
                }
                modal?.classList.remove('hidden');
                modal?.classList.add('flex');
                document.body.classList.add('overflow-hidden');
            }

            if (closeButton) {
                const modal = closeButton.closest('[data-modal]');
                modal?.classList.add('hidden');
                modal?.classList.remove('flex');
                document.body.classList.remove('overflow-hidden');
            }
        });

        document.addEventListener('keydown', (event) => {
            if (event.key !== 'Escape') {
                return;
            }

            document.querySelectorAll('[data-modal]').forEach((modal) => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            });
            document.body.classList.remove('overflow-hidden');
        });

        render();
    })();
</script>
