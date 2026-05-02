<x-app-layout>
<div class="container mx-auto p-4 max-w-6xl">
    <div class="bg-white rounded-lg shadow-lg p-8">
        <h2 class="text-3xl font-bold mb-8 text-center">📅 Agendar Novo Atendimento</h2>

        {{-- Indicador de Passos --}}
        <div class="flex justify-between mb-8 border-b pb-4 flex-wrap gap-2">
            <div id="indicador-1" class="font-bold text-blue-600 text-sm md:text-base">1️⃣ Serviço</div>
            <div id="indicador-2" class="text-gray-400 text-sm md:text-base">2️⃣ Calendário</div>
            <div id="indicador-3" class="text-gray-400 text-sm md:text-base">3️⃣ Horário</div>
            <div id="indicador-4" class="text-gray-400 text-sm md:text-base">4️⃣ Profissional</div>
            <div id="indicador-5" class="text-gray-400 text-sm md:text-base">5️⃣ Confirmar</div>
        </div>

        <form id="formAgendamento" action="{{ route('cliente.agendar.salvar') }}" method="POST">
            @csrf
            
            {{-- Campos ocultos para armazenar dados --}}
            <input type="hidden" id="servicos_ids" name="servicos_ids">
            <input type="hidden" id="profissional_id" name="profissional_id">

            {{-- PASSO 1: ESCOLHER SERVIÇO(S) --}}
            <div id="passo-1" class="passo">
                <h3 class="text-2xl font-semibold mb-8">Quais serviços desejas realizar? (Podes escolher mais de um)</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($servicos as $servico)
                        <div class="flex items-start p-5 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-blue-400 hover:bg-blue-50 transition servico-card" 
                             id="servico-card-{{ $servico->id_servico }}"
                             data-id="{{ $servico->id_servico }}"
                             data-nome="{{ e($servico->nome) }}"
                             data-duracao="{{ $servico->duracao }}">
                            <input type="checkbox" id="checkbox-servico-{{ $servico->id_servico }}" class="mt-1 mr-3" onclick="event.stopPropagation();">
                            <div class="w-full">
                                <p class="font-bold text-lg text-gray-800">{{ $servico->nome }}</p>
                                <p class="text-sm text-gray-600 mt-2">R$ {{ number_format($servico->preco, 2, ',', '.') }}</p>
                                <p class="text-xs text-gray-500 mt-2">⏱️ Duração: {{ $servico->duracao }} min</p>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Resumo dos Serviços Selecionados --}}
                <div id="resumo-servicos-selecionados" class="mt-8 p-4 bg-blue-50 border-2 border-blue-200 rounded-lg hidden">
                    <h4 class="font-semibold text-blue-800 mb-3">📋 Serviços Selecionados:</h4>
                    <ul id="lista-servicos-selecionados" class="space-y-2"></ul>
                    <div class="mt-4 pt-4 border-t-2 border-blue-200">
                        <p class="font-bold text-blue-900">⏱️ Tempo Total: <span id="tempo-total">0</span> minutos</p>
                    </div>
                </div>
                
                <div class="mt-12 flex justify-end">
                    <button type="button" onclick="irParaPasso(2)" class="bg-blue-600 text-white px-8 py-3 rounded-lg font-bold hover:bg-blue-700 transition">Próximo ➔</button>
                </div>
            </div>

            {{-- PASSO 2: CALENDÁRIO --}}
            <div id="passo-2" class="passo hidden">
                <h3 class="text-2xl font-semibold mb-8">Seleciona uma data 📆</h3>
                
                <div class="bg-gray-50 p-8 rounded-lg border-2 border-gray-200">
                    <div id="calendario" class="calendar-container"></div>
                </div>

                <div id="info-data-selecionada" class="mt-6 p-4 bg-green-50 border-2 border-green-200 rounded-lg hidden">
                    <p class="text-green-800 font-semibold">✓ Data selecionada: <span id="data-selecionada-texto" class="text-green-900 font-bold"></span></p>
                </div>
                
                <div class="mt-12 flex justify-between">
                    <button type="button" onclick="irParaPasso(1)" class="bg-gray-300 px-8 py-3 rounded-lg hover:bg-gray-400 font-bold transition">⬅ Voltar</button>
                    <button type="button" onclick="irParaPasso(3)" class="bg-blue-600 text-white px-8 py-3 rounded-lg font-bold hover:bg-blue-700 transition">Próximo ➔</button>
                </div>
            </div>

            {{-- PASSO 3: HORÁRIO --}}
            <div id="passo-3" class="passo hidden">
                <h3 class="text-2xl font-semibold mb-8">Seleciona um horário 🕐</h3>
                
                <div id="grade_horarios" class="grid grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
                    <p class="text-gray-500 col-span-full text-center py-8">Carregando horários disponíveis...</p>
                </div>

                <input type="hidden" id="data_agendamento" name="data_agendamento">
                <input type="hidden" id="hora_agendamento" name="hora_agendamento">
                
                <div id="info-hora-selecionada" class="mt-6 p-4 bg-green-50 border-2 border-green-200 rounded-lg hidden">
                    <p class="text-green-800 font-semibold">✓ Horário selecionado: <span id="hora-selecionada-texto" class="text-green-900 font-bold"></span></p>
                </div>
                
                <div class="mt-12 flex justify-between">
                    <button type="button" onclick="irParaPasso(2)" class="bg-gray-300 px-8 py-3 rounded-lg hover:bg-gray-400 font-bold transition">⬅ Voltar</button>
                    <button type="button" onclick="irParaPasso(4)" class="bg-blue-600 text-white px-8 py-3 rounded-lg font-bold hover:bg-blue-700 transition">Próximo ➔</button>
                </div>
            </div>

            {{-- PASSO 4: PROFISSIONAL --}}
            <div id="passo-4" class="passo hidden">
                <h3 class="text-2xl font-semibold mb-8">Seleciona o profissional 👤</h3>
                
                <div id="grade_profissionais" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <p class="text-gray-500 col-span-full text-center py-8">Carregando profissionais disponíveis...</p>
                </div>

                <div id="info-profissional-selecionado" class="mt-6 p-4 bg-green-50 border-2 border-green-200 rounded-lg hidden">
                    <p class="text-green-800 font-semibold">✓ Profissional selecionado: <span id="profissional-selecionado-texto" class="text-green-900 font-bold"></span></p>
                </div>
                
                <div class="mt-12 flex justify-between">
                    <button type="button" onclick="irParaPasso(3)" class="bg-gray-300 px-8 py-3 rounded-lg hover:bg-gray-400 font-bold transition">⬅ Voltar</button>
                    <button type="button" onclick="irParaPasso(5)" class="bg-blue-600 text-white px-8 py-3 rounded-lg font-bold hover:bg-blue-700 transition">Próximo ➔</button>
                </div>
            </div>

            {{-- PASSO 5: CONFIRMAR --}}
            <div id="passo-5" class="passo hidden">
                <h3 class="text-2xl font-semibold mb-8">Confirma os dados do teu agendamento ✓</h3>
                
                <div class="bg-gradient-to-r from-blue-50 to-blue-100 p-8 rounded-lg border-2 border-blue-300 space-y-4">
                    <div class="flex items-center justify-between pb-4 border-b-2 border-blue-200">
                        <span class="text-gray-700 font-semibold">📋 Serviço:</span>
                        <span id="resumo_servico" class="font-bold text-blue-800 text-lg"></span>
                    </div>
                    <div class="flex items-center justify-between pb-4 border-b-2 border-blue-200">
                        <span class="text-gray-700 font-semibold">👤 Profissional:</span>
                        <span id="resumo_profissional" class="font-bold text-blue-800 text-lg"></span>
                    </div>
                    <div class="flex items-center justify-between pb-4 border-b-2 border-blue-200">
                        <span class="text-gray-700 font-semibold">📅 Data:</span>
                        <span id="resumo_data" class="font-bold text-blue-800 text-lg"></span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-700 font-semibold">🕐 Hora:</span>
                        <span id="resumo_hora" class="font-bold text-blue-800 text-lg"></span>
                    </div>
                </div>

                <div class="mt-12 flex justify-between">
                    <button type="button" onclick="irParaPasso(4)" class="bg-gray-300 px-8 py-3 rounded-lg hover:bg-gray-400 font-bold transition">⬅ Voltar</button>
                    <button type="submit" class="bg-green-600 text-white px-12 py-3 rounded-lg font-bold hover:bg-green-700 transition text-lg">✅ Confirmar Agendamento</button>
                </div>
            </div>

        </form>
    </div>
</div>

<style>
    .hidden { display: none; }
    
    /* Calendário */
    .calendar-container {
        max-width: 100%;
    }
    
    .calendar-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        font-size: 20px;
        font-weight: bold;
    }
    
    .calendar-header button {
        background: #3b82f6;
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 8px;
        cursor: pointer;
        font-size: 18px;
        transition: background 0.3s;
    }
    
    .calendar-header button:hover {
        background: #2563eb;
    }
    
    .calendar-days {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 10px;
        margin-bottom: 20px;
    }
    
    .calendar-day-name {
        text-align: center;
        font-weight: bold;
        color: #1f2937;
        padding: 12px;
        font-size: 14px;
        background: #f3f4f6;
        border-radius: 8px;
    }
    
    .calendar-date {
        aspect-ratio: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        cursor: pointer;
        font-weight: 600;
        transition: all 0.3s;
        background: white;
        font-size: 16px;
        min-height: 50px;
    }
    
    .calendar-date:hover:not(.outro-mes):not(.indisponivel) {
        border-color: #3b82f6;
        background: #eff6ff;
        transform: scale(1.05);
    }
    
    .calendar-date.outro-mes {
        color: #d1d5db;
        cursor: not-allowed;
        background: #f9fafb;
    }
    
    .calendar-date.selecionado {
        background: #3b82f6;
        color: white;
        border-color: #3b82f6;
        box-shadow: 0 6px 20px rgba(59, 130, 246, 0.5);
        font-weight: 700;
    }
    
    .calendar-date.indisponivel {
        color: #9ca3af;
        background: #f3f4f6;
        cursor: not-allowed;
        border-color: #e5e7eb;
    }
    
    /* Horários */
    .horario-option {
        aspect-ratio: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px solid #e5e7eb;
        border-radius: 10px;
        cursor: pointer;
        font-weight: 600;
        transition: all 0.3s;
        background: white;
        min-height: 50px;
    }
    
    .horario-option:hover:not(.ocupado) {
        border-color: #3b82f6;
        background: #eff6ff;
        transform: scale(1.05);
    }
    
    .horario-option.selecionado {
        background: #3b82f6;
        color: white;
        border-color: #3b82f6;
        box-shadow: 0 6px 20px rgba(59, 130, 246, 0.5);
    }
    
    .horario-option.ocupado {
        color: #9ca3af;
        background: #f3f4f6;
        cursor: not-allowed;
        border-color: #e5e7eb;
    }
    
    /* Profissional */
    .profissional-card {
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        padding: 24px;
        cursor: pointer;
        transition: all 0.3s;
        background: white;
    }
    
    .profissional-card:hover {
        border-color: #3b82f6;
        background: #eff6ff;
        box-shadow: 0 10px 30px rgba(59, 130, 246, 0.2);
        transform: translateY(-4px);
    }
    
    .profissional-card.selecionado {
        background: #dbeafe;
        border-color: #3b82f6;
        box-shadow: 0 10px 30px rgba(59, 130, 246, 0.4);
        transform: scale(1.02);
    }
    
    .profissional-card h4 {
        font-size: 18px;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 8px;
    }
    
    .profissional-card p {
        font-size: 14px;
        color: #6b7280;
        margin-bottom: 6px;
    }
</style>

<script>
    console.log('Script carregado!');
    
    // Constantes
    const urlProfissionais = "{{ route('api.profissionais') }}";
    const urlHorarios = "{{ route('api.horarios') }}";

    // Estado Global
    let estadoAgendamento = {
        servicosIds: [],
        servicosNomes: [],
        servicosDuracao: {},
        duraoTotal: 0,
        dataSelecionada: null,
        horaSelecionada: null,
        profissionalId: null,
        profissionalNome: null
    };

    let mesAtual = new Date().getMonth();
    let anoAtual = new Date().getFullYear();

    // ==================== PASSO 1: SERVIÇO(S) ====================
    function toggleServico(servicoId, servicoNome, duracao) {
        const checkbox = document.getElementById(`checkbox-servico-${servicoId}`);
        const card = document.getElementById(`servico-card-${servicoId}`);
        
        if (estadoAgendamento.servicosIds.includes(servicoId)) {
            // Remover serviço
            estadoAgendamento.servicosIds = estadoAgendamento.servicosIds.filter(id => id !== servicoId);
            estadoAgendamento.servicosNomes = estadoAgendamento.servicosNomes.filter(nome => nome !== servicoNome);
            delete estadoAgendamento.servicosDuracao[servicoId];
            checkbox.checked = false;
            card.classList.remove('border-blue-400', 'border-4', 'bg-blue-100');
        } else {
            // Adicionar serviço
            estadoAgendamento.servicosIds.push(servicoId);
            estadoAgendamento.servicosNomes.push(servicoNome);
            estadoAgendamento.servicosDuracao[servicoId] = duracao;
            checkbox.checked = true;
            card.classList.add('border-blue-400', 'border-4', 'bg-blue-100');
        }
        
        atualizarResumoServicos();
    }

    function atualizarResumoServicos() {
        const resumoDiv = document.getElementById('resumo-servicos-selecionados');
        const listaUl = document.getElementById('lista-servicos-selecionados');
        
        if (estadoAgendamento.servicosIds.length === 0) {
            resumoDiv.classList.add('hidden');
            document.getElementById('servicos_ids').value = '';
            return;
        }
        
        // Mostrar resumo
        resumoDiv.classList.remove('hidden');
        listaUl.innerHTML = '';
        
        let tempoTotal = 0;
        estadoAgendamento.servicosIds.forEach((id, index) => {
            const nome = estadoAgendamento.servicosNomes[index];
            const duracao = estadoAgendamento.servicosDuracao[id];
            tempoTotal += duracao;
            
            const li = document.createElement('li');
            li.innerHTML = `<span class="text-blue-900">✓ ${nome}</span> <span class="text-gray-600">(${duracao} min)</span>`;
            listaUl.appendChild(li);
        });
        
        document.getElementById('tempo-total').innerText = tempoTotal;
        estadoAgendamento.duraoTotal = tempoTotal;
        
        // Atualizar campo oculto
        document.getElementById('servicos_ids').value = estadoAgendamento.servicosIds.join(',');
    }

    // ==================== PASSO 2: CALENDÁRIO ====================
    function construirCalendario() {
        const container = document.getElementById('calendario');
        container.innerHTML = '';
        
        const header = document.createElement('div');
        header.className = 'calendar-header';
        header.innerHTML = `
            <button type="button" onclick="mesAnterior()">◀ Anterior</button>
            <span id="mes-ano-actual" style="flex: 1; text-align: center;"></span>
            <button type="button" onclick="proxMes()">Próximo ▶</button>
        `;
        container.appendChild(header);
        
        atualizarCalendario(mesAtual, anoAtual);
    }

    function mesAnterior() {
        mesAtual--;
        if (mesAtual < 0) { mesAtual = 11; anoAtual--; }
        atualizarCalendario(mesAtual, anoAtual);
    }

    function proxMes() {
        mesAtual++;
        if (mesAtual > 11) { mesAtual = 0; anoAtual++; }
        atualizarCalendario(mesAtual, anoAtual);
    }

    function atualizarCalendario(mes, ano) {
        const container = document.getElementById('calendario');
        const nomeMes = ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'];
        
        document.getElementById('mes-ano-actual').innerText = `${nomeMes[mes]} ${ano}`;
        
        let gridAnterior = container.querySelector('.calendar-days');
        if (gridAnterior) gridAnterior.remove();
        
        const grid = document.createElement('div');
        grid.className = 'calendar-days';
        
        // Dias da semana
        const diasSemana = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sab'];
        diasSemana.forEach(dia => {
            const dayName = document.createElement('div');
            dayName.className = 'calendar-day-name';
            dayName.innerText = dia;
            grid.appendChild(dayName);
        });
        
        // Primeira data do mês
        const primeiroD = new Date(ano, mes, 1);
        const ultimoD = new Date(ano, mes + 1, 0);
        const diaInicio = primeiroD.getDay();
        const totalDias = ultimoD.getDate();
        
        // Dias do mês anterior
        const ultimoDoMesAnterior = new Date(ano, mes, 0).getDate();
        for (let i = diaInicio - 1; i >= 0; i--) {
            const dateDiv = document.createElement('div');
            dateDiv.className = 'calendar-date outro-mes';
            dateDiv.innerText = ultimoDoMesAnterior - i;
            grid.appendChild(dateDiv);
        }
        
        // Dias do mês atual
        const hoje = new Date();
        const hojeDate = new Date(hoje.getFullYear(), hoje.getMonth(), hoje.getDate());
        
        for (let dia = 1; dia <= totalDias; dia++) {
            const dateDiv = document.createElement('div');
            dateDiv.className = 'calendar-date';
            dateDiv.innerText = dia;
            
            const dataObj = new Date(ano, mes, dia);
            
            // Desabilitar datas passadas
            if (dataObj < hojeDate) {
                dateDiv.classList.add('indisponivel');
            } else {
                dateDiv.onclick = () => selecionarData(dataObj);
            }
            
            // Marcar data selecionada
            if (estadoAgendamento.dataSelecionada && 
                dataObj.toDateString() === estadoAgendamento.dataSelecionada.toDateString()) {
                dateDiv.classList.add('selecionado');
            }
            
            grid.appendChild(dateDiv);
        }
        
        container.appendChild(grid);
    }

    function selecionarData(data) {
        estadoAgendamento.dataSelecionada = data;
        const dataTxt = data.toLocaleDateString('pt-PT', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
        
        document.getElementById('data-selecionada-texto').innerText = dataTxt;
        document.getElementById('info-data-selecionada').classList.remove('hidden');
        
        atualizarCalendario(mesAtual, anoAtual);
    }

    // ==================== PASSO 3: HORÁRIOS ====================
    function carregarHorarios() {
        if (estadoAgendamento.servicosIds.length === 0 || !estadoAgendamento.dataSelecionada) return;
        
        const data = estadoAgendamento.dataSelecionada.toISOString().split('T')[0];
        const inputData = document.getElementById('data_agendamento');
        inputData.value = data;
        
        const gradeHorarios = document.getElementById('grade_horarios');
        gradeHorarios.innerHTML = '<p class="text-gray-500 col-span-full text-center">Carregando horários...</p>';
        
        // Passar o primeiro serviço e a duração total
        const servicoId = estadoAgendamento.servicosIds[0];
        const duracao = estadoAgendamento.duraoTotal;
        
        fetch(`${urlHorarios}?data=${data}&servico_id=${servicoId}&duracao=${duracao}`)
            .then(r => r.json())
            .then(horarios => {
                gradeHorarios.innerHTML = '';
                
                if (!horarios || horarios.length === 0) {
                    gradeHorarios.innerHTML = '<p class="text-red-500 col-span-full text-center">Nenhum horário disponível para este dia.</p>';
                    return;
                }
                
                horarios.forEach(h => {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = `horario-option ${h.ocupado ? 'ocupado' : ''}`;
                    btn.innerText = h.hora;
                    
                    if (!h.ocupado) {
                        btn.onclick = () => selecionarHora(h.hora);
                    }
                    
                    gradeHorarios.appendChild(btn);
                });
            });
    }

    function selecionarHora(hora) {
        estadoAgendamento.horaSelecionada = hora;
        document.getElementById('hora_agendamento').value = hora;
        
        document.getElementById('hora-selecionada-texto').innerText = hora;
        document.getElementById('info-hora-selecionada').classList.remove('hidden');
    }

    // ==================== PASSO 4: PROFISSIONAIS ====================
    function carregarProfissionaisDisponiveis() {
        if (estadoAgendamento.servicosIds.length === 0 || !estadoAgendamento.dataSelecionada || !estadoAgendamento.horaSelecionada) return;
        
        const gradeProfissionais = document.getElementById('grade_profissionais');
        gradeProfissionais.innerHTML = '<p class="text-gray-500 col-span-full text-center">Carregando profissionais...</p>';
        
        // Formatar data e hora para enviar ao backend
        const data = estadoAgendamento.dataSelecionada.toISOString().split('T')[0];
        const hora = estadoAgendamento.horaSelecionada;
        const dataHora = `${data} ${hora}`;
        const servicosIds = estadoAgendamento.servicosIds.join(',');
        const duracao = estadoAgendamento.duraoTotal;
        
        fetch(`${urlProfissionais}?servicos_ids=${servicosIds}&data_hora=${encodeURIComponent(dataHora)}&duracao=${duracao}`)
            .then(r => r.json())
            .then(profissionais => {
                gradeProfissionais.innerHTML = '';
                
                if (!profissionais || profissionais.length === 0) {
                    gradeProfissionais.innerHTML = '<p class="text-red-500 col-span-full text-center">Nenhum profissional disponível neste horário.</p>';
                    return;
                }
                
                profissionais.forEach(prof => {
                    const card = document.createElement('div');
                    card.className = 'profissional-card';
                    card.innerHTML = `
                        <h4>👤 ${prof.name}</h4>
                        <p>✓ Disponível para os serviços</p>
                    `;
                    card.onclick = () => selecionarProfissional(prof.id, prof.name, card);
                    gradeProfissionais.appendChild(card);
                });
            });
    }

    function selecionarProfissional(profId, profNome, card) {
        estadoAgendamento.profissionalId = profId;
        estadoAgendamento.profissionalNome = profNome;
        document.getElementById('profissional_id').value = profId;
        
        document.querySelectorAll('.profissional-card').forEach(c => c.classList.remove('selecionado'));
        card.classList.add('selecionado');
        
        document.getElementById('profissional-selecionado-texto').innerText = profNome;
        document.getElementById('info-profissional-selecionado').classList.remove('hidden');
    }

    // ==================== NAVEGAÇÃO ====================
    function irParaPasso(passo) {
        if (passo === 2 && estadoAgendamento.servicosIds.length === 0) {
            alert('Por favor, escolhe pelo menos um serviço!');
            return;
        }
        if (passo === 3 && !estadoAgendamento.dataSelecionada) {
            alert('Por favor, seleciona uma data!');
            return;
        }
        if (passo === 4 && !estadoAgendamento.horaSelecionada) {
            alert('Por favor, seleciona um horário!');
            return;
        }
        if (passo === 5 && !estadoAgendamento.profissionalId) {
            alert('Por favor, seleciona um profissional!');
            return;
        }
        
        document.querySelectorAll('.passo').forEach(el => el.classList.add('hidden'));
        document.getElementById(`passo-${passo}`).classList.remove('hidden');
        
        for (let i = 1; i <= 5; i++) {
            const ind = document.getElementById(`indicador-${i}`);
            if (i === passo) {
                ind.classList.add('font-bold', 'text-blue-600');
                ind.classList.remove('text-gray-400', 'text-green-600');
            } else if (i < passo) {
                ind.classList.add('text-green-600', 'font-semibold');
                ind.classList.remove('font-bold', 'text-blue-600', 'text-gray-400');
            } else {
                ind.classList.remove('font-bold', 'text-blue-600', 'text-green-600', 'font-semibold');
                ind.classList.add('text-gray-400');
            }
        }
        
        if (passo === 2 && !document.querySelector('.calendar-days')) {
            construirCalendario();
        }
        
        if (passo === 3) {
            carregarHorarios();
        }
        
        if (passo === 4) {
            carregarProfissionaisDisponiveis();
        }
        
        if (passo === 5) {
            preencherResumo();
        }
    }

    function preencherResumo() {
        const servicos = estadoAgendamento.servicosNomes.join(', ');
        document.getElementById('resumo_servico').innerText = servicos;
        document.getElementById('resumo_profissional').innerText = estadoAgendamento.profissionalNome;
        
        const dataTxt = estadoAgendamento.dataSelecionada.toLocaleDateString('pt-PT', { weekday: 'short', year: 'numeric', month: 'long', day: 'numeric' });
        document.getElementById('resumo_data').innerText = dataTxt;
        document.getElementById('resumo_hora').innerText = estadoAgendamento.horaSelecionada;
    }

    // Inicialização
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOMContentLoaded disparado');
        // Event listener para cards de serviços
        document.querySelectorAll('.servico-card').forEach(card => {
            card.addEventListener('click', function() {
                const servicoId = parseInt(this.dataset.id);
                const servicoNome = this.dataset.nome;
                const servicoDuracao = parseInt(this.dataset.duracao);
                console.log('Card clicado:', servicoId, servicoNome, servicoDuracao);
                toggleServico(servicoId, servicoNome, servicoDuracao);
            });
        });
        
        irParaPasso(1);
    });
</script>
</x-app-layout>
