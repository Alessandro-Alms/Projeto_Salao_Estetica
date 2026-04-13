<x-app-layout>
    <div class="py-12 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <h1 class="text-2xl font-bold mb-6">Gestão de Pacotes Promocionais</h1>

        {{-- Mensagem de Sucesso --}}
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        {{-- Formulário de Criação --}}
        <div class="bg-white p-6 rounded-lg shadow-md mb-8">
            <h2 class="text-lg font-semibold mb-4">Criar Novo Pacote</h2>
            <form action="{{ route('admin.pacotes.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @csrf
                
                <div class="col-span-1 md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700">Nome do Pacote (Ex: Combo Verão Laser)</label>
                    <input type="text" name="nome" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Serviço Vinculado</label>
                    <select name="servico_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        <option value="">Selecione...</option>
                        @foreach($servicos as $servico)
                            <option value="{{ $servico->id_servico }}">{{ $servico->nome }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Qtd. de Sessões</label>
                    <input type="number" name="quantidade_sessoes" min="2" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Valor Total (R$)</label>
                    <input type="number" name="valor_total" step="0.01" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Validade (em dias)</label>
                    <input type="number" name="validade_dias" value="90" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>

                <div class="col-span-1 md:col-span-3 mt-4">
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded shadow hover:bg-blue-700">
                        Salvar Pacote
                    </button>
                </div>
            </form>
        </div>

        {{-- Lista de Pacotes --}}
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nome / Serviço</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Sessões</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Valor</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Validade</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($pacotes as $pacote)
                        <tr>
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-900">{{ $pacote->nome }}</div>
                                <div class="text-sm text-gray-500">{{ $pacote->servico->nome ?? 'Serviço Removido' }}</div>
                            </td>
                            <td class="px-6 py-4 text-center text-sm text-gray-900">{{ $pacote->quantidade_sessoes }}</td>
                            <td class="px-6 py-4 text-center text-sm text-gray-900">R$ {{ number_format($pacote->valor_total, 2, ',', '.') }}</td>
                            <td class="px-6 py-4 text-center text-sm text-gray-500">{{ $pacote->validade_dias }} dias</td>
                            <td class="px-6 py-4 text-center text-sm font-medium">
                                <a href="{{ route('admin.pacotes.edit', $pacote->id_pacote) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Editar</a>

                                <form action="{{ route('admin.pacotes.destroy', $pacote->id_pacote) }}" method="POST" class="inline-block" onsubmit="return confirm('Tem certeza que deseja excluir este pacote?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">Excluir</button>
                                </form>
                            </td>   
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>