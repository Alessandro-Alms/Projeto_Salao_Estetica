<x-app-layout>
<div class="container mx-auto p-4 max-w-3xl">
    <div class="glass-card rounded-2xl shadow-xl overflow-hidden p-6">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 bg-gradient-to-br from-[#7B19E5] to-[#FF2EB6] rounded-xl flex items-center justify-center shadow-md">
                <span class="text-white text-lg">✧</span>
            </div>
            <h2 class="text-2xl font-title text-[#4A00B9]">Agendar Novo Atendimento</h2>
        </div>

        {{-- Indicador de Passos --}}
        <div class="flex justify-between mb-8 border-b border-[#FFD6F4] pb-4">
            <div id="indicador-1" class="font-bold text-[#7B19E5]">✧ Serviço</div>
            <div id="indicador-2" class="text-gray-400">✦ Profissional</div>
            <div id="indicador-3" class="text-gray-400">✧ Data e Hora</div>
            <div id="indicador-4" class="text-gray-400">✦ Confirmar</div>
        </div>

        <form id="formAgendamento" action="{{ route('cliente.agendar.salvar') }}" method="POST">
            @csrf

            <div id="passo-1" class="passo">
                <h3 class="text-lg font-title text-[#4A00B9] mb-4">Qual serviço desejas realizar?</h3>
                <div class="grid gap-3">
                    <select id="servico_id" name="servico_id" class="w-full px-4 py-3 bg-white/50 border border-[#FFD6F4] rounded-lg focus:outline-none focus:border-[#7B19E5] focus:ring-2 focus:ring-[#7B19E5]/20 transition-all" required>
                        <option value="">Selecione um serviço...</option>
                        @foreach($servicos as $servico)
                            <option value="{{ $servico->id_servico }}">{{ $servico->nome }} - R$ {{ number_format($servico->preco, 2, ',', '.') }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mt-6 flex justify-end">
                    <button type="button" onclick="irParaPasso(2)" class="bg-gradient-to-r from-[#7B19E5] to-[#FF2EB6] text-white px-6 py-2 rounded-lg font-medium btn-primary shadow-md hover:shadow-lg transition">
                        Próximo →
                    </button>
                </div>
            </div>

            <div id="passo-2" class="passo hidden">
                <h3 class="text-lg font-title text-[#4A00B9] mb-4">Escolhe o profissional</h3>
                <select id="profissional_id" name="profissional_id" class="w-full px-4 py-3 bg-white/50 border border-[#FFD6F4] rounded-lg focus:outline-none focus:border-[#7B19E5] focus:ring-2 focus:ring-[#7B19E5]/20 transition-all" required>
                    <option value="">Aguardando seleção do serviço...</option>
                </select>
                <div class="mt-6 flex justify-between">
                    <button type="button" onclick="irParaPasso(1)" class="bg-gray-200 px-6 py-2 rounded-lg font-medium hover:bg-gray-300 transition">← Voltar</button>
                    <button type="button" onclick="irParaPasso(3)" class="bg-gradient-to-r from-[#7B19E5] to-[#FF2EB6] text-white px-6 py-2 rounded-lg font-medium btn-primary shadow-md hover:shadow-lg transition">Próximo →</button>
                </div>
            </div>

            <div id="passo-3" class="passo hidden">
                <h3 class="text-lg font-title text-[#4A00B9] mb-4">Escolhe a data e horário</h3>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-[#4A00B9] mb-2">Data:</label>
                    <input type="date" id="data_agendamento" name="data_agendamento" class="w-full px-4 py-3 bg-white/50 border border-[#FFD6F4] rounded-lg focus:outline-none focus:border-[#7B19E5] focus:ring-2 focus:ring-[#7B19E5]/20 transition-all" required min="{{ date('Y-m-d') }}">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-[#4A00B9] mb-2">Horários Disponíveis:</label>
                    <div id="grade_horarios" class="grid grid-cols-4 gap-2">
                        <p class="text-gray-500 col-span-4">Seleciona uma data para ver os horários.</p>
                    </div>
                </div>
                
                <input type="hidden" id="hora_agendamento" name="hora_agendamento" required>

                <div class="mt-6 flex justify-between">
                    <button type="button" onclick="irParaPasso(2)" class="bg-gray-200 px-6 py-2 rounded-lg font-medium hover:bg-gray-300 transition">← Voltar</button>
                    <button type="button" onclick="irParaPasso(4)" class="bg-gradient-to-r from-[#7B19E5] to-[#FF2EB6] text-white px-6 py-2 rounded-lg font-medium btn-primary shadow-md hover:shadow-lg transition">Próximo →</button>
                </div>
            </div>

            <div id="passo-4" class="passo hidden">
                <h3 class="text-lg font-title text-[#4A00B9] mb-4">Confirma os dados do teu agendamento</h3>
                
                <div class="bg-white/50 p-4 rounded-lg border border-[#FFD6F4] mb-6 space-y-2">
                    <p class="text-[#1A002B]"><strong class="text-[#4A00B9]">✧ Serviço:</strong> <span id="resumo_servico"></span></p>
                    <p class="text-[#1A002B]"><strong class="text-[#4A00B9]">✦ Profissional:</strong> <span id="resumo_profissional"></span></p>
                    <p class="text-[#1A002B]"><strong class="text-[#4A00B9]">✧ Data:</strong> <span id="resumo_data"></span></p>
                    <p class="text-[#1A002B]"><strong class="text-[#4A00B9]">✦ Hora:</strong> <span id="resumo_hora"></span></p>
                </div>

                <div class="mt-6 flex justify-between">
                    <button type="button" onclick="irParaPasso(3)" class="bg-gray-200 px-6 py-2 rounded-lg font-medium hover:bg-gray-300 transition">← Voltar</button>
                    <button type="submit" class="bg-gradient-to-r from-[#7B19E5] to-[#FF2EB6] text-white px-8 py-3 rounded-lg font-bold btn-primary shadow-md hover:shadow-lg transition">Confirmar Agendamento</button>
                </div>
            </div>

        </form>
    </div>
</div>

<style>
    .hidden { display: none; }
    
    .horario-radio { display: none; }
    .horario-label {
        display: block; text-align: center; padding: 8px; border: 1px solid #FFD6F4; 
        border-radius: 8px; cursor: pointer; transition: 0.3s;
    }
    .horario-radio:checked + .horario-label { 
        background: linear-gradient(135deg, #7B19E5, #FF2EB6); 
        color: white; 
        border-color: #7B19E5; 
    }
    .horario-label.ocupado { 
        background-color: #f3f4f6; 
        color: #9ca3af; 
        cursor: not-allowed; 
        border-color: #e5e7eb; 
    }
    
    .glass-card {
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        box-shadow: 0 8px 32px rgba(123, 25, 229, 0.1);
    }
    
    .font-title {
        font-family: 'Playfair Display', serif;
        font-weight: 700;
        letter-spacing: -0.02em;
    }
    
    .btn-primary {
        position: relative;
        overflow: hidden;
        transition: all 0.3s ease;
        z-index: 1;
    }
    
    .btn-primary::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.3);
        transform: translate(-50%, -50%);
        transition: width 0.6s ease, height 0.6s ease;
        z-index: -1;
    }
    
    .btn-primary:hover::before {
        width: 300px;
        height: 300px;
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
    }
</style>

<script>
    const urlProfissionais = "{{ route('api.profissionais') }}";
    const urlHorarios = "{{ route('api.horarios') }}";

    const inputServico = document.getElementById('servico_id');
    const inputProfissional = document.getElementById('profissional_id');
    const inputData = document.getElementById('data_agendamento');
    const inputHora = document.getElementById('hora_agendamento');
    const gradeHorarios = document.getElementById('grade_horarios');

    function irParaPasso(passo) {
        if (passo === 2 && !inputServico.value) { alert('Por favor, escolhe um serviço!'); return; }
        if (passo === 3 && !inputProfissional.value) { alert('Por favor, escolhe um profissional!'); return; }
        if (passo === 4 && (!inputData.value || !inputHora.value)) { alert('Por favor, escolhe uma data e horário!'); return; }

        document.querySelectorAll('.passo').forEach(el => el.classList.add('hidden'));
        document.getElementById(`passo-${passo}`).classList.remove('hidden');

        for(let i=1; i<=4; i++) {
            let ind = document.getElementById(`indicador-${i}`);
            if(i === passo) {
                ind.classList.add('font-bold', 'text-[#7B19E5]');
                ind.classList.remove('text-gray-400');
            } else {
                ind.classList.remove('font-bold', 'text-[#7B19E5]');
                ind.classList.add('text-gray-400');
            }
        }

        if (passo === 4) preencherResumo();
    }

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

    function selecionarHora(hora) {
        inputHora.value = hora;
    }

    function preencherResumo() {
        document.getElementById('resumo_servico').innerText = inputServico.options[inputServico.selectedIndex].text;
        document.getElementById('resumo_profissional').innerText = inputProfissional.options[inputProfissional.selectedIndex].text;
        
        let d = inputData.value.split('-');
        document.getElementById('resumo_data').innerText = `${d[2]}/${d[1]}/${d[0]}`;
        document.getElementById('resumo_hora').innerText = inputHora.value;
    }
</script>
</x-app-layout>
