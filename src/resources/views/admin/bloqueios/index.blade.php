<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Bloqueios de Agenda (Folgas e Feriados)') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <div class="mb-4">
                        <a href="{{ route('admin.bloqueios.create') }}" class="btn btn-primary">Novo Bloqueio</a>
                    </div>

                    @if(session('success'))
                        <div class="alert alert-success mb-4">{{ session('success') }}</div>
                    @endif

                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Profissional</th>
                                <th>Início</th>
                                <th>Fim</th>
                                <th>Motivo</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($bloqueios as $bloqueio)
                                <tr>
                                    <td>{{ $bloqueio->profissional->name ?? 'TODOS (Feriado/Geral)' }}</td>
                                    <td>{{ \Carbon\Carbon::parse($bloqueio->data_hora_inicio)->format('d/m/Y H:i') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($bloqueio->data_hora_fim)->format('d/m/Y H:i') }}</td>
                                    <td>{{ $bloqueio->motivo }}</td>
                                    <td>
                                        <form action="{{ route('admin.bloqueios.destroy', $bloqueio->id_bloqueio) }}" method="POST" onsubmit="return confirm('Remover este bloqueio?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">Excluir</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">Nenhum bloqueio registrado.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>