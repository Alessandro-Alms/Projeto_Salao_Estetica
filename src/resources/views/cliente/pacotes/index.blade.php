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
                        Escolha um pacote e envie o pedido para confirmação no caixa antes da liberação das sessões.
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

            @if($pacotesPendentes->count() > 0)
                <section class="mb-12">
                    <div class="flex items-center gap-2 mb-5">
                        <span class="text-[#FF2EB6] text-xl">✧</span>
                        <h2 class="text-2xl font-title text-[#4A00B9]">Pagamentos pendentes</h2>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        @foreach($pacotesPendentes as $clientePacote)
                            <article class="glass-card rounded-2xl p-5 border border-white/40 bg-white/75">
                                <div class="flex flex-col md:flex-row gap-5">
                                    <div class="flex-1">
                                        <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold {{ $clientePacote->status_pagamento === 'aguardando_confirmacao' ? 'bg-amber-100 text-amber-700' : 'bg-[#7B19E5]/10 text-[#7B19E5]' }}">
                                            {{ $clientePacote->status_pagamento === 'aguardando_confirmacao' ? 'Aguardando confirmação' : 'PIX pendente' }}
                                        </span>
                                        <h3 class="text-xl font-title text-[#4A00B9] mt-4">{{ $clientePacote->pacote->nome ?? 'Pacote removido' }}</h3>
                                        <p class="text-sm text-gray-600 mt-1">
                                            {{ $clientePacote->pacote->servicos->pluck('nome')->join(', ') ?: 'Serviço removido' }}
                                        </p>
                                        <p class="text-3xl font-black text-[#FF2EB6] mt-4">
                                            R$ {{ number_format($clientePacote->pacote->valor_total ?? 0, 2, ',', '.') }}
                                        </p>
                                        <p class="text-sm text-gray-600 mt-3">
                                            As sessões só ficam ativas depois que a equipe confirmar que o PIX entrou.
                                        </p>
                                    </div>

                                    <div class="md:w-56">
                                        <div class="p-3 rounded-xl bg-white border border-[#FFD6F4] text-center">
                                            <img
                                                data-pix-qr
                                                data-pix-name="{{ $clientePacote->pacote->nome ?? 'Pacote' }}"
                                                data-pix-amount="{{ number_format((float) ($clientePacote->pacote->valor_total ?? 0), 2, '.', '') }}"
                                                alt="QR Code Pix"
                                                class="w-44 h-44 mx-auto rounded-lg"
                                            >
                                            <textarea data-pix-copy readonly rows="3" class="mt-3 w-full text-xs p-2 rounded-lg border border-[#FFD6F4] bg-[#7B19E5]/5"></textarea>
                                            <button type="button" data-copy-pix class="mt-2 w-full px-4 py-2 rounded-full bg-white border border-[#FFD6F4] text-[#7B19E5] font-bold">
                                                Copiar PIX
                                            </button>
                                        </div>

                                        @if($clientePacote->status_pagamento === 'pendente')
                                            <form action="{{ route('cliente.pacotes.informar-pagamento', $clientePacote) }}" method="POST" class="mt-3">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="w-full px-4 py-3 rounded-full bg-gradient-to-r from-[#7B19E5] to-[#FF2EB6] text-white font-bold">
                                                    Já paguei
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </section>
            @endif

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

                                <form action="{{ route('cliente.pacotes.comprar') }}" method="POST" class="mt-auto">
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

<script>
    (() => {
        const pixKey = '09885376305';
        const merchantName = 'CHEIAS DE CHARME';
        const merchantCity = 'SAO PAULO';

        const field = (id, value) => {
            const stringValue = String(value);
            return id + String(stringValue.length).padStart(2, '0') + stringValue;
        };

        const crc16 = (payload) => {
            let crc = 0xFFFF;

            for (let i = 0; i < payload.length; i++) {
                crc ^= payload.charCodeAt(i) << 8;
                for (let bit = 0; bit < 8; bit++) {
                    crc = (crc & 0x8000) ? ((crc << 1) ^ 0x1021) : (crc << 1);
                    crc &= 0xFFFF;
                }
            }

            return crc.toString(16).toUpperCase().padStart(4, '0');
        };

        const pixPayload = (amount, description) => {
            const merchantAccount = field('00', 'br.gov.bcb.pix') + field('01', pixKey) + field('02', description.slice(0, 25));
            const payloadWithoutCrc = [
                field('00', '01'),
                field('26', merchantAccount),
                field('52', '0000'),
                field('53', '986'),
                field('54', amount),
                field('58', 'BR'),
                field('59', merchantName.slice(0, 25)),
                field('60', merchantCity.slice(0, 15)),
                field('62', field('05', 'PACOTE')),
                '6304',
            ].join('');

            return payloadWithoutCrc + crc16(payloadWithoutCrc);
        };

        document.querySelectorAll('[data-pix-qr]').forEach((image) => {
            const payload = pixPayload(image.dataset.pixAmount, image.dataset.pixName || 'Pacote');
            const wrapper = image.closest('div');
            const textarea = wrapper?.querySelector('[data-pix-copy]');

            image.src = 'https://api.qrserver.com/v1/create-qr-code/?size=220x220&data=' + encodeURIComponent(payload);

            if (textarea) {
                textarea.value = payload;
            }
        });

        document.querySelectorAll('[data-copy-pix]').forEach((button) => {
            button.addEventListener('click', async () => {
                const textarea = button.closest('div')?.querySelector('[data-pix-copy]');

                if (!textarea) {
                    return;
                }

                await navigator.clipboard.writeText(textarea.value);
                button.textContent = 'PIX copiado';
                window.setTimeout(() => button.textContent = 'Copiar PIX', 1600);
            });
        });
    })();
</script>
