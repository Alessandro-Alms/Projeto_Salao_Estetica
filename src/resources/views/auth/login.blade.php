<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cheias de Charme - Entrar</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700;800&family=Playfair+Display:wght@700&family=Space+Grotesk:wght@300;400;500;600;700&display=swap');
        
        * { font-family: 'Syne', sans-serif; }
        .font-title { font-family: 'Playfair Display', serif; font-weight: 700; letter-spacing: -0.02em; }
        .font-body { font-family: 'Space Grotesk', sans-serif; }

        ::-webkit-scrollbar { width: 8px; background: #f8f0ff; }
            ::-webkit-scrollbar-thumb { background: linear-gradient(135deg, #7B19E5, #FF2EB6); border-radius: 10px; }
        
        .glass-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 8px 32px rgba(123, 25, 229, 0.1);
        }
        
        .btn-primary {
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
            z-index: 1;
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
        
        input {
            transition: all 0.3s ease;
        }
        
        input:focus {
            transform: translateY(-1px);
        }
    </style>
</head>
<body class="font-body antialiased relative min-h-screen flex items-center justify-center p-4">
    <!-- Fundo igual ao site principal -->
    <div class="fixed inset-0 -z-10 overflow-hidden">
        <div class="absolute top-0 -left-20 w-[600px] h-[600px] bg-[#7B19E5]/20 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 -right-20 w-[700px] h-[700px] bg-[#FF2EB6]/20 rounded-full blur-3xl"></div>
        <div class="absolute top-1/3 left-1/3 w-[400px] h-[400px] bg-[#A955D3]/15 rounded-full blur-3xl"></div>
        <div class="absolute inset-0 bg-gradient-to-br from-[#7B19E5]/5 via-white/30 to-[#FF2EB6]/5"></div>
    </div>

    <div class="max-w-md w-full">
        <!-- Logo -->
        <div class="text-center mb-8">
            <img src="{{ asset('img/Prancheta2.png') }}" alt="Cheias de Charme" class="h-20 mx-auto mb-4">
            <h1 class="text-3xl font-title text-[#1A002B]">Bem-vinda</h1>
            <p class="text-[#4A00B9] mt-2">Entre com sua conta ✧</p>
        </div>

        <!-- Card de login -->
        <div class="glass-card rounded-2xl p-8 shadow-xl">
            <!-- Session Status -->
            @if (session('status'))
                <div class="mb-4 text-sm text-green-600 bg-green-50/50 rounded-lg p-3">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Email -->
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-[#4A00B9] mb-2">E-mail</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                           class="w-full px-4 py-3 bg-white/50 border border-[#FFD6F4] rounded-lg focus:outline-none focus:border-[#7B19E5] focus:ring-2 focus:ring-[#7B19E5]/20 transition-all">
                    @error('email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Senha -->
                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium text-[#4A00B9] mb-2">Senha</label>
                    <input id="password" type="password" name="password" required autocomplete="current-password"
                           class="w-full px-4 py-3 bg-white/50 border border-[#FFD6F4] rounded-lg focus:outline-none focus:border-[#7B19E5] focus:ring-2 focus:ring-[#7B19E5]/20 transition-all">
                    @error('password')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between mb-6">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="remember" class="rounded border-[#FFD6F4] text-[#7B19E5] focus:ring-[#FF2EB6]">
                        <span class="text-sm text-gray-600">Lembrar de mim</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-sm text-[#7B19E5] hover:text-[#FF2EB6] transition-colors">
                            Esqueceu a senha?
                        </a>
                    @endif
                </div>

                <!-- Botão -->
                <button type="submit" class="w-full bg-gradient-to-r from-[#7B19E5] to-[#FF2EB6] text-white py-3 rounded-full font-medium btn-primary shadow-lg">
                    ENTRAR
                </button>

                <!-- Link para cadastro -->
                <div class="text-center mt-6">
                    <a href="{{ route('register') }}" class="text-sm text-[#7B19E5] hover:text-[#FF2EB6] transition-colors">
                        ✧ Não tem uma conta? Cadastre-se
                    </a>
                </div>
            </form>
        </div>

        <!-- Voltar para o site -->
        <div class="text-center mt-6">
            <a href="/" class="text-sm text-gray-500 hover:text-[#7B19E5] transition-colors inline-flex items-center gap-1">
                <i class="fa-solid fa-arrow-left text-xs"></i>
                Voltar para o site
            </a>
        </div>
    </div>
</body>
</html>