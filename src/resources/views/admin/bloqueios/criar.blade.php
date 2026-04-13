<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Cadastrar Novo Bloqueio') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <form action="{{ route('admin.bloqueios.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Profissional (Opcional)</label>
                            <select name="profissional_id" class="form-control">
                                <option value="">-- TODO O SALÃO (Feriado/Geral) --</option>
                                @foreach($profissionais as $p)
                                    <option value="{{ $p->id }}">{{ $p->name }}</option>
                                @endforeach
                            </select>
                            <small class="text-muted">Deixe vazio para bloquear a agenda de todos os profissionais.</small>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Início do Bloqueio</label>
                                <input type="datetime-local" name="data_hora_inicio" class="form-control @error('data_hora_inicio') is-invalid @enderror" required>
                                @error('data_hora_inicio') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Fim do Bloqueio</label>
                                <input type="datetime-local" name="data_hora_fim" class="form-control @error('data_hora_fim') is-invalid @enderror" required>
                                @error('data_hora_fim') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Motivo / Descrição</label>
                            <input type="text" name="motivo" class="form-control" placeholder="Ex: Feriado de Páscoa, Consulta Médica, Manutenção...">
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('admin.bloqueios.index') }}" class="btn btn-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-success">Salvar Bloqueio</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout> 