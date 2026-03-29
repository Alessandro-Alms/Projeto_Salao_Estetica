<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800">Meus Agendamentos</h2>
                <a href="{{ route('cliente.agendar') }}" class="bg-pink-600 text-white px-4 py-2 rounded-lg font-bold hover:bg-pink-700 transition">
                    + Novo Agendamento
                </a>
            </div>

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

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($agendamentos as $agenda)
                    <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-200 p-5 flex flex-col justify-between">
                        <div>
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
                    <div class="col-span-full bg-white p-10 text-center rounded-xl shadow">
                        <p class="text-gray-500 text-lg">Você ainda não possui nenhum agendamento.</p>
                        <a href="{{ route('cliente.agendar') }}" class="text-pink-600 font-bold hover:underline mt-2 inline-block">Clique aqui para marcar seu primeiro horário!</a>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>