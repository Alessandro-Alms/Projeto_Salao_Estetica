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
                    <h1 class="text-4xl font-title text-[#4A00B9] mt-2">Vender pacote</h1>
                    <p class="text-gray-600 mt-2 max-w-2xl">
                        Escolha o cliente, selecione o pacote e finalize o pagamento em uma tela limpa.
                    </p>
                </div>

                <a href="{{ route('admin.vendas.pendentes') }}" class="inline-flex items-center justify-center px-5 py-3 rounded-full bg-white/80 border border-[#FFD6F4] text-[#7B19E5] font-bold hover:bg-[#7B19E5] hover:text-white transition-all">
                    Ver pendentes
                </a>
            </div>

            @if(session('success'))
                <div class="mb-6 p-4 rounded-lg bg-green-50/80 border border-green-200 text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 p-4 rounded-lg bg-red-50/80 border border-red-200 text-red-700">
                    @foreach($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form action="{{ route('admin.venda.store') }}" method="POST" class="space-y-6" data-package-sale-form>
                @csrf
                <input type="hidden" name="pacote_id" value="{{ old('pacote_id') }}" data-selected-package>

                <section class="glass-card rounded-2xl overflow-visible">
                    <div class="p-5 bg-white/75 border-b border-[#FFD6F4]">
                        <h2 class="text-2xl font-title text-[#4A00B9]">Cliente</h2>
                    </div>
                    <div class="p-5 bg-white/70">
                        <label class="block text-sm font-bold text-[#4A00B9] mb-2">Para quem e o pacote?</label>
                        <select name="cliente_id" required
                            data-searchable-select
                            data-searchable-placeholder="Digite nome ou CPF do cliente..."
                            class="w-full px-4 py-3 bg-white/80 border border-[#FFD6F4] rounded-xl focus:outline-none focus:border-[#7B19E5] focus:ring-2 focus:ring-[#7B19E5]/20 transition-all">
                            <option value="">Selecione o cliente...</option>
                            @foreach($clientes as $cliente)
                                <option value="{{ $cliente->id }}" @selected(old('cliente_id') == $cliente->id)>
                                    {{ $cliente->name }}{{ $cliente->cpf ? ' - CPF: ' . preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $cliente->cpf) : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </section>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
                    <section class="lg:col-span-2 min-w-0">
                        <div class="mb-6">
                            <label class="block text-sm font-bold text-[#4A00B9] mb-2">Pesquisar pacote</label>
                            <input type="search" data-package-search placeholder="Digite o nome do pacote..."
                                class="w-full px-4 py-3 bg-white/80 border border-[#FFD6F4] rounded-xl focus:outline-none focus:border-[#7B19E5] focus:ring-2 focus:ring-[#7B19E5]/20 transition-all">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6" data-packages-grid>
                            @forelse($pacotes as $pacote)
                                <article
                                    class="glass-card rounded-2xl overflow-hidden border border-white/40 hover-lift"
                                    data-package-card
                                    data-package-id="{{ $pacote->id_pacote }}"
                                    data-package-name="{{ strtolower((string) $pacote->nome) }}"
                                    data-package-title="{{ $pacote->nome }}"
                                    data-package-price="{{ $pacote->valor_total }}"
                                    data-package-sessions="{{ $pacote->quantidade_sessoes }}"
                                    data-package-validity="{{ $pacote->validade_dias }}"
                                >
                                    <div class="p-6 bg-white/75 h-full flex flex-col">
                                        <div class="flex items-center justify-between gap-3 mb-4">
                                            <span class="px-3 py-1 rounded-full bg-[#7B19E5]/10 text-[#7B19E5] text-xs font-bold">
                                                Pacote
                                            </span>
                                            <span class="text-xs font-bold text-gray-500">{{ $pacote->quantidade_sessoes }} sessao(oes)</span>
                                        </div>

                                        <h2 class="text-2xl font-title text-[#4A00B9]">{{ $pacote->nome }}</h2>

                                        <div class="mt-6 mb-6">
                                            <p class="text-sm text-gray-500 font-semibold">Valor do pacote</p>
                                            <p class="text-4xl font-black text-[#FF2EB6]">R$ {{ number_format($pacote->valor_total, 2, ',', '.') }}</p>
                                        </div>

                                        <div class="mb-6 grid grid-cols-2 gap-3">
                                            <div class="p-4 rounded-xl bg-[#7B19E5]/10 border border-[#FFD6F4]">
                                                <p class="text-xs text-[#4A00B9] font-bold uppercase">Sessoes</p>
                                                <p class="text-3xl font-black text-[#7B19E5]">{{ $pacote->quantidade_sessoes }}</p>
                                            </div>
                                            <div class="p-4 rounded-xl bg-[#7B19E5]/10 border border-[#FFD6F4]">
                                                <p class="text-xs text-[#4A00B9] font-bold uppercase">Validade</p>
                                                <p class="text-3xl font-black text-[#7B19E5]">{{ $pacote->validade_dias }}</p>
                                                <p class="text-xs text-gray-500">dias</p>
                                            </div>
                                        </div>

                                        <button type="button" data-select-package class="mt-auto w-full bg-gradient-to-r from-[#7B19E5] to-[#FF2EB6] text-white px-6 py-3 rounded-full font-bold btn-primary shadow-lg hover:shadow-xl transition-all">
                                            Selecionar pacote
                                        </button>
                                    </div>
                                </article>
                            @empty
                                <div class="glass-card rounded-2xl p-8 text-center col-span-full border border-white/40">
                                    <p class="text-gray-500">Nenhum pacote ativo no momento.</p>
                                </div>
                            @endforelse
                        </div>

                        <div class="mt-8 flex items-center justify-center gap-2" data-package-pagination></div>
                    </section>

                    <aside class="glass-card rounded-2xl overflow-hidden lg:sticky lg:top-6 min-w-0">
                        <div class="p-5 bg-white/75 border-b border-[#FFD6F4]">
                            <h2 class="text-2xl font-title text-[#4A00B9]">Resumo</h2>
                        </div>

                        <div class="p-5 bg-white/70 space-y-5">
                            <div class="p-4 rounded-xl bg-white/80 border border-[#FFD6F4]">
                                <p class="text-sm text-gray-500">Pacote selecionado</p>
                                <p class="font-bold text-[#4A00B9] mt-1" data-summary-package>Nenhum pacote selecionado</p>
                            </div>

                            <div class="grid grid-cols-2 gap-3 text-sm">
                                <div class="rounded-xl bg-[#7B19E5]/10 px-4 py-3">
                                    <p class="text-gray-500">Sessoes</p>
                                    <p class="font-bold text-[#4A00B9] mt-2" data-summary-sessions>-</p>
                                </div>
                                <div class="rounded-xl bg-[#7B19E5]/10 px-4 py-3">
                                    <p class="text-gray-500">Validade</p>
                                    <p class="font-bold text-[#4A00B9] mt-2" data-summary-validity>-</p>
                                </div>
                            </div>

                            <div class="p-4 rounded-xl bg-[#7B19E5]/10 border border-[#FFD6F4] flex items-center justify-between gap-3">
                                <span class="font-bold text-[#4A00B9]">Total</span>
                                <span class="text-3xl font-black text-[#FF2EB6]" data-summary-total>R$ 0,00</span>
                            </div>

                            <div class="grid grid-cols-1 gap-3">
                                <button type="button" data-checkout-open class="bg-gradient-to-r from-[#7B19E5] to-[#FF2EB6] text-white px-8 py-3 rounded-full font-bold btn-primary shadow-lg hover:shadow-xl transition-all">
                                    Finalizar pacote
                                </button>
                                <a href="{{ route('admin.financeiro.fechamento') }}" class="inline-flex items-center justify-center px-5 py-3 rounded-full bg-white border border-[#FFD6F4] text-[#7B19E5] font-bold hover:bg-[#7B19E5] hover:text-white transition-all">
                                    Ver fechamento
                                </a>
                                <p class="hidden text-sm font-bold text-red-600 text-center" data-package-feedback></p>
                            </div>
                        </div>
                    </aside>
                </div>

                <div data-checkout-modal class="hidden fixed inset-0 z-[100] items-center justify-center p-4 sm:p-6">
                    <div class="absolute inset-0 bg-[#14001F]/75 backdrop-blur-sm" data-checkout-close></div>
                    <div class="relative w-full max-w-4xl max-h-[90vh] overflow-hidden rounded-[30px] bg-white shadow-[0_32px_120px_rgba(20,0,31,0.42)] border border-white/80">
                        <div class="absolute inset-x-0 top-0 h-2 bg-gradient-to-r from-[#7B19E5] via-[#FF2EB6] to-[#FFD6F4]"></div>
                        <div class="max-h-[90vh] overflow-y-auto p-6 md:p-8">
                            <div class="flex items-start justify-between gap-4 mb-5">
                                <div>
                                    <p class="text-xs uppercase tracking-[0.2em] text-[#FF2EB6] font-bold">finalizar</p>
                                    <h3 class="text-3xl font-title text-[#4A00B9] mt-1">Fechar pacote</h3>
                                    <p class="text-sm text-gray-600 mt-1">Confira o pacote e informe como o cliente pagou.</p>
                                </div>
                                <button type="button" data-checkout-close class="w-10 h-10 rounded-full bg-[#7B19E5]/10 text-[#7B19E5] font-black">x</button>
                            </div>

                            <div class="grid grid-cols-1 lg:grid-cols-[1fr_360px] gap-5">
                                <section class="rounded-2xl border border-[#FFD6F4] bg-white/80 overflow-hidden">
                                    <div class="px-4 py-3 bg-[#7B19E5]/10 border-b border-[#FFD6F4]">
                                        <h4 class="font-bold text-[#4A00B9]">Resumo do pacote</h4>
                                    </div>
                                    <div class="p-4 space-y-3">
                                        <div class="p-4 rounded-xl bg-white border border-[#FFD6F4]">
                                            <p class="text-sm text-gray-500">Pacote</p>
                                            <p class="font-bold text-[#4A00B9] mt-1" data-checkout-package>Nenhum pacote selecionado</p>
                                        </div>
                                        <div class="grid grid-cols-2 gap-3">
                                            <div class="p-4 rounded-xl bg-[#7B19E5]/10 border border-[#FFD6F4]">
                                                <p class="text-xs text-gray-500">Sessoes</p>
                                                <p class="font-bold text-[#4A00B9]" data-checkout-sessions>-</p>
                                            </div>
                                            <div class="p-4 rounded-xl bg-[#7B19E5]/10 border border-[#FFD6F4]">
                                                <p class="text-xs text-gray-500">Validade</p>
                                                <p class="font-bold text-[#4A00B9]" data-checkout-validity>-</p>
                                            </div>
                                        </div>
                                    </div>
                                </section>

                                <section class="space-y-4">
                                    <div class="p-4 rounded-2xl bg-[#7B19E5]/10 border border-[#FFD6F4] flex items-center justify-between gap-3">
                                        <span class="font-bold text-[#4A00B9]">Total</span>
                                        <span class="text-3xl font-black text-[#FF2EB6]" data-checkout-total>R$ 0,00</span>
                                    </div>

                                    <div class="rounded-2xl border border-[#FFD6F4] bg-white/80 p-4">
                                        <h4 class="font-bold text-[#4A00B9]">Pagamento</h4>
                                        <p class="text-sm text-gray-600 mt-1 mb-4">Use uma forma unica ou divida o valor.</p>
                                        <x-payment-fields :formas-pagamento="$formasPagamento" />
                                    </div>

                                    <button type="submit" class="w-full bg-gradient-to-r from-[#7B19E5] to-[#FF2EB6] text-white px-8 py-3 rounded-full font-bold btn-primary shadow-lg hover:shadow-xl transition-all">
                                        Confirmar pagamento
                                    </button>
                                </section>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
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

    .hover-lift {
        transition: transform 0.28s ease, box-shadow 0.28s ease, border-color 0.28s ease;
    }

    .hover-lift:hover {
        transform: translateY(-5px);
        box-shadow: 0 24px 55px rgba(255, 46, 182, 0.18);
    }

    .package-selected {
        border-color: #FF2EB6;
        box-shadow: 0 24px 55px rgba(255, 46, 182, 0.22);
    }

    .btn-primary {
        position: relative;
        overflow: hidden;
    }

    [data-package-sale-form] [data-searchable-wrapper] input {
        background: rgba(255, 255, 255, 0.8);
        border-color: #FFD6F4;
        border-radius: 0.75rem;
        padding: 0.75rem 1rem;
    }

    [data-package-sale-form] [data-searchable-dropdown] {
        border-radius: 0.75rem;
        overflow: hidden;
    }
</style>

<script>
    (() => {
        const form = document.querySelector('[data-package-sale-form]');
        const packageCards = Array.from(document.querySelectorAll('[data-package-card]'));
        const packageSearch = document.querySelector('[data-package-search]');
        const packagePagination = document.querySelector('[data-package-pagination]');
        const selectedPackageInput = form?.querySelector('[data-selected-package]');
        const summaryPackage = form?.querySelector('[data-summary-package]');
        const summarySessions = form?.querySelector('[data-summary-sessions]');
        const summaryValidity = form?.querySelector('[data-summary-validity]');
        const summaryTotal = form?.querySelector('[data-summary-total]');
        const feedback = form?.querySelector('[data-package-feedback]');
        const checkoutModal = document.querySelector('[data-checkout-modal]');
        const checkoutPackage = document.querySelector('[data-checkout-package]');
        const checkoutSessions = document.querySelector('[data-checkout-sessions]');
        const checkoutValidity = document.querySelector('[data-checkout-validity]');
        const checkoutTotal = document.querySelector('[data-checkout-total]');
        const payment = form?.querySelector('[data-payment-split]');
        const currency = new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' });
        const perPage = 6;
        let currentPage = 1;
        let selectedPackage = null;

        const normalizeText = (text) => (text || '')
            .toString()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .toLowerCase();

        const showFeedback = (message) => {
            feedback.textContent = message;
            feedback.classList.remove('hidden');
        };

        const clearFeedback = () => {
            feedback.textContent = '';
            feedback.classList.add('hidden');
        };

        const syncPaymentTotal = () => {
            const total = Number(selectedPackage?.price || 0);
            if (!payment) {
                return;
            }

            payment.dataset.paymentTotal = total ? total.toFixed(2) : '';
            payment.dispatchEvent(new CustomEvent('payment-total-change'));
        };

        const renderSummary = () => {
            const total = Number(selectedPackage?.price || 0);

            summaryPackage.textContent = selectedPackage?.title || 'Nenhum pacote selecionado';
            summarySessions.textContent = selectedPackage ? selectedPackage.sessions : '-';
            summaryValidity.textContent = selectedPackage ? `${selectedPackage.validity} dias` : '-';
            summaryTotal.textContent = currency.format(total);

            checkoutPackage.textContent = selectedPackage?.title || 'Nenhum pacote selecionado';
            checkoutSessions.textContent = selectedPackage ? selectedPackage.sessions : '-';
            checkoutValidity.textContent = selectedPackage ? `${selectedPackage.validity} dias` : '-';
            checkoutTotal.textContent = currency.format(total);

            syncPaymentTotal();
        };

        const selectPackage = (card) => {
            selectedPackage = {
                id: card.dataset.packageId,
                title: card.dataset.packageTitle,
                price: Number(card.dataset.packagePrice || 0),
                sessions: card.dataset.packageSessions,
                validity: card.dataset.packageValidity,
            };

            packageCards.forEach((item) => item.classList.remove('package-selected'));
            card.classList.add('package-selected');
            selectedPackageInput.value = selectedPackage.id;
            clearFeedback();
            renderSummary();
        };

        const openCheckout = () => {
            if (!selectedPackageInput?.value) {
                showFeedback('Selecione um pacote antes de finalizar.');
                return;
            }

            renderSummary();
            checkoutModal?.classList.remove('hidden');
            checkoutModal?.classList.add('flex');
            document.body.classList.add('overflow-hidden');
        };

        const closeCheckout = () => {
            checkoutModal?.classList.add('hidden');
            checkoutModal?.classList.remove('flex');
            document.body.classList.remove('overflow-hidden');
        };

        const renderPackages = () => {
            const query = normalizeText(packageSearch?.value || '');
            const visibleCards = packageCards.filter((card) => normalizeText(card.dataset.packageName).includes(query));
            const totalPages = Math.max(1, Math.ceil(visibleCards.length / perPage));

            currentPage = Math.min(currentPage, totalPages);

            packageCards.forEach((card) => card.classList.add('hidden'));
            visibleCards
                .slice((currentPage - 1) * perPage, currentPage * perPage)
                .forEach((card) => card.classList.remove('hidden'));

            packagePagination.innerHTML = '';

            if (totalPages <= 1) {
                return;
            }

            for (let page = 1; page <= totalPages; page++) {
                const button = document.createElement('button');
                button.type = 'button';
                button.textContent = page;
                button.className = `w-10 h-10 rounded-full font-bold transition-all ${page === currentPage ? 'bg-[#7B19E5] text-white' : 'bg-white border border-[#FFD6F4] text-[#7B19E5]'}`;
                button.addEventListener('click', () => {
                    currentPage = page;
                    renderPackages();
                });
                packagePagination.appendChild(button);
            }
        };

        packageCards.forEach((card) => {
            card.querySelector('[data-select-package]')?.addEventListener('click', () => selectPackage(card));
        });

        document.querySelector('[data-checkout-open]')?.addEventListener('click', openCheckout);
        document.querySelectorAll('[data-checkout-close]').forEach((button) => button.addEventListener('click', closeCheckout));

        form?.addEventListener('submit', (event) => {
            if (!selectedPackageInput?.value) {
                event.preventDefault();
                closeCheckout();
                showFeedback('Selecione um pacote antes de confirmar.');
            }
        });

        packageSearch?.addEventListener('input', () => {
            currentPage = 1;
            renderPackages();
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                closeCheckout();
            }
        });

        const oldPackage = selectedPackageInput?.value;
        const oldCard = oldPackage ? packageCards.find((card) => card.dataset.packageId === oldPackage) : null;
        if (oldCard) {
            selectPackage(oldCard);
        } else {
            renderSummary();
        }

        renderPackages();
    })();
</script>
