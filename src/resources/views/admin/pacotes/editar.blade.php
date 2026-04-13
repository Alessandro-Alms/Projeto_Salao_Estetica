<x-app-layout>
    <div class="py-12 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Editar Pacote: {{ $pacote->nome }}</h1>
            <a href="{{ route('admin.pacotes.index') }}" class="text-gray-600 hover:text-gray-900 underline">Voltar para a lista</a>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-md mb-8">
            <form action="{{ route('admin.pacotes.update', $pacote->id_pacote) }}" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @csrf
                @method('PUT')
                
                <div class="col-span-1 md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700">Nome do Pacote</label>
                    <input type="text" name="nome" value="{{ $pacote->nome }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Serviço Vinculado</label>
                    <select name="servico_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        @foreach($servicos as $servico)
                            <option value="{{ $servico->id_servico }}" {{ $pacote->servico_id == $servico->id_servico ? 'selected' : '' }}>
                                {{ $servico->nome }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Qtd. de Sessões</label>
                    <input type="number" name="quantidade_sessoes" value="{{ $pacote->quantidade_sessoes }}" min="2" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Valor Total (R$)</label>
                    <input type="number" name="valor_total" value="{{ $pacote->valor_total }}" step="0.01" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Validade (em dias)</label>
                    <input type="number" name="validade_dias" value="{{ $pacote->validade_dias }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>

                <div class="col-span-1 md:col-span-3 mt-4">
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded shadow hover:bg-blue-700">
                        Atualizar Pacote
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>