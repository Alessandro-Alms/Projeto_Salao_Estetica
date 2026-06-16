<x-app-layout>
<div class="py-6 sm:py-12 relative">
    <div class="fixed inset-0 -z-10 overflow-hidden">
        <div class="absolute top-0 -left-20 w-[600px] h-[600px] bg-[#7B19E5]/20 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 -right-20 w-[700px] h-[700px] bg-[#FF2EB6]/20 rounded-full blur-3xl"></div>
        <div class="absolute top-1/3 left-1/3 w-[400px] h-[400px] bg-[#A955D3]/15 rounded-full blur-3xl"></div>
        <div class="absolute inset-0 bg-gradient-to-br from-[#7B19E5]/5 via-white/30 to-[#FF2EB6]/5"></div>
    </div>

    <div class="max-w-6xl mx-auto px-3 sm:px-4">
        <div class="glass-card rounded-2xl sm:rounded-3xl shadow-xl overflow-hidden">
            <div class="p-4 sm:p-8 bg-white/70 backdrop-blur-sm">
                <div class="flex items-start sm:items-center gap-3 mb-6 sm:mb-8 pb-4 border-b border-[#FFD6F4]">
                    <div class="w-12 h-12 shrink-0 bg-gradient-to-br from-[#7B19E5] to-[#FF2EB6] rounded-2xl flex items-center justify-center shadow-md">
                        <span class="text-white text-xl">✧</span>
                    </div>
                    <div>
                        <h2 class="text-2xl font-title text-[#4A00B9]">Disponibilidade dos Profissionais</h2>
                        <p class="text-sm text-gray-500 mt-0.5">Escolha a data para ver o dia todo ou selecione um horário para filtrar.</p>
                    </div>
                </div>

                <div class="flex justify-start sm:justify-between mb-8 sm:mb-10 flex-wrap gap-3">
                    <div id="indicador-1" class="flex items-center gap-2 px-4 py-2 rounded-full bg-gradient-to-r from-[#7B19E5] to-[#A855F7] text-white shadow-md">
                        <span class="text-sm">✧</span>
                        <span class="text-sm font-medium">Data</span>
                    </div>
                    <div id="indicador-2" class="flex items-center gap-2 px-4 py-2 rounded-full bg-white/50 border border-[#FFD6F4] text-[#4A00B9]">
                        <span class="text-sm">✦</span>
                        <span class="text-sm font-medium">Horário</span>
                    </div>
                    <div id="indicador-3" class="flex items-center gap-2 px-4 py-2 rounded-full bg-white/50 border border-[#FFD6F4] text-[#4A00B9]">
                        <span class="text-sm">✧</span>
                        <span class="text-sm font-medium">Profissionais</span>
                    </div>
                </div>

                <div id="passo-1" class="passo">
                    <div class="bg-white/40 rounded-xl p-4 sm:p-6 border border-[#FFD6F4]">
                        <div id="calendario" class="calendar-container"></div>
                    </div>

                    <div id="info-data-selecionada" class="mt-4 p-3 bg-green-50/80 rounded-xl border border-green-200 hidden">
                        <p class="text-green-700">✓ Data selecionada: <span id="data-selecionada-texto" class="font-semibold"></span></p>
                    </div>

                    <div class="mt-8 flex justify-end">
                        <button type="button" onclick="irParaPasso(2)" class="btn-primary w-full sm:w-auto bg-gradient-to-r from-[#7B19E5] to-[#FF2EB6] text-white px-8 py-3 rounded-xl font-medium shadow-md hover:shadow-lg transition-all">Continuar →</button>
                    </div>
                </div>

                <div id="passo-2" class="passo hidden">
                    <div id="grade_horarios" class="grid grid-cols-4 md:grid-cols-6 lg:grid-cols-8 gap-3">
                        <p class="text-gray-500 col-span-full text-center py-8">Escolha uma data para carregar os horários...</p>
                    </div>

                    <div id="aviso-horario-especial" class="mt-4 p-3 bg-yellow-50/80 rounded-xl border border-yellow-200 hidden">
                        <p class="text-yellow-700 text-sm">⚠️ Este horário possui acréscimo especial</p>
                    </div>

                    <div id="info-hora-selecionada" class="mt-4 p-3 bg-green-50/80 rounded-xl border border-green-200 hidden">
                        <p class="text-green-700">✓ Horário selecionado: <span id="hora-selecionada-texto" class="font-semibold"></span></p>
                    </div>

                    <div id="resumo-consulta" class="hidden mt-4 p-4 rounded-xl bg-[#7B19E5]/5 border border-[#FFD6F4] text-sm text-[#1A002B]"></div>

                    <div class="mt-8 flex flex-col-reverse sm:flex-row sm:justify-between gap-3">
                        <button type="button" onclick="irParaPasso(1)" class="w-full sm:w-auto bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2 rounded-xl font-medium transition-all">← Voltar</button>
                        <button type="button" onclick="irParaPasso(3)" class="btn-primary w-full sm:w-auto bg-gradient-to-r from-[#7B19E5] to-[#FF2EB6] text-white px-8 py-2 rounded-xl font-medium shadow-md hover:shadow-lg transition-all">Ver disponibilidade →</button>
                    </div>
                </div>

                <div id="passo-3" class="passo hidden">
                    <div class="bg-gradient-to-r from-[#7B19E5]/5 to-[#FF2EB6]/5 rounded-2xl p-5 mb-6 border border-[#FFD6F4]">
                        <div class="flex items-start gap-3">
                            <span class="text-2xl">✧</span>
                            <div>
                                <p id="titulo-lista-profissionais" class="text-[#1A002B] font-medium">Profissionais disponíveis para esta consulta</p>
                                <p id="subtitulo-lista-profissionais" class="text-sm text-gray-500 mt-1">Use a busca para filtrar a lista se necessário.</p>
                            </div>
                        </div>
                    </div>

                    <div id="filtro-profissionais" data-local-filter class="hidden mb-6 bg-white/40 rounded-xl p-4 border border-[#FFD6F4]">
                        <label for="busca-profissional" class="block text-sm font-medium text-[#4A00B9] mb-2">Pesquisar profissional</label>
                        <input type="text" id="busca-profissional" data-filter-search placeholder="Digite o nome do profissional..." class="w-full px-4 py-3 bg-white/60 border border-[#FFD6F4] rounded-lg focus:outline-none focus:border-[#7B19E5] focus:ring-2 focus:ring-[#7B19E5]/20 transition-all">
                    </div>

                    <div id="estado-disponibilidade" class="text-center text-gray-500 py-10">
                        Selecione uma data para consultar os profissionais.
                    </div>

                    <div id="lista-profissionais" class="hidden mt-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4"></div>

                    <div id="sem-resultados-filtro" class="hidden mt-8 text-center text-gray-500 py-10">
                        Nenhum profissional encontrado nessa pesquisa.
                    </div>

                    <div class="mt-8 flex flex-col-reverse sm:flex-row sm:justify-between gap-3">
                        <button type="button" onclick="irParaPasso(2)" class="w-full sm:w-auto bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2 rounded-xl font-medium transition-all">← Voltar</button>
                        <a href="{{ route('dashboard') }}" class="btn-primary w-full sm:w-auto bg-gradient-to-r from-[#7B19E5] to-[#FF2EB6] text-white px-8 py-2 rounded-xl font-medium shadow-md hover:shadow-lg transition-all inline-flex items-center justify-center">Voltar ao painel</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700;800&family=Playfair+Display:wght@700&family=Space+Grotesk:wght@300;400;500;600;700&display=swap');

    .font-title { font-family: 'Playfair Display', serif; font-weight: 700; letter-spacing: -0.02em; }
    .glass-card { background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.3); box-shadow: 0 8px 32px rgba(123, 25, 229, 0.1); }
    .btn-primary { position: relative; overflow: hidden; transition: all 0.3s ease; z-index: 1; }
    .btn-primary::before { content: ''; position: absolute; top: 50%; left: 50%; width: 0; height: 0; border-radius: 50%; background: rgba(255, 255, 255, 0.3); transform: translate(-50%, -50%); transition: width 0.6s ease, height 0.6s ease; z-index: -1; }
    .btn-primary:hover::before { width: 300px; height: 300px; }
    .btn-primary:hover { transform: translateY(-2px); }

    .calendar-container { width: 100%; max-width: 100%; }
    .calendar-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
    .calendar-header button { background: linear-gradient(135deg, #7B19E5, #FF2EB6); color: white; border: none; padding: 6px 14px; border-radius: 10px; cursor: pointer; font-size: 14px; transition: opacity 0.3s; }
    .calendar-header button:hover { opacity: 0.9; }
    .calendar-days { display: grid; grid-template-columns: repeat(7, minmax(0, 1fr)); gap: 8px; margin-bottom: 15px; }
    .calendar-day-name { text-align: center; font-weight: bold; color: #4A00B9; padding: 10px; font-size: 12px; background: rgba(123, 25, 229, 0.1); border-radius: 10px; min-width: 0; }
    .calendar-date { aspect-ratio: 1; display: flex; align-items: center; justify-content: center; border: 2px solid #FFD6F4; border-radius: 12px; cursor: pointer; font-weight: 500; transition: all 0.3s; background: white; font-size: 14px; min-width: 0; }
    .calendar-date:hover:not(.outro-mes):not(.indisponivel) { border-color: #7B19E5; background: rgba(123, 25, 229, 0.1); transform: scale(1.05); }
    .calendar-date.outro-mes { color: #d1d5db; cursor: not-allowed; background: #f9fafb; }
    .calendar-date.selecionado { background: linear-gradient(135deg, #7B19E5, #FF2EB6); color: white; border-color: #7B19E5; box-shadow: 0 4px 12px rgba(123, 25, 229, 0.3); }
    .calendar-date.indisponivel { color: #9ca3af; background: #f3f4f6; cursor: not-allowed; border-color: #e5e7eb; }

    @media (max-width: 640px) {
        .calendar-header { margin-bottom: 12px; }
        .calendar-header button { padding: 4px 10px; font-size: 12px; border-radius: 8px; }
        .calendar-days { gap: 4px; margin-bottom: 10px; }
        .calendar-day-name { padding: 6px; font-size: 10px; border-radius: 8px; }
        .calendar-date { font-size: 12px; border-width: 1px; border-radius: 10px; }
        .calendar-date:hover:not(.outro-mes):not(.indisponivel) { transform: none; }
    }

    .horario-option { padding: 10px; border: 2px solid #FFD6F4; border-radius: 10px; cursor: pointer; text-align: center; font-weight: 500; transition: all 0.3s; background: white; font-size: 14px; }
    .horario-option:hover:not(.ocupado) { border-color: #7B19E5; background: rgba(123, 25, 229, 0.1); transform: scale(1.05); }
    .horario-option.selecionado { background: linear-gradient(135deg, #7B19E5, #FF2EB6); color: white; border-color: #7B19E5; }
    .horario-option.ocupado { color: #9ca3af; background: #f3f4f6; cursor: not-allowed; border-color: #e5e7eb; }
    .horario-option .badge { display: inline-block; background: #fef3c7; color: #92400e; font-size: 9px; padding: 2px 6px; border-radius: 20px; margin-top: 4px; }

    .profissional-card { border: 2px solid #FFD6F4; border-radius: 14px; padding: 16px; cursor: pointer; transition: all 0.3s; background: white; }
    .profissional-card:hover { border-color: #7B19E5; background: rgba(123, 25, 229, 0.05); transform: translateY(-2px); box-shadow: 0 8px 20px rgba(123, 25, 229, 0.1); }
    .profissional-card.selecionado { background: rgba(123, 25, 229, 0.1); border-color: #7B19E5; box-shadow: 0 4px 12px rgba(123, 25, 229, 0.2); }
    .profissional-card h4 { font-size: 16px; font-weight: 700; color: #1A002B; margin-bottom: 4px; }
    .profissional-card p { font-size: 13px; color: #6b7280; }
</style>

<script>
    const urlHorarios = "{{ route('api.horarios.disponibilidade') }}";
    const urlProfissionaisDisponiveis = "{{ route('api.profissionais-disponiveis') }}";
    const limiteAgendamento = new Date("{{ $limiteAgendamento->toDateString() }}T23:59:59");
    const duracaoPadrao = 30;

    const estadoConsulta = {
        dataSelecionada: null,
        horaSelecionada: null,
        horarioEspecial: null,
    };

    let mesAtual = new Date().getMonth();
    let anoAtual = new Date().getFullYear();

    const resumoConsulta = document.getElementById('resumo-consulta');
    const estadoTexto = document.getElementById('estado-disponibilidade');
    const lista = document.getElementById('lista-profissionais');
    const filtro = document.getElementById('filtro-profissionais');
    const busca = document.getElementById('busca-profissional');
    const semResultadosFiltro = document.getElementById('sem-resultados-filtro');
    const tituloListaProfissionais = document.getElementById('titulo-lista-profissionais');
    const subtituloListaProfissionais = document.getElementById('subtitulo-lista-profissionais');
    const infoDataSelecionada = document.getElementById('info-data-selecionada');
    const dataSelecionadaTexto = document.getElementById('data-selecionada-texto');
    const infoHoraSelecionada = document.getElementById('info-hora-selecionada');
    const horaSelecionadaTexto = document.getElementById('hora-selecionada-texto');
    const avisoHorarioEspecial = document.getElementById('aviso-horario-especial');
    const gradeHorarios = document.getElementById('grade_horarios');

    function formatarDataLocal(data) {
        const ano = data.getFullYear();
        const mes = String(data.getMonth() + 1).padStart(2, '0');
        const dia = String(data.getDate()).padStart(2, '0');

        return `${ano}-${mes}-${dia}`;
    }

    function formatarDataBR(data) {
        return data.toLocaleDateString('pt-BR');
    }

    function normalizarTexto(texto) {
        return (texto || '')
            .toString()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .toLowerCase();
    }

    function mostrarEstado(mensagem) {
        estadoTexto.textContent = mensagem;
        estadoTexto.classList.remove('hidden');
        lista.classList.add('hidden');
        filtro.classList.add('hidden');
        semResultadosFiltro.classList.add('hidden');
    }

    function limparProfissionais() {
        lista.innerHTML = '';
        lista.classList.add('hidden');
        filtro.classList.add('hidden');
        semResultadosFiltro.classList.add('hidden');
    }

    function limparEscolhaHorarioEProfissional() {
        estadoConsulta.horaSelecionada = null;
        estadoConsulta.horarioEspecial = null;

        infoHoraSelecionada.classList.add('hidden');
        avisoHorarioEspecial.classList.add('hidden');
        resumoConsulta.classList.add('hidden');
        limparProfissionais();
        mostrarEstado('Selecione um horário para filtrar ou continue para ver o dia todo.');
    }

    function construirCalendario() {
        const container = document.getElementById('calendario');
        container.innerHTML = '';

        const header = document.createElement('div');
        header.className = 'calendar-header';
        header.innerHTML = `<button type="button" onclick="mesAnterior()">←</button><span id="mes-ano-actual"></span><button type="button" onclick="proxMes()">→</button>`;
        container.appendChild(header);

        atualizarCalendario(mesAtual, anoAtual);
    }

    function mesAnterior() {
        mesAtual--;
        if (mesAtual < 0) {
            mesAtual = 11;
            anoAtual--;
        }

        atualizarCalendario(mesAtual, anoAtual);
    }

    function proxMes() {
        const proxMesIndex = mesAtual === 11 ? 0 : mesAtual + 1;
        const proxAno = mesAtual === 11 ? anoAtual + 1 : anoAtual;
        const primeiroDia = new Date(proxAno, proxMesIndex, 1);

        if (primeiroDia <= new Date(limiteAgendamento.getFullYear(), limiteAgendamento.getMonth(), 1)) {
            mesAtual = proxMesIndex;
            anoAtual = proxAno;
            atualizarCalendario(mesAtual, anoAtual);
        }
    }

    function atualizarCalendario(mes, ano) {
        const meses = ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'];
        document.getElementById('mes-ano-actual').innerText = `${meses[mes]} ${ano}`;

        const container = document.getElementById('calendario');
        let grid = container.querySelector('.calendar-days');
        if (grid) {
            grid.remove();
        }

        grid = document.createElement('div');
        grid.className = 'calendar-days';
        ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sab'].forEach((dia) => {
            const cell = document.createElement('div');
            cell.className = 'calendar-day-name';
            cell.innerText = dia;
            grid.appendChild(cell);
        });

        const primeiroDia = new Date(ano, mes, 1);
        const ultimoDia = new Date(ano, mes + 1, 0);
        const diaInicio = primeiroDia.getDay();
        const ultimoMesAnterior = new Date(ano, mes, 0).getDate();

        for (let i = diaInicio - 1; i >= 0; i--) {
            const anterior = document.createElement('div');
            anterior.className = 'calendar-date outro-mes';
            anterior.innerText = ultimoMesAnterior - i;
            grid.appendChild(anterior);
        }

        const hoje = new Date();
        const hojeDate = new Date(hoje.getFullYear(), hoje.getMonth(), hoje.getDate());

        for (let dia = 1; dia <= ultimoDia.getDate(); dia++) {
            const dataObj = new Date(ano, mes, dia);
            const dateDiv = document.createElement('div');
            dateDiv.className = 'calendar-date';
            dateDiv.innerText = dia;

            if (dataObj < hojeDate || dataObj > limiteAgendamento) {
                dateDiv.classList.add('indisponivel');
            } else {
                dateDiv.onclick = () => selecionarData(dataObj);
            }

            if (estadoConsulta.dataSelecionada && dataObj.toDateString() === estadoConsulta.dataSelecionada.toDateString()) {
                dateDiv.classList.add('selecionado');
            }

            grid.appendChild(dateDiv);
        }

        container.appendChild(grid);
    }

    function selecionarData(data) {
        if (data > limiteAgendamento) {
            alert('Agendamento permitido apenas para os próximos 3 meses.');
            return;
        }

        estadoConsulta.dataSelecionada = data;
        dataSelecionadaTexto.textContent = formatarDataBR(data);
        infoDataSelecionada.classList.remove('hidden');
        limparEscolhaHorarioEProfissional();
        atualizarCalendario(mesAtual, anoAtual);
    }

    function carregarHorarios() {
        if (!estadoConsulta.dataSelecionada) {
            return;
        }

        const data = formatarDataLocal(estadoConsulta.dataSelecionada);
        gradeHorarios.innerHTML = '<p class="text-gray-500 col-span-full text-center py-8">Carregando horários...</p>';

        fetch(`${urlHorarios}?data=${data}&duracao=${duracaoPadrao}`)
            .then((resposta) => resposta.json())
            .then((dados) => {
                const horarios = Array.isArray(dados) ? dados : (dados.horarios || []);

                gradeHorarios.innerHTML = '';

                if (!horarios.length) {
                    gradeHorarios.innerHTML = '<p class="text-red-500 col-span-full text-center">Nenhum horário disponível</p>';
                    return;
                }

                horarios.forEach((horario) => {
                    const botao = document.createElement('button');
                    botao.type = 'button';
                    botao.className = 'horario-option';
                    botao.innerHTML = horario.atendimento_especial
                        ? `<div>${horario.hora}</div><div class="badge">+${horario.percentual_acrescimo}%</div>`
                        : `<div>${horario.hora}</div>`;

                    botao.onclick = () => selecionarHora(horario);

                    gradeHorarios.appendChild(botao);
                });
            });
    }

    function selecionarHora(horario) {
        estadoConsulta.horaSelecionada = horario.hora;
        estadoConsulta.horarioEspecial = horario;
        horaSelecionadaTexto.textContent = horario.hora;
        infoHoraSelecionada.classList.remove('hidden');

        if (horario.atendimento_especial) {
            avisoHorarioEspecial.classList.remove('hidden');
        } else {
            avisoHorarioEspecial.classList.add('hidden');
        }

        resumoConsulta.textContent = `Consulta para ${formatarDataBR(estadoConsulta.dataSelecionada)} às ${horario.hora}.`;
        resumoConsulta.classList.remove('hidden');
    }

    function aplicarFiltroProfissionais() {
        const termo = normalizarTexto(busca.value);
        let visiveis = 0;

        lista.querySelectorAll('[data-profissional-card]').forEach((card) => {
            const visivel = !termo || normalizarTexto(card.dataset.filterText).includes(termo);
            card.classList.toggle('hidden', !visivel);

            if (visivel) {
                visiveis++;
            }
        });

        semResultadosFiltro.classList.toggle('hidden', visiveis > 0 || lista.classList.contains('hidden'));
    }

    function renderizarProfissionais(profissionais) {
        lista.innerHTML = '';
        busca.value = '';

        if (!profissionais.length) {
            mostrarEstado(estadoConsulta.horaSelecionada ? 'Nenhum profissional disponivel nesse dia e horario.' : 'Nenhum profissional disponivel nesse dia.');
            return;
        }

        estadoTexto.classList.add('hidden');
        lista.classList.remove('hidden');
        filtro.classList.remove('hidden');

        profissionais.forEach((profissional) => {
            const servicos = profissional.servicos?.length ? profissional.servicos.join(', ') : 'Sem servicos vinculados';
            const horarios = Array.isArray(profissional.horarios_disponiveis) ? profissional.horarios_disponiveis : [];
            const horariosTexto = horarios.map((item) => item.hora).join(' ');
            const horariosHtml = horarios.length
                ? `<div class="mt-4 flex flex-wrap gap-2">${horarios.map((item) => `<span class="rounded-full border border-[#FFD6F4] bg-[#7B19E5]/5 px-3 py-1 text-xs font-bold text-[#4A00B9]">${item.hora}${item.atendimento_especial ? ` +${item.percentual_acrescimo}%` : ''}</span>`).join('')}</div>`
                : '';
            const especial = profissional.atendimento_especial
                ? `<span class="inline-flex mt-3 rounded-full bg-yellow-100 px-3 py-1 text-xs font-bold text-yellow-700">+${profissional.percentual_acrescimo}% especial</span>`
                : '';

            const card = document.createElement('article');
            card.className = 'profissional-card';
            card.dataset.profissionalCard = 'true';
            card.dataset.filterText = `${profissional.name} ${profissional.email || ''} ${profissional.telefone || ''} ${servicos} ${horariosTexto}`;
            card.innerHTML = `
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h3 class="font-title text-lg text-[#4A00B9]">${profissional.name}</h3>
                        <p class="text-xs text-gray-500 mt-1">${profissional.telefone || 'Telefone nao informado'}</p>
                        <p class="text-xs text-gray-500">${profissional.email || 'E-mail nao informado'}</p>
                    </div>
                    <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-bold text-green-700">Livre</span>
                </div>
                <p class="mt-4 text-sm text-gray-600 line-clamp-3">${servicos}</p>
                ${horariosHtml}
                ${especial}
            `;

            lista.appendChild(card);
        });

        aplicarFiltroProfissionais();
    }

    function carregarProfissionais() {
        if (!estadoConsulta.dataSelecionada) {
            return;
        }

        mostrarEstado('Buscando profissionais disponiveis...');
        tituloListaProfissionais.textContent = estadoConsulta.horaSelecionada
            ? 'Profissionais disponiveis no horario selecionado'
            : 'Profissionais disponiveis no dia selecionado';
        subtituloListaProfissionais.textContent = estadoConsulta.horaSelecionada
            ? 'Use a busca para filtrar a lista se necessario.'
            : 'Cada card mostra os horarios livres encontrados nesse dia.';

        const params = new URLSearchParams({
            data: formatarDataLocal(estadoConsulta.dataSelecionada),
            duracao: duracaoPadrao,
        });

        if (estadoConsulta.horaSelecionada) {
            params.set('hora', estadoConsulta.horaSelecionada);
        }

        fetch(`${urlProfissionaisDisponiveis}?${params.toString()}`, {
            headers: { Accept: 'application/json' },
        })
            .then((resposta) => {
                if (!resposta.ok) {
                    throw new Error('Nao foi possivel consultar agora.');
                }

                return resposta.json();
            })
            .then((dados) => {
                renderizarProfissionais(dados.profissionais || []);
            })
            .catch((erro) => {
                mostrarEstado(erro.message || 'Nao foi possivel consultar agora.');
            });
    }

    function irParaPasso(passo) {
        if (passo === 2 && !estadoConsulta.dataSelecionada) {
            alert('Selecione uma data');
            return;
        }

        if (passo === 3 && !estadoConsulta.dataSelecionada) {
            alert('Selecione uma data');
            return;
        }

        document.querySelectorAll('.passo').forEach((el) => el.classList.add('hidden'));
        document.getElementById(`passo-${passo}`).classList.remove('hidden');

        for (let i = 1; i <= 3; i++) {
            const indicador = document.getElementById(`indicador-${i}`);
            if (i === passo) {
                indicador.className = 'flex items-center gap-2 px-4 py-2 rounded-full bg-gradient-to-r from-[#7B19E5] to-[#A855F7] text-white shadow-md';
            } else {
                indicador.className = 'flex items-center gap-2 px-4 py-2 rounded-full bg-white/50 border border-[#FFD6F4] text-[#4A00B9]';
            }
        }

        if (passo === 2) {
            carregarHorarios();
        }

        if (passo === 3) {
            carregarProfissionais();
        }
    }

    busca.addEventListener('input', aplicarFiltroProfissionais);

    construirCalendario();
    irParaPasso(1);
</script>
</x-app-layout>
