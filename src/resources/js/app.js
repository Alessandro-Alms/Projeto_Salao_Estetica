import './bootstrap';

import Alpine from 'alpinejs';
import Chart from 'chart.js/auto';

window.Alpine = Alpine;
window.Chart = Chart;

window.SalaoCharts = {
    ready(callback) {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', callback);
            return;
        }

        callback();
    },

    money(value) {
        return new Intl.NumberFormat('pt-BR', {
            style: 'currency',
            currency: 'BRL',
        }).format(value || 0);
    },

    create(canvasId, config) {
        const canvas = document.getElementById(canvasId);

        if (!canvas) {
            return null;
        }

        window.SalaoCharts.instances ||= {};

        if (window.SalaoCharts.instances[canvasId]) {
            window.SalaoCharts.instances[canvasId].destroy();
        }

        const context = canvas.getContext('2d');
        const primaryGradient = context.createLinearGradient(0, 0, canvas.clientWidth || 600, canvas.clientHeight || 320);
        primaryGradient.addColorStop(0, '#7B19E5');
        primaryGradient.addColorStop(1, '#FF2EB6');

        const softGradient = context.createLinearGradient(0, 0, 0, canvas.clientHeight || 320);
        softGradient.addColorStop(0, 'rgba(123, 25, 229, 0.28)');
        softGradient.addColorStop(1, 'rgba(255, 46, 182, 0.03)');

        const decorateDataset = (dataset, index) => ({
            borderRadius: dataset.borderRadius ?? 14,
            hoverBorderWidth: dataset.hoverBorderWidth ?? 3,
            hoverOffset: dataset.hoverOffset ?? 8,
            ...dataset,
            backgroundColor: dataset.backgroundColor || (index === 0 ? primaryGradient : softGradient),
        });

        config.data = {
            ...config.data,
            datasets: (config.data?.datasets || []).map(decorateDataset),
        };

        const datasets = config.data?.datasets || [];
        const hasData = datasets.some((dataset) => {
            const values = Array.isArray(dataset.data) ? dataset.data : [];

            return values.some((value) => Number(value) !== 0);
        });

        if (!hasData) {
            const wrapper = canvas.parentElement;

            canvas.classList.add('hidden');

            if (wrapper && !wrapper.querySelector('[data-chart-empty]')) {
                const emptyState = document.createElement('div');
                emptyState.dataset.chartEmpty = 'true';
                emptyState.className = 'h-full min-h-64 flex flex-col items-center justify-center text-center rounded-2xl border border-dashed border-[#FFD6F4] bg-white/40 px-6';
                emptyState.innerHTML = `
                    <p class="text-3xl text-[#7B19E5] mb-3">✧</p>
                    <p class="font-bold text-[#4A00B9]">Sem dados para gerar este grafico</p>
                    <p class="text-sm text-gray-500 mt-1">Tente outro periodo ou registre movimentos primeiro.</p>
                `;

                wrapper.appendChild(emptyState);
            }

            return null;
        }

        window.SalaoCharts.instances[canvasId] = new Chart(canvas, {
            ...config,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: {
                    duration: 900,
                    easing: 'easeOutQuart',
                },
                interaction: {
                    intersect: false,
                    mode: 'nearest',
                },
                plugins: {
                    legend: {
                        labels: {
                            color: '#4A00B9',
                            font: {
                                family: 'Syne',
                                weight: '700',
                            },
                        },
                    },
                    tooltip: {
                        backgroundColor: '#1A002B',
                        titleColor: '#FFFFFF',
                        bodyColor: '#FFFFFF',
                        padding: 12,
                        cornerRadius: 12,
                        ...config.options?.plugins?.tooltip,
                    },
                    ...config.options?.plugins,
                },
                scales: config.options?.scales,
                ...config.options,
            },
        });

        return window.SalaoCharts.instances[canvasId];
    },

    renderConfiguredCharts() {
        document.querySelectorAll('script[type="application/json"][data-salao-chart]').forEach((script) => {
            const canvasId = script.dataset.salaoChart;

            if (!canvasId || script.dataset.rendered === 'true') {
                return;
            }

            try {
                const config = JSON.parse(script.textContent || '{}');
                window.SalaoCharts.create(canvasId, config);
                script.dataset.rendered = 'true';
            } catch (error) {
                console.error(`Erro ao renderizar grafico ${canvasId}:`, error);
            }
        });
    },
};

const queuedChartCallbacks = Array.isArray(window.SalaoChartQueue)
    ? window.SalaoChartQueue
    : [];

window.SalaoChartQueue = {
    push(callback) {
        window.SalaoCharts.ready(callback);
    },
};

queuedChartCallbacks.forEach((callback) => {
    window.SalaoCharts.ready(callback);
});

window.SalaoCharts.ready(() => {
    window.SalaoCharts.renderConfiguredCharts();
});

const applyInputMasks = () => {
    if (!window.IMask) {
        return;
    }

    const masks = [
        { selector: '[data-mask="cpf"], input[name="cpf"], input#cpf', pattern: '000.000.000-00' },
        { selector: '[data-mask="telefone"], input[name="telefone"], input#telefone', pattern: '(00) 00000-0000' },
    ];

    masks.forEach(({ selector, pattern }) => {
        document.querySelectorAll(selector).forEach((input) => {
            if (!(input instanceof HTMLInputElement)) {
                return;
            }

            if (input.dataset.maskApplied === 'true') {
                return;
            }

            window.IMask(input, { mask: pattern });
            input.setAttribute('inputmode', 'numeric');
            input.dataset.maskApplied = 'true';
        });
    });
};

const scrollRestoreKey = 'salao-scroll-restore';

const saveScrollPosition = () => {
    const payload = {
        path: `${window.location.pathname}${window.location.search}`,
        y: window.scrollY || 0,
    };

    sessionStorage.setItem(scrollRestoreKey, JSON.stringify(payload));
};

const restoreScrollPosition = () => {
    const raw = sessionStorage.getItem(scrollRestoreKey);

    if (!raw) {
        return;
    }

    sessionStorage.removeItem(scrollRestoreKey);

    let payload;

    try {
        payload = JSON.parse(raw);
    } catch (error) {
        return;
    }

    if (!payload || payload.path !== `${window.location.pathname}${window.location.search}`) {
        return;
    }

    requestAnimationFrame(() => {
        window.scrollTo(0, Math.max(0, Number(payload.y) || 0));
    });
};

const onDomReady = () => {
    applyInputMasks();
    restoreScrollPosition();
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', onDomReady);
} else {
    onDomReady();
}

document.addEventListener('submit', (event) => {
    const form = event.target;

    if (!(form instanceof HTMLFormElement)) {
        return;
    }

    saveScrollPosition();

    form.querySelectorAll('[data-mask="cpf"], input[name="cpf"], input#cpf, [data-mask="telefone"], input[name="telefone"], input#telefone')
        .forEach((input) => {
            if (input instanceof HTMLInputElement) {
                input.value = input.value.replace(/\D/g, '');
            }
        });
}, true);

Alpine.start();
