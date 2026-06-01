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
                        Escolha os produtos, monte seu pedido e reserve para retirar e pagar presencialmente.
                    </p>
                </div>

                <div class="flex flex-col sm:flex-row gap-3">
                    <button type="button" data-cart-open class="inline-flex items-center justify-center px-5 py-3 rounded-full bg-[#7B19E5] text-white font-bold hover:bg-[#FF2EB6] transition-all">
                        Pedido <span data-cart-count class="ml-2 px-2 py-0.5 rounded-full bg-white text-[#7B19E5] text-xs">0</span>
                    </button>
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center px-5 py-3 rounded-full bg-white/80 border border-[#FFD6F4] text-[#7B19E5] font-bold hover:bg-[#7B19E5] hover:text-white transition-all">
                        Voltar ao painel
                    </a>
                </div>
            </div>

            <form id="cartCheckoutForm" action="{{ route('cliente.produtos.comprar') }}" method="POST" class="hidden">
                @csrf
                <div data-cart-hidden-fields></div>
            </form>

            <section>
                <div class="mb-6">
                    <label class="block text-sm font-bold text-[#4A00B9] mb-2">Pesquisar produto</label>
                    <input type="search" data-product-search placeholder="Digite o nome do produto..."
                        class="w-full px-4 py-3 bg-white/80 border border-[#FFD6F4] rounded-xl focus:outline-none focus:border-[#7B19E5] focus:ring-2 focus:ring-[#7B19E5]/20 transition-all">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" data-products-grid>
                    @forelse($produtos as $produto)
                        <article class="glass-card rounded-2xl overflow-hidden border border-white/40 hover-lift" data-product-card data-product-name="{{ strtolower((string) $produto->nome . ' ' . (string) $produto->tipo . ' ' . (string) $produto->descricao) }}">
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

                                <div class="mt-auto space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-[#4A00B9] mb-2">Quantidade</label>
                                        <input type="number" value="1" min="1" max="{{ $produto->quantidade_estoque }}" required data-product-quantity="{{ $produto->id_produto }}"
                                            class="w-full px-4 py-3 bg-white/60 border border-[#FFD6F4] rounded-lg focus:outline-none focus:border-[#7B19E5] focus:ring-2 focus:ring-[#7B19E5]/20 transition-all">
                                    </div>

                                    <button type="button"
                                        data-add-to-cart
                                        data-product-id="{{ $produto->id_produto }}"
                                        data-product-name="{{ $produto->nome }}"
                                        data-product-price="{{ $produto->valor_unitario }}"
                                        data-product-stock="{{ $produto->quantidade_estoque }}"
                                        class="w-full bg-gradient-to-r from-[#7B19E5] to-[#FF2EB6] text-white px-6 py-3 rounded-full font-bold btn-primary shadow-lg hover:shadow-xl transition-all">
                                        Comprar para mim
                                    </button>
                                </div>
                            </div>
                        </article>
                    @empty
                        <div class="glass-card rounded-2xl p-8 text-center col-span-full border border-white/40">
                            <p class="text-gray-500">Nenhum produto disponivel em estoque no momento.</p>
                        </div>
                    @endforelse
                </div>

                <div class="mt-8 flex items-center justify-center gap-2" data-product-pagination></div>
            </section>

            @if($pedidosPendentes->count() > 0)
                <section class="glass-card rounded-2xl p-5 border border-white/40 mt-10 bg-white/75" data-open-order-section>
                    <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4 mb-5">
                        <div>
                            <h2 class="text-2xl font-title text-[#4A00B9]">Pedido em aberto</h2>
                            <p class="text-sm text-gray-600 mt-1">Enquanto o produto nao for pago na recepcao, voce pode editar ou cancelar.</p>
                        </div>
                        <form action="{{ route('cliente.produtos.comanda.cancelar-tudo') }}" method="POST" data-confirm-form data-confirm-title="Cancelar pedido?" data-confirm-message="Todos os itens pendentes serao removidos e o estoque volta para a loja." data-confirm-button="Cancelar pedido">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="px-5 py-3 rounded-full bg-red-50 border border-red-200 text-red-600 font-bold hover:bg-red-100 transition-all">
                                Cancelar pedido
                            </button>
                        </form>
                    </div>

                    <div class="space-y-3">
                        @foreach($pedidosPendentes as $pedido)
                            <div class="p-4 rounded-xl bg-white/80 border border-[#FFD6F4] flex flex-col lg:flex-row lg:items-center gap-4" data-open-order-item>
                                <div class="flex-1">
                                    <p class="font-title text-lg text-[#4A00B9]">{{ $pedido->produto->nome ?? 'Produto removido' }}</p>
                                    <p class="text-sm text-gray-600">Reservado para retirada e pagamento presencial</p>
                                </div>

                                <form action="{{ route('cliente.produtos.pedido.produto.atualizar', $pedido->produto_id) }}" method="POST" class="flex flex-col sm:flex-row sm:items-center gap-3">
                                    @csrf
                                    @method('PATCH')
                                    <label class="text-sm font-bold text-[#4A00B9]">Qtd</label>
                                    <input type="number" name="quantidade" value="{{ $pedido->quantidade }}" min="1" max="{{ ($pedido->produto->quantidade_estoque ?? 0) + $pedido->quantidade }}" required
                                        class="w-24 px-3 py-2 bg-white border border-[#FFD6F4] rounded-lg focus:outline-none focus:border-[#7B19E5]">
                                    <button type="submit" class="px-4 py-2 rounded-full bg-[#7B19E5] text-white font-bold hover:bg-[#FF2EB6] transition-all">
                                        Atualizar
                                    </button>
                                </form>

                                <div class="text-lg font-black text-[#FF2EB6] min-w-[120px] lg:text-right">
                                    R$ {{ number_format($pedido->valor_venda, 2, ',', '.') }}
                                </div>

                                <form action="{{ route('cliente.produtos.pedido.produto.cancelar', $pedido->produto_id) }}" method="POST" data-confirm-form data-confirm-title="Remover item?" data-confirm-message="Este item sera retirado do seu pedido." data-confirm-button="Remover">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="px-4 py-2 rounded-full bg-white border border-red-200 text-red-600 font-bold hover:bg-red-50 transition-all">
                                        Remover
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                    <div class="pt-3 flex flex-wrap justify-center gap-2" data-open-order-pagination></div>

                    <div class="mt-5 p-4 rounded-xl bg-[#7B19E5]/10 border border-[#FFD6F4] flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                        <span class="font-bold text-[#4A00B9]">Total pendente</span>
                        <span class="text-2xl font-black text-[#FF2EB6]">R$ {{ number_format($pedidosPendentes->sum('valor_venda'), 2, ',', '.') }}</span>
                    </div>
                </section>
            @endif
        </div>

        <aside data-cart-panel class="fixed inset-y-0 right-0 z-[70] w-full max-w-md translate-x-full transition-transform duration-300 bg-white shadow-2xl border-l border-[#FFD6F4] flex flex-col">
            <div class="p-5 border-b border-[#FFD6F4] flex items-start justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-title text-[#4A00B9]">Meu pedido</h2>
                    <p class="text-sm text-gray-600 mt-1">Revise antes de reservar para retirada.</p>
                </div>
                <button type="button" data-cart-close class="w-10 h-10 rounded-full bg-[#7B19E5]/10 text-[#7B19E5] font-black">x</button>
            </div>

            <div class="flex-1 overflow-y-auto p-5 space-y-3" data-cart-items>
                <p class="text-center text-gray-500 py-10">Seu pedido esta vazio.</p>
            </div>

            <div class="p-5 border-t border-[#FFD6F4] bg-white">
                <div class="flex items-center justify-between mb-4">
                    <span class="font-bold text-[#4A00B9]">Total</span>
                    <span class="text-3xl font-black text-[#FF2EB6]" data-cart-total>R$ 0,00</span>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <button type="button" data-cart-clear class="px-4 py-3 rounded-full bg-white border border-red-200 text-red-600 font-bold">
                        Cancelar
                    </button>
                    <button type="button" data-cart-checkout class="px-4 py-3 rounded-full bg-gradient-to-r from-[#7B19E5] to-[#FF2EB6] text-white font-bold">
                        Fechar pedido
                    </button>
                </div>
            </div>
        </aside>
        <div data-cart-backdrop class="hidden fixed inset-0 z-[60] bg-[#1A002B]/35 backdrop-blur-sm"></div>

        <div data-modal class="hidden fixed inset-0 z-[90] items-center justify-center px-4">
            <div class="absolute inset-0 bg-[#1A002B]/40 backdrop-blur-sm" data-modal-cancel></div>
            <div class="relative w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl border border-[#FFD6F4]">
                <h3 class="text-2xl font-title text-[#4A00B9]" data-modal-title>Confirmar?</h3>
                <p class="text-gray-600 mt-2" data-modal-message></p>
                <div class="mt-6 flex flex-col sm:flex-row gap-3 sm:justify-end">
                    <button type="button" data-modal-cancel class="px-5 py-3 rounded-full bg-white border border-[#FFD6F4] text-[#7B19E5] font-bold">
                        Voltar
                    </button>
                    <button type="button" data-modal-confirm class="px-5 py-3 rounded-full bg-gradient-to-r from-[#7B19E5] to-[#FF2EB6] text-white font-bold">
                        Confirmar
                    </button>
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

<script>
    (() => {
        const currency = new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' });
        const cartPanel = document.querySelector('[data-cart-panel]');
        const cartBackdrop = document.querySelector('[data-cart-backdrop]');
        const cartItems = document.querySelector('[data-cart-items]');
        const cartTotal = document.querySelector('[data-cart-total]');
        const cartCount = document.querySelector('[data-cart-count]');
        const checkoutForm = document.getElementById('cartCheckoutForm');
        const hiddenFields = document.querySelector('[data-cart-hidden-fields]');
        const modal = document.querySelector('[data-modal]');
        const modalTitle = document.querySelector('[data-modal-title]');
        const modalMessage = document.querySelector('[data-modal-message]');
        const modalConfirm = document.querySelector('[data-modal-confirm]');
        const productSearch = document.querySelector('[data-product-search]');
        const productCards = Array.from(document.querySelectorAll('[data-product-card]'));
        const productPagination = document.querySelector('[data-product-pagination]');
        const productsPerPage = 9;
        let currentProductPage = 1;
        const openOrderItems = Array.from(document.querySelectorAll('[data-open-order-item]'));
        const openOrderPagination = document.querySelector('[data-open-order-pagination]');
        let currentOpenOrderPage = 1;
        let cart = [];
        let modalAction = null;

        const openCart = () => {
            cartPanel.classList.remove('translate-x-full');
            cartBackdrop.classList.remove('hidden');
        };

        const closeCart = () => {
            cartPanel.classList.add('translate-x-full');
            cartBackdrop.classList.add('hidden');
        };

        const openModal = ({ title, message, button, action }) => {
            modalTitle.textContent = title;
            modalMessage.textContent = message;
            modalConfirm.textContent = button || 'Confirmar';
            modalAction = action;
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        };

        const closeModal = () => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            modalAction = null;
        };

        const renderCart = () => {
            const count = cart.reduce((total, item) => total + item.quantity, 0);
            const total = cart.reduce((sum, item) => sum + item.quantity * item.price, 0);

            cartCount.textContent = count;
            cartTotal.textContent = currency.format(total);
            cartItems.innerHTML = '';

            if (cart.length === 0) {
                cartItems.innerHTML = '<p class="text-center text-gray-500 py-10">Seu pedido esta vazio.</p>';
                return;
            }

            cart.forEach((item) => {
                const row = document.createElement('div');
                row.className = 'p-4 rounded-xl border border-[#FFD6F4] bg-white/80';
                row.innerHTML = `
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="font-bold text-[#4A00B9]">${item.name}</p>
                            <p class="text-sm text-gray-500">${currency.format(item.price)} cada</p>
                        </div>
                        <button type="button" class="text-red-600 font-bold" data-cart-remove="${item.id}">Remover</button>
                    </div>
                    <div class="mt-4 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-2">
                            <button type="button" class="w-9 h-9 rounded-full bg-[#7B19E5]/10 text-[#7B19E5] font-black" data-cart-step="${item.id}" data-step="-1">-</button>
                            <input type="number" min="1" max="${item.stock}" value="${item.quantity}" class="w-20 px-3 py-2 border border-[#FFD6F4] rounded-lg text-center" data-cart-quantity="${item.id}">
                            <button type="button" class="w-9 h-9 rounded-full bg-[#7B19E5]/10 text-[#7B19E5] font-black" data-cart-step="${item.id}" data-step="1">+</button>
                        </div>
                        <p class="font-black text-[#FF2EB6]">${currency.format(item.price * item.quantity)}</p>
                    </div>
                `;
                cartItems.appendChild(row);
            });
        };

        const normalizeText = (text) => (text || '')
            .toString()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .toLowerCase();

        const renderProducts = () => {
            const query = normalizeText(productSearch?.value || '');
            const visibleCards = productCards.filter((card) => normalizeText(card.dataset.productName).includes(query));
            const totalPages = Math.max(1, Math.ceil(visibleCards.length / productsPerPage));

            currentProductPage = Math.min(currentProductPage, totalPages);

            productCards.forEach((card) => card.classList.add('hidden'));
            visibleCards
                .slice((currentProductPage - 1) * productsPerPage, currentProductPage * productsPerPage)
                .forEach((card) => card.classList.remove('hidden'));

            productPagination.innerHTML = '';

            if (totalPages <= 1) {
                return;
            }

            for (let page = 1; page <= totalPages; page++) {
                const button = document.createElement('button');
                button.type = 'button';
                button.textContent = page;
                button.className = `w-10 h-10 rounded-full font-bold transition-all ${page === currentProductPage ? 'bg-[#7B19E5] text-white' : 'bg-white border border-[#FFD6F4] text-[#7B19E5]'}`;
                button.addEventListener('click', () => {
                    currentProductPage = page;
                    renderProducts();
                });
                productPagination.appendChild(button);
            }
        };

        const renderOpenOrders = () => {
            if (!openOrderPagination || openOrderItems.length <= 10) {
                return;
            }

            const totalPages = Math.ceil(openOrderItems.length / 10);
            currentOpenOrderPage = Math.min(currentOpenOrderPage, totalPages);

            openOrderItems.forEach((item, index) => {
                item.classList.toggle('hidden', !(index >= (currentOpenOrderPage - 1) * 10 && index < currentOpenOrderPage * 10));
            });

            openOrderPagination.innerHTML = '';

            for (let page = 1; page <= totalPages; page++) {
                const button = document.createElement('button');
                button.type = 'button';
                button.textContent = page;
                button.className = `w-10 h-10 rounded-full font-bold transition-all ${page === currentOpenOrderPage ? 'bg-[#7B19E5] text-white' : 'bg-white border border-[#FFD6F4] text-[#7B19E5]'}`;
                button.addEventListener('click', () => {
                    currentOpenOrderPage = page;
                    renderOpenOrders();
                    document.querySelector('[data-open-order-section]')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
                });
                openOrderPagination.appendChild(button);
            }
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

        document.querySelectorAll('[data-add-to-cart]').forEach((button) => {
            button.addEventListener('click', () => {
                const id = button.dataset.productId;
                const quantityInput = document.querySelector(`[data-product-quantity="${id}"]`);
                const quantity = Math.max(1, Number(quantityInput.value || 1));
                const stock = Number(button.dataset.productStock);
                const existing = cart.find((item) => item.id === id);

                if (existing) {
                    existing.quantity = Math.min(stock, existing.quantity + quantity);
                } else {
                    cart.push({
                        id,
                        name: button.dataset.productName,
                        price: Number(button.dataset.productPrice),
                        stock,
                        quantity: Math.min(stock, quantity),
                    });
                }

                renderCart();
                openCart();
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

        document.querySelector('[data-cart-checkout]').addEventListener('click', () => {
            if (cart.length === 0) {
                openModal({
                    title: 'Pedido vazio',
                    message: 'Adicione pelo menos um produto antes de fechar o pedido.',
                    button: 'Entendi',
                    action: closeModal,
                });
                return;
            }

            openModal({
                title: 'Fechar pedido?',
                message: 'Seu produto sera reservado para retirada e pagamento presencial. Enquanto nao for pago, voce ainda pode editar ou cancelar.',
                button: 'Reservar produtos',
                action: () => {
                    hiddenFields.innerHTML = '';
                    cart.forEach((item, index) => {
                        hiddenFields.insertAdjacentHTML('beforeend', `
                            <input type="hidden" name="itens[${index}][produto_id]" value="${item.id}">
                            <input type="hidden" name="itens[${index}][quantidade]" value="${item.quantity}">
                        `);
                    });
                    checkoutForm.submit();
                },
            });
        });

        document.querySelector('[data-cart-clear]').addEventListener('click', () => {
            if (cart.length === 0) {
                closeCart();
                return;
            }

            openModal({
                title: 'Cancelar pedido?',
                message: 'Os itens que ainda nao foram enviados serao removidos.',
                button: 'Cancelar pedido',
                action: () => {
                    cart = [];
                    renderCart();
                    closeModal();
                    closeCart();
                },
            });
        });

        document.querySelectorAll('[data-confirm-form]').forEach((form) => {
            form.addEventListener('submit', (event) => {
                event.preventDefault();
                openModal({
                    title: form.dataset.confirmTitle,
                    message: form.dataset.confirmMessage,
                    button: form.dataset.confirmButton,
                    action: () => form.submit(),
                });
            });
        });

        document.querySelector('[data-cart-open]').addEventListener('click', openCart);
        document.querySelector('[data-cart-close]').addEventListener('click', closeCart);
        cartBackdrop.addEventListener('click', closeCart);
        productSearch?.addEventListener('input', () => {
            currentProductPage = 1;
            renderProducts();
        });
        document.querySelectorAll('[data-modal-cancel]').forEach((button) => button.addEventListener('click', closeModal));
        modalConfirm.addEventListener('click', () => modalAction && modalAction());
        renderCart();
        renderProducts();
        renderOpenOrders();
    })();
</script>
