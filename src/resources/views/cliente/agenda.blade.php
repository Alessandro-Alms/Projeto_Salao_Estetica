<x-app-layout>
    <div class="py-12">
        <div class="max-w-2xl mx-auto bg-white p-8 shadow rounded-lg">
            <h1 class="text-2xl font-bold mb-6 text-pink-600">Agendar meu Horário</h1>

            @if ($errors->any())
                <div class="bg-red-100 text-red-700 p-4 mb-4 rounded">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form action="{{ route('cliente.agendar.salvar') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block font-medium">Com quem você deseja agendar?</label>
                        <select name="profissional_id" class="w-full border-gray-300 rounded-md shadow-sm">
                            @foreach($profissionais as $pro)
                                <option value="{{ $pro->id }}">{{ $pro->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block font-medium">Qual serviço?</label>
                        <select name="servico_id" class="w-full border-gray-300 rounded-md shadow-sm">
                            @foreach($servicos as $serv)
                                <option value="{{ $serv->id_servico }}">{{ $serv->nome }} - R$ {{ number_format($serv->preco, 2, ',', '.') }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block font-medium">Quando?</label>
                        <input type="datetime-local" name="data_hora" class="w-full border-gray-300 rounded-md shadow-sm">
                    </div>

                    <button type="submit" class="w-full bg-pink-600 text-white py-2 rounded-md hover:bg-pink-700 font-bold">
                        Confirmar Agendamento
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>