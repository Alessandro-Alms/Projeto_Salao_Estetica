@props([
    'formasPagamento' => [],
    'total' => null,
    'lockedPix' => false,
])

@php
    $valorInicial = $total !== null ? number_format((float) $total, 2, '.', '') : '';
@endphp

<div class="space-y-3" data-payment-split data-payment-total="{{ $valorInicial }}">
    <input type="hidden" name="forma_pagamento" value="{{ $lockedPix ? 'pix' : '' }}" data-payment-summary>

    <div class="space-y-2" data-payment-rows>
        <div class="grid grid-cols-1 md:grid-cols-[1fr_140px_auto] gap-2 items-end" data-payment-row>
            <div>
                <label class="block text-xs font-bold text-[#4A00B9] mb-1">Forma</label>
                <select name="pagamentos[0][forma_pagamento]" required data-payment-forma
                    class="w-full px-4 py-3 bg-white/80 border border-[#FFD6F4] rounded-lg focus:outline-none focus:border-[#7B19E5]">
                    <option value="">Selecione...</option>
                    @foreach($formasPagamento as $formaPagamento)
                        <option value="{{ $formaPagamento }}" @selected($lockedPix && $formaPagamento === 'pix')>
                            {{ ucfirst(str_replace('_', ' ', $formaPagamento)) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-[#4A00B9] mb-1">Valor</label>
                <input type="number" name="pagamentos[0][valor]" value="{{ $valorInicial }}" min="0.01" step="0.01" required data-payment-valor
                    class="w-full px-4 py-3 bg-white/80 border border-[#FFD6F4] rounded-lg focus:outline-none focus:border-[#7B19E5]">
            </div>
            <button type="button" data-payment-remove class="hidden px-4 py-3 rounded-full bg-red-50 text-red-600 font-bold border border-red-200">
                Remover
            </button>
        </div>
    </div>

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
        <button type="button" data-payment-add class="px-4 py-2 rounded-full bg-white border border-[#FFD6F4] text-[#7B19E5] font-bold">
            + Dividir pagamento
        </button>
        <p class="text-xs font-bold text-gray-500" data-payment-feedback></p>
    </div>
</div>

@once
    <script>
        (() => {
            const currency = new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' });
            const formas = @json(array_values($formasPagamento));
            const labels = Object.fromEntries(formas.map((forma) => [forma, forma.replaceAll('_', ' ').replace(/^\w/, (c) => c.toUpperCase())]));

            const optionsHtml = () => ['<option value="">Selecione...</option>']
                .concat(formas.map((forma) => `<option value="${forma}">${labels[forma]}</option>`))
                .join('');

            const init = (root) => {
                if (root.dataset.paymentReady === 'true') {
                    return;
                }

                root.dataset.paymentReady = 'true';
                const rows = root.querySelector('[data-payment-rows]');
                const summary = root.querySelector('[data-payment-summary]');
                const feedback = root.querySelector('[data-payment-feedback]');
                const addButton = root.querySelector('[data-payment-add]');
                const form = root.closest('form');

                const total = () => Number(root.dataset.paymentTotal || 0);

                const paymentState = () => {
                    const rowList = Array.from(rows.querySelectorAll('[data-payment-row]'));
                    const filled = rowList
                        .map((row) => ({
                            forma: row.querySelector('[data-payment-forma]').value,
                            valor: Number(row.querySelector('[data-payment-valor]').value || 0),
                        }))
                        .filter((item) => item.forma || item.valor > 0);

                    const soma = Math.round(filled.reduce((total, item) => total + item.valor, 0) * 100) / 100;
                    const diff = Math.round((total() - soma) * 100) / 100;

                    return {
                        filled,
                        soma,
                        diff,
                        exceeded: total() > 0 && diff < 0,
                        valid: total() <= 0 || diff === 0,
                    };
                };

                const refreshNames = () => {
                    Array.from(rows.querySelectorAll('[data-payment-row]')).forEach((row, index) => {
                        row.querySelector('[data-payment-forma]').name = `pagamentos[${index}][forma_pagamento]`;
                        row.querySelector('[data-payment-valor]').name = `pagamentos[${index}][valor]`;
                        row.querySelector('[data-payment-remove]').classList.toggle('hidden', index === 0);
                    });
                };

                const update = () => {
                    const state = paymentState();
                    const { filled, soma, diff, exceeded, valid } = state;
                    summary.value = filled.length > 1 ? (filled[0]?.forma || '') : (filled[0]?.forma || '');

                    form?.querySelectorAll('button[type="submit"]:not([form])').forEach((button) => {
                        button.disabled = !valid;
                        button.classList.toggle('opacity-50', !valid);
                        button.classList.toggle('cursor-not-allowed', !valid);
                    });

                    if (!feedback) {
                        return;
                    }

                    if (total() > 0) {
                        feedback.textContent = valid
                            ? `Total fechado: ${currency.format(soma)}`
                            : exceeded
                                ? `Esse valor excede o total do pedido em ${currency.format(Math.abs(diff))}.`
                                : `Ainda falta ${currency.format(diff)} para fechar o total.`;
                        feedback.classList.toggle('text-green-600', diff === 0);
                        feedback.classList.toggle('text-red-600', diff !== 0);
                    } else {
                        feedback.textContent = `Informado: ${currency.format(soma)}`;
                    }
                };

                addButton?.addEventListener('click', () => {
                    const row = rows.querySelector('[data-payment-row]').cloneNode(true);
                    row.querySelector('[data-payment-forma]').innerHTML = optionsHtml();
                    row.querySelector('[data-payment-forma]').value = '';
                    row.querySelector('[data-payment-valor]').value = '';
                    rows.appendChild(row);
                    refreshNames();
                    update();
                });

                rows.addEventListener('click', (event) => {
                    const button = event.target.closest('[data-payment-remove]');
                    if (!button) {
                        return;
                    }

                    button.closest('[data-payment-row]')?.remove();
                    refreshNames();
                    update();
                });

                rows.addEventListener('input', update);
                rows.addEventListener('change', update);
                form?.addEventListener('submit', (event) => {
                    const state = paymentState();

                    if (state.valid) {
                        return;
                    }

                    event.preventDefault();
                    update();
                    feedback?.scrollIntoView({ behavior: 'smooth', block: 'center' });
                });
                root.addEventListener('payment-total-change', () => {
                    const firstValue = rows.querySelector('[data-payment-valor]');
                    if (firstValue && rows.querySelectorAll('[data-payment-row]').length === 1) {
                        firstValue.value = total() ? total().toFixed(2) : '';
                    }
                    update();
                });

                refreshNames();
                update();
            };

            window.initPaymentSplits = () => document.querySelectorAll('[data-payment-split]').forEach(init);
            window.initPaymentSplits();
            document.addEventListener('DOMContentLoaded', window.initPaymentSplits);
        })();
    </script>
@endonce
