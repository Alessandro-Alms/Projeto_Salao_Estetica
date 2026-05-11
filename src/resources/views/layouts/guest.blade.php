<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            html.dark-mode { color-scheme: dark; }
            html.dark-mode body,
            html.dark-mode .min-h-screen {
                background: radial-gradient(circle at top left, rgba(123, 25, 229, 0.22), transparent 34%),
                    radial-gradient(circle at bottom right, rgba(255, 46, 182, 0.18), transparent 38%),
                    linear-gradient(135deg, #0B0712 0%, #150B22 48%, #1D0F2A 100%) !important;
                color: #F7ECFF;
            }
            html.dark-mode .bg-white {
                background-color: rgba(20, 10, 32, 0.86) !important;
                border: 1px solid rgba(255, 214, 244, 0.16);
                box-shadow: 0 18px 42px rgba(0, 0, 0, 0.32) !important;
            }
            html.dark-mode [class*="text-gray-"],
            html.dark-mode label {
                color: #F7ECFF !important;
            }
            html.dark-mode input {
                background-color: rgba(13, 7, 22, 0.88) !important;
                border-color: rgba(255, 214, 244, 0.28) !important;
                color: #F7ECFF !important;
            }
            html.dark-mode .dark-mode-toggle {
                background: rgba(31, 16, 48, 0.86);
                border-color: rgba(255, 214, 244, 0.25);
                color: #F7ECFF;
            }
        </style>
        <script>
            (() => {
                if (localStorage.getItem('salao-tema') === 'escuro') {
                    document.documentElement.classList.add('dark-mode');
                }
            })();
        </script>
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
            <div>
                <a href="/">
                    <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
                </a>
            </div>

            <button type="button" data-theme-toggle class="dark-mode-toggle mt-4 rounded-full border border-[#FFD6F4] bg-white px-5 py-2 text-sm font-semibold text-[#7B19E5] shadow-sm transition-colors">
                Escuro
            </button>

            <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
                {{ $slot }}
            </div>
        </div>
        <script>
            (() => {
                const updateThemeButtons = () => {
                    const isDark = document.documentElement.classList.contains('dark-mode');
                    document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
                        button.textContent = isDark ? 'Claro' : 'Escuro';
                    });
                };

                updateThemeButtons();
                document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
                    button.addEventListener('click', () => {
                        const isDark = document.documentElement.classList.toggle('dark-mode');
                        localStorage.setItem('salao-tema', isDark ? 'escuro' : 'claro');
                        updateThemeButtons();
                    });
                });
            })();
        </script>
    </body>
</html>
