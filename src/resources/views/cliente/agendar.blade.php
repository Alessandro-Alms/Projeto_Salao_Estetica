<x-app-layout>
    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h2 class="text-2xl font-bold mb-6 text-gray-800">Novo Agendamento</h2>

                <form action="{{ route('cliente.agendar.salvar') }}" method="POST">
                    @csrf
                    
                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label class="block font-medium text-gray-700">Profissional</label>
                            <select name="profissional_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-pink-500 focus:border-pink-500">
                                @foreach($profissionais as $pro)
                                    <option value="{{ $pro->id }}">{{ $pro->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block font-medium text-gray-700">Serviço</label>
                            <select name="servico_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-pink-500 focus:border-pink-500">
                                @foreach($servicos as $serv)
                                    <option value="{{ $serv->id_servico }}">
                                        {{ $serv->nome }} - R$ {{ number_format($serv->preco, 2, ',', '.') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block font-medium text-gray-700">Data e Horário</label>
                            <input type="datetime-local" name="data_hora" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-pink-500 focus:border-pink-500">
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <button type="submit" class="bg-pink-600 hover:bg-pink-700 text-white font-bold py-2 px-6 rounded-lg transition duration-150">
                                Confirmar Agendamento
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>