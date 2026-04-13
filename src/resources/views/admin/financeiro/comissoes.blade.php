<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Extrato de Comissões por Profissional') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- FILTRO DE BUSCA --}}
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 mb-6">
                <form action="{{ route('admin.financeiro.comissoes') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Profissional</label>
                        <select name="profissional_id" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 text-sm">
                            <option value="">Selecione um profissional</option>
                            
                            @foreach($profissionais as $p)
                                {{-- Aqui testamos se a chave é id_profissional ou apenas id --}}
                                @php 
                                    $id = $p->id_profissional ?? $p->id; 
                                    $nome = $p->nome ?? $p->name ?? 'Profissional sem nome';
                                @endphp
                                
                                <option value="{{ $id }}" {{ request('profissional_id') == $id ? 'selected' : '' }}>
                                    {{ $nome }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Mês</label>
                        <select name="mes" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 text-sm">
                            @for($i = 1; $i <= 12; $i++)
                                <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}" {{ request('mes', date('m')) == $i ? 'selected' : '' }}>
                                    {{ date('F', mktime(0, 0, 0, $i, 1)) }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Ano</label>
                        <select name="ano" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 text-sm">
                            <option value="2026" selected>2026</option>
                            <option value="2025">2025</option>
                        </select>
                    </div>

                    <x-primary-button class="justify-center h-10">
                        📊 Gerar Extrato
                    </x-primary-button>
                </form>
            </div>

            @if(request('id_profissional') || request('profissional_id'))
                {{-- RESUMO FINANCEIRO DO PROFISSIONAL --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div class="bg-indigo-600 p-6 rounded-xl shadow-md text-white">
                        <p class="text-indigo-100 text-xs font-bold uppercase">Total Produzido (Bruto)</p>
                        <h3 class="text-3xl font-black">R$ {{ number_format(collect($comissoes)->sum('valor_total'), 2, ',', '.') }}</h3>
                    </div>
                    <div class="bg-green-500 p-6 rounded-xl shadow-md text-white">
                        <p class="text-green-100 text-xs font-bold uppercase">Comissão a Pagar (Líquido)</p>
                        <h3 class="text-3xl font-black">R$ {{ number_format($totalComissao, 2, ',', '.') }}</h3>
                    </div>
                </div>

                {{-- TABELA DETALHADA --}}
                <div class="bg-white shadow-sm border border-gray-200 rounded-xl overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Data</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Serviço</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Valor do Serviço</th>
                                <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase">Sua Comissão</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($comissoes as $c)
                                <tr>
                                    <td class="px-6 py-4 text-sm text-gray-600">{{ $c['data'] }}</td>
                                    <td class="px-6 py-4 text-sm font-bold text-gray-800">{{ $c['descricao'] }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600">R$ {{ number_format($c['valor_total'], 2, ',', '.') }}</td>
                                    <td class="px-6 py-4 text-sm text-right font-bold text-green-600">R$ {{ number_format($c['comissao'], 2, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-10 text-center text-gray-500 italic">Nenhum serviço executado por este profissional no período selecionado.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @else
                <div class="bg-blue-50 border-l-4 border-blue-400 p-8 text-center rounded-lg">
                    <p class="text-blue-700">Selecione um profissional acima para visualizar o extrato de comissões.</p>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>