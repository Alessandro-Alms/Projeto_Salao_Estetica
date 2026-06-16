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
                    <h1 class="text-4xl font-title text-[#4A00B9] mt-2">Vender produto</h1>
                    <p class="text-gray-600 mt-2 max-w-2xl">
                        Escolha o cliente, monte a comanda no carrinho e finalize o pagamento.
                    </p>
                </div>

                <a href="{{ route('admin.vendas.pendentes') }}" class="inline-flex items-center justify-center px-5 py-3 rounded-full bg-white/80 border border-[#FFD6F4] text-[#7B19E5] font-bold hover:bg-[#7B19E5] hover:text-white transition-all">
                    Ver pendentes
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

            <form action="{{ route('admin.vendas.produtos.store') }}" method="POST" class="space-y-6" data-cart-form data-sales-products>
                @csrf
                <div data-cart-hidden-fields></div>

                <section class="glass-card rounded-2xl overflow-visible">
                    <div class="p-5 bg-white/75 border-b border-[#FFD6F4]">
                        <h2 class="text-2xl font-title text-[#4A00B9]">Cliente</h2>
                    </div>
                    <div class="p-5 bg-white/70">
                        <label class="block text-sm font-bold text-[#4A00B9] mb-2">Para quem e a venda?</label>
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
                            <label class="block text-sm font-bold text-[#4A00B9] mb-2">Pesquisar produto</label>
                            <input type="search" data-product-search placeholder="Digite o nome do produto..."
                                class="w-full px-4 py-3 bg-white/80 border border-[#FFD6F4] rounded-xl focus:outline-none focus:border-[#7B19E5] focus:ring-2 focus:ring-[#7B19E5]/20 transition-all">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6" data-products-grid>
                            @forelse($produtos as $produto)
                                <article
                                    class="glass-card rounded-2xl overflow-hidden border border-white/40 hover-lift"
                                    data-product-card
                                    data-product-id="{{ $produto->id_produto }}"
                                    data-product-name="{{ strtolower((string) $produto->nome . ' ' . (string) $produto->tipo . ' ' . (string) $produto->descricao) }}"
                                >
                                    <div class="p-6 bg-white/75 h-full flex flex-col">
                                        <div class="flex items-center justify-between gap-3 mb-4">
                                            <span class="px-3 py-1 rounded-full bg-[#7B19E5]/10 text-[#7B19E5] text-xs font-bold">
                                                {{ ucfirst($produto->tipo) }}
                                            </span>
                                            <span class="text-xs font-bold text-gray-500">Estoque: {{ $produto->quantidade_estoque }}</span>
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
                                            <span class="text-sm text-[#4A00B9] font-bold uppercase">Disponivel</span>
                                            <span class="text-3xl font-black text-[#7B19E5]">{{ $produto->quantidade_estoque }}</span>
                                        </div>

                                        <div class="mt-auto space-y-4">
                                            <div>
                                                <label class="block text-sm font-medium text-[#4A00B9] mb-2">Quantidade</label>
                                                <input type="number" value="1" min="1" max="{{ max(1, $produto->quantidade_estoque) }}" data-product-quantity="{{ $produto->id_produto }}"
                                                    class="w-full px-4 py-3 bg-white/60 border border-[#FFD6F4] rounded-lg focus:outline-none focus:border-[#7B19E5] focus:ring-2 focus:ring-[#7B19E5]/20 transition-all">
                                            </div>

                                            <button
                                                type="button"
                                                data-add-to-cart
                                                data-product-id="{{ $produto->id_produto }}"
                                                data-product-name="{{ $produto->nome }}"
                                                data-product-price="{{ $produto->valor_unitario }}"
                                                data-product-stock="{{ $produto->quantidade_estoque }}"
                                                class="w-full bg-gradient-to-r from-[#7B19E5] to-[#FF2EB6] text-white px-6 py-3 rounded-full font-bold btn-primary shadow-lg hover:shadow-xl transition-all"
                                            >
                                                Adicionar na comanda
                                            </button>
                                        </div>
                                    </div>
                                </article>
                            @empty
                                <div class="glass-card rounded-2xl p-8 text-center col-span-full border border-white/40">
                                    <p class="text-gray-500">Nenhum produto disponivel no momento.</p>
                                </div>
                            @endforelse
                        </div>

                        <div class="mt-8 flex items-center justify-center gap-2" data-product-pagination></div>
                    </section>

                    <aside class="glass-card rounded-2xl overflow-hidden lg:sticky lg:top-6 min-w-0">
                    <div class="p-5 bg-white/75 border-b border-[#FFD6F4] flex items-start justify-between gap-4">
                        <div>
                            <h2 class="text-2xl font-title text-[#4A00B9]">Comanda</h2>
                            <p class="text-sm text-gray-600 mt-1">
                                <span data-cart-count>0</span> item(ns) na venda.
                            </p>
                        </div>
                        <button type="button" data-cart-clear class="px-4 py-2 rounded-full bg-red-50 text-red-600 font-bold border border-red-200">
                            Limpar
                        </button>
                    </div>

                    <div class="p-5 bg-white/70 space-y-5">
                        <div class="space-y-3 max-h-[330px] overflow-y-auto pr-1" data-cart-items>
                            <p class="text-center text-gray-500 py-8">Nenhum produto na comanda.</p>
                        </div>

                        <div class="p-4 rounded-xl bg-[#7B19E5]/10 border border-[#FFD6F4] flex items-center justify-between gap-3">
                            <span class="font-bold text-[#4A00B9]">Total da comanda</span>
                            <span class="text-3xl font-black text-[#FF2EB6]" data-cart-total>R$ 0,00</span>
                        </div>

                        <div class="grid grid-cols-1 gap-3">
                            <button type="button" data-checkout-open class="bg-gradient-to-r from-[#7B19E5] to-[#FF2EB6] text-white px-8 py-3 rounded-full font-bold btn-primary shadow-lg hover:shadow-xl transition-all">
                                Fechar comanda
                            </button>
                            <a href="{{ route('admin.financeiro.fechamento') }}" class="inline-flex items-center justify-center px-5 py-3 rounded-full bg-white border border-[#FFD6F4] text-[#7B19E5] font-bold hover:bg-[#7B19E5] hover:text-white transition-all">
                                Ver fechamento
                            </a>
                            <p class="hidden text-sm font-bold text-red-600 text-center" data-cart-feedback></p>
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
                                    <h3 class="text-3xl font-title text-[#4A00B9] mt-1">Fechar comanda</h3>
                                    <p class="text-sm text-gray-600 mt-1">Confira os produtos e informe como o cliente pagou.</p>
                                </div>
                                <button type="button" data-checkout-close class="w-10 h-10 rounded-full bg-[#7B19E5]/10 text-[#7B19E5] font-black">x</button>
                            </div>

                            <div class="grid grid-cols-1 lg:grid-cols-[1fr_360px] gap-5">
                                <section class="rounded-2xl border border-[#FFD6F4] bg-white/80 overflow-hidden">
                                    <div class="px-4 py-3 bg-[#7B19E5]/10 border-b border-[#FFD6F4]">
                                        <h4 class="font-bold text-[#4A00B9]">Itens da comanda</h4>
                                    </div>
                                    <div class="p-4 space-y-3 max-h-[360px] overflow-y-auto" data-checkout-items>
                                        <p class="text-center text-gray-500 py-8">Nenhum produto na comanda.</p>
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

    [data-sales-products] [data-searchable-wrapper] input {
        background: rgba(255, 255, 255, 0.8);
        border-color: #FFD6F4;
        border-radius: 0.75rem;
        padding: 0.75rem 1rem;
    }

    [data-sales-products] [data-searchable-dropdown] {
        border-radius: 0.75rem;
        overflow: hidden;
    }
</style>

<script>
    (() => {
        const form = document.querySelector('[data-cart-form]');
        const hiddenFields = document.querySelector('[data-cart-hidden-fields]');
        const cartItems = document.querySelector('[data-cart-items]');
        const cartTotal = document.querySelector('[data-cart-total]');
        const cartCount = document.querySelector('[data-cart-count]');
        const cartFeedback = document.querySelector('[data-cart-feedback]');
        const checkoutModal = document.querySelector('[data-checkout-modal]');
        const checkoutItems = document.querySelector('[data-checkout-items]');
        const checkoutTotal = document.querySelector('[data-checkout-total]');
        const payment = form?.querySelector('[data-payment-split]');
        const productCards = Array.from(document.querySelectorAll('[data-product-card]'));
        const productSearch = document.querySelector('[data-product-search]');
        const productPagination = document.querySelector('[data-product-pagination]');
        const currency = new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' });
        const perPage = 6;
        let currentPage = 1;
        let cart = [];

        const normalizeText = (text) => (text || '')
            .toString()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .toLowerCase();

        const cartSum = () => cart.reduce((total, item) => total + item.quantity * item.price, 0);
        const cartQuantity = () => cart.reduce((total, item) => total + item.quantity, 0);

        const syncPaymentTotal = () => {
            const total = cartSum();
            if (!payment) {
                return;
            }

            payment.dataset.paymentTotal = total ? total.toFixed(2) : '';
            payment.dispatchEvent(new CustomEvent('payment-total-change'));
        };

        const syncHiddenFields = () => {
            hiddenFields.innerHTML = '';

            cart.forEach((item, index) => {
                hiddenFields.insertAdjacentHTML('beforeend', `
                    <input type="hidden" name="itens[${index}][produto_id]" value="${item.id}">
                    <input type="hidden" name="itens[${index}][quantidade]" value="${item.quantity}">
                `);
            });
        };

        const showFeedback = (message) => {
            cartFeedback.textContent = message;
            cartFeedback.classList.remove('hidden');
        };

        const clearFeedback = () => {
            cartFeedback.textContent = '';
            cartFeedback.classList.add('hidden');
        };

        const openCheckout = () => {
            if (cart.length === 0) {
                showFeedback('Adicione pelo menos um produto antes de fechar a comanda.');
                return;
            }

            renderCheckout();
            checkoutModal?.classList.remove('hidden');
            checkoutModal?.classList.add('flex');
            document.body.classList.add('overflow-hidden');
        };

        const closeCheckout = () => {
            checkoutModal?.classList.add('hidden');
            checkoutModal?.classList.remove('flex');
            document.body.classList.remove('overflow-hidden');
        };

        const setQuantity = (id, quantity) => {
            cart = cart.map((item) => {
                if (item.id !== id) {
                    return item;
                }

                return { ...item, quantity: Math.max(1, Math.min(item.stock, quantity)) };
            });
            renderCart();
        };

        const renderCart = () => {
            clearFeedback();
            cartItems.innerHTML = '';
            cartCount.textContent = cartQuantity();
            cartTotal.textContent = currency.format(cartSum());

            if (cart.length === 0) {
                cartItems.innerHTML = '<p class="text-center text-gray-500 py-8">Nenhum produto na comanda.</p>';
                syncHiddenFields();
                syncPaymentTotal();
                return;
            }

            cart.forEach((item) => {
                const row = document.createElement('div');
                row.className = 'p-4 rounded-xl bg-white/80 border border-[#FFD6F4]';
                row.innerHTML = `
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="font-bold text-[#4A00B9]">${item.name}</p>
                            <p class="text-xs text-gray-500">${currency.format(item.price)} cada</p>
                        </div>
                        <button type="button" class="text-red-600 text-sm font-bold" data-cart-remove="${item.id}">Remover</button>
                    </div>
                    <div class="mt-4 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-2">
                            <button type="button" class="w-9 h-9 rounded-full bg-[#7B19E5]/10 text-[#7B19E5] font-black" data-cart-step="${item.id}" data-step="-1">-</button>
                            <input type="number" min="1" max="${item.stock}" value="${item.quantity}" class="w-20 px-3 py-2 bg-white border border-[#FFD6F4] rounded-lg text-center" data-cart-quantity="${item.id}">
                            <button type="button" class="w-9 h-9 rounded-full bg-[#7B19E5]/10 text-[#7B19E5] font-black" data-cart-step="${item.id}" data-step="1">+</button>
                        </div>
                        <p class="font-black text-[#FF2EB6]">${currency.format(item.price * item.quantity)}</p>
                    </div>
                `;
                cartItems.appendChild(row);
            });

            syncHiddenFields();
            syncPaymentTotal();
            renderCheckout();
        };

        const renderCheckout = () => {
            if (!checkoutItems || !checkoutTotal) {
                return;
            }

            checkoutItems.innerHTML = '';
            checkoutTotal.textContent = currency.format(cartSum());

            if (cart.length === 0) {
                checkoutItems.innerHTML = '<p class="text-center text-gray-500 py-8">Nenhum produto na comanda.</p>';
                return;
            }

            cart.forEach((item) => {
                const row = document.createElement('div');
                row.className = 'p-4 rounded-xl bg-white border border-[#FFD6F4] flex items-start justify-between gap-4';
                row.innerHTML = `
                    <div>
                        <p class="font-bold text-[#4A00B9]">${item.name}</p>
                        <p class="text-xs text-gray-500">Qtd: ${item.quantity} x ${currency.format(item.price)}</p>
                    </div>
                    <p class="font-black text-[#FF2EB6]">${currency.format(item.price * item.quantity)}</p>
                `;
                checkoutItems.appendChild(row);
            });
        };

        const renderProducts = () => {
            const query = normalizeText(productSearch?.value || '');
            const visibleCards = productCards.filter((card) => normalizeText(card.dataset.productName).includes(query));
            const totalPages = Math.max(1, Math.ceil(visibleCards.length / perPage));

            currentPage = Math.min(currentPage, totalPages);

            productCards.forEach((card) => card.classList.add('hidden'));
            visibleCards
                .slice((currentPage - 1) * perPage, currentPage * perPage)
                .forEach((card) => card.classList.remove('hidden'));

            productPagination.innerHTML = '';

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
                    renderProducts();
                });
                productPagination.appendChild(button);
            }
        };

        document.querySelectorAll('[data-add-to-cart]').forEach((button) => {
            button.addEventListener('click', () => {
                const id = button.dataset.productId;
                const quantityInput = document.querySelector(`[data-product-quantity="${id}"]`);
                const quantity = Math.max(1, Number(quantityInput?.value || 1));
                const stock = Number(button.dataset.productStock || 0);
                const existing = cart.find((item) => item.id === id);

                if (stock <= 0) {
                    showFeedback('Produto sem estoque disponivel.');
                    return;
                }

                if (existing) {
                    existing.quantity = Math.min(stock, existing.quantity + quantity);
                } else {
                    cart.push({
                        id,
                        name: button.dataset.productName,
                        price: Number(button.dataset.productPrice || 0),
                        stock,
                        quantity: Math.min(stock, quantity),
                    });
                }

                renderCart();
            });
        });

        cartItems.addEventListener('click', (event) => {
            const removeButton = event.target.closest('[data-cart-remove]');
            const stepButton = event.target.closest('[data-cart-step]');

            if (removeButton) {
                cart = cart.filter((item) => item.id !== removeButton.dataset.cartRemove);
                renderCart();
            }

            if (stepButton) {
                const item = cart.find((cartItem) => cartItem.id === stepButton.dataset.cartStep);
                if (item) {
                    setQuantity(item.id, item.quantity + Number(stepButton.dataset.step));
                }
            }
        });

        cartItems.addEventListener('change', (event) => {
            const input = event.target.closest('[data-cart-quantity]');
            if (input) {
                setQuantity(input.dataset.cartQuantity, Number(input.value || 1));
            }
        });

        document.querySelector('[data-cart-clear]')?.addEventListener('click', () => {
            cart = [];
            renderCart();
            closeCheckout();
        });

        document.querySelector('[data-checkout-open]')?.addEventListener('click', openCheckout);
        document.querySelectorAll('[data-checkout-close]').forEach((button) => {
            button.addEventListener('click', closeCheckout);
        });

        form?.addEventListener('submit', (event) => {
            if (cart.length === 0) {
                event.preventDefault();
                showFeedback('Adicione pelo menos um produto antes de fechar a comanda.');
                closeCheckout();
                return;
            }

            syncHiddenFields();
        });

        productSearch?.addEventListener('input', () => {
            currentPage = 1;
            renderProducts();
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                closeCheckout();
            }
        });

        renderCart();
        renderProducts();
    })();
</script>
