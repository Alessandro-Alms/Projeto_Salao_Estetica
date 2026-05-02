<x-app-layout>
<div class="container mx-auto p-4 max-w-3xl">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h2 class="text-2xl font-bold mb-6 text-center">Agendar Novo Atendimento</h2>

        {{-- Indicador de Passos --}}
        <div class="flex justify-between mb-8 border-b pb-4">
            <div id="indicador-1" class="font-bold text-blue-600">1. Serviço</div>
            <div id="indicador-2" class="text-gray-400">2. Profissional</div>
            <div id="indicador-3" class="text-gray-400">3. Data e Hora</div>
            <div id="indicador-4" class="text-gray-400">4. Confirmar</div>
        </div>

        <form id="formAgendamento" action="{{ route('cliente.agendar.salvar') }}" method="POST">
            @csrf

            <div id="passo-1" class="passo">
                <h3 class="text-lg font-semibold mb-4">Qual serviço desejas realizar?</h3>
                <div class="grid gap-3">
                    <select id="servico_id" name="servico_id" class="w-full p-3 border rounded-lg" required>
                        <option value="">Selecione um serviço...</option>
                        @foreach($servicos as $servico)
                            <option value="{{ $servico->id_servico }}">{{ $servico->nome }} - R$ {{ number_format($servico->preco, 2, ',', '.') }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mt-6 flex justify-end">
                    <button type="button" onclick="irParaPasso(2)" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">Próximo ➔</button>
                </div>
            </div>

            <div id="passo-2" class="passo hidden">
                <h3 class="text-lg font-semibold mb-4">Escolhe o profissional</h3>
                <select id="profissional_id" name="profissional_id" class="w-full p-3 border rounded-lg" required>
                    <option value="">Aguardando seleção do serviço...</option>
                </select>
                <div class="mt-6 flex justify-between">
                    <button type="button" onclick="irParaPasso(1)" class="bg-gray-300 px-6 py-2 rounded-lg hover:bg-gray-400">⬅ Voltar</button>
                    <button type="button" onclick="irParaPasso(3)" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">Próximo ➔</button>
                </div>
            </div>

            <div id="passo-3" class="passo hidden">
                <h3 class="text-lg font-semibold mb-4">Escolhe a data e horário</h3>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Data:</label>
                    <input type="date" id="data_agendamento" name="data_agendamento" class="w-full p-3 border rounded-lg" required min="{{ date('Y-m-d') }}">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Horários Disponíveis:</label>
                    <div id="grade_horarios" class="grid grid-cols-4 gap-2">
                        <p class="text-gray-500 col-span-4">Seleciona uma data para ver os horários.</p>
                    </div>
                </div>
                
                <input type="hidden" id="hora_agendamento" name="hora_agendamento" required>

                <div class="mt-6 flex justify-between">
                    <button type="button" onclick="irParaPasso(2)" class="bg-gray-300 px-6 py-2 rounded-lg hover:bg-gray-400">⬅ Voltar</button>
                    <button type="button" onclick="irParaPasso(4)" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">Próximo ➔</button>
                </div>
            </div>

            <div id="passo-4" class="passo hidden">
                <h3 class="text-lg font-semibold mb-4">Confirma os dados do teu agendamento</h3>
                
                <div class="bg-gray-50 p-4 rounded-lg border mb-6">
                    <p><strong>Serviço:</strong> <span id="resumo_servico"></span></p>
                    <p><strong>Profissional:</strong> <span id="resumo_profissional"></span></p>
                    <p><strong>Data:</strong> <span id="resumo_data"></span></p>
                    <p><strong>Hora:</strong> <span id="resumo_hora"></span></p>
                </div>

                <div class="mt-6 flex justify-between">
                    <button type="button" onclick="irParaPasso(3)" class="bg-gray-300 px-6 py-2 rounded-lg hover:bg-gray-400">⬅ Voltar</button>
                    <button type="submit" class="bg-green-600 text-white px-8 py-3 rounded-lg font-bold hover:bg-green-700">✅ Confirmar Agendamento</button>
                </div>
            </div>

        </form>
    </div>
</div>

<style>
    .hidden { display: none; }
    /* Estilo para botões de rádio de horário (escondemos o input e estilizamos a label) */
    .horario-radio { display: none; }
    .horario-label {
        display: block; text-align: center; padding: 8px; border: 1px solid #ccc; 
        border-radius: 8px; cursor: pointer; transition: 0.3s;
    }
    .horario-radio:checked + .horario-label { background-color: #2563eb; color: white; border-color: #2563eb; }
    .horario-label.ocupado { background-color: #f3f4f6; color: #9ca3af; cursor: not-allowed; border-color: #e5e7eb; }
</style>

<script>
    // Rotas injetadas pelo Laravel
    const urlProfissionais = "{{ route('api.profissionais') }}";
    const urlHorarios = "{{ route('api.horarios') }}";

    // Elementos do DOM
    const inputServico = document.getElementById('servico_id');
    const inputProfissional = document.getElementById('profissional_id');
    const inputData = document.getElementById('data_agendamento');
    const inputHora = document.getElementById('hora_agendamento');
    const gradeHorarios = document.getElementById('grade_horarios');

    // Mudar de Passo
    function irParaPasso(passo) {
        // Validações simples antes de avançar
        if (passo === 2 && !inputServico.value) { alert('Por favor, escolhe um serviço!'); return; }
        if (passo === 3 && !inputProfissional.value) { alert('Por favor, escolhe um profissional!'); return; }
        if (passo === 4 && (!inputData.value || !inputHora.value)) { alert('Por favor, escolhe uma data e horário!'); return; }

        // Esconde todos os passos
        document.querySelectorAll('.passo').forEach(el => el.classList.add('hidden'));
        
        // Mostra o passo atual
        document.getElementById(`passo-${passo}`).classList.remove('hidden');

        // Atualiza indicadores visuais
        for(let i=1; i<=4; i++) {
            let ind = document.getElementById(`indicador-${i}`);
            if(i === passo) {
                ind.classList.add('font-bold', 'text-blue-600');
                ind.classList.remove('text-gray-400');
            } else {
                ind.classList.remove('font-bold', 'text-blue-600');
                ind.classList.add('text-gray-400');
            }
        }

        // Se for o passo 4, preenche o resumo
        if (passo === 4) preencherResumo();
    }

    // Ao mudar o Serviço -> Buscar Profissionais
    inputServico.addEventListener('change', function() {
        const servicoId = this.value;
        inputProfissional.innerHTML = '<option value="">Carregando...</option>';
        
        if (!servicoId) return;

        fetch(`${urlProfissionais}?servico_id=${servicoId}`)
            .then(response => response.json())
            .then(data => {
                inputProfissional.innerHTML = '<option value="">Selecione o profissional...</option>';
                data.forEach(prof => {
                    inputProfissional.innerHTML += `<option value="${prof.id}">${prof.name}</option>`;
                });
            });
    });

    // Ao mudar Data ou Profissional -> Buscar Horários
    function carregarHorarios() {
        const data = inputData.value;
        const profissionalId = inputProfissional.value;
        const servicoId = inputServico.value;

        if (!data || !profissionalId || !servicoId) return;

        gradeHorarios.innerHTML = '<p class="text-gray-500 col-span-4">A procurar horários...</p>';

        fetch(`${urlHorarios}?data=${data}&profissional_id=${profissionalId}&servico_id=${servicoId}`)
            .then(response => response.json())
            .then(horarios => {
                gradeHorarios.innerHTML = '';
                
                if (horarios.length === 0) {
                    gradeHorarios.innerHTML = '<p class="text-red-500 col-span-4">Nenhum horário disponível para este dia.</p>';
                    return;
                }

                horarios.forEach(h => {
                    if (h.ocupado) {
                        gradeHorarios.innerHTML += `<div class="horario-label ocupado">${h.hora}</div>`;
                    } else {
                        gradeHorarios.innerHTML += `
                            <div>
                                <input type="radio" name="hora_opcao" id="hora_${h.hora}" value="${h.hora}" class="horario-radio" onchange="selecionarHora('${h.hora}')">
                                <label for="hora_${h.hora}" class="horario-label">${h.hora}</label>
                            </div>
                        `;
                    }
                });
            });
    }

    inputData.addEventListener('change', carregarHorarios);
    inputProfissional.addEventListener('change', () => { if(inputData.value) carregarHorarios(); });

    // Quando clica num horário
    function selecionarHora(hora) {
        inputHora.value = hora;
    }

    // Preencher o Passo 4 (Resumo)
    function preencherResumo() {
        document.getElementById('resumo_servico').innerText = inputServico.options[inputServico.selectedIndex].text;
        document.getElementById('resumo_profissional').innerText = inputProfissional.options[inputProfissional.selectedIndex].text;
        
        // Formatar data de yyyy-mm-dd para dd/mm/yyyy
        let d = inputData.value.split('-');
        document.getElementById('resumo_data').innerText = `${d[2]}/${d[1]}/${d[0]}`;
        document.getElementById('resumo_hora').innerText = inputHora.value;
    }
</script>
</x-app-layout>