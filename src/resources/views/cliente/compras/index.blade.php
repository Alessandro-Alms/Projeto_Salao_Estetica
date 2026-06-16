<x-app-layout>
    <x-slot name="header">
        <div class="hidden"></div>
    </x-slot>

    @php
        $statusLabel = [
            'pendente' => 'Pendente',
            'aguardando_confirmacao' => 'Aguardando confirmação',
            'pago' => 'Pago',
            'cancelado' => 'Cancelado',
        ];

        $formaLabel = [
            'dinheiro' => 'Dinheiro',
            'pix' => 'PIX',
            'cartao_debito' => 'Cartão de débito',
            'cartao_credito' => 'Cartão de crédito',
            'pacote' => 'Pacote',
            'dividido' => 'Pagamento dividido',
        ];

        $pagamentoService = app(\App\Services\PagamentoService::class);
    @endphp

    <div class="py-12 relative">
        <div class="fixed inset-0 -z-10 overflow-hidden">
            <div class="absolute top-0 -left-20 w-[600px] h-[600px] bg-[#7B19E5]/20 rounded-full blur-3xl"></div>
            <div class="absolute bottom-0 -right-20 w-[700px] h-[700px] bg-[#FF2EB6]/20 rounded-full blur-3xl"></div>
            <div class="absolute inset-0 bg-gradient-to-br from-[#7B19E5]/5 via-white/30 to-[#FF2EB6]/5"></div>
        </div>

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 mb-8">
                <div>
                    <p class="text-sm uppercase tracking-[0.25em] text-[#FF2EB6] font-bold">histórico</p>
                    <h1 class="text-4xl font-title text-[#4A00B9] mt-2">Minhas compras</h1>
                    <p class="text-gray-600 mt-2 max-w-2xl">
                        Acompanhe tudo que você reservou, pagou ou teve cancelado.
                    </p>
                </div>
                <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center px-5 py-3 rounded-full bg-white/80 border border-[#FFD6F4] text-[#7B19E5] font-bold hover:bg-[#7B19E5] hover:text-white transition-all">
                    Voltar ao painel
                </a>
            </div>

            <section class="glass-card rounded-2xl overflow-hidden mb-6" data-history-section>
                <div class="p-5 bg-white/75 border-b border-[#FFD6F4]">
                    <h2 class="text-2xl font-title text-[#4A00B9]">Serviços</h2>
                </div>
                <div class="p-5 bg-white/70 space-y-3">
                    @forelse($agendamentos as $agendamento)
                        <article class="p-4 rounded-xl bg-white/80 border border-[#FFD6F4]" data-history-item>
                            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
                                <div>
                                    <p class="font-bold text-[#4A00B9]">{{ $agendamento->servico->nome ?? 'Serviço removido' }}</p>
                                    <p class="text-sm text-gray-600">Profissional: {{ $agendamento->profissional->name ?? '-' }}</p>
                                    <p class="text-xs text-gray-500">Atendido em {{ $agendamento->updated_at->format('d/m/Y H:i') }}</p>
                                </div>
                                <span class="px-3 py-1 rounded-full bg-[#7B19E5]/10 text-[#7B19E5] text-xs font-bold">
                                    {{ $statusLabel[$agendamento->status_pagamento] ?? 'Pago' }}
                                </span>
                            </div>
                            <div class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-3 text-sm">
                                <div>
                                    <p class="text-gray-500">Valor</p>
                                    <p class="font-black text-[#FF2EB6]">R$ {{ number_format($agendamento->valor_total, 2, ',', '.') }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-500">Pagamento</p>
                                    <p class="font-bold text-[#4A00B9]">{{ $pagamentoService->descricao($agendamento->pagamentos, $formaLabel[$agendamento->forma_pagamento] ?? ucfirst($agendamento->forma_pagamento ?? '-')) }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-500">Pago em</p>
                                    <p class="font-bold text-[#4A00B9]">{{ $agendamento->pago_em ? \Carbon\Carbon::parse($agendamento->pago_em)->format('d/m/Y H:i') : '-' }}</p>
                                </div>
                            </div>
                        </article>
                    @empty
                        <p class="text-center text-gray-500 py-8">Nenhum serviço pago ainda.</p>
                    @endforelse
                    <div class="pt-3 flex flex-wrap justify-center gap-2" data-history-pagination></div>
                </div>
            </section>

            <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
                <section class="glass-card rounded-2xl overflow-hidden" data-history-section>
                    <div class="p-5 bg-white/75 border-b border-[#FFD6F4]">
                        <h2 class="text-2xl font-title text-[#4A00B9]">Produtos</h2>
                    </div>
                    <div class="p-5 bg-white/70 space-y-3">
                        @forelse($vendas as $venda)
                            <article class="p-4 rounded-xl bg-white/80 border border-[#FFD6F4]" data-history-item>
                                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
                                    <div>
                                        <p class="font-bold text-[#4A00B9]">{{ $venda->produto->nome ?? 'Produto removido' }}</p>
                                        <p class="text-sm text-gray-600">Qtd: {{ $venda->quantidade }}</p>
                                        <p class="text-xs text-gray-500">Pedido em {{ $venda->created_at->format('d/m/Y H:i') }}</p>
                                    </div>
                                    <span class="px-3 py-1 rounded-full bg-[#7B19E5]/10 text-[#7B19E5] text-xs font-bold">
                                        {{ $statusLabel[$venda->status_pagamento] ?? ucfirst($venda->status_pagamento ?? 'pago') }}
                                    </span>
                                </div>
                                <div class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-3 text-sm">
                                    <div>
                                        <p class="text-gray-500">Valor</p>
                                        <p class="font-black text-[#FF2EB6]">R$ {{ number_format($venda->valor_venda, 2, ',', '.') }}</p>
                                    </div>
                                    <div>
                                        <p class="text-gray-500">Pagamento</p>
                                        <p class="font-bold text-[#4A00B9]">{{ $pagamentoService->descricao($venda->pagamentos, $formaLabel[$venda->forma_pagamento] ?? 'Presencial') }}</p>
                                    </div>
                                    <div>
                                        <p class="text-gray-500">Pago em</p>
                                        <p class="font-bold text-[#4A00B9]">{{ $venda->pago_em ? \Carbon\Carbon::parse($venda->pago_em)->format('d/m/Y H:i') : '-' }}</p>
                                    </div>
                                </div>
                            </article>
                        @empty
                            <p class="text-center text-gray-500 py-8">Nenhum produto comprado ainda.</p>
                        @endforelse
                        <div class="pt-3 flex flex-wrap justify-center gap-2" data-history-pagination></div>
                    </div>
                </section>

                <section class="glass-card rounded-2xl overflow-hidden" data-history-section>
                    <div class="p-5 bg-white/75 border-b border-[#FFD6F4]">
                        <h2 class="text-2xl font-title text-[#4A00B9]">Pacotes</h2>
                    </div>
                    <div class="p-5 bg-white/70 space-y-3">
                        @forelse($pacotes as $clientePacote)
                            <article class="p-4 rounded-xl bg-white/80 border border-[#FFD6F4]" data-history-item>
                                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
                                    <div>
                                        <p class="font-bold text-[#4A00B9]">{{ $clientePacote->pacote->nome ?? 'Pacote removido' }}</p>
                                        <p class="text-sm text-gray-600">
                                            {{ $clientePacote->pacote->servicos->pluck('nome')->join(', ') ?: 'Serviço removido' }}
                                        </p>
                                        <p class="text-xs text-gray-500">Pedido em {{ $clientePacote->created_at->format('d/m/Y H:i') }}</p>
                                    </div>
                                    <span class="px-3 py-1 rounded-full bg-[#7B19E5]/10 text-[#7B19E5] text-xs font-bold">
                                        {{ $statusLabel[$clientePacote->status_pagamento] ?? ucfirst($clientePacote->status_pagamento ?? 'pago') }}
                                    </span>
                                </div>
                                <div class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-3 text-sm">
                                    <div>
                                        <p class="text-gray-500">Valor</p>
                                        <p class="font-black text-[#FF2EB6]">R$ {{ number_format($clientePacote->pacote->valor_total ?? 0, 2, ',', '.') }}</p>
                                    </div>
                                    <div>
                                        <p class="text-gray-500">Pagamento</p>
                                        <p class="font-bold text-[#4A00B9]">{{ $pagamentoService->descricao($clientePacote->pagamentos, $formaLabel[$clientePacote->forma_pagamento] ?? '-') }}</p>
                                    </div>
                                    <div>
                                        <p class="text-gray-500">Pago em</p>
                                        <p class="font-bold text-[#4A00B9]">{{ $clientePacote->pago_em ? \Carbon\Carbon::parse($clientePacote->pago_em)->format('d/m/Y H:i') : '-' }}</p>
                                    </div>
                                </div>
                            </article>
                        @empty
                            <p class="text-center text-gray-500 py-8">Nenhum pacote comprado ainda.</p>
                        @endforelse
                        <div class="pt-3 flex flex-wrap justify-center gap-2" data-history-pagination></div>
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
        const perPage = 5;

        document.querySelectorAll('[data-history-section]').forEach((section) => {
            const items = Array.from(section.querySelectorAll('[data-history-item]'));
            const pagination = section.querySelector('[data-history-pagination]');
            let currentPage = 1;

            if (!pagination || items.length <= perPage) {
                return;
            }

            const render = () => {
                const totalPages = Math.ceil(items.length / perPage);

                items.forEach((item, index) => {
                    const visible = index >= (currentPage - 1) * perPage && index < currentPage * perPage;
                    item.classList.toggle('hidden', !visible);
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
