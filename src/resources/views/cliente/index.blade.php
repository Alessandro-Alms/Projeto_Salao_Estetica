<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Alertas de Sucesso ou Erro --}}
            @if(session('status'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 shadow-sm">
                    {{ session('status') }}
                </div>
            @endif

            @if($errors->any())
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 shadow-sm">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            {{-- SESSÃO DE MEUS PACOTES (ADICIONADA AQUI) --}}
            <div class="mb-12">
                <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center gap-2">
                    🎟️ Meus Pacotes Ativos
                </h2>

                @if($pacotes->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($pacotes as $meuPacote)
                            <div class="bg-gradient-to-br from-blue-50 to-blue-100 border border-blue-200 rounded-xl p-5 shadow-sm relative overflow-hidden">
                                
                                {{-- Decoração de fundo --}}
                                <div class="absolute -right-6 -top-6 text-blue-200 opacity-50">
                                    <svg class="w-24 h-24" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 2a4 4 0 00-4 4v1H5a1 1 0 00-.994.89l-1 9A1 1 0 004 18h12a1 1 0 00.994-1.11l-1-9A1 1 0 0015 7h-1V6a4 4 0 00-4-4zm2 5V6a2 2 0 10-4 0v1h4zm-6 3a1 1 0 112 0 1 1 0 01-2 0zm7-1a1 1 0 100 2 1 1 0 000-2z" clip-rule="evenodd" />
                                    </svg>
                                </div>

                                <div class="relative z-10">
                                    <span class="bg-blue-600 text-white text-xs font-bold px-2 py-1 rounded uppercase tracking-wide">
                                        Ativo
                                    </span>
                                    
                                    <h3 class="text-lg font-black text-blue-900 mt-3 mb-1">
                                        {{ $meuPacote->pacote->nome }}
                                    </h3>
                                    
                                    <p class="text-sm text-blue-700 mb-4">
                                        Válido até: <span class="font-bold">{{ \Carbon\Carbon::parse($meuPacote->data_validade)->format('d/m/Y') }}</span>
                                    </p>

                                    <div class="bg-white rounded-lg p-3 border border-blue-100 shadow-inner flex justify-between items-center">
                                        <span class="text-gray-600 text-sm font-medium">Sessões Restantes:</span>
                                        <span class="text-2xl font-black text-blue-600">
                                            {{ $meuPacote->sessoes_restantes }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="bg-white border border-gray-200 rounded-xl p-6 text-center shadow-sm">
                        <p class="text-gray-500 mb-2">Você não possui nenhum pacote ativo no momento.</p>
                    </div>
                @endif
            </div>

            <hr class="border-gray-200 mb-8">

            {{-- SESSÃO DE MEUS AGENDAMENTOS --}}
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800">Meus Agendamentos</h2>
                <a href="{{ route('cliente.agendar') }}" class="bg-pink-600 text-white px-4 py-2 rounded-lg font-bold hover:bg-pink-700 transition shadow-sm">
                    + Novo Agendamento
                </a>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($agendamentos as $agenda)
                    <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-200 p-5 flex flex-col justify-between">
                        <div>
                            @if(auth()->user()->contador_fidelidade == 5)
                                <span class="bg-yellow-100 text-yellow-800 text-xs font-bold px-2 py-1 rounded border border-yellow-300 mb-3 inline-block">
                                    🎁 PRÓXIMO SERVIÇO COM 50% OFF!
                                </span>
                            @else
                                <span class="bg-blue-100 text-blue-800 text-xs font-bold px-2 py-1 rounded mb-3 inline-block">
                                    Fidelidade: {{ auth()->user()->contador_fidelidade }}/5
                                </span>
                            @endif

                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <p class="text-sm text-gray-500 font-semibold uppercase">
                                        {{ \Carbon\Carbon::parse($agenda->data_hora_inicio)->translatedFormat('d \d\e F') }}
                                    </p>
                                    <h3 class="text-lg font-bold text-gray-900">{{ $agenda->servico->nome }}</h3>
                                </div>
                                <span class="px-2 py-1 text-xs font-bold rounded-full 
                                    {{ $agenda->status == 'confirmado' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    {{ ucfirst($agenda->status) }}
                                </span>
                            </div>

                            <div class="space-y-2 text-sm text-gray-600 mb-4">
                                <p><strong>🕒 Horário:</strong> {{ \Carbon\Carbon::parse($agenda->data_hora_inicio)->format('H:i') }} às {{ \Carbon\Carbon::parse($agenda->data_hora_fim)->format('H:i') }}</p>
                                <p><strong>👤 Profissional:</strong> {{ $agenda->profissional->name }}</p>
                                <p><strong>💰 Valor:</strong> R$ {{ number_format($agenda->valor_total, 2, ',', '.') }}</p>
                            </div>
                        </div>

                        @if($agenda->status == 'confirmado')
                            <div class="mt-4 pt-4 border-t border-gray-100">
                                <form action="{{ route('cliente.agendamento.cancelar', ['id' => $agenda->id_agendamento]) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja cancelar este agendamento?')">
                                    @csrf
                                    <button type="submit" class="w-full text-red-600 border border-red-600 py-2 rounded-lg hover:bg-red-50 text-sm font-bold transition duration-150">
                                        ❌ Cancelar Agendamento
                                    </button>
                                </form>
                            </div>
                        @endif
                        
                    </div>
                @empty
                    <div class="col-span-full bg-white p-10 text-center rounded-xl shadow border border-gray-200">
                        <p class="text-gray-500 text-lg">Você ainda não possui nenhum agendamento.</p>
                        <a href="{{ route('cliente.agendar') }}" class="text-pink-600 font-bold hover:underline mt-2 inline-block">Clique aqui para marcar seu primeiro horário!</a>
                    </div>
                @endforelse
            </div>
            
        </div>
    </div>
</x-app-layout>