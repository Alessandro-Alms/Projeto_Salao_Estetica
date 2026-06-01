<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $titulo }} - Cheias de Charme</title>
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

        .glass-card {
            background: rgba(255, 255, 255, 0.72);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.42);
            box-shadow: 0 8px 32px rgba(123, 25, 229, 0.1);
        }

        .hover-lift { transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275), box-shadow 0.4s ease; }
        .hover-lift:hover { transform: translateY(-8px) scale(1.02); box-shadow: 0 25px 35px -12px rgba(123, 25, 229, 0.25); }

        .btn-primary { position: relative; overflow: hidden; transition: all 0.3s ease; z-index: 1; transform: translateY(0); }
        .btn-primary::before { content: ''; position: absolute; top: 50%; left: 50%; width: 0; height: 0; border-radius: 50%; background: rgba(255, 255, 255, 0.3); transform: translate(-50%, -50%); transition: width 0.6s ease, height 0.6s ease; z-index: -1; }
        .btn-primary:hover::before { width: 300px; height: 300px; }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 20px -6px rgba(255, 46, 182, 0.4); }

        .image-zoom { overflow: hidden; }
        .image-zoom img { transition: transform 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275); filter: grayscale(100%); }
        .image-zoom:hover img { transform: scale(1.08); }

        .title-glow { text-shadow: 0 2px 10px rgba(123, 25, 229, 0.2); }
        header.bg-white { background: rgba(255, 255, 255, 0.9) !important; backdrop-filter: blur(8px); box-shadow: 0 2px 20px rgba(0,0,0,0.05); }
    </style>
</head>
<body class="font-body antialiased relative text-[#1A002B]">
    @php
        $servicoImagens = [
            'https://images.unsplash.com/photo-1580618672591-eb180b1a973f?w=700&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1632345031435-8727f6897d53?w=700&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1613966802194-d46a163af70d?q=80&w=700&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1519415387722-a1c3bbef716c?w=700&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1634449571010-02389ed0f9b0?w=700&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1527799820374-dcf8d9d4a388?q=80&w=700&auto=format&fit=crop',
        ];
        $produtoImagens = [
            'https://images.unsplash.com/photo-1512496015851-a90fb38ba796?w=700&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1601070846144-6be3aad73f7b?w=700&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1596462502278-27bfdc403348?w=700&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1522338242992-e1a54906a8da?w=700&auto=format&fit=crop',
        ];
    @endphp

    <div class="fixed inset-0 -z-10 overflow-hidden">
        <div class="absolute top-0 -left-20 w-[600px] h-[600px] bg-[#7B19E5]/20 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 -right-20 w-[700px] h-[700px] bg-[#FF2EB6]/20 rounded-full blur-3xl"></div>
        <div class="absolute top-1/3 left-1/3 w-[400px] h-[400px] bg-[#A955D3]/15 rounded-full blur-3xl"></div>
        <div class="absolute inset-0 bg-gradient-to-br from-[#7B19E5]/5 via-white/30 to-[#FF2EB6]/5"></div>
    </div>

    <div class="bg-gradient-to-r from-[#7B19E5] via-[#FF2EB6] to-[#A955D3] text-white py-2.5 overflow-hidden relative z-20 shadow-md">
        <div class="marquee text-xs tracking-[0.25em] font-medium">
            <span class="mx-8">CORTE</span><span class="mx-8">*</span>
            <span class="mx-8">UNHAS</span><span class="mx-8">*</span>
            <span class="mx-8">MAQUIAGEM</span><span class="mx-8">*</span>
            <span class="mx-8">SOBRANCELHAS</span><span class="mx-8">*</span>
            <span class="mx-8">PRODUTOS</span><span class="mx-8">*</span>
            <span class="mx-8">DEPOIMENTOS</span><span class="mx-8">*</span>
            <span class="mx-8">CORTE</span><span class="mx-8">*</span>
            <span class="mx-8">UNHAS</span><span class="mx-8">*</span>
            <span class="mx-8">MAQUIAGEM</span><span class="mx-8">*</span>
        </div>
    </div>

    <header class="border-b border-[#FFD6F4] bg-white/80 sticky top-0 z-30 backdrop-blur-md shadow-sm">
        <div class="container mx-auto px-6 py-4">
            <div class="flex items-center justify-between gap-6">
                <a href="{{ route('public.home') }}" class="w-44 shrink-0">
                    <img src="{{ asset('img/Prancheta1.png') }}" alt="Cheias de Charme" class="h-14 w-auto" loading="eager">
                </a>

                <nav class="hidden md:flex items-center gap-8" aria-label="Navegação pública">
                    <a href="{{ route('public.home') }}#servicos" class="text-[#1A002B] text-sm hover:text-[#7B19E5] transition-colors font-semibold">SERVIÇOS</a>
                    <a href="{{ route('public.home') }}#produtos" class="text-[#1A002B] text-sm hover:text-[#7B19E5] transition-colors font-semibold">PRODUTOS</a>
                    <a href="{{ route('public.home') }}#depoimentos" class="text-[#1A002B] text-sm hover:text-[#7B19E5] transition-colors font-semibold">DEPOIMENTOS</a>
                    <a href="{{ route('public.home') }}#contato" class="text-[#1A002B] text-sm hover:text-[#7B19E5] transition-colors font-semibold">CONTATO</a>
                </nav>

                <div class="flex items-center gap-2">
                    <a href="{{ route('login') }}" class="bg-[#7B19E5] text-white px-5 py-2.5 text-sm rounded-full btn-primary shadow-lg">LOGIN</a>
                    <a href="{{ route('agendar') }}" class="bg-[#FF2EB6] text-white px-5 py-2.5 text-sm rounded-full btn-primary shadow-lg">AGENDAR</a>
                </div>
            </div>
        </div>
    </header>

    <main>
        <section class="relative min-h-[42vh] flex items-center justify-center overflow-hidden">
            <div class="absolute inset-0">
                <img src="https://images.unsplash.com/photo-1470259078422-826894b933aa?q=80&w=1600&auto=format&fit=crop"
                     alt="Salão Cheias de Charme"
                     class="w-full h-full object-cover"
                     loading="eager">
                <div class="absolute inset-0 bg-gradient-to-r from-[#7B19E5]/55 via-[#A955D3]/35 to-[#FF2EB6]/55"></div>
            </div>

            <div class="relative z-10 text-center text-white max-w-4xl mx-auto px-6 py-20">
                <span class="text-sm tracking-[0.28em] font-medium block mb-4">CHEIAS DE CHARME</span>
                <h1 class="text-5xl md:text-7xl font-title mb-5 drop-shadow-xl title-glow">{{ $titulo }}</h1>
                <p class="text-lg md:text-xl opacity-95 max-w-2xl mx-auto font-body leading-relaxed drop-shadow-lg">{{ $subtitulo }}</p>
            </div>
        </section>

        <section class="py-24 relative">
            <div class="container mx-auto px-6">
                <div class="mb-12 flex justify-end lg:pr-0">
                    <a href="{{ route('public.home') }}"
                    class="group inline-flex items-center gap-3 rounded-full bg-gradient-to-r from-[#7B19E5] via-[#A955D3] to-[#FF2EB6] px-8 py-3.5 text-sm font-bold tracking-wider text-white shadow-xl shadow-purple-500/25 transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl hover:shadow-pink-500/30 focus:outline-none focus:ring-4 focus:ring-purple-300">

                        <span class="flex h-7 w-7 items-center justify-center rounded-full bg-white/20 transition-transform duration-300 group-hover:-translate-x-1">
                            <i class="fa-solid fa-arrow-left text-xs"></i>
                        </span>

                        VOLTAR PARA A HOME
                    </a>
                </div>
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse($itens as $item)
                        @if($tipo === 'servicos')
                            <article class="glass-card rounded-2xl overflow-hidden shadow-xl hover-lift border border-white/40">
                                <div class="image-zoom h-52">
                                    <img src="{{ $servicoImagens[$loop->index % count($servicoImagens)] }}" alt="{{ $item->nome }}" class="w-full h-full object-cover" loading="lazy">
                                </div>
                                <div class="p-6">
                                    <h2 class="text-2xl font-title text-[#4A00B9] mb-3">{{ $item->nome }}</h2>
                                    <p class="text-sm text-[#1A002B] font-body leading-relaxed mb-5 min-h-[64px]">{{ $item->descricao ?? 'Serviço profissional do salão.' }}</p>
                                    <div class="flex items-center justify-between gap-4 border-t border-[#FFD6F4] pt-4">
                                        <span class="text-[#FF2EB6] font-bold">R$ {{ number_format($item->preco, 2, ',', '.') }}</span>
                                        <span class="text-xs text-[#7B19E5] font-bold bg-[#F3E8FF] px-3 py-1 rounded-full">{{ $item->duracao }} min</span>
                                    </div>
                                </div>
                            </article>
                        @elseif($tipo === 'produtos')
                            <article class="glass-card rounded-2xl overflow-hidden shadow-xl hover-lift border border-white/40">
                                <div class="image-zoom h-52">
                                    <img src="{{ $produtoImagens[$loop->index % count($produtoImagens)] }}" alt="{{ $item->nome }}" class="w-full h-full object-cover" loading="lazy">
                                </div>
                                <div class="p-6">
                                    <h2 class="text-2xl font-title text-[#4A00B9] mb-3">{{ $item->nome }}</h2>
                                    <p class="text-sm text-[#1A002B] font-body leading-relaxed mb-5 min-h-[64px]">{{ $item->descricao ?? 'Produto profissional disponível no salão.' }}</p>
                                    <div class="flex items-center justify-between gap-4 border-t border-[#FFD6F4] pt-4">
                                        <span class="text-[#FF2EB6] font-bold">R$ {{ number_format($item->valor_unitario, 2, ',', '.') }}</span>
                                        <span class="text-xs text-[#7B19E5] font-bold bg-[#F3E8FF] px-3 py-1 rounded-full">{{ ucfirst($item->tipo) }}</span>
                                    </div>
                                </div>
                            </article>
                        @else
                            <article class="glass-card p-8 rounded-2xl shadow-xl hover-lift border border-white/40">
                                <div class="flex gap-1 text-[#FF2EB6] mb-4 text-2xl">
                                    @for($i = 1; $i <= 5; $i++)
                                        <span class="{{ $i <= $item->nota ? '' : 'opacity-30' }}">&#9733;</span>
                                    @endfor
                                </div>
                                <p class="text-[#1A002B] mb-6 font-body leading-relaxed min-h-[112px]">{{ $item->comentario }}</p>
                                <div class="border-t border-[#FFD6F4] pt-4">
                                    <h2 class="font-medium text-[#4A00B9] font-title text-xl">{{ $item->cliente_nome }}</h2>
                                    <p class="text-sm text-[#7B19E5] font-body">{{ $item->profissional_nome ? 'Atendimento com ' . $item->profissional_nome : 'Cliente' }}</p>
                                </div>
                            </article>
                        @endif
                    @empty
                        <div class="glass-card rounded-2xl p-10 md:col-span-2 lg:col-span-3 text-center text-[#4A00B9] shadow-xl">
                            Nenhum item encontrado.
                        </div>
                    @endforelse
                </div>
            </div>
        </section>
    </main>
</body>
</html>
