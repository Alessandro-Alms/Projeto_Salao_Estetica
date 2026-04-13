<x-app-layout>
    <div class="py-12 max-w-4xl mx-auto sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold mb-6 text-gray-800">Vender Pacote Promocional</h1>

        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow-sm">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white p-8 rounded-xl shadow-md border border-gray-100">
            <form action="{{ route('admin.venda.store') }}" method="POST">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Selecione o Cliente</label>
                        <select name="cliente_id" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-3">
                            <option value="">Buscar cliente...</option>
                            @foreach($clientes as $cliente)
                                <option value="{{ $cliente->id }}">{{ $cliente->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Selecione o Pacote</label>
                        <select name="pacote_id" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-3">
                            <option value="">Escolher pacote...</option>
                            @foreach($pacotes as $pacote)
                                <option value="{{ $pacote->id_pacote }}">
                                    {{ $pacote->nome }} ({{ $pacote->quantidade_sessoes }} sessões) - R$ {{ number_format($pacote->valor_total, 2, ',', '.') }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mt-8 flex justify-end">
                    <button type="submit" class="bg-indigo-600 text-white px-6 py-3 rounded-lg font-bold shadow-lg hover:bg-indigo-700 transition duration-200 w-full md:w-auto text-center">
                        Confirmar Venda e Ativar Pacote
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>