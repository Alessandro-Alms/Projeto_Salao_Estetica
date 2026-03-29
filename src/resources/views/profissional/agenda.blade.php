<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <h1 class="text-2xl font-bold mb-6">Minha Agenda de Atendimentos</h1>

            @forelse($agendamentos as $dia => $itens)
                <div class="mb-8">
                    <h2 class="bg-pink-100 text-pink-800 p-2 rounded-lg font-bold inline-block mb-3">
                        📅 Dia: {{ $dia }}
                    </h2>
                    
                    <div class="grid gap-4">
                        @foreach($itens as $agenda)
                            <div class="bg-white p-4 shadow rounded-xl border-l-4 border-pink-500 flex justify-between items-center">
                                <div>
                                    <span class="text-lg font-black text-gray-700">
                                        {{ \Carbon\Carbon::parse($agenda->data_hora_inicio)->format('H:i') }}
                                    </span>
                                    <span class="ml-4 font-bold text-gray-600">{{ $agenda->servico->nome }}</span>
                                    <p class="text-sm text-gray-400">Cliente: {{ $agenda->cliente->name }}</p>
                                </div>
                                <div class="text-right">
                                    <span class="text-xs font-bold uppercase p-1 bg-gray-100 rounded">{{ $agenda->status }}</span>
                                </div>
                                
                                @if($agenda->status == 'confirmado')
                                    <form action="{{ route('profissional.agendamento.executado', $agenda->id_agendamento) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-lg">
                                            ✅ Finalizar 
                                        </button>
                                    </form>
                                @elseif($agenda->status == 'concluido')
                                    <span class="text-green-600 font-bold bg-green-50 px-3 py-1 rounded-full text-xs border border-green-200">
                                        ✔ EXECUTADO
                                    </span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @empty
                <p>Você não tem agendamentos marcados.</p>
            @endforelse
        </div>
    </div>
</x-app-layout>