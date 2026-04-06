<x-app-layout>
    <div class="py-12">
        <div class="max-w-2xl mx-auto bg-white p-8 shadow rounded-lg border-t-4 border-pink-600">
            <h1 class="text-2xl font-bold mb-6 text-pink-600 italic">✨ Agendar meu Horário</h1>

            {{-- BLOCO DE ERROS: Aqui aparecerão as mensagens de "Horário de Almoço", "Conflito", etc. --}}
            @if ($errors->any())
                <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow-sm">
                    <p class="font-bold mb-1">Não foi possível agendar:</p>
                    <ul class="list-disc list-inside text-sm italic">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('cliente.agendar.salvar') }}" method="POST">
                @csrf
                
                {{-- GARANTINDO O CLIENTE_ID: Se for o próprio cliente logado --}}
                <input type="hidden" name="cliente_id" value="{{ auth()->id() }}">

                <div class="space-y-4">
                    <div>
                        <label class="block font-medium text-gray-700">Com quem você deseja agendar?</label>
                        <select name="profissional_id" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-pink-500 focus:border-pink-500">
                            <option value="">Selecione o profissional</option>
                            @foreach($profissionais as $pro)
                                <option value="{{ $pro->id }}" {{ old('profissional_id') == $pro->id ? 'selected' : '' }}>
                                    {{ $pro->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block font-medium text-gray-700">Qual serviço?</label>
                        <select name="servico_id" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-pink-500 focus:border-pink-500">
                            <option value="">Selecione o serviço</option>
                            @foreach($servicos as $serv)
                                <option value="{{ $serv->id_servico }}" {{ old('servico_id') == $serv->id_servico ? 'selected' : '' }}>
                                    {{ $serv->nome }} — R$ {{ number_format($serv->preco, 2, ',', '.') }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block font-medium text-gray-700">Quando?</label>
                        {{-- O old('data_hora') impede que o cliente tenha que digitar a data de novo se der erro --}}
                        <input type="datetime-local" name="data_hora" value="{{ old('data_hora') }}" 
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-pink-500 focus:border-pink-500">
                    </div>

                    <button type="submit" class="w-full bg-pink-600 text-white py-3 rounded-md hover:bg-pink-700 font-bold shadow-lg transition duration-200">
                        🚀 Confirmar Agendamento
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>