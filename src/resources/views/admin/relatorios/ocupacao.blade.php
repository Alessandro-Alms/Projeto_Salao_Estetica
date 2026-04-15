<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Relatório 2: Ocupação da Agenda') }}
            </h2>
            <a href="{{ route('admin.relatorios.index') }}" class="text-sm text-blue-600 hover:underline">⬅ Voltar</a>
        </div>
    </x-slot>

    <div class="container mx-auto px-4 py-6">
        <div class="bg-white p-4 rounded-lg shadow mb-6">
            <form method="GET" action="{{ route('admin.relatorios.ocupacao') }}" class="flex items-end gap-3">
                <div>
                    <label class="block text-sm text-gray-600">Início</label>
                    <input type="date" name="data_inicio" value="{{ $dataInicio }}" class="border rounded p-2">
                </div>
                <div>
                    <label class="block text-sm text-gray-600">Fim</label>
                    <input type="date" name="data_fim" value="{{ $dataFim }}" class="border rounded p-2">
                </div>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Analisar Ocupação</button>
            </form>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div class="bg-white p-6 rounded-lg shadow flex flex-col items-center justify-center">
                <h3 class="text-gray-500 font-bold mb-2">Taxa Média de Ocupação</h3>
                <div class="text-5xl font-black {{ $taxaOcupacao > 70 ? 'text-green-500' : 'text-yellow-500' }}">
                    {{ number_format($taxaOcupacao, 1) }}%
                </div>
                <p class="text-sm text-gray-400 mt-2 text-center">Baseado em uma capacidade estimada de 8 atendimentos/dia por profissional.</p>
            </div>

            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="font-bold mb-4">Volume por Dia da Semana</h3>
                <canvas id="chartDias"></canvas>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="font-bold mb-4">Horários de Maior Movimento (Calor da Agenda)</h3>
            <div class="h-80">
                <canvas id="chartHoras"></canvas>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Dados Dias da Semana
            const dadosDias = @json($dadosDias);
            new Chart(document.getElementById('chartDias'), {
                type: 'polarArea',
                data: {
                    labels: dadosDias.map(d => d.label),
                    datasets: [{
                        data: dadosDias.map(d => d.total),
                        backgroundColor: [
                            '#ef4444', '#f97316', '#f59e0b', '#10b981', '#3b82f6', '#6366f1', '#8b5cf6'
                        ]
                    }]
                }
            });

            // Dados Horários
            const dadosHoras = @json($ocupacaoPorHora);
            new Chart(document.getElementById('chartHoras'), {
                type: 'bar',
                data: {
                    labels: dadosHoras.map(h => (h.hora ? h.hora : '00') + ':00'),
                    datasets: [{
                        label: 'Total de Agendamentos',
                        data: dadosHoras.map(h => h.total),
                        backgroundColor: '#3b82f6',
                        borderRadius: 5
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
                }
            });
        });
    </script>
</x-app-layout>