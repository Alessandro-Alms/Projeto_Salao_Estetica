<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cheias de Charme - Salão de Beleza</title>
    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700;800&family=Playfair+Display:wght@700&family=Space+Grotesk:wght@300;400;500;600;700&display=swap');
        
        * { font-family: 'Syne', sans-serif; }
        .font-title { font-family: 'Playfair Display', serif; font-weight: 700; letter-spacing: -0.02em; }
        .font-body { font-family: 'Space Grotesk', sans-serif; }
        html { scroll-behavior: smooth; }
        
        ::-webkit-scrollbar { width: 8px; background: #f8f0ff; }
            ::-webkit-scrollbar-thumb { background: linear-gradient(135deg, #7B19E5, #FF2EB6); border-radius: 10px; }
        
        .marquee {
            display: flex;
            animation: marquee 30s linear infinite;
            white-space: nowrap;
            width: fit-content;
        }
        .marquee:hover { animation-play-state: paused; }
        @keyframes marquee {
            0% { transform: translateX(0); }
            100% { transform: translateX(-50%); }
        }
        
        .hover-lift { transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275), box-shadow 0.4s ease; }
        .hover-lift:hover { transform: translateY(-8px) scale(1.02); box-shadow: 0 25px 35px -12px rgba(123, 25, 229, 0.25); }
        
        .btn-primary { position: relative; overflow: hidden; transition: all 0.3s ease; z-index: 1; transform: translateY(0); }
        .btn-primary::before { content: ''; position: absolute; top: 50%; left: 50%; width: 0; height: 0; border-radius: 50%; background: rgba(255, 255, 255, 0.3); transform: translate(-50%, -50%); transition: width 0.6s ease, height 0.6s ease; z-index: -1; }
        .btn-primary:hover::before { width: 300px; height: 300px; }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 20px -6px rgba(255, 46, 182, 0.4); }
        .btn-primary:active { transform: translateY(1px); box-shadow: 0 4px 12px -4px rgba(123, 25, 229, 0.4); }
        
        .image-zoom { overflow: hidden; }
        .image-zoom img { transition: transform 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
        .image-zoom:hover img { transform: scale(1.08); }
        
        .fade-in { opacity: 0; animation: fadeIn 1s ease forwards; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        .delay-1 { animation-delay: 0.1s; } .delay-2 { animation-delay: 0.2s; } .delay-3 { animation-delay: 0.3s; }
        
        .glass-card { 
            background: rgba(255, 255, 255, 0.7); 
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 8px 32px rgba(123, 25, 229, 0.1);
        }
        
        header.bg-white { 
            background: rgba(255, 255, 255, 0.9) !important; 
            backdrop-filter: blur(8px); 
            box-shadow: 0 2px 20px rgba(0,0,0,0.05); 
        }
        
        .title-glow { text-shadow: 0 2px 10px rgba(123, 25, 229, 0.2); }
        
        img { opacity: 0; transition: opacity 0.5s ease; }
        img.loaded { opacity: 1; }
        
        .servicos img, .hero img:not(.logo-colorida), section:not(.hero) img:not(header img) { filter: grayscale(100%); }
        header img, .hero .logo-colorida { filter: grayscale(0%) !important; }
        
        section { position: relative; z-index: 1; }
        
        .btn-primary.bg-white { background: white !important; color: #7B19E5 !important; border: 2px solid rgba(123, 25, 229, 0.2); box-shadow: 0 10px 25px -5px rgba(123, 25, 229, 0.3); }
        .btn-primary.bg-\[\#7B19E5\] { background: #7B19E5 !important; color: white !important; border: 2px solid rgba(255, 255, 255, 0.2); box-shadow: 0 10px 25px -5px rgba(123, 25, 229, 0.4); }
        .btn-primary.bg-\[\#FF2EB6\] { background: #FF2EB6 !important; color: white !important; border: 2px solid rgba(255, 255, 255, 0.2); box-shadow: 0 10px 25px -5px rgba(255, 46, 182, 0.4); }
        
        .hero .border-2 { background: rgba(255, 255, 255, 0.15); backdrop-filter: blur(8px); border: 2px solid white !important; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.2); }
        
        #contato { background: linear-gradient(to bottom right, #7B19E5, #FF2EB6) !important; }
        
        nav a { font-weight: 600; text-shadow: 0 1px 2px rgba(255,255,255,0.5); }
    </style>
</head>
<body class="font-body antialiased relative">

    <div class="fixed inset-0 -z-10 overflow-hidden">
        <div class="absolute top-0 -left-20 w-[600px] h-[600px] bg-[#7B19E5]/20 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 -right-20 w-[700px] h-[700px] bg-[#FF2EB6]/20 rounded-full blur-3xl"></div>
        <div class="absolute top-1/3 left-1/3 w-[400px] h-[400px] bg-[#A955D3]/15 rounded-full blur-3xl"></div>
        <div class="absolute inset-0 bg-gradient-to-br from-[#7B19E5]/5 via-white/30 to-[#FF2EB6]/5"></div>
    </div>

    <!-- Top Marquee -->
    <div class="bg-gradient-to-r from-[#7B19E5] via-[#FF2EB6] to-[#A955D3] text-white py-2.5 overflow-hidden relative z-20 shadow-md">
        <div class="marquee text-xs tracking-[0.25em] font-medium">
            <span class="mx-8"> CORTE</span>
            <span class="mx-8">✦</span>
            <span class="mx-8"> UNHAS</span>
            <span class="mx-8">✦</span>
            <span class="mx-8"> MAQUIAGEM</span>
            <span class="mx-8">✦</span>
            <span class="mx-8"> SOBRANCELHAS</span>
            <span class="mx-8">✦</span>
            <span class="mx-8"> ALONGAMENTO</span>
            <span class="mx-8">✦</span>
            <span class="mx-8"> COLORAÇÃO</span>
            <span class="mx-8">✦</span>
            <span class="mx-8"> TRATAMENTOS</span>
            <span class="mx-8">✦</span>
            <span class="mx-8"> CORTE</span>
            <span class="mx-8">✦</span>
            <span class="mx-8"> UNHAS</span>
            <span class="mx-8">✦</span>
            <span class="mx-8"> MAQUIAGEM</span>
            <span class="mx-8">✦</span>
            <span class="mx-8"> SOBRANCELHAS</span>
            <span class="mx-8">✦</span>
        </div>
    </div>

    <!-- Header -->
    <header class="border-b border-[#FFD6F4] bg-white/80 sticky top-0 z-30 backdrop-blur-md shadow-sm">
        <div class="container mx-auto px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="w-48">
                    <img src="{{ asset('img/Prancheta1.png') }}" alt="Cheias de Charme" class="h-16 w-auto" loading="eager">
                </div>
                
                <nav class="hidden md:flex items-center gap-10" aria-label="Navegação principal">
                    <a href="#servicos" class="text-[#1A002B] text-sm hover:text-[#7B19E5] transition-colors font-semibold"> SERVIÇOS </a>
                    <a href="#produtos" class="text-[#1A002B] text-sm hover:text-[#7B19E5] transition-colors font-semibold"> PRODUTOS </a>
                    <a href="#depoimentos" class="text-[#1A002B] text-sm hover:text-[#7B19E5] transition-colors font-semibold"> DEPOIMENTOS </a>
                    <a href="#contato" class="text-[#1A002B] text-sm hover:text-[#7B19E5] transition-colors font-semibold"> CONTATO </a>
                </nav>
                
                <div class="flex items-center gap-2">
                    @auth
                        <a href="{{ route('dashboard') }}" class="bg-[#7B19E5] text-white px-6 py-2.5 text-sm rounded-full btn-primary shadow-lg">
                            IR PARA O PAINEL
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="bg-[#7B19E5] text-white px-6 py-2.5 text-sm rounded-full btn-primary shadow-lg">
                            LOGIN
                        </a>
                    @endauth
                    <a href="/agendar" class="bg-[#FF2EB6] text-white px-6 py-2.5 text-sm rounded-full btn-primary shadow-lg">
                        AGENDAR
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Hero -->
    <section class="relative h-[85vh] flex items-center justify-center overflow-hidden hero">
        <div class="absolute inset-0">
            <img src="https://images.unsplash.com/photo-1470259078422-826894b933aa?q=80&w=874&auto=format&fit=crop" 
                 alt="Salão Cheias de Charme" 
                 class="w-full h-full object-cover"
                 loading="eager">
            <div class="absolute inset-0 bg-gradient-to-r from-[#7B19E5]/40 via-[#A955D3]/30 to-[#FF2EB6]/40"></div>
        </div>
        
        <div class="relative z-10 text-center text-white max-w-4xl mx-auto px-6 fade-in">
            <div class="mb-4 flex justify-center">
                <img src="{{ asset('img/Prancheta2.png') }}" alt="Cheias de Charme" class="h-48 w-auto logo-colorida drop-shadow-2xl" loading="eager">
            </div>
            
            <p class="text-xl mb-7 opacity-95 max-w-xl mx-auto font-body leading-relaxed text-white drop-shadow-lg">
                Beleza, personalidade e a atitude que você merece.
            </p>
            <div class="flex gap-4 justify-center">
                <a href="#servicos" class="bg-white text-[#7B19E5] px-8 py-3.5 text-sm tracking-wider rounded-full font-medium btn-primary shadow-xl">
                    NOSSOS SERVIÇOS
                </a>
                <a href="#contato" class="border-2 border-white text-white px-8 py-3.5 text-sm tracking-wider rounded-full font-medium btn-primary hover:bg-white/20 backdrop-blur-md shadow-xl">
                    FALE CONOSCO
                </a>
            </div>
        </div>
    </section>

    <!-- Sobre -->
    <section class="py-24 relative">
        <div class="container mx-auto px-6">
            <div class="text-center mb-16 fade-in">
                <span class="text-[#7B19E5] text-sm tracking-[0.25em] mb-3 block font-medium">SOBRE NÓS</span>
                <h2 class="text-5xl font-title text-[#1A002B] mb-4 title-glow">Mais que um salão</h2>
                <p class="text-[#4A00B9] max-w-2xl mx-auto font-body text-lg">
                    Um espaço pensado para você expressar sua personalidade através da beleza
                </p>
            </div>
            
            <div class="grid md:grid-cols-3 gap-8">
                <div class="glass-card p-8 rounded-2xl shadow-xl hover-lift border border-white/40 fade-in delay-1">
                    <div class="text-5xl mb-5 text-[#7B19E5]">✦</div>
                    <h3 class="text-2xl font-title text-[#4A00B9] mb-4">Nossa história</h3>
                    <p class="text-[#1A002B] leading-relaxed font-body">
                        Há mais de 10 anos transformando a forma como as mulheres se relacionam com a beleza. 
                        Começamos como um pequeno salão e hoje somos referência em estilo e personalidade.
                    </p>
                </div>
                
                <div class="glass-card p-8 rounded-2xl shadow-xl hover-lift border border-white/40 fade-in delay-2">
                    <div class="text-5xl mb-5 text-[#FF2EB6]">✧</div>
                    <h3 class="text-2xl font-title text-[#4A00B9] mb-4">Nossa missão</h3>
                    <p class="text-[#1A002B] leading-relaxed font-body">
                        Fazer cada cliente se sentir única e cheia de charme, realçando sua beleza natural 
                        com técnicas inovadoras e um atendimento acolhedor.
                    </p>
                </div>
                
                <div class="glass-card p-8 rounded-2xl shadow-xl hover-lift border border-white/40 fade-in delay-3">
                    <div class="text-5xl mb-5 text-[#A955D3]">✦</div>
                    <h3 class="text-2xl font-title text-[#4A00B9] mb-4">Nossos valores</h3>
                    <p class="text-[#1A002B] leading-relaxed font-body">
                        Respeito, criatividade e excelência. Acreditamos que a beleza verdadeira vem da 
                        confiança e do bem-estar de cada mulher.
                    </p>
                </div>
            </div>
            
            <div class="grid grid-cols-3 gap-8 max-w-3xl mx-auto mt-16 pt-8 border-t border-[#FFD6F4]">
                <div class="text-center">
                    <span class="text-4xl font-title text-[#FF2EB6]">10+</span>
                    <p class="text-sm text-[#1A002B] mt-2 font-medium">anos de história</p>
                </div>
                <div class="text-center">
                    <span class="text-4xl font-title text-[#7B19E5]">5k+</span>
                    <p class="text-sm text-[#1A002B] mt-2 font-medium">clientes atendidas</p>
                </div>
                <div class="text-center">
                    <span class="text-4xl font-title text-[#A955D3]">15</span>
                    <p class="text-sm text-[#1A002B] mt-2 font-medium">profissionais</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Serviços -->
    <section id="servicos" class="py-24 relative servicos">
        <div class="container mx-auto px-6">
            <div class="text-center mb-16 fade-in">
                <span class="text-[#7B19E5] text-sm tracking-[0.25em] mb-3 block font-medium">SERVIÇOS</span>
                <h2 class="text-5xl font-title text-[#1A002B] mb-4 title-glow">O que oferecemos</h2>
                <p class="text-[#4A00B9] font-body text-lg">Cada serviço pensado para realçar sua beleza</p>
            </div>

            @php
                $servicoImagens = [
                    'https://images.unsplash.com/photo-1580618672591-eb180b1a973f?w=500&auto=format&fit=crop',
                    'https://images.unsplash.com/photo-1632345031435-8727f6897d53?w=500&auto=format&fit=crop',
                    'https://images.unsplash.com/photo-1613966802194-d46a163af70d?q=80&w=870&auto=format&fit=crop',
                    'https://images.unsplash.com/photo-1519415387722-a1c3bbef716c?w=500&auto=format&fit=crop',
                ];
            @endphp

            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
                @forelse($servicosDestaque as $servico)
                    <div class="glass-card rounded-2xl overflow-hidden shadow-xl hover-lift border border-white/40">
                        <div class="image-zoom h-48 overflow-hidden">
                            <img src="{{ $servicoImagens[$loop->index % count($servicoImagens)] }}"
                                 alt="{{ $servico->nome }}"
                                 class="w-full h-full object-cover"
                                 loading="lazy">
                        </div>
                        <div class="p-6">
                            <h3 class="text-xl font-title text-[#4A00B9] mb-3">{{ $servico->nome }}</h3>
                            <p class="text-sm text-[#1A002B] mb-4 font-body leading-relaxed min-h-[72px]">{{ $servico->descricao ?? 'Serviço profissional do salão.' }}</p>
                            <div class="flex items-center justify-between gap-3">
                                <span class="text-[#FF2EB6] font-medium">R$ {{ number_format($servico->preco, 2, ',', '.') }}</span>
                                <span class="text-xs text-[#7B19E5] font-semibold">{{ $servico->duracao }} min</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="glass-card p-8 rounded-2xl shadow-xl border border-white/40 md:col-span-2 lg:col-span-4 text-center text-[#4A00B9]">
                        Nenhum serviço cadastrado ainda.
                    </div>
                @endforelse
            </div>

            @if(false)
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Cabelo -->
                <div class="glass-card rounded-2xl overflow-hidden shadow-xl hover-lift border border-white/40">
                    <div class="image-zoom h-48 overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1580618672591-eb180b1a973f?w=500&auto=format&fit=crop" 
                             alt="Serviços de cabelo" 
                             class="w-full h-full object-cover"
                             loading="lazy">
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-title text-[#4A00B9] mb-3">Cabelo</h3>
                        <ul class="space-y-1.5 text-sm text-[#1A002B] mb-4 font-body">
                            <li>✧ Corte e finalização</li>
                            <li>✧ Progressiva e botox</li>
                        </ul>
                        <span class="text-[#FF2EB6] font-medium">a partir de R$ 89</span>
                    </div>
                </div>
                
                <!-- Unhas -->
                <div class="glass-card rounded-2xl overflow-hidden shadow-xl hover-lift border border-white/40">
                    <div class="image-zoom h-48 overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1632345031435-8727f6897d53?w=500&auto=format&fit=crop" 
                             alt="Serviços de unhas" 
                             class="w-full h-full object-cover"
                             loading="lazy">
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-title text-[#4A00B9] mb-3">Unhas</h3>
                        <ul class="space-y-1.5 text-sm text-[#1A002B] mb-4 font-body">
                            <li>✧ Manicure e pedicure</li>
                            <li>✧ Alongamento de fibra</li>
                            <li>✧ Nail art exclusiva</li>
                            <li>✧ Esmaltação em gel</li>
                        </ul>
                        <span class="text-[#FF2EB6] font-medium">a partir de R$ 49</span>
                    </div>
                </div>
                
                <!-- Maquiagem -->
                <div class="glass-card rounded-2xl overflow-hidden shadow-xl hover-lift border border-white/40">
                    <div class="image-zoom h-48 overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1613966802194-d46a163af70d?q=80&w=870&auto=format&fit=crop" 
                             alt="Serviços de maquiagem" 
                             class="w-full h-full object-cover"
                             loading="lazy">
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-title text-[#4A00B9] mb-3">Maquiagem</h3>
                        <ul class="space-y-1.5 text-sm text-[#1A002B] mb-4 font-body">
                            <li>✧ Social e noiva</li>
                            <li>✧ Make artística</li>
                            <li>✧ Efeitos especiais</li>
                            <li>✧ Prova de make</li>
                        </ul>
                        <span class="text-[#FF2EB6] font-medium">a partir de R$ 79</span>
                    </div>
                </div>
                
                <!-- Sobrancelhas -->
                <div class="glass-card rounded-2xl overflow-hidden shadow-xl hover-lift border border-white/40">
                    <div class="image-zoom h-48 overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1519415387722-a1c3bbef716c?w=500&auto=format&fit=crop" 
                             alt="Serviços de sobrancelhas" 
                             class="w-full h-full object-cover"
                             loading="lazy">
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-title text-[#4A00B9] mb-3">Sobrancelhas</h3>
                        <ul class="space-y-1.5 text-sm text-[#1A002B] mb-4 font-body">
                            <li>✧ Design tradicional</li>
                            <li>✧ Henna e fio a fio</li>
                            <li>✧ Alongamento de cílios</li>
                            <li>✧ Lifting de cílios</li>
                        </ul>
                        <span class="text-[#FF2EB6] font-medium">a partir de R$ 39</span>
                    </div>
                </div>
            </div>
            
            <!-- Segunda linha -->
            <div class="grid md:grid-cols-3 gap-6 mt-6">
                <!-- Tratamentos -->
                <div class="glass-card rounded-2xl overflow-hidden shadow-xl hover-lift border border-white/40">
                    <div class="image-zoom h-40 overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1634449571010-02389ed0f9b0?w=500&auto=format&fit=crop" 
                             alt="Tratamentos capilares" 
                             class="w-full h-full object-cover"
                             loading="lazy">
                    </div>
                    <div class="p-5">
                        <h3 class="text-lg font-title text-[#4A00B9] mb-2">Tratamentos</h3>
                        <p class="text-sm text-[#1A002B] mb-2 font-body">Hidratação, nutrição e reconstrução</p>
                        <span class="text-[#FF2EB6] font-medium text-sm">a partir de R$ 59</span>
                    </div>
                </div>
                
                <!-- Coloração -->
                <div class="glass-card rounded-2xl overflow-hidden shadow-xl hover-lift border border-white/40">
                    <div class="image-zoom h-40 overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1707812343087-c9ff9e5abb43?w=500&auto=format&fit=crop" 
                             alt="Coloração e mechas" 
                             class="w-full h-full object-cover"
                             loading="lazy">
                    </div>
                    <div class="p-5">
                        <h3 class="text-lg font-title text-[#4A00B9] mb-2">Coloração</h3>
                        <p class="text-sm text-[#1A002B] mb-2 font-body">Mechas, luzes e coloração completa</p>
                        <span class="text-[#FF2EB6] font-medium text-sm">a partir de R$ 129</span>
                    </div>
                </div>
                
                <!-- Penteados -->
                <div class="glass-card rounded-2xl overflow-hidden shadow-xl hover-lift border border-white/40">
                    <div class="image-zoom h-40 overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1527799820374-dcf8d9d4a388?q=80&w=2069&auto=format&fit=crop" 
                             alt="Penteados para eventos" 
                             class="w-full h-full object-cover"
                             loading="lazy">
                    </div>
                    <div class="p-5">
                        <h3 class="text-lg font-title text-[#4A00B9] mb-2">Penteados</h3>
                        <p class="text-sm text-[#1A002B] mb-2 font-body">Festas, noivas e eventos especiais</p>
                        <span class="text-[#FF2EB6] font-medium text-sm">a partir de R$ 69</span>
                    </div>
                </div>
            </div>
            
            @endif

            <div class="text-center mt-12">
                <a href="{{ route('public.servicos') }}" class="bg-[#7B19E5] text-white px-8 py-3.5 text-sm tracking-wider rounded-full font-medium btn-primary inline-block shadow-xl">
                    VER TODOS OS SERVIÇOS
                </a>
            </div>
        </div>
    </section>

    <!-- Produtos -->
    <section id="produtos" class="py-24 relative">
        <div class="container mx-auto px-6">
            <div class="text-center mb-16 fade-in">
                <span class="text-[#FF2EB6] text-sm tracking-[0.25em] mb-3 block font-medium">PRODUTOS</span>
                <h2 class="text-5xl font-title text-[#1A002B] mb-4 title-glow">Produtos exclusivos</h2>
                <p class="text-[#4A00B9] font-body text-lg">Complete seu look com nossos produtos</p>
            </div>

            @php
                $produtoImagens = [
                    'https://plus.unsplash.com/premium_photo-1681276169690-a22f1193c784?q=80&w=500&auto=format&fit=crop',
                    'https://images.unsplash.com/photo-1601070846144-6be3aad73f7b?w=500&auto=format&fit=crop',
                    'https://images.unsplash.com/photo-1512496015851-a90fb38ba796?w=500&auto=format&fit=crop',
                    'https://plus.unsplash.com/premium_photo-1728693697249-1d56feca531a?w=500&auto=format&fit=crop',
                ];
            @endphp

            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
                @forelse($produtosDestaque as $produto)
                    <div class="glass-card rounded-2xl overflow-hidden shadow-xl hover-lift border border-white/40">
                        <div class="image-zoom h-48 overflow-hidden">
                            <img src="{{ $produtoImagens[$loop->index % count($produtoImagens)] }}"
                                 alt="{{ $produto->nome }}"
                                 class="w-full h-full object-cover"
                                 loading="lazy">
                        </div>
                        <div class="p-6">
                            <h3 class="text-xl font-title text-[#4A00B9] mb-2">{{ $produto->nome }}</h3>
                            <p class="text-sm text-[#1A002B] mb-4 font-body leading-relaxed min-h-[72px]">{{ $produto->descricao ?? 'Produto profissional disponivel no salao.' }}</p>
                            <div class="flex items-center justify-between gap-3">
                                <span class="text-[#FF2EB6] font-medium">R$ {{ number_format($produto->valor_unitario, 2, ',', '.') }}</span>
                                <span class="text-xs text-[#7B19E5] font-semibold">{{ ucfirst($produto->tipo) }}</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="glass-card p-8 rounded-2xl shadow-xl border border-white/40 md:col-span-2 lg:col-span-4 text-center text-[#4A00B9]">
                        Nenhum produto cadastrado ainda.
                    </div>
                @endforelse
            </div>

            @if(false)
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Acessórios -->
                <div class="glass-card rounded-2xl overflow-hidden shadow-xl hover-lift border border-white/40">
                    <div class="image-zoom h-48 overflow-hidden">
                        <img src="https://plus.unsplash.com/premium_photo-1681276169690-a22f1193c784?q=80&w=387&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D" 
                             alt="Acessórios" 
                             class="w-full h-full object-cover"
                             loading="lazy">
                    </div>
                    <div class="p-6">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="text-[#7B19E5] text-xl">✧</span>
                            <h3 class="text-xl font-title text-[#4A00B9]">Acessórios</h3>
                        </div>
                        <p class="text-sm text-[#1A002B] mb-4 font-body">Tiaras, presilhas, headbands e muito mais para deixar seu visual ainda mais especial.</p>
                        <span class="text-[#FF2EB6] font-medium">a partir de R$ 29</span>
                    </div>
                </div>

                <!-- Kits -->
                <div class="glass-card rounded-2xl overflow-hidden shadow-xl hover-lift border border-white/40">
                    <div class="image-zoom h-48 overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1601070846144-6be3aad73f7b?w=500&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8NHx8aGFpciUyMHByb2R1Y3RzfGVufDB8fDB8fHww" 
                             alt="Kits" 
                             class="w-full h-full object-cover"
                             loading="lazy">
                    </div>
                    <div class="p-6">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="text-[#7B19E5] text-xl">✦</span>
                            <h3 class="text-xl font-title text-[#4A00B9]">Kits</h3>
                        </div>
                        <p class="text-sm text-[#1A002B] mb-4 font-body">Kits completos com shampoo, condicionador e finalizadores para cabelos perfeitos.</p>
                        <span class="text-[#FF2EB6] font-medium">a partir de R$ 79</span>
                    </div>
                </div>

                <!-- Cosméticos -->
                <div class="glass-card rounded-2xl overflow-hidden shadow-xl hover-lift border border-white/40">
                    <div class="image-zoom h-48 overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1512496015851-a90fb38ba796?w=500&auto=format&fit=crop" 
                             alt="Cosméticos" 
                             class="w-full h-full object-cover"
                             loading="lazy">
                    </div>
                    <div class="p-6">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="text-[#7B19E5] text-xl">✧</span>
                            <h3 class="text-xl font-title text-[#4A00B9]">Cosméticos</h3>
                        </div>
                        <p class="text-sm text-[#1A002B] mb-4 font-body">Pó compacto, batons, gloss e make básica para você arrasar no dia a dia.</p>
                        <span class="text-[#FF2EB6] font-medium">a partir de R$ 39</span>
                    </div>
                </div>

                <!-- Cabelo -->
                <div class="glass-card rounded-2xl overflow-hidden shadow-xl hover-lift border border-white/40">
                    <div class="image-zoom h-48 overflow-hidden">
                        <img src="https://plus.unsplash.com/premium_photo-1728693697249-1d56feca531a?w=500&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8Mzd8fGhhaXIlMjBwcm9kdWN0c3xlbnwwfHwwfHx8MA%3D%3D" 
                             alt="Cabelo" 
                             class="w-full h-full object-cover"
                             loading="lazy">
                    </div>
                    <div class="p-6">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="text-[#7B19E5] text-xl">✦</span>
                            <h3 class="text-xl font-title text-[#4A00B9]">Cabelo</h3>
                        </div>
                        <p class="text-sm text-[#1A002B] mb-4 font-body">Finalizadores, óleos, leave-ins e produtos para todos os tipos de cabelo.</p>
                        <span class="text-[#FF2EB6] font-medium">a partir de R$ 49</span>
                    </div>
                </div>
            </div>

            @endif

            <div class="text-center mt-12">
                <a href="{{ route('public.produtos') }}" class="bg-[#7B19E5] text-white px-8 py-3.5 text-sm tracking-wider rounded-full font-medium btn-primary inline-block shadow-xl">
                    VER TODOS OS PRODUTOS
                </a>
            </div>
        </div>
    </section>

    <!-- Depoimentos -->
    <section id="depoimentos" class="py-24 relative">
        <div class="container mx-auto px-6">
            <div class="text-center mb-16 fade-in">
                <span class="text-[#FF2EB6] text-sm tracking-[0.25em] mb-3 block font-medium">DEPOIMENTOS</span>
                <h2 class="text-5xl font-title text-[#1A002B] mb-4 title-glow">O que nossas clientes dizem</h2>
                <p class="text-[#4A00B9] font-body text-lg">Quem ama, recomenda</p>
            </div>
            
            <div class="grid md:grid-cols-3 gap-8">
                @forelse($depoimentosDestaque as $depoimento)
                    <div class="glass-card p-8 rounded-2xl shadow-xl hover-lift border border-white/40">
                        <div class="flex gap-1 text-[#FF2EB6] mb-4 text-2xl">
                            @for($i = 1; $i <= 5; $i++)
                                <span class="{{ $i <= $depoimento->nota ? '' : 'opacity-30' }}">&#9733;</span>
                            @endfor
                        </div>
                        <p class="text-[#1A002B] mb-6 font-body leading-relaxed">{{ $depoimento->comentario }}</p>
                        <div>
                            <h4 class="font-medium text-[#4A00B9] font-title">{{ $depoimento->cliente_nome }}</h4>
                            <span class="text-sm text-[#7B19E5] font-body">{{ $depoimento->profissional_nome ? 'com ' . $depoimento->profissional_nome : 'Cliente' }}</span>
                        </div>
                    </div>
                @empty
                    <div class="glass-card p-8 rounded-2xl shadow-xl border border-white/40 md:col-span-3 text-center text-[#4A00B9]">
                        Nenhum depoimento cadastrado ainda.
                    </div>
                @endforelse
            </div>

            @if(false)
            <div class="grid md:grid-cols-3 gap-8">
                <div class="glass-card p-8 rounded-2xl shadow-xl hover-lift border border-white/40">
                    <div class="flex gap-1 text-[#FF2EB6] mb-4 text-2xl">★★★★★</div>
                    <p class="text-[#1A002B] mb-6 font-body leading-relaxed">"Simplesmente amei! Fiz mechas e hidratação, meu cabelo ficou perfeito. Ambiente lindo e meninas super talentosas."</p>
                    <div><h4 class="font-medium text-[#4A00B9] font-title">Aghata Nunes</h4><span class="text-sm text-[#7B19E5] font-body">@Aghatanunes</span></div>
                </div>
                
                <div class="glass-card p-8 rounded-2xl shadow-xl hover-lift border border-white/40">
                    <div class="flex gap-1 text-[#FF2EB6] mb-4 text-2xl">★★★★★</div>
                    <p class="text-[#1A002B] mb-6 font-body leading-relaxed">"Fiz unhas de fibra pela primeira vez e amei! Duração incrível e as meninas são super atenciosas."</p>
                    <div><h4 class="font-medium text-[#4A00B9] font-title">Ygona Moura</h4><span class="text-sm text-[#7B19E5] font-body">@Ygonarainha</span></div>
                </div>
                
                <div class="glass-card p-8 rounded-2xl shadow-xl hover-lift border border-white/40">
                    <div class="flex gap-1 text-[#FF2EB6] mb-4 text-2xl">★★★★★</div>
                    <p class="text-[#1A002B] mb-6 font-body leading-relaxed">"Maquiagem pra formatura ficou PERFEITA! Duração ótima, durou a festa inteira. Profissionais muito talentosas."</p>
                    <div><h4 class="font-medium text-[#4A00B9] font-title">Patixa Teló</h4><span class="text-sm text-[#7B19E5] font-body">@Eupatixa</span></div>
                </div>
            </div>
            @endif
            
            <div class="text-center mt-12">
                <a href="{{ route('public.depoimentos') }}" class="bg-[#FF2EB6] text-white px-8 py-3.5 text-sm tracking-wider rounded-full font-medium btn-primary inline-block shadow-xl">
                    VER MAIS DEPOIMENTOS
                </a>
            </div>
        </div>
    </section>

    <!-- Contato -->
    <section id="contato" class="py-24 bg-gradient-to-br from-[#7B19E5] to-[#FF2EB6] text-white relative">
        <div class="container mx-auto px-6 max-w-3xl">
            <div class="text-center mb-12 fade-in">
                <span class="text-white/90 text-sm tracking-[0.25em] mb-3 block font-medium">CONTATO</span>
                <h2 class="text-5xl font-title mb-4">Envie uma mensagem</h2>
                <p class="text-white/90 font-body text-lg">Tire dúvidas ou agende seu horário</p>
            </div>
            
            @if(session('contato_sucesso'))
                <div class="mb-6 rounded-2xl border border-white/30 bg-white/20 px-5 py-4 text-white shadow-lg">
                    {{ session('contato_sucesso') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 rounded-2xl border border-white/30 bg-white/20 px-5 py-4 text-white shadow-lg">
                    Confira os campos e tente enviar novamente.
                </div>
            @endif

            <form method="POST" action="{{ route('public.contato.enviar') }}" class="space-y-6">
                @csrf
                <div class="grid md:grid-cols-2 gap-6">
                    <input type="text" name="nome" placeholder="Seu nome completo" required
                         class="w-full px-4 py-3.5 bg-white/10 backdrop-blur-sm border border-white/30 text-white placeholder:text-white/70 rounded-lg focus:outline-none focus:border-white focus:ring-2 focus:ring-white/30 focus:bg-white/10 hover:bg-white/20 font-body 
                        [&:-webkit-autofill]:bg-transparent [&:-webkit-autofill]:text-white [&:-webkit-autofill]:[transition:background-color_0s_600000s] [&:-webkit-autofill]:[box-shadow:0_0_0_100px_transparent_inset] [&:-webkit-autofill]:[-webkit-text-fill-color:white]">
                    <input type="email" name="email" placeholder="seu@email.com" required
                         class="w-full px-4 py-3.5 bg-white/10 backdrop-blur-sm border border-white/30 text-white placeholder:text-white/70 rounded-lg focus:outline-none focus:border-white focus:ring-2 focus:ring-white/30 focus:bg-white/10 hover:bg-white/20 font-body 
                        [&:-webkit-autofill]:bg-transparent [&:-webkit-autofill]:text-white [&:-webkit-autofill]:[transition:background-color_0s_600000s] [&:-webkit-autofill]:[box-shadow:0_0_0_100px_transparent_inset] [&:-webkit-autofill]:[-webkit-text-fill-color:white]">
                </div>
                
                <input type="text" name="assunto" placeholder="Assunto da mensagem" required
                         class="w-full px-4 py-3.5 bg-white/10 backdrop-blur-sm border border-white/30 text-white placeholder:text-white/70 rounded-lg focus:outline-none focus:border-white focus:ring-2 focus:ring-white/30 focus:bg-white/10 hover:bg-white/20 font-body 
                        [&:-webkit-autofill]:bg-transparent [&:-webkit-autofill]:text-white [&:-webkit-autofill]:[transition:background-color_0s_600000s] [&:-webkit-autofill]:[box-shadow:0_0_0_100px_transparent_inset] [&:-webkit-autofill]:[-webkit-text-fill-color:white]">
                
                <textarea rows="4" name="mensagem" placeholder="Escreva sua mensagem aqui..." required
                          class="w-full px-4 py-3.5 bg-white/20 backdrop-blur-sm border border-white/30 text-white placeholder:text-white/70 rounded-lg focus:outline-none focus:border-white focus:ring-2 focus:ring-white/30 transition-all hover:bg-white/30 font-body resize-none"></textarea>
                
                <div class="text-center">
                    <button type="submit" class="bg-white text-[#7B19E5] px-12 py-4 text-sm tracking-wider rounded-full font-medium btn-primary shadow-xl">
                        ENVIAR MENSAGEM
                    </button>
                </div>
            </form>
            
            <div class="flex flex-wrap justify-center gap-8 mt-12 pt-8 border-t border-white/20">
                <div class="flex items-center gap-2 font-body"><span class="text-white/90">(85) 98765-4321</span></div>
                <div class="flex items-center gap-2 font-body"><span class="text-white/90">cheiasdecharme@gmail.com</span></div>
                <div class="flex items-center gap-2 font-body"><span class="text-white/90">Fortaleza, Ceará</span></div>
            </div>
        </div>
    </section>

    <!-- Bottom Marquee -->
    <div class="bg-gradient-to-r from-[#FF2EB6] via-[#7B19E5] to-[#A955D3] text-white py-2.5 overflow-hidden relative z-20 shadow-md">
        <div class="marquee text-xs tracking-[0.25em] font-medium">
            <span class="mx-8"> SIGA A GENTE @CHEIASDECHARME</span>
            <span class="mx-8">✦</span>
            <span class="mx-8"> AGENDE SEU HORÁRIO</span>
            <span class="mx-8">✦</span>
            <span class="mx-8"> BELEZA COM ATITUDE</span>
            <span class="mx-8">✦</span>
            <span class="mx-8"> SIGA A GENTE @CHEIASDECHARME</span>
            <span class="mx-8">✦</span>
            <span class="mx-8"> AGENDE SEU HORÁRIO</span>
            <span class="mx-8">✦</span>
            <span class="mx-8"> BELEZA COM ATITUDE</span>
            <span class="mx-8">✦</span>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-white/80 backdrop-blur-md py-6 border-t border-[#FFD6F4] relative z-20 shadow-sm">
        <div class="container mx-auto px-6">
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                <div class="text-sm text-[#4A00B9] font-body">{{ date('Y') }} Cheias de Charme - feito com carinho</div>
                <div class="flex gap-8">
                    <a href="#" target="_blank" rel="noopener noreferrer" class="text-[#7B19E5] text-sm hover:text-[#FF2EB6] transition-colors font-body">Instagram</a>
                    <a href="#" target="_blank" rel="noopener noreferrer" class="text-[#7B19E5] text-sm hover:text-[#FF2EB6] transition-colors font-body">TikTok</a>
                    <a href="#" target="_blank" rel="noopener noreferrer" class="text-[#7B19E5] text-sm hover:text-[#FF2EB6] transition-colors font-body">Pinterest</a>
                </div>
            </div>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('img').forEach(img => {
                if (img.complete) img.classList.add('loaded');
                else img.addEventListener('load', function() { this.classList.add('loaded'); });
            });
        });
    </script>
</body>
</html>
