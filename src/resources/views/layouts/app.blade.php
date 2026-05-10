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

        <style>
            * { font-family: 'Syne', sans-serif; }
            .font-title { font-family: 'Playfair Display', serif; font-weight: 700; letter-spacing: -0.02em; }
            .font-body { font-family: 'Space Grotesk', sans-serif; }
            
            ::-webkit-scrollbar { width: 8px; background: #f8f0ff; }
            ::-webkit-scrollbar-thumb { background: linear-gradient(135deg, #7B19E5, #FF2EB6); border-radius: 10px; }

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
            <main>
                {{ $slot }}
            </main>
        </div>

        <script>
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
