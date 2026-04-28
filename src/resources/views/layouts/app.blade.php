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

            /* ✅ EFEITO DO BOTÃO */
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
    </body>
</html>