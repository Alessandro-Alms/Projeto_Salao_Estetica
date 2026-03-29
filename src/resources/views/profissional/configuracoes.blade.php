<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Minhas Configurações de Atendimento
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-6 p-4 bg-green-100 border-l-4 border-green-500 text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('profissional.servicos.atualizar') }}">
                @csrf
                @method('PUT')

                <div class="bg-white p-6 rounded-lg shadow mb-6 border border-gray-200">
                    <h3 class="text-lg font-bold text-pink-600 mb-4 flex items-center">
                        <span class="mr-2">✂️</span> Meus Serviços e Especialidades
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($servicos as $servico)
                            @php $vinculo = $usuario->servicos->find($servico->id_servico); @endphp
                            <div class="flex items-center p-4 border rounded-xl {{ $vinculo ? 'bg-pink-50 border-pink-200' : 'bg-gray-50 border-gray-100' }}">
                                <input type="checkbox" name="servicos[{{ $servico->id_servico }}][ativo]" {{ $vinculo ? 'checked' : '' }} class="rounded text-pink-600 focus:ring-pink-500 w-5 h-5">
                                <div class="ml-3 flex-1">
                                    <span class="block font-bold text-gray-700">{{ $servico->nome }}</span>
                                    <span class="text-xs text-gray-500">Padrão: {{ $servico->duracao }} min</span>
                                </div>
                                <div class="text-right">
                                    <label class="block text-[10px] uppercase text-gray-400 font-bold">Comissão %</label>
                                    <input type="number" step="0.01" name="servicos[{{ $servico->id_servico }}][comissao]" value="{{ $vinculo ? $vinculo->pivot->comissao_percentual : '50.00' }}" class="w-20 text-sm border-gray-300 rounded shadow-sm">
                                </div>
                                <div class="w-28">
                                    <label class="block text-[10px] uppercase font-bold text-gray-400">Tempo (min)</label>
                                    <input type="number" name="servicos[{{ $servico->id_servico }}][duracao]" value="{{ $vinculo ? $vinculo->pivot->duracao_customizada : $servico->duracao }}" class="w-full border-gray-200 rounded text-sm focus:border-pink-500">
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="py-12">
                    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div class="bg-white p-6 rounded-lg shadow border border-gray-200">
                        <h3 class="text-lg font-bold text-blue-600 mb-4 flex items-center">
                            <span class="mr-2">⏰</span> Minha Grade de Horários
                        </h3>
                        <div class="space-y-3">
                            @php
                                $dias = [1 => 'Segunda-feira', 2 => 'Terça-feira', 3 => 'Quarta-feira', 4 => 'Quinta-feira', 5 => 'Sexta-feira', 6 => 'Sábado', 0 => 'Domingo'];
                            @endphp
                            @foreach($dias as $num => $nome)
                                @php $h = $usuario->horariosTrabalho->where('dia_semana', $num)->first(); @endphp
                                <div class="flex flex-wrap items-center justify-between p-3 border-b last:border-0 hover:bg-gray-50 transition">
                                    <div class="w-40 font-semibold text-gray-700">{{ $nome }}</div>
                                    <div class="flex items-center gap-3">
                                        <input type="time" name="horarios[{{ $num }}][inicio]" value="{{ $h->hora_inicio ?? '08:00' }}" class="rounded border-gray-300 text-sm focus:ring-blue-500">
                                        <span class="text-gray-400">às</span>
                                        <input type="time" name="horarios[{{ $num }}][fim]" value="{{ $h->hora_fim ?? '18:00' }}" class="rounded border-gray-300 text-sm focus:ring-blue-500">
                                    </div>
                                    <div class="flex items-center ml-4">
                                        <input type="checkbox" name="horarios[{{ $num }}][trabalha]" value="1" {{ ($h->trabalha ?? true) ? 'checked' : '' }} class="rounded text-blue-600">
                                        <span class="ml-2 text-xs font-bold text-gray-500 uppercase">Ativo</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
               <div class="mt-8 flex justify-end">
                    <x-primary-button class="bg-slate-800 px-8 py-3">
                        Atualizar Meu Perfil
                    </x-primary-button>
                </div>
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mb-4">
                    <a href="{{ route('dashboard') }}" class="text-pink-600 hover:text-pink-800 font-bold flex items-center">
                        ← Voltar para o Painel Principal
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>