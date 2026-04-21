<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Central de Relatórios e Inteligência') }}
        </h2>
    </x-slot>

    <div class="container mx-auto px-4 py-6">
        
        <div class="bg-white p-4 rounded-xl shadow mb-6 flex flex-wrap justify-between items-center gap-4">
            <form method="GET" action="{{ route('admin.relatorios.index') }}" class="flex flex-wrap items-end gap-3">
                <div>
                    <label class="block text-sm text-gray-600 font-medium mb-1">Data Início</label>
                    <input type="date" name="data_inicio" value="{{ $dataInicio }}" class="border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm text-gray-600 font-medium mb-1">Data Fim</label>
                    <input type="date" name="data_fim" value="{{ $dataFim }}" class="border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <button type="submit" class="bg-blue-600 text-white px-5 py-2.5 rounded-lg font-bold hover:bg-blue-700 transition shadow-sm">
                    Atualizar Visão Geral
                </button>
            </form>

            <div class="flex gap-2">
                <a href="{{ route('admin.relatorios.exportarPdf', ['data_inicio' => $dataInicio, 'data_fim' => $dataFim]) }}" class="bg-red-50 text-red-600 border border-red-200 px-4 py-2 rounded-lg hover:bg-red-100 transition font-bold flex items-center gap-2">
                    📄 Exportar PDF
                </a>
                <a href="{{ route('admin.relatorios.exportarExcel', ['data_inicio' => $dataInicio, 'data_fim' => $dataFim]) }}" class="bg-green-50 text-green-600 border border-green-200 px-4 py-2 rounded-lg hover:bg-green-100 transition font-bold flex items-center gap-2">
                    📊 Exportar Excel
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
            <div class="bg-white p-6 rounded-xl shadow border-l-4 border-green-500 flex flex-col justify-center">
                <h3 class="text-gray-500 font-bold uppercase text-xs mb-2">Faturamento (Executados)</h3>
                <p class="text-3xl font-black text-gray-800">R$ {{ number_format($faturamentoTotal ?? 0, 2, ',', '.') }}</p>
            </div>

            <div class="bg-white p-6 rounded-xl shadow border-l-4 border-blue-500 flex flex-col justify-center">
                <h3 class="text-gray-500 font-bold uppercase text-xs mb-2">Agendamentos Executados</h3>
                <p class="text-3xl font-black text-gray-800">{{ $totalExecutados ?? 0 }} <span class="text-sm font-bold text-gray-400">/ {{ $totalAgendamentos ?? 0 }}</span></p>
            </div>

            <div class="bg-white p-6 rounded-xl shadow border-l-4 border-purple-500 flex flex-col justify-center">
                <h3 class="text-gray-500 font-bold uppercase text-xs mb-2">Taxa de Ocupação/Sucesso</h3>
                @php
                    $taxa = ($totalAgendamentos ?? 0) > 0 ? (($totalExecutados ?? 0) / $totalAgendamentos) * 100 : 0;
                @endphp
                <p class="text-3xl font-black text-gray-800">{{ number_format($taxa, 1, ',', '.') }}%</p>
            </div>
        </div>

        <div class="mb-6 border-b pb-2">
            <h2 class="text-2xl font-black text-gray-800">Módulos de Relatórios</h2>
            <p class="text-gray-500 text-sm">Selecione uma categoria abaixo para ver análises aprofundadas.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            
            @php 
                $query = ['data_inicio' => $dataInicio, 'data_fim' => $dataFim]; 
            @endphp

            <a href="{{ route('admin.relatorios.faturamento', $query) }}" class="bg-white p-5 rounded-xl shadow hover:shadow-lg hover:-translate-y-1 transition group border border-gray-100">
                <div class="text-3xl mb-3 group-hover:scale-110 transition-transform origin-left">💰</div>
                <h3 class="font-bold text-gray-800 text-lg mb-1 group-hover:text-blue-600 transition-colors">Faturamento</h3>
                <p class="text-xs text-gray-500">Total de receitas, ticket médio e comparativo com período anterior.</p>
            </a>

            <a href="{{ route('admin.relatorios.ocupacao', $query) }}" class="bg-white p-5 rounded-xl shadow hover:shadow-lg hover:-translate-y-1 transition group border border-gray-100">
                <div class="text-3xl mb-3 group-hover:scale-110 transition-transform origin-left">🗓️</div>
                <h3 class="font-bold text-gray-800 text-lg mb-1 group-hover:text-blue-600 transition-colors">Ocupação da Agenda</h3>
                <p class="text-xs text-gray-500">Taxa de preenchimento de horários, identificação de picos e horas mortas.</p>
            </a>

            <a href="{{ route('admin.relatorios.desempenho', $query) }}" class="bg-white p-5 rounded-xl shadow hover:shadow-lg hover:-translate-y-1 transition group border border-gray-100">
                <div class="text-3xl mb-3 group-hover:scale-110 transition-transform origin-left">⭐</div>
                <h3 class="font-bold text-gray-800 text-lg mb-1 group-hover:text-blue-600 transition-colors">Desempenho da Equipa</h3>
                <p class="text-xs text-gray-500">Ranking de profissionais por serviços feitos, avaliações e valores gerados.</p>
            </a>

            <a href="{{ route('admin.relatorios.produtos', $query) }}" class="bg-white p-5 rounded-xl shadow hover:shadow-lg hover:-translate-y-1 transition group border border-gray-100">
                <div class="text-3xl mb-3 group-hover:scale-110 transition-transform origin-left">🛍️</div>
                <h3 class="font-bold text-gray-800 text-lg mb-1 group-hover:text-blue-600 transition-colors">Produtos Mais Vendidos</h3>
                <p class="text-xs text-gray-500">Ranking de vendas físicas, giro de prateleira e lucro direto por produto.</p>
            </a>

            <a href="{{ route('admin.relatorios.fidelizacao', $query) }}" class="bg-white p-5 rounded-xl shadow hover:shadow-lg hover:-translate-y-1 transition group border border-gray-100">
                <div class="text-3xl mb-3 group-hover:scale-110 transition-transform origin-left">🤝</div>
                <h3 class="font-bold text-gray-800 text-lg mb-1 group-hover:text-blue-600 transition-colors">Fidelização e VIPs</h3>
                <p class="text-xs text-gray-500">Taxa de retorno de clientes e ranking dos clientes que mais investem no salão.</p>
            </a>

            <a href="{{ route('admin.relatorios.cancelamentos', $query) }}" class="bg-white p-5 rounded-xl shadow hover:shadow-lg hover:-translate-y-1 transition group border border-gray-100">
                <div class="text-3xl mb-3 group-hover:scale-110 transition-transform origin-left">🚫</div>
                <h3 class="font-bold text-gray-800 text-lg mb-1 group-hover:text-blue-600 transition-colors">Análise de Cancelamentos</h3>
                <p class="text-xs text-gray-500">Motivos de desistência, cálculo de prejuízos e ranking de clientes ofensores.</p>
            </a>

            <a href="{{ route('admin.relatorios.financeiro', $query) }}" class="bg-white p-5 rounded-xl shadow hover:shadow-lg hover:-translate-y-1 transition group border border-gray-100">
                <div class="text-3xl mb-3 group-hover:scale-110 transition-transform origin-left">🏦</div>
                <h3 class="font-bold text-gray-800 text-lg mb-1 group-hover:text-blue-600 transition-colors">Financeiro Detalhado</h3>
                <p class="text-xs text-gray-500">Balanço de entradas (serviços e produtos) vs despesas (comissões geradas).</p>
            </a>

            <a href="{{ route('admin.relatorios.comissoes', $query) }}" class="bg-white p-5 rounded-xl shadow hover:shadow-lg hover:-translate-y-1 transition group border border-gray-100">
                <div class="text-3xl mb-3 group-hover:scale-110 transition-transform origin-left">💸</div>
                <h3 class="font-bold text-gray-800 text-lg mb-1 group-hover:text-blue-600 transition-colors">Comissões a Pagar</h3>
                <p class="text-xs text-gray-500">Folha de pagamento exata baseada nos serviços executados. Exportável.</p>
            </a>

            <a href="{{ route('admin.relatorios.estoque', $query) }}" class="bg-white p-5 rounded-xl shadow hover:shadow-lg hover:-translate-y-1 transition group border border-gray-100">
                <div class="text-3xl mb-3 group-hover:scale-110 transition-transform origin-left">📦</div>
                <h3 class="font-bold text-gray-800 text-lg mb-1 group-hover:text-blue-600 transition-colors">Estoque de Produtos</h3>
                <p class="text-xs text-gray-500">Saldo atual, capital empatado na prateleira e alertas de reposição urgente.</p>
            </a>

            <a href="{{ route('admin.relatorios.sazonalidade', $query) }}" class="bg-white p-5 rounded-xl shadow hover:shadow-lg hover:-translate-y-1 transition group border border-gray-100">
                <div class="text-3xl mb-3 group-hover:scale-110 transition-transform origin-left">📅</div>
                <h3 class="font-bold text-gray-800 text-lg mb-1 group-hover:text-blue-600 transition-colors">Sazonalidade</h3>
                <p class="text-xs text-gray-500">Descobre quais são os dias da semana mais fortes e fracos do teu salão.</p>
            </a>

            <a href="{{ route('admin.relatorios.avaliacoes', $query) }}" class="bg-white p-5 rounded-xl shadow hover:shadow-lg hover:-translate-y-1 transition group border border-gray-100">
                <div class="text-3xl mb-3 group-hover:scale-110 transition-transform origin-left">💬</div>
                <h3 class="font-bold text-gray-800 text-lg mb-1 group-hover:text-blue-600 transition-colors">Avaliações e Reputação</h3>
                <p class="text-xs text-gray-500">Média de estrelas, taxa de aprovação dos clientes e feed de comentários.</p>
            </a>

            <a href="{{ route('admin.relatorios.previsao', $query) }}" class="bg-white p-5 rounded-xl shadow hover:shadow-lg hover:-translate-y-1 transition group border border-gray-100">
                <div class="text-3xl mb-3 group-hover:scale-110 transition-transform origin-left">🔮</div>
                <h3 class="font-bold text-gray-800 text-lg mb-1 group-hover:text-blue-600 transition-colors">Previsão de Demanda</h3>
                <p class="text-xs text-gray-500">Projeção inteligente de clientes para os próximos 7 dias baseada no histórico.</p>
            </a>

        </div>
    </div>
</x-app-layout> 