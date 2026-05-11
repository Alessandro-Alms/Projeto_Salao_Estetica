<nav x-data="{ open: false }" class="fixed inset-x-0 top-0 w-full z-50 bg-white/85 backdrop-blur-md border-b border-[#FFD6F4] shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between py-3 gap-4">
            
            <div class="flex items-center min-w-0">
                <a href="{{ route('dashboard') }}" class="flex items-center">
                    <img src="{{ asset('img/Prancheta1.png') }}" alt="Cheias de Charme" class="h-8 w-auto">
                </a>
            </div>

            <div class="hidden sm:flex sm:items-center">
                <div class="flex items-center gap-1 rounded-full border border-[#FFD6F4] bg-white/70 p-0.5 shadow-sm">
                    <a href="{{ route('dashboard') }}" class="px-4 py-1.5 rounded-full text-sm font-semibold {{ request()->routeIs('dashboard') ? 'bg-[#7B19E5] text-white shadow-md' : 'text-[#1A002B] hover:bg-[#FFD6F4]/70 hover:text-[#7B19E5]' }} transition-all">
                        Painel
                    </a>
                    <a href="{{ route('public.home') }}" class="px-4 py-1.5 rounded-full text-sm font-semibold {{ request()->routeIs('public.home') ? 'bg-[#7B19E5] text-white shadow-md' : 'text-[#1A002B] hover:bg-[#FFD6F4]/70 hover:text-[#7B19E5]' }} transition-all">
                        Site
                    </a>

                    @if(auth()->user()->isCliente())
                        <a href="{{ route('cliente.produtos.index') }}" class="px-4 py-1.5 rounded-full text-sm font-semibold {{ request()->routeIs('cliente.produtos.*') ? 'bg-[#7B19E5] text-white shadow-md' : 'text-[#1A002B] hover:bg-[#FFD6F4]/70 hover:text-[#7B19E5]' }} transition-all">
                            Produtos
                        </a>
                        <a href="{{ route('cliente.pacotes.index') }}" class="px-4 py-1.5 rounded-full text-sm font-semibold {{ request()->routeIs('cliente.pacotes.*') ? 'bg-[#7B19E5] text-white shadow-md' : 'text-[#1A002B] hover:bg-[#FFD6F4]/70 hover:text-[#7B19E5]' }} transition-all">
                            Pacotes
                        </a>
                    @endif

                    @if(auth()->user()->cargo === 'gerente')
                        <a href="{{ route('admin.usuarios.index') }}" class="px-4 py-1.5 rounded-full text-sm font-semibold {{ request()->routeIs('admin.usuarios.*') ? 'bg-[#7B19E5] text-white shadow-md' : 'text-[#1A002B] hover:bg-[#FFD6F4]/70 hover:text-[#7B19E5]' }} transition-all">
                            Usuários
                        </a>
                        <a href="{{ route('admin.servicos.index') }}" class="px-4 py-1.5 rounded-full text-sm font-semibold {{ request()->routeIs('admin.servicos.*') ? 'bg-[#7B19E5] text-white shadow-md' : 'text-[#1A002B] hover:bg-[#FFD6F4]/70 hover:text-[#7B19E5]' }} transition-all">
                            Serviços
                        </a>
                        <a href="{{ route('admin.produtos.index') }}" class="px-4 py-1.5 rounded-full text-sm font-semibold {{ request()->routeIs('admin.produtos.*') ? 'bg-[#7B19E5] text-white shadow-md' : 'text-[#1A002B] hover:bg-[#FFD6F4]/70 hover:text-[#7B19E5]' }} transition-all">
                            Produtos
                        </a>
                    @endif
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center gap-2">
                <button type="button" data-theme-toggle class="dark-mode-toggle rounded-full border border-[#FFD6F4] bg-white/70 px-4 py-1.5 text-sm font-semibold text-[#1A002B] shadow-sm hover:text-[#FF2EB6] transition-colors">
                    Escuro
                </button>
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="flex items-center gap-2 rounded-full border border-[#FFD6F4] bg-white/70 px-4 py-1.5 text-sm font-semibold text-[#1A002B] shadow-sm hover:text-[#FF2EB6] transition-colors">
                        <span class="max-w-36 truncate">{{ Auth::user()->name }}</span>
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>

                    <div x-show="open" @click.away="open = false" 
                         class="absolute right-0 mt-2 w-44 bg-white rounded-xl shadow-lg border border-[#FFD6F4] py-1 z-50">
                        <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-[#1A002B] hover:bg-[#FFD6F4] transition-colors">
                            Meu Perfil
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-[#FF2EB6] hover:bg-[#FFD6F4] transition-colors">
                                Sair
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="-mr-2 flex items-center sm:hidden">
                <button @click="open = !open" class="p-2 rounded-md text-[#1A002B] hover:text-[#FF2EB6]">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': !open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div x-show="open" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-1"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="sm:hidden bg-white/95 border-b border-[#FFD6F4]">
        <div class="px-2 pt-2 pb-3 space-y-1">
            <a href="{{ route('dashboard') }}" class="block px-3 py-2 text-base text-[#1A002B] hover:text-[#FF2EB6] hover:bg-[#FFD6F4] rounded-md">
                Painel
            </a>
            <a href="{{ route('public.home') }}" class="block px-3 py-2 text-base text-[#1A002B] hover:text-[#FF2EB6] hover:bg-[#FFD6F4] rounded-md">
                Site
            </a>

            @if(auth()->user()->isCliente())
                <a href="{{ route('cliente.produtos.index') }}" class="block px-3 py-2 text-base text-[#1A002B] hover:text-[#FF2EB6] hover:bg-[#FFD6F4] rounded-md">Produtos</a>
                <a href="{{ route('cliente.pacotes.index') }}" class="block px-3 py-2 text-base text-[#1A002B] hover:text-[#FF2EB6] hover:bg-[#FFD6F4] rounded-md">Pacotes</a>
            @endif

            @if(auth()->user()->cargo === 'gerente')
                <a href="{{ route('admin.usuarios.index') }}" class="block px-3 py-2 text-base text-[#1A002B] hover:text-[#FF2EB6] hover:bg-[#FFD6F4] rounded-md">Usuários</a>
                <a href="{{ route('admin.servicos.index') }}" class="block px-3 py-2 text-base text-[#1A002B] hover:text-[#FF2EB6] hover:bg-[#FFD6F4] rounded-md">Serviços</a>
                <a href="{{ route('admin.produtos.index') }}" class="block px-3 py-2 text-base text-[#1A002B] hover:text-[#FF2EB6] hover:bg-[#FFD6F4] rounded-md">Produtos</a>
            @endif
        </div>
        <div class="pt-4 pb-3 border-t border-[#FFD6F4]">
            <div class="px-4 py-2 text-sm text-[#1A002B]">{{ Auth::user()->name }}</div>
            <button type="button" data-theme-toggle class="dark-mode-toggle mx-4 mb-2 rounded-full border border-[#FFD6F4] bg-white/70 px-4 py-2 text-sm font-semibold text-[#1A002B] shadow-sm hover:text-[#FF2EB6] transition-colors">
                Escuro
            </button>
            <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-[#1A002B] hover:bg-[#FFD6F4]">Meu Perfil</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-[#FF2EB6] hover:bg-[#FFD6F4]">Sair</button>
            </form>
        </div>
    </div>
</nav>
