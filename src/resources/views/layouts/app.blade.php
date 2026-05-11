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
            html.dark-mode { color-scheme: dark; }
            
            ::-webkit-scrollbar { width: 8px; background: #f8f0ff; }
            ::-webkit-scrollbar-thumb { background: linear-gradient(135deg, #7B19E5, #FF2EB6); border-radius: 10px; }
            html.dark-mode ::-webkit-scrollbar { background: #12091f; }


            /* =========================
            DARK MODE GLOBAL
            ========================= */

            html.dark-mode {
                color-scheme: dark;

                --salao-bg: #0B0712;
                --salao-bg-soft: #150B22;
                --salao-card: rgba(24, 13, 38, 0.94);
                --salao-card-soft: rgba(34, 18, 54, 0.92);
                --salao-border: rgba(255, 214, 244, 0.20);
                --salao-border-strong: rgba(255, 214, 244, 0.32);

                --salao-text: #F8ECFF;
                --salao-muted: #D8C6E8;
                --salao-soft: #BFAED0;

                --salao-purple: #D8B4FE;
                --salao-pink: #FDA4D9;
            }

            html.dark-mode body {
                background:
                    radial-gradient(circle at top left, rgba(123, 25, 229, 0.24), transparent 32%),
                    radial-gradient(circle at bottom right, rgba(255, 46, 182, 0.18), transparent 35%),
                    linear-gradient(135deg, var(--salao-bg), var(--salao-bg-soft)) !important;
                color: var(--salao-text) !important;
            }

            /* Header e navegação */
            html.dark-mode nav,
            html.dark-mode header {
                background: rgba(13, 7, 22, 0.92) !important;
                border-color: var(--salao-border) !important;
                box-shadow: 0 14px 36px rgba(0, 0, 0, 0.35) !important;
            }

            /* Cards, blocos e fundos claros */
            html.dark-mode .glass-card,
            html.dark-mode .bg-white,
            html.dark-mode [class*="bg-white"],
            html.dark-mode [class*="bg-gray-50"],
            html.dark-mode [class*="bg-gray-100"],
            html.dark-mode [class*="bg-gray-200"],
            html.dark-mode [class*="bg-gray-300"] {
                background: var(--salao-card) !important;
                color: var(--salao-text) !important;
                border-color: var(--salao-border) !important;
                box-shadow: 0 18px 42px rgba(0, 0, 0, 0.35) !important;
            }

            /* Corrige gradientes que usam via-white */
            html.dark-mode [class*="via-white"] {
                --tw-gradient-via: rgba(24, 13, 38, 0.50) !important;
            }

            /* Textos escuros */
            html.dark-mode .text-black,
            html.dark-mode .text-gray-700,
            html.dark-mode .text-gray-800,
            html.dark-mode .text-gray-900,
            html.dark-mode [class*="text-[#1A002B]"],
            html.dark-mode [class*="text-[#4A00B9]"] {
                color: var(--salao-text) !important;
            }

            /* Textos secundários */
            html.dark-mode .text-gray-400,
            html.dark-mode .text-gray-500,
            html.dark-mode .text-gray-600,
            html.dark-mode [class*="text-gray-400"],
            html.dark-mode [class*="text-gray-500"],
            html.dark-mode [class*="text-gray-600"] {
                color: var(--salao-muted) !important;
            }

            /* Roxo e rosa no dark */
            html.dark-mode [class*="text-[#7B19E5]"] {
                color: var(--salao-purple) !important;
            }

            html.dark-mode [class*="text-[#FF2EB6]"] {
                color: var(--salao-pink) !important;
            }

            /* Títulos */
            html.dark-mode h1,
            html.dark-mode h2,
            html.dark-mode h3,
            html.dark-mode h4,
            html.dark-mode .font-title {
                color: var(--salao-text) !important;
            }

            /* Bordas */
            html.dark-mode .border,
            html.dark-mode [class*="border-[#FFD6F4]"],
            html.dark-mode [class*="border-white"],
            html.dark-mode [class*="divide-[#FFD6F4]"] > :not([hidden]) ~ :not([hidden]) {
                border-color: var(--salao-border) !important;
            }

            /* Inputs, selects e textarea */
            html.dark-mode input,
            html.dark-mode select,
            html.dark-mode textarea,
            html.dark-mode [data-searchable-dropdown] {
                background: rgba(13, 7, 22, 0.96) !important;
                color: var(--salao-text) !important;
                border-color: var(--salao-border-strong) !important;
            }

            html.dark-mode input::placeholder,
            html.dark-mode textarea::placeholder {
                color: var(--salao-soft) !important;
            }

            html.dark-mode input[type="date"] {
                color-scheme: dark;
            }

            /* Dropdown pesquisável */
            html.dark-mode [data-searchable-dropdown] button {
                color: var(--salao-text) !important;
            }

            html.dark-mode [data-searchable-dropdown] button:hover {
                background: rgba(123, 25, 229, 0.22) !important;
            }

            /* Tabelas */
            html.dark-mode table,
            html.dark-mode th,
            html.dark-mode td {
                color: var(--salao-text) !important;
                border-color: var(--salao-border) !important;
            }

            html.dark-mode thead,
            html.dark-mode th {
                background: rgba(123, 25, 229, 0.20) !important;
            }

            html.dark-mode tbody tr:hover {
                background: rgba(255, 255, 255, 0.06) !important;
            }

            /* Alertas verdes */
            html.dark-mode .bg-green-50,
            html.dark-mode .bg-green-100,
            html.dark-mode [class*="bg-green-50/"] {
                background: rgba(34, 197, 94, 0.16) !important;
                color: #BBF7D0 !important;
                border-color: rgba(34, 197, 94, 0.35) !important;
            }

            html.dark-mode .text-green-500,
            html.dark-mode .text-green-600,
            html.dark-mode .text-green-700 {
                color: #BBF7D0 !important;
            }

            /* Alertas amarelos */
            html.dark-mode .bg-yellow-50,
            html.dark-mode .bg-yellow-100,
            html.dark-mode .bg-amber-50,
            html.dark-mode .bg-amber-100,
            html.dark-mode [class*="bg-yellow-50/"],
            html.dark-mode [class*="bg-amber-50/"] {
                background: rgba(245, 158, 11, 0.18) !important;
                color: #FDE68A !important;
                border-color: rgba(245, 158, 11, 0.35) !important;
            }

            html.dark-mode .text-yellow-600,
            html.dark-mode .text-yellow-700,
            html.dark-mode .text-amber-600,
            html.dark-mode .text-amber-700 {
                color: #FDE68A !important;
            }

            /* Alertas vermelhos */
            html.dark-mode .bg-red-50,
            html.dark-mode .bg-red-100,
            html.dark-mode [class*="bg-red-50/"] {
                background: rgba(239, 68, 68, 0.17) !important;
                color: #FECACA !important;
                border-color: rgba(239, 68, 68, 0.35) !important;
            }

            html.dark-mode .text-red-500,
            html.dark-mode .text-red-600,
            html.dark-mode .text-red-700 {
                color: #FECACA !important;
            }

            /* Fundos personalizados do projeto */
            html.dark-mode [class*="bg-[#F3E8FF]"],
            html.dark-mode [class*="bg-[#7B19E5]/5"],
            html.dark-mode [class*="bg-[#7B19E5]/10"] {
                background: rgba(123, 25, 229, 0.18) !important;
            }

            html.dark-mode [class*="bg-[#FFD6F4]"],
            html.dark-mode [class*="bg-[#FF2EB6]/5"],
            html.dark-mode [class*="bg-[#FF2EB6]/10"] {
                background: rgba(255, 46, 182, 0.14) !important;
            }

            /* Calendário, horários, serviços e profissionais */
            html.dark-mode .calendar-day-name {
                background: rgba(123, 25, 229, 0.22) !important;
                color: var(--salao-text) !important;
            }

            html.dark-mode .calendar-date,
            html.dark-mode .horario-option,
            html.dark-mode .profissional-card,
            html.dark-mode .servico-card {
                background: rgba(24, 13, 38, 0.94) !important;
                color: var(--salao-text) !important;
                border-color: var(--salao-border) !important;
            }

            html.dark-mode .calendar-date:hover:not(.outro-mes):not(.indisponivel),
            html.dark-mode .horario-option:hover:not(.ocupado),
            html.dark-mode .profissional-card:hover,
            html.dark-mode .servico-card:hover {
                background: rgba(123, 25, 229, 0.22) !important;
                border-color: var(--salao-purple) !important;
            }

            html.dark-mode .calendar-date.outro-mes,
            html.dark-mode .calendar-date.indisponivel,
            html.dark-mode .horario-option.ocupado {
                background: rgba(255, 255, 255, 0.06) !important;
                color: #8B7A9C !important;
                border-color: rgba(255, 255, 255, 0.10) !important;
            }

            html.dark-mode .calendar-date.selecionado,
            html.dark-mode .horario-option.selecionado,
            html.dark-mode .profissional-card.selecionado {
                background: linear-gradient(135deg, #7B19E5, #FF2EB6) !important;
                color: #FFFFFF !important;
                border-color: var(--salao-purple) !important;
            }

            html.dark-mode .profissional-card h4,
            html.dark-mode .profissional-card p,
            html.dark-mode .servico-card p {
                color: var(--salao-text) !important;
            }

            html.dark-mode .horario-option .badge {
                background: rgba(245, 158, 11, 0.22) !important;
                color: #FDE68A !important;
            }

            /* Estado vazio dos gráficos */
            html.dark-mode [data-chart-empty] {
                background: rgba(24, 13, 38, 0.94) !important;
                border-color: var(--salao-border) !important;
            }

            html.dark-mode [data-chart-empty] p {
                color: var(--salao-text) !important;
            }

            /* Botão modo escuro */
            html.dark-mode .dark-mode-toggle {
                background: var(--salao-card-soft) !important;
                color: var(--salao-text) !important;
                border-color: var(--salao-border-strong) !important;
            }

            /* Hover claro */
            html.dark-mode [class*="hover:bg-white/"]:hover,
            html.dark-mode [class*="hover:bg-[#FFD6F4]"]:hover,
            html.dark-mode [class*="hover:bg-[#FFD6F4]/70"]:hover {
                background-color: rgba(123, 25, 229, 0.22) !important;
            }

            /* Scrollbar */
            html.dark-mode ::-webkit-scrollbar {
                background: #12091E;
            }

            html.dark-mode ::-webkit-scrollbar-thumb {
                background: linear-gradient(135deg, #7B19E5, #FF2EB6);
                border-radius: 10px;
            }
            /* Destaque de serviço selecionado no dark mode */
            html.dark-mode .servico-card[class*="bg-[#7B19E5]/5"],
            html.dark-mode .servico-card:has(input[type="checkbox"]:checked) {
                background: rgba(123, 25, 229, 0.34) !important;
                border-color: #D8B4FE !important;
                box-shadow:
                    0 0 0 2px rgba(216, 180, 254, 0.35),
                    0 14px 28px rgba(123, 25, 229, 0.28) !important;
            }

            html.dark-mode .servico-card[class*="bg-[#7B19E5]/5"] p,
            html.dark-mode .servico-card:has(input[type="checkbox"]:checked) p {
                color: #FFFFFF !important;
            }

            html.dark-mode .servico-card[class*="bg-[#7B19E5]/5"] input[type="checkbox"],
            html.dark-mode .servico-card:has(input[type="checkbox"]:checked) input[type="checkbox"] {
                accent-color: #D8B4FE;
            }

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
        <script>
            (() => {
                if (localStorage.getItem('salao-tema') === 'escuro') {
                    document.documentElement.classList.add('dark-mode');
                }
            })();
        </script>
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
                const updateThemeButtons = () => {
                    const isDark = document.documentElement.classList.contains('dark-mode');
                    document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
                        button.textContent = isDark ? 'Claro' : 'Escuro';
                        button.setAttribute('aria-label', isDark ? 'Ativar modo claro' : 'Ativar modo escuro');
                    });
                };

                document.addEventListener('DOMContentLoaded', () => {
                    updateThemeButtons();

                    document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
                        button.addEventListener('click', () => {
                            const isDark = document.documentElement.classList.toggle('dark-mode');
                            localStorage.setItem('salao-tema', isDark ? 'escuro' : 'claro');
                            updateThemeButtons();
                        });
                    });
                });
            })();

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
                                            color: document.documentElement.classList.contains('dark-mode') ? '#F8ECFF' : '#4A00B9',
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
