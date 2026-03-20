<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cheias de Charme — Agendar</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome para ícones -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700&family=Playfair+Display:wght@700&display=swap');
        * { font-family: 'Syne', sans-serif; }
        .font-title { font-family: 'Playfair Display', serif; }
        
        .glass-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .hover-lift {
            transition: all 0.3s ease;
        }
        .hover-lift:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(123, 25, 229, 0.2);
        }
    </style>
</head>
<body class="bg-gradient-to-br from-[#7B19E5]/10 via-white to-[#FF2EB6]/10 min-h-screen flex items-center justify-center p-4">
    
    <!-- Background decorativo -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-0 -left-20 w-96 h-96 bg-[#7B19E5]/10 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 -right-20 w-96 h-96 bg-[#FF2EB6]/10 rounded-full blur-3xl"></div>
    </div>

    <div class="max-w-2xl w-full relative z-10">
        <!-- Logo e cabeçalho -->
        <div class="text-center mb-10">
            <img src="{{ asset('img/Prancheta2.png') }}" alt="Cheias de Charme" class="h-24 mx-auto mb-4 drop-shadow-xl">
            <h1 class="text-4xl font-title text-[#1A002B] mb-2">Agendar horário</h1>
            <p class="text-[#4A00B9] text-lg">Escolha como deseja continuar</p>
        </div>
        
        <!-- Cards lado a lado -->
        <div class="grid md:grid-cols-2 gap-6">
            <!-- Card Login -->
            <a href="{{ route('login') }}" class="glass-card p-8 rounded-3xl shadow-xl hover-lift group">
                <div class="flex flex-col items-center text-center">
                    <div class="w-20 h-20 bg-gradient-to-br from-[#7B19E5] to-[#A855F7] rounded-2xl flex items-center justify-center mb-5 group-hover:scale-110 transition-transform shadow-lg">
                        <i class="fa-solid fa-lock text-4xl text-white"></i>
                    </div>
                    <h2 class="text-2xl font-title text-[#4A00B9] mb-2">Já tenho conta</h2>
                    <p class="text-gray-600 mb-5">Acesse sua conta para agendar seu horário</p>
                    <span class="inline-flex items-center gap-2 bg-gradient-to-r from-[#7B19E5] to-[#A855F7] text-white px-6 py-3 rounded-full text-sm font-medium shadow-lg group-hover:shadow-xl transition-all">
                        FAZER LOGIN
                        <i class="fa-solid fa-arrow-right text-xs"></i>
                    </span>
                </div>
            </a>
            
            <!-- Card Registro -->
            <a href="{{ route('register') }}" class="glass-card p-8 rounded-3xl shadow-xl hover-lift group">
                <div class="flex flex-col items-center text-center">
                    <div class="w-20 h-20 bg-gradient-to-br from-[#FF2EB6] to-[#FF69B4] rounded-2xl flex items-center justify-center mb-5 group-hover:scale-110 transition-transform shadow-lg">
                        <span class="text-4xl text-white">✧</span>
                    </div>
                    <h2 class="text-2xl font-title text-[#FF2EB6] mb-2">Sou nova aqui</h2>
                    <p class="text-gray-600 mb-5">Crie sua conta e agende agora mesmo</p>
                    <span class="inline-flex items-center gap-2 bg-gradient-to-r from-[#FF2EB6] to-[#FF69B4] text-white px-6 py-3 rounded-full text-sm font-medium shadow-lg group-hover:shadow-xl transition-all">
                        CRIAR CONTA
                        <i class="fa-solid fa-arrow-right text-xs"></i>
                    </span>
                </div>
            </a>
        </div>
        
        <!-- Voltar -->
        <div class="text-center mt-10">
            <a href="/" class="inline-flex items-center gap-2 text-gray-600 hover:text-[#7B19E5] transition-colors group">
                <i class="fa-solid fa-arrow-left group-hover:-translate-x-1 transition-transform"></i>
                <span>Voltar para página inicial</span>
            </a>
        </div>
        
        <!-- Rodapé decorativo -->
        <div class="flex justify-center gap-2 mt-8 text-gray-400 text-sm">
            <i class="fa-regular fa-circle-check"></i>
            <i class="fa-regular fa-circle-check"></i>
            <i class="fa-regular fa-circle-check"></i>
        </div>
    </div>
</body>
</html>