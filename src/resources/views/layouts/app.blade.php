<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Cheias de Charme') }}</title>

        <!-- Favicon -->
        <link rel="icon" type="image/x-icon" href="{{ asset('img/Prancheta1.ico') }}">
        <link rel="shortcut icon" type="image/x-icon" href="{{ asset('img/Prancheta1.ico') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400;500;600&display=swap" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
        
        <!-- Fontes Cheias de Charme -->
        <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700;800&family=Playfair+Display:wght@700&family=Space+Grotesk:wght@300;400;500;600;700&display=swap" rel="stylesheet">

        <script src="https://unpkg.com/imask"></script>

        <style>
            * { font-family: 'Syne', sans-serif; }
            .font-title { font-family: 'Playfair Display', serif; font-weight: 700; letter-spacing: -0.02em; }
            .font-body { font-family: 'Space Grotesk', sans-serif; }
            html { color-scheme: light; }
            
            ::-webkit-scrollbar { width: 8px; background: #f8f0ff; }
            ::-webkit-scrollbar-thumb { background: linear-gradient(135deg, #7B19E5, #FF2EB6); border-radius: 10px; }


            /* =========================
            DARK MODE GLOBAL
            ========================= */



            /* Header e navegação */

            /* Cards, blocos e fundos claros */

            /* Corrige gradientes que usam via-white */

            /* Textos escuros */

            /* Textos secundários */

            /* Roxo e rosa no dark */


            /* Títulos */

            /* Bordas */

            /* Inputs, selects e textarea */



            /* Dropdown pesquisável */


            /* Tabelas */



            /* Alertas verdes */


            /* Alertas amarelos */


            /* Alertas vermelhos */


            /* Fundos personalizados do projeto */


            /* Calendário, horários, serviços e profissionais */







            /* Estado vazio dos gráficos */


            /* Botão modo escuro */

            /* Hover claro */

            /* Scrollbar */

            /* Destaque de serviço selecionado no dark mode */



            /* Efeito do botão */
            .btn-primary {
                position: relative;
                overflow: hidden;
                transition: all 0.3s ease;
                z-index: 1;
                transform: translateY(0);
            }
            /* Efeito do botão */
            .btn-primary {
                position: relative;
                overflow: hidden;
                transition: all 0.3s ease;
                z-index: 1;
                transform: translateY(0);
            }

            .btn-primary::before {
                content: '';
                position: absolute;
                top: 50%;
                left: 50%;
                width: 0;
                height: 0;
                border-radius: 50%;
                background: rgba(255, 255, 255, 0.3);
                transform: translate(-50%, -50%);
                transition: width 0.6s ease, height 0.6s ease;
                z-index: -1;
            }

            .btn-primary:hover::before {
                width: 300px;
                height: 300px;
            }

            .btn-primary:hover {
                transform: translateY(-2px);
            }
        </style>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    </head>
    <body class="font-body antialiased bg-gradient-to-br from-[#7B19E5]/5 via-white to-[#FF2EB6]/5">
        <div class="min-h-screen">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white/70 backdrop-blur-sm border-b border-[#FFD6F4] shadow-sm hidden">
                    <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                        <h2 class="font-title text-xl text-[#1A002B]">
                            {{ $header }}
                        </h2>
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main class="pt-6 md:pt-8">
                @if(session('acesso_restrito'))
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div class="mb-4 rounded-xl border border-[#FFD6F4] bg-[#FF2EB6]/10 px-4 py-3 text-sm font-semibold text-[#4A00B9]">
                            {{ session('acesso_restrito') }}
                        </div>
                    </div>
                @endif
                {{ $slot }}
            </main>
        </div>

        <script>
(() => {
                const baseInputClasses = 'w-full px-4 py-3 bg-white/50 border border-[#FFD6F4] rounded-lg focus:outline-none focus:border-[#7B19E5] focus:ring-2 focus:ring-[#7B19E5]/20 transition-all';
                const compactInputClasses = 'w-full px-4 py-2.5 bg-white/50 border border-[#FFD6F4] rounded-lg focus:outline-none focus:border-[#7B19E5] focus:ring-2 focus:ring-[#7B19E5]/20 transition-all text-sm';

                const closeAllSearchableSelects = (except = null) => {
                    document.querySelectorAll('[data-searchable-dropdown]').forEach((dropdown) => {
                        if (dropdown !== except) {
                            dropdown.classList.add('hidden');
                        }
                    });
                };

                const normalizeText = (text) => {
                    return (text || '')
                        .toString()
                        .normalize('NFD')
                        .replace(/[\u0300-\u036f]/g, '')
                        .toLowerCase();
                };

                const buildSearchableSelect = (select) => {
                    if (select.dataset.searchableReady === 'true') {
                        return;
                    }

                    const wrapper = document.createElement('div');
                    wrapper.className = 'relative';
                    wrapper.dataset.searchableWrapper = 'true';

                    const input = document.createElement('input');
                    input.type = 'text';
                    input.autocomplete = 'off';
                    input.placeholder = select.dataset.searchablePlaceholder || select.options[0]?.text?.trim() || 'Pesquise uma opção...';
                    input.className = select.dataset.searchableCompact === 'true' ? compactInputClasses : baseInputClasses;

                    const isRequired = select.required || select.dataset.searchableRequired === 'true';
                    if (isRequired) {
                        select.dataset.searchableRequired = 'true';
                        select.required = false;
                        input.required = true;
                    }

                    const dropdown = document.createElement('div');
                    dropdown.dataset.searchableDropdown = 'true';
                    dropdown.className = 'hidden absolute top-full left-0 right-0 mt-1 bg-white/95 border border-[#FFD6F4] rounded-lg shadow-lg z-50 max-h-60 overflow-y-auto';

                    const emptyState = document.createElement('div');
                    emptyState.className = 'hidden px-4 py-3 text-sm text-gray-500';
                    emptyState.textContent = 'Nenhum resultado encontrado.';

                    const options = Array.from(select.options).map((option) => {
                        const item = document.createElement('button');
                        item.type = 'button';
                        item.className = 'block w-full px-4 py-3 text-left hover:bg-[#7B19E5]/10 transition-colors';
                        item.dataset.value = option.value;
                        item.dataset.search = normalizeText(option.text);
                        item.disabled = option.disabled;
                        const label = document.createElement('span');
                        label.className = `block font-medium ${option.value ? 'text-[#4A00B9]' : 'text-gray-500'}`;
                        label.textContent = option.text.trim();
                        item.appendChild(label);

                        item.addEventListener('click', () => {
                            select.value = option.value;
                            input.value = option.value ? option.text.trim() : '';
                            input.setCustomValidity(option.value || !isRequired ? '' : 'Selecione uma opção da lista.');
                            dropdown.classList.add('hidden');
                            select.dispatchEvent(new Event('change', { bubbles: true }));
                        });

                        dropdown.appendChild(item);
                        return item;
                    });

                    dropdown.appendChild(emptyState);
                    select.classList.add('hidden');
                    select.dataset.searchableReady = 'true';
                    select.parentNode.insertBefore(wrapper, select.nextSibling);
                    wrapper.appendChild(input);
                    wrapper.appendChild(dropdown);
                    select.addEventListener('searchable:refresh', () => {
                        wrapper.remove();
                        select.classList.remove('hidden');
                        select.dataset.searchableReady = 'false';
                        buildSearchableSelect(select);
                    }, { once: true });

                    const selected = select.selectedOptions[0];
                    if (selected?.value) {
                        input.value = selected.text.trim();
                    }
                    input.setCustomValidity(select.value || !isRequired ? '' : 'Selecione uma opção da lista.');

                    const filterOptions = () => {
                        const query = normalizeText(input.value);
                        let visible = 0;

                        options.forEach((item) => {
                            const matches = !query || item.dataset.search.includes(query);
                            item.classList.toggle('hidden', !matches);
                            if (matches) {
                                visible++;
                            }
                        });

                        emptyState.classList.toggle('hidden', visible > 0);
                    };

                    input.addEventListener('focus', () => {
                        closeAllSearchableSelects(dropdown);
                        filterOptions();
                        dropdown.classList.remove('hidden');
                    });

                    input.addEventListener('input', () => {
                        if (select.value) {
                            select.value = '';
                            input.setCustomValidity(isRequired ? 'Selecione uma opção da lista.' : '');
                            select.dispatchEvent(new Event('change', { bubbles: true }));
                        }

                        filterOptions();
                        dropdown.classList.remove('hidden');
                    });

                    document.addEventListener('click', (event) => {
                        if (!wrapper.contains(event.target)) {
                            dropdown.classList.add('hidden');
                        }
                    });
                };

                window.iniciarSelectsPesquisaveis = (root = document) => {
                    root.querySelectorAll('select[data-searchable-select]').forEach(buildSearchableSelect);
                };

                document.addEventListener('DOMContentLoaded', () => window.iniciarSelectsPesquisaveis());
            })();

            (() => {
                document.addEventListener('DOMContentLoaded', () => {
                    const normalizeText = (text) => {
                        return (text || '')
                            .toString()
                            .normalize('NFD')
                            .replace(/[\u0300-\u036f]/g, '')
                            .toLowerCase();
                    };

                    document.querySelectorAll('[data-local-table-filter]').forEach((filter) => {
                        const targetSelector = filter.dataset.localTableFilter;
                        const rows = Array.from(document.querySelectorAll(`${targetSelector} [data-filter-row]`));
                        const emptyRow = document.querySelector(`${targetSelector} [data-filter-empty]`);
                        const searchInput = filter.querySelector('[data-filter-search]');
                        const selectFilters = Array.from(filter.querySelectorAll('[data-filter-select]'));

                        const applyFilter = () => {
                            const search = normalizeText(searchInput?.value || '');
                            let visibleCount = 0;

                            rows.forEach((row) => {
                                const matchesSearch = !search || normalizeText(row.dataset.filterText).includes(search);
                                const matchesSelects = selectFilters.every((select) => {
                                    const key = select.dataset.filterSelect;
                                    return !select.value || row.dataset[`filter${key.charAt(0).toUpperCase()}${key.slice(1)}`] === select.value;
                                });
                                const visible = matchesSearch && matchesSelects;

                                row.classList.toggle('hidden', !visible);
                                if (visible) {
                                    visibleCount++;
                                }
                            });

                            emptyRow?.classList.toggle('hidden', visibleCount > 0);
                        };

                        filter.addEventListener('submit', (event) => event.preventDefault());
                        searchInput?.addEventListener('input', applyFilter);
                        selectFilters.forEach((select) => select.addEventListener('change', applyFilter));
                        applyFilter();
                    });

                    document.querySelectorAll('form[data-auto-submit]').forEach((form) => {
                        let timeoutId = null;

                        const submitForm = () => {
                            window.clearTimeout(timeoutId);
                            form.requestSubmit();
                        };

                        form.querySelectorAll('[data-auto-submit-control]').forEach((field) => {
                            const delay = field.tagName === 'SELECT' ? 0 : 450;
                            field.addEventListener(field.tagName === 'SELECT' ? 'change' : 'input', () => {
                                window.clearTimeout(timeoutId);
                                timeoutId = window.setTimeout(submitForm, delay);
                            });
                        });
                    });
                });
            })();

            (() => {
                const loadChartJs = () => new Promise((resolve, reject) => {
                    if (window.Chart) {
                        resolve(window.Chart);
                        return;
                    }

                    const existingScript = document.querySelector('script[data-chartjs-cdn]');

                    if (existingScript) {
                        existingScript.addEventListener('load', () => resolve(window.Chart));
                        existingScript.addEventListener('error', reject);
                        return;
                    }

                    const script = document.createElement('script');
                    script.src = 'https://cdn.jsdelivr.net/npm/chart.js@4.5.1/dist/chart.umd.min.js';
                    script.defer = true;
                    script.dataset.chartjsCdn = 'true';
                    script.onload = () => resolve(window.Chart);
                    script.onerror = reject;
                    document.head.appendChild(script);
                });

                const hasDatasetData = (config) => {
                    return (config.data?.datasets || []).some((dataset) => {
                        return (dataset.data || []).some((value) => Number(value) !== 0);
                    });
                };

                const showEmptyChart = (canvas) => {
                    const wrapper = canvas.parentElement;
                    canvas.classList.add('hidden');

                    if (!wrapper || wrapper.querySelector('[data-chart-empty]')) {
                        return;
                    }

                    const emptyState = document.createElement('div');
                    emptyState.dataset.chartEmpty = 'true';
                    emptyState.className = 'h-full min-h-64 flex flex-col items-center justify-center text-center rounded-2xl border border-dashed border-[#FFD6F4] bg-white/40 px-6';
                    emptyState.innerHTML = [
                        '<p class="text-3xl text-[#7B19E5] mb-3">✧</p>',
                        '<p class="font-bold text-[#4A00B9]">Sem dados para gerar este gráfico</p>',
                        '<p class="text-sm text-gray-500 mt-1">Tente outro período ou registre movimentos primeiro.</p>',
                    ].join('');
                    wrapper.appendChild(emptyState);
                };

                const renderSalaoCharts = async () => {
                    const chartConfigs = document.querySelectorAll('script[type="application/json"][data-salao-chart]');

                    if (!chartConfigs.length) {
                        return;
                    }

                    try {
                        await loadChartJs();
                    } catch (error) {
                        console.error('Chart.js não carregou:', error);
                        return;
                    }

                    window.SalaoChartInstances ||= {};

                    chartConfigs.forEach((script) => {
                        const canvasId = script.dataset.salaoChart;
                        const canvas = document.getElementById(canvasId);

                        if (!canvas || script.dataset.rendered === 'true') {
                            return;
                        }

                        let config;

                        try {
                            config = JSON.parse(script.textContent || '{}');
                        } catch (error) {
                            console.error(`Configuração inválida do gráfico ${canvasId}:`, error);
                            return;
                        }

                        if (!hasDatasetData(config)) {
                            showEmptyChart(canvas);
                            script.dataset.rendered = 'true';
                            return;
                        }

                        if (window.SalaoChartInstances[canvasId]) {
                            window.SalaoChartInstances[canvasId].destroy();
                        }

                        window.SalaoChartInstances[canvasId] = new window.Chart(canvas, {
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
                                    },
                                    ...config.options?.plugins,
                                },
                                scales: config.options?.scales,
                                ...config.options,
                            },
                        });

                        script.dataset.rendered = 'true';
                    });
                };

                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', renderSalaoCharts);
                } else {
                    renderSalaoCharts();
                }
            })();
        </script>
    </body>
</html>
