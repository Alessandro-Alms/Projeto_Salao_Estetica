<x-app-layout>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow sm:rounded-lg">
                <h1 class="text-xl font-bold mb-4">Teste de Agendamento (Simples)</h1>

                @if(session('status'))
                    <div class="mb-4 text-green-600 font-bold">{{ session('status') }}</div>
                @endif

                <form action="{{ route('admin.agenda.store') }}" method="POST">
                    @csrf
                    
                    @if ($errors->any())
                        <div class="mb-4 p-4 bg-red-100 border-l-4 border-red-500 text-red-700">
                            <ul class="list-disc ml-5">
                                @foreach ($errors->all() as $error)
                                    <li class="font-bold">{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label>Cliente:</label>
                            <select name="cliente_id" class="w-full border-gray-300 rounded">
                                @foreach(\App\Models\User::where('cargo', 'cliente')->get() as $cliente)
                                    <option value="{{ $cliente->id }}">{{ $cliente->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label>Profissional:</label>
                            <select name="profissional_id" class="w-full border-gray-300 rounded">
                                @foreach(\App\Models\User::where('cargo', 'profissional')->get() as $pro)
                                    <option value="{{ $pro->id }}">{{ $pro->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label>Serviço:</label>
                            <select name="servico_id" class="w-full border-gray-300 rounded">
                                @foreach(\App\Models\Servico::all() as $servico)
                                    <option value="{{ $servico->id_servico }}">{{ $servico->nome }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label>Data e Hora do Início:</label>
                            <input type="datetime-local" name="data_hora" class="w-full border-gray-300 rounded">
                        </div>

                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded mt-4">
                            Salvar Agendamento
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>