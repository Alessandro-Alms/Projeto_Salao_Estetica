<nav x-data="{ open: false }" class="bg-white/70 backdrop-blur-sm border-b border-[#FFD6F4] shadow-sm sticky top-0 z-30">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <!-- Logo -->
            <div class="flex items-center">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                    <img src="{{ asset('img/Prancheta1.png') }}" alt="Cheias de Charme" class="h-10 w-auto">
                </a>
            </div>

            <!-- Navigation Links (Desktop) -->
            <div class="hidden sm:flex sm:items-center sm:ml-6">
                <div class="flex space-x-4">
                    <a href="{{ route('dashboard') }}" class="text-sm text-[#1A002B] hover:text-[#FF2EB6] transition-colors">
                        Dashboard
                    </a>
                    
                    @if(auth()->user()->cargo === 'gerente')
                        <a href="{{ route('admin.usuarios.index') }}" class="text-sm text-[#1A002B] hover:text-[#FF2EB6] transition-colors">
                            Usuários
                        </a>
                        <a href="{{ route('admin.servicos.index') }}" class="text-sm text-[#1A002B] hover:text-[#FF2EB6] transition-colors">
                            Serviços
                        </a>
                        <a href="{{ route('admin.produtos.index') }}" class="text-sm text-[#1A002B] hover:text-[#FF2EB6] transition-colors">
                            Produtos
                        </a>
                    @endif
                </div>
            </div>

            <!-- Dropdown do usuário -->
            <div class="hidden sm:flex sm:items-center sm:ml-6">
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="flex items-center gap-1 text-sm text-[#1A002B] hover:text-[#FF2EB6] transition-colors">
                        {{ Auth::user()->name }}
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    
                    <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-40 bg-white rounded-lg shadow-lg border border-[#FFD6F4] py-1 z-50">
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

            <!-- Menu Mobile -->
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

    <!-- Mobile Menu -->
    <div :class="{'block': open, 'hidden': !open}" class="hidden sm:hidden bg-white/95 border-b border-[#FFD6F4]">
        <div class="px-2 pt-2 pb-3 space-y-1">
            <a href="{{ route('dashboard') }}" class="block px-3 py-2 text-base text-[#1A002B] hover:text-[#FF2EB6] hover:bg-[#FFD6F4] rounded-md">
                Dashboard
            </a>
            
            @if(auth()->user()->cargo === 'gerente')
                <a href="{{ route('admin.usuarios.index') }}" class="block px-3 py-2 text-base text-[#1A002B] hover:text-[#FF2EB6] hover:bg-[#FFD6F4] rounded-md">
                    Usuários
                </a>
                <a href="{{ route('admin.servicos.index') }}" class="block px-3 py-2 text-base text-[#1A002B] hover:text-[#FF2EB6] hover:bg-[#FFD6F4] rounded-md">
                    Serviços
                </a>
                <a href="{{ route('admin.produtos.index') }}" class="block px-3 py-2 text-base text-[#1A002B] hover:text-[#FF2EB6] hover:bg-[#FFD6F4] rounded-md">
                    Produtos
                </a>
            @endif
        </div>
        <div class="pt-4 pb-3 border-t border-[#FFD6F4]">
            <div class="px-4 py-2 text-sm text-[#1A002B]">{{ Auth::user()->name }}</div>
            <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-[#1A002B] hover:bg-[#FFD6F4]">Meu Perfil</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-[#FF2EB6] hover:bg-[#FFD6F4]">Sair</button>
            </form>
        </div>
    </div>
</nav>