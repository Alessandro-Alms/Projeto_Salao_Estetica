<x-app-layout>
<div class="py-12 relative">
    <!-- Fundo -->
    <div class="fixed inset-0 -z-10 overflow-hidden">
        <div class="absolute top-0 -left-20 w-[600px] h-[600px] bg-[#7B19E5]/20 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 -right-20 w-[700px] h-[700px] bg-[#FF2EB6]/20 rounded-full blur-3xl"></div>
        <div class="absolute top-1/3 left-1/3 w-[400px] h-[400px] bg-[#A955D3]/15 rounded-full blur-3xl"></div>
        <div class="absolute inset-0 bg-gradient-to-br from-[#7B19E5]/5 via-white/30 to-[#FF2EB6]/5"></div>
    </div>

    <div class="max-w-6xl mx-auto px-4">
        <div class="glass-card rounded-3xl shadow-xl overflow-hidden">
            <div class="p-8 bg-white/70 backdrop-blur-sm">
                <!-- Cabeçalho -->
                <div class="flex items-center gap-3 mb-8 pb-4 border-b border-[#FFD6F4]">
                    <div class="w-12 h-12 bg-gradient-to-br from-[#7B19E5] to-[#FF2EB6] rounded-2xl flex items-center justify-center shadow-md">
                        <span class="text-white text-xl">✧</span>
                    </div>
                    <div>
                        <h2 class="text-2xl font-title text-[#4A00B9]">Agendar Novo Atendimento</h2>
                        <p class="text-sm text-gray-500 mt-0.5">Preencha os dados abaixo para marcar seu horário</p>
                    </div>
                </div>

                <!-- Indicador de Passos -->
                <div class="flex justify-between mb-10 flex-wrap gap-3">
                    <div id="indicador-1" class="flex items-center gap-2 px-4 py-2 rounded-full bg-gradient-to-r from-[#7B19E5] to-[#A855F7] text-white shadow-md">
                        <span class="text-sm">✧</span>
                        <span class="text-sm font-medium">Serviço</span>
                    </div>
                    <div id="indicador-2" class="flex items-center gap-2 px-4 py-2 rounded-full bg-white/50 border border-[#FFD6F4] text-[#4A00B9]">
                        <span class="text-sm">✦</span>
                        <span class="text-sm font-medium">Calendário</span>
                    </div>
                    <div id="indicador-3" class="flex items-center gap-2 px-4 py-2 rounded-full bg-white/50 border border-[#FFD6F4] text-[#4A00B9]">
                        <span class="text-sm">✧</span>
                        <span class="text-sm font-medium">Horário</span>
                    </div>
                    <div id="indicador-4" class="flex items-center gap-2 px-4 py-2 rounded-full bg-white/50 border border-[#FFD6F4] text-[#4A00B9]">
                        <span class="text-sm">✦</span>
                        <span class="text-sm font-medium">Profissional</span>
                    </div>
                    <div id="indicador-5" class="flex items-center gap-2 px-4 py-2 rounded-full bg-white/50 border border-[#FFD6F4] text-[#4A00B9]">
                        <span class="text-sm">✧</span>
                        <span class="text-sm font-medium">Confirmar</span>
                    </div>
                </div>

                <form id="formAgendamento" action="{{ route('cliente.agendar.salvar') }}" method="POST">
                    @csrf
                    
                    <input type="hidden" id="servicos_ids" name="servicos_ids">
                    <input type="hidden" id="profissional_id" name="profissional_id">

                    {{-- PASSO 1: ESCOLHER SERVIÇO(S) --}}
                    <div id="passo-1" class="passo">
                        <div class="bg-gradient-to-r from-[#7B19E5]/5 to-[#FF2EB6]/5 rounded-2xl p-5 mb-6 border border-[#FFD6F4]">
                            <div class="flex items-start gap-3">
                                <span class="text-2xl">✧</span>
                                <div>
                                    <p class="text-[#1A002B] font-medium">Você pode escolher até <strong class="text-[#7B19E5]">5 serviços</strong> por agendamento</p>
                                    <p class="text-sm text-gray-500 mt-1">Ao selecionar 5 serviços, você ganha <strong class="text-[#FF2EB6]">10% de desconto</strong> no total</p>
                                </div>
                            </div>
                        </div>
                        <div class="relative mb-4">
                            <input type="text" id="servico-search" placeholder="Digite o nome do serviço..."
                                class="w-full px-4 py-3 bg-white/50 border border-[#FFD6F4] rounded-lg focus:outline-none focus:border-[#7B19E5] focus:ring-2 focus:ring-[#7B19E5]/20 transition-all">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($servicos as $servico)
                                <div class="servico-card border-2 border-[#FFD6F4] rounded-xl p-4 cursor-pointer transition-all hover:border-[#7B19E5] hover:shadow-md" 
                                     id="servico-card-{{ $servico->id_servico }}"
                                     data-id="{{ $servico->id_servico }}"
                                     data-nome="{{ e($servico->nome) }}"
                                     data-duracao="{{ $servico->duracao }}"
                                     data-preco="{{ $servico->preco }}">
                                    <div class="flex items-start gap-3">
                                        <input type="checkbox" id="checkbox-servico-{{ $servico->id_servico }}" class="mt-1 w-5 h-5 rounded border-[#FFD6F4] text-[#7B19E5] focus:ring-[#7B19E5]">
                                        <div class="flex-1">
                                            <p class="font-bold text-[#1A002B]">{{ $servico->nome }}</p>
                                            <p class="text-sm text-[#7B19E5] font-semibold mt-1">R$ {{ number_format($servico->preco, 2, ',', '.') }}</p>
                                            <p class="text-xs text-gray-400 mt-1"> {{ $servico->duracao }} min</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Resumo dos Serviços -->
                        <div id="resumo-servicos-selecionados" class="mt-6 p-4 bg-[#7B19E5]/5 rounded-xl border border-[#FFD6F4] hidden">
                            <h4 class="font-semibold text-[#4A00B9] mb-2">✧ Serviços Selecionados</h4>
                            <ul id="lista-servicos-selecionados" class="space-y-1 text-sm text-gray-600"></ul>
                            <div class="mt-3 pt-3 border-t border-[#FFD6F4] flex justify-between items-center">
                                <span class="text-[#4A00B9] font-medium">Tempo total:</span>
                                <span class="font-bold text-[#7B19E5]" id="tempo-total">0</span>
                                <span class="text-gray-500">minutos</span>
                            </div>
                            <div class="mt-2 flex justify-between items-center">
                                <span class="text-[#4A00B9] font-medium">Serviços:</span>
                                <span class="font-bold text-[#FF2EB6]" id="qtd-servicos">0</span>
                                <span class="text-gray-500">/5</span>
                            </div>
                            <p id="aviso-desconto-combo" class="hidden mt-2 text-sm text-green-600 font-semibold">✨ Combo completo! 10% de desconto aplicado</p>
                        </div>
                        
                        <div class="mt-8 flex justify-end">
                            <button type="button" onclick="irParaPasso(2)" class="bg-gradient-to-r from-[#7B19E5] to-[#FF2EB6] text-white px-8 py-3 rounded-xl font-medium btn-primary shadow-md hover:shadow-lg transition-all">
                                Continuar →
                            </button>
                        </div>
                    </div>

                    {{-- PASSO 2: CALENDÁRIO --}}
                    <div id="passo-2" class="passo hidden">
                        <div class="bg-gradient-to-r from-[#7B19E5]/5 to-[#FF2EB6]/5 rounded-2xl p-5 mb-6 border border-[#FFD6F4]">
                            <p class="text-[#1A002B]"> Selecione a data desejada para seu atendimento</p>
                        </div>
                        
                        <div class="bg-white/40 rounded-xl p-6 border border-[#FFD6F4]">
                            <div id="calendario" class="calendar-container"></div>
                        </div>

                        <div id="info-data-selecionada" class="mt-4 p-3 bg-green-50/80 rounded-xl border border-green-200 hidden">
                            <p class="text-green-700">✓ Data selecionada: <span id="data-selecionada-texto" class="font-semibold"></span></p>
                        </div>
                        
                        <div class="mt-8 flex justify-between">
                            <button type="button" onclick="irParaPasso(1)" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2 rounded-xl font-medium transition-all">← Voltar</button>
                            <button type="button" onclick="irParaPasso(3)" class="bg-gradient-to-r from-[#7B19E5] to-[#FF2EB6] text-white px-8 py-2 rounded-xl font-medium btn-primary shadow-md hover:shadow-lg transition-all">Continuar →</button>
                        </div>
                    </div>

                    {{-- PASSO 3: HORÁRIO --}}
                    <div id="passo-3" class="passo hidden">
                        <div class="bg-gradient-to-r from-[#7B19E5]/5 to-[#FF2EB6]/5 rounded-2xl p-5 mb-6 border border-[#FFD6F4]">
                            <p class="text-[#1A002B]"> Selecione o horário disponível</p>
                        </div>
                        
                        <div id="grade_horarios" class="grid grid-cols-4 md:grid-cols-6 lg:grid-cols-8 gap-3">
                            <p class="text-gray-500 col-span-full text-center py-8">Carregando horários...</p>
                        </div>

                        <input type="hidden" id="data_agendamento" name="data_agendamento">
                        <input type="hidden" id="hora_agendamento" name="hora_agendamento">

                        <div id="aviso-horario-especial" class="mt-4 p-3 bg-yellow-50/80 rounded-xl border border-yellow-200 hidden">
                            <p class="text-yellow-700 text-sm">⚠️ Este horário possui acréscimo especial</p>
                        </div>
                        
                        <div id="info-hora-selecionada" class="mt-4 p-3 bg-green-50/80 rounded-xl border border-green-200 hidden">
                            <p class="text-green-700">✓ Horário selecionado: <span id="hora-selecionada-texto" class="font-semibold"></span></p>
                        </div>
                        
                        <div class="mt-8 flex justify-between">
                            <button type="button" onclick="irParaPasso(2)" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2 rounded-xl font-medium transition-all">← Voltar</button>
                            <button type="button" onclick="irParaPasso(4)" class="bg-gradient-to-r from-[#7B19E5] to-[#FF2EB6] text-white px-8 py-2 rounded-xl font-medium btn-primary shadow-md hover:shadow-lg transition-all">Continuar →</button>
                        </div>
                    </div>

                    {{-- PASSO 4: PROFISSIONAL --}}
                    <div id="passo-4" class="passo hidden">
                        <div class="bg-gradient-to-r from-[#7B19E5]/5 to-[#FF2EB6]/5 rounded-2xl p-5 mb-6 border border-[#FFD6F4]">
                            <p class="text-[#1A002B]"> Escolha o profissional que fará seu atendimento</p>
                        </div>
                        
                        <div class="relative mb-4">
                            <input type="text" id="profissional-search" placeholder="Digite o nome do profissional..."
                                class="w-full px-4 py-3 bg-white/50 border border-[#FFD6F4] rounded-lg focus:outline-none focus:border-[#7B19E5] focus:ring-2 focus:ring-[#7B19E5]/20 transition-all">
                        </div>

                        <div id="grade_profissionais" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <p class="text-gray-500 col-span-full text-center py-8">Carregando profissionais...</p>
                        </div>

                        <div id="info-profissional-selecionado" class="mt-4 p-3 bg-green-50/80 rounded-xl border border-green-200 hidden">
                            <p class="text-green-700">✓ Profissional selecionado: <span id="profissional-selecionado-texto" class="font-semibold"></span></p>
                        </div>
                        
                        <div class="mt-8 flex justify-between">
                            <button type="button" onclick="irParaPasso(3)" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2 rounded-xl font-medium transition-all">← Voltar</button>
                            <button type="button" onclick="irParaPasso(5)" class="bg-gradient-to-r from-[#7B19E5] to-[#FF2EB6] text-white px-8 py-2 rounded-xl font-medium btn-primary shadow-md hover:shadow-lg transition-all">Continuar →</button>
                        </div>
                    </div>

                    {{-- PASSO 5: CONFIRMAR --}}
                    <div id="passo-5" class="passo hidden">
                        <div class="bg-gradient-to-r from-[#7B19E5]/5 to-[#FF2EB6]/5 rounded-2xl p-5 mb-6 border border-[#FFD6F4]">
                            <p class="text-[#1A002B]">✓ Confirme os dados do seu agendamento</p>
                        </div>
                        
                        <div class="bg-white/50 rounded-xl p-6 space-y-3 border border-[#FFD6F4]">
                            <div class="flex justify-between py-2 border-b border-[#FFD6F4]">
                                <span class="text-gray-600">✧ Serviço(s):</span>
                                <span id="resumo_servico" class="font-semibold text-[#4A00B9]"></span>
                            </div>
                            <div class="flex justify-between py-2 border-b border-[#FFD6F4]">
                                <span class="text-gray-600">✦ Profissional:</span>
                                <span id="resumo_profissional" class="font-semibold text-[#4A00B9]"></span>
                            </div>
                            <div class="flex justify-between py-2 border-b border-[#FFD6F4]">
                                <span class="text-gray-600">✧ Data:</span>
                                <span id="resumo_data" class="font-semibold text-[#4A00B9]"></span>
                            </div>
                            <div class="flex justify-between py-2 border-b border-[#FFD6F4]">
                                <span class="text-gray-600">✦ Hora:</span>
                                <span id="resumo_hora" class="font-semibold text-[#4A00B9]"></span>
                            </div>
                            <div class="flex justify-between py-2 border-t border-[#FFD6F4] mt-2 pt-3">
                                <span class="font-bold text-gray-800">Total a pagar:</span>
                                <span id="resumo_valor_total" class="font-bold text-2xl text-green-600"></span>
                            </div>
                        </div>

                        <div class="mt-8 flex justify-between">
                            <button type="button" onclick="irParaPasso(4)" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2 rounded-xl font-medium transition-all">← Voltar</button>
                            <button type="submit" class="bg-gradient-to-r from-green-500 to-green-600 text-white px-10 py-3 rounded-xl font-bold shadow-md hover:shadow-lg transition-all">✓ Confirmar Agendamento</button>
                        </div>
                    </div>
                </form>
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
    
    .hidden { display: none; }
    
    /* Calendário */
    .calendar-container { max-width: 100%; }
    .calendar-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
    .calendar-header button { background: linear-gradient(135deg, #7B19E5, #FF2EB6); color: white; border: none; padding: 6px 14px; border-radius: 10px; cursor: pointer; font-size: 14px; transition: opacity 0.3s; }
    .calendar-header button:hover { opacity: 0.9; }
    .calendar-days { display: grid; grid-template-columns: repeat(7, 1fr); gap: 8px; margin-bottom: 15px; }
    .calendar-day-name { text-align: center; font-weight: bold; color: #4A00B9; padding: 10px; font-size: 12px; background: rgba(123, 25, 229, 0.1); border-radius: 10px; }
    .calendar-date { aspect-ratio: 1; display: flex; align-items: center; justify-content: center; border: 2px solid #FFD6F4; border-radius: 12px; cursor: pointer; font-weight: 500; transition: all 0.3s; background: white; font-size: 14px; }
    .calendar-date:hover:not(.outro-mes):not(.indisponivel) { border-color: #7B19E5; background: rgba(123, 25, 229, 0.1); transform: scale(1.05); }
    .calendar-date.outro-mes { color: #d1d5db; cursor: not-allowed; background: #f9fafb; }
    .calendar-date.selecionado { background: linear-gradient(135deg, #7B19E5, #FF2EB6); color: white; border-color: #7B19E5; box-shadow: 0 4px 12px rgba(123, 25, 229, 0.3); }
    .calendar-date.indisponivel { color: #9ca3af; background: #f3f4f6; cursor: not-allowed; border-color: #e5e7eb; }
    
    /* Horários */
    .horario-option { padding: 10px; border: 2px solid #FFD6F4; border-radius: 10px; cursor: pointer; text-align: center; font-weight: 500; transition: all 0.3s; background: white; font-size: 14px; }
    .horario-option:hover:not(.ocupado) { border-color: #7B19E5; background: rgba(123, 25, 229, 0.1); transform: scale(1.05); }
    .horario-option.selecionado { background: linear-gradient(135deg, #7B19E5, #FF2EB6); color: white; border-color: #7B19E5; }
    .horario-option.ocupado { color: #9ca3af; background: #f3f4f6; cursor: not-allowed; border-color: #e5e7eb; }
    .horario-option .badge { display: inline-block; background: #fef3c7; color: #92400e; font-size: 9px; padding: 2px 6px; border-radius: 20px; margin-top: 4px; }
    
    /* Profissionais */
    .profissional-card { border: 2px solid #FFD6F4; border-radius: 14px; padding: 16px; cursor: pointer; transition: all 0.3s; background: white; }
    .profissional-card:hover { border-color: #7B19E5; background: rgba(123, 25, 229, 0.05); transform: translateY(-2px); box-shadow: 0 8px 20px rgba(123, 25, 229, 0.1); }
    .profissional-card.selecionado { background: rgba(123, 25, 229, 0.1); border-color: #7B19E5; box-shadow: 0 4px 12px rgba(123, 25, 229, 0.2); }
    .profissional-card h4 { font-size: 16px; font-weight: 700; color: #1A002B; margin-bottom: 4px; }
    .profissional-card p { font-size: 13px; color: #6b7280; }
</style>

<script>
    const urlProfissionais = "{{ route('api.profissionais') }}";
    const urlHorarios = "{{ route('api.horarios') }}";
    const limiteAgendamento = new Date("{{ $limiteAgendamento->toDateString() }}T23:59:59");
    const maxServicosPorAgendamento = {{ \App\Services\AgendaService::MAX_SERVICOS_POR_AGENDAMENTO }};
    const descontoComboPercentual = {{ \App\Services\AgendaService::DESCONTO_COMBO_SERVICOS_PERCENTUAL }};

    let estadoAgendamento = {
        servicosIds: [], servicosNomes: [], servicosDuracao: {}, servicosPrecos: {},
        duraoTotal: 0, valorBase: 0, dataSelecionada: null, horaSelecionada: null,
        horarioEspecial: null, profissionalId: null, profissionalNome: null, resumoFinanceiro: null
    };

    let mesAtual = new Date().getMonth();
    let anoAtual = new Date().getFullYear();

    function toggleServico(id, nome, duracao, preco) {
        const checkbox = document.getElementById(`checkbox-servico-${id}`);
        const card = document.getElementById(`servico-card-${id}`);
        
        if (estadoAgendamento.servicosIds.includes(id)) {
            estadoAgendamento.servicosIds = estadoAgendamento.servicosIds.filter(i => i !== id);
            estadoAgendamento.servicosNomes = estadoAgendamento.servicosNomes.filter(n => n !== nome);
            delete estadoAgendamento.servicosDuracao[id];
            delete estadoAgendamento.servicosPrecos[id];
            checkbox.checked = false;
            card.classList.remove('border-[#7B19E5]', 'bg-[#7B19E5]/5');
        } else {
            if (estadoAgendamento.servicosIds.length >= maxServicosPorAgendamento) {
                alert(`Máximo de ${maxServicosPorAgendamento} serviços por agendamento`);
                return;
            }
            estadoAgendamento.servicosIds.push(id);
            estadoAgendamento.servicosNomes.push(nome);
            estadoAgendamento.servicosDuracao[id] = duracao;
            estadoAgendamento.servicosPrecos[id] = preco;
            checkbox.checked = true;
            card.classList.add('border-[#7B19E5]', 'bg-[#7B19E5]/5');
        }
        atualizarResumo();
    }

    function atualizarResumo() {
        const resumoDiv = document.getElementById('resumo-servicos-selecionados');
        const listaUl = document.getElementById('lista-servicos-selecionados');
        
        if (estadoAgendamento.servicosIds.length === 0) {
            resumoDiv.classList.add('hidden');
            document.getElementById('servicos_ids').value = '';
            return;
        }
        
        resumoDiv.classList.remove('hidden');
        listaUl.innerHTML = '';
        let tempoTotal = 0, valorBase = 0;
        
        estadoAgendamento.servicosIds.forEach((id, idx) => {
            tempoTotal += estadoAgendamento.servicosDuracao[id];
            valorBase += estadoAgendamento.servicosPrecos[id];
            const li = document.createElement('li');
            li.innerHTML = `✧ ${estadoAgendamento.servicosNomes[idx]} (${estadoAgendamento.servicosDuracao[id]} min)`;
            listaUl.appendChild(li);
        });
        
        document.getElementById('tempo-total').innerText = tempoTotal;
        document.getElementById('qtd-servicos').innerText = estadoAgendamento.servicosIds.length;
        document.getElementById('aviso-desconto-combo').classList.toggle('hidden', estadoAgendamento.servicosIds.length !== maxServicosPorAgendamento);
        estadoAgendamento.duraoTotal = tempoTotal;
        estadoAgendamento.valorBase = valorBase;
        document.getElementById('servicos_ids').value = estadoAgendamento.servicosIds.join(',');
    }

    function construirCalendario() {
        const container = document.getElementById('calendario');
        container.innerHTML = '';
        const header = document.createElement('div');
        header.className = 'calendar-header';
        header.innerHTML = `<button onclick="mesAnterior()">←</button><span id="mes-ano-actual"></span><button onclick="proxMes()">→</button>`;
        container.appendChild(header);
        atualizarCalendario(mesAtual, anoAtual);
    }

    function mesAnterior() { mesAtual--; if (mesAtual < 0) { mesAtual = 11; anoAtual--; } atualizarCalendario(mesAtual, anoAtual); }
    function proxMes() { const prox = mesAtual === 11 ? 0 : mesAtual + 1; const proxAno = mesAtual === 11 ? anoAtual + 1 : anoAtual; const primeiroDia = new Date(proxAno, prox, 1); if (primeiroDia <= new Date(limiteAgendamento.getFullYear(), limiteAgendamento.getMonth(), 1)) { mesAtual++; if (mesAtual > 11) { mesAtual = 0; anoAtual++; } atualizarCalendario(mesAtual, anoAtual); } }

    function atualizarCalendario(mes, ano) {
        const meses = ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'];
        document.getElementById('mes-ano-actual').innerText = `${meses[mes]} ${ano}`;
        const container = document.getElementById('calendario');
        let grid = container.querySelector('.calendar-days');
        if (grid) grid.remove();
        grid = document.createElement('div');
        grid.className = 'calendar-days';
        ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sab'].forEach(d => { const day = document.createElement('div'); day.className = 'calendar-day-name'; day.innerText = d; grid.appendChild(day); });
        const primeiroDia = new Date(ano, mes, 1);
        const ultimoDia = new Date(ano, mes + 1, 0);
        const diaInicio = primeiroDia.getDay();
        const ultimoMesAnterior = new Date(ano, mes, 0).getDate();
        for (let i = diaInicio - 1; i >= 0; i--) { const d = document.createElement('div'); d.className = 'calendar-date outro-mes'; d.innerText = ultimoMesAnterior - i; grid.appendChild(d); }
        const hoje = new Date();
        const hojeDate = new Date(hoje.getFullYear(), hoje.getMonth(), hoje.getDate());
        for (let dia = 1; dia <= ultimoDia.getDate(); dia++) {
            const dataObj = new Date(ano, mes, dia);
            const dateDiv = document.createElement('div');
            dateDiv.className = 'calendar-date';
            dateDiv.innerText = dia;
            if (dataObj < hojeDate || dataObj > limiteAgendamento) dateDiv.classList.add('indisponivel');
            else dateDiv.onclick = () => selecionarData(dataObj);
            if (estadoAgendamento.dataSelecionada && dataObj.toDateString() === estadoAgendamento.dataSelecionada.toDateString()) dateDiv.classList.add('selecionado');
            grid.appendChild(dateDiv);
        }
        container.appendChild(grid);
    }

    function selecionarData(data) {
        if (data > limiteAgendamento) { alert('Agendamento permitido apenas para os próximos 3 meses.'); return; }
        estadoAgendamento.dataSelecionada = data;
        document.getElementById('data-selecionada-texto').innerText = data.toLocaleDateString('pt-BR');
        document.getElementById('info-data-selecionada').classList.remove('hidden');
        atualizarCalendario(mesAtual, anoAtual);
    }

    function carregarHorarios() {
        if (!estadoAgendamento.dataSelecionada) return;
        const data = estadoAgendamento.dataSelecionada.toISOString().split('T')[0];
        document.getElementById('data_agendamento').value = data;
        const grade = document.getElementById('grade_horarios');
        grade.innerHTML = '<p class="text-gray-500 col-span-full text-center py-8">Carregando horários...</p>';
        const servicoId = estadoAgendamento.servicosIds[0];
        fetch(`${urlHorarios}?data=${data}&servico_id=${servicoId}&duracao=${estadoAgendamento.duraoTotal}`)
            .then(r => r.json())
            .then(h => {
                grade.innerHTML = '';
                if (!h.length) { grade.innerHTML = '<p class="text-red-500 col-span-full text-center">Nenhum horário disponível</p>'; return; }
                h.forEach(hor => {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = `horario-option ${hor.ocupado ? 'ocupado' : ''}`;
                    btn.innerHTML = hor.atendimento_especial ? `<div>${hor.hora}</div><div class="badge">+${hor.percentual_acrescimo}%</div>` : `<div>${hor.hora}</div>`;
                    if (!hor.ocupado) btn.onclick = () => selecionarHora(hor);
                    grade.appendChild(btn);
                });
            });
    }

    function selecionarHora(horario) {
        estadoAgendamento.horaSelecionada = horario.hora;
        estadoAgendamento.horarioEspecial = horario;
        document.getElementById('hora_agendamento').value = horario.hora;
        document.getElementById('hora-selecionada-texto').innerText = horario.hora;
        document.getElementById('info-hora-selecionada').classList.remove('hidden');
        const aviso = document.getElementById('aviso-horario-especial');
        if (horario.atendimento_especial) aviso.classList.remove('hidden');
        else aviso.classList.add('hidden');
    }

    function carregarProfissionais() {
        if (!estadoAgendamento.dataSelecionada || !estadoAgendamento.horaSelecionada) return;
        const grade = document.getElementById('grade_profissionais');
        grade.innerHTML = '<p class="text-gray-500 col-span-full text-center py-8">Carregando profissionais...</p>';
        const data = estadoAgendamento.dataSelecionada.toISOString().split('T')[0];
        const dataHora = `${data} ${estadoAgendamento.horaSelecionada}`;
        fetch(`${urlProfissionais}?servicos_ids=${estadoAgendamento.servicosIds.join(',')}&data_hora=${encodeURIComponent(dataHora)}&duracao=${estadoAgendamento.duraoTotal}`)
            .then(r => r.json())
            .then(profs => {
                grade.innerHTML = '';
                if (!profs.length) { grade.innerHTML = '<p class="text-red-500 col-span-full text-center">Nenhum profissional disponível</p>'; return; }
                profs.forEach(prof => {
                    const card = document.createElement('div');
                    card.className = 'profissional-card';
                    card.dataset.nome = prof.name;
                    card.innerHTML = `<h4>✧ ${prof.name}</h4><p>✓ Disponível</p><p class="text-[#7B19E5] font-bold mt-2">Total: ${formatarMoeda(prof.valor_total)}</p>`;
                    card.onclick = () => selecionarProfissional(prof, card);
                    grade.appendChild(card);
                });
                filtrarProfissionais();
            });
    }

    function filtrarProfissionais() {
        const busca = document.getElementById('profissional-search');
        const termo = (busca?.value || '')
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .toLowerCase();

        document.querySelectorAll('.profissional-card').forEach(card => {
            const nome = (card.dataset.nome || '')
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')
                .toLowerCase();

            card.classList.toggle('hidden', termo && !nome.includes(termo));
        });
    }

    function selecionarProfissional(prof, card) {
        estadoAgendamento.profissionalId = prof.id;
        estadoAgendamento.profissionalNome = prof.name;
        estadoAgendamento.resumoFinanceiro = prof;
        document.getElementById('profissional_id').value = prof.id;
        document.querySelectorAll('.profissional-card').forEach(c => c.classList.remove('selecionado'));
        card.classList.add('selecionado');
        document.getElementById('profissional-selecionado-texto').innerText = prof.name;
        document.getElementById('info-profissional-selecionado').classList.remove('hidden');
    }

    function formatarMoeda(v) { return Number(v || 0).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }); }

    function preencherResumo() {
        document.getElementById('resumo_servico').innerText = estadoAgendamento.servicosNomes.join(', ');
        document.getElementById('resumo_profissional').innerText = estadoAgendamento.profissionalNome;
        document.getElementById('resumo_data').innerText = estadoAgendamento.dataSelecionada.toLocaleDateString('pt-BR');
        document.getElementById('resumo_hora').innerText = estadoAgendamento.horaSelecionada;
        const financeiro = estadoAgendamento.resumoFinanceiro || { valor_total: estadoAgendamento.valorBase };
        document.getElementById('resumo_valor_total').innerText = formatarMoeda(financeiro.valor_total);
    }

    function irParaPasso(passo) {
        if (passo === 2 && !estadoAgendamento.servicosIds.length) { alert('Selecione pelo menos um serviço'); return; }
        if (passo === 3 && !estadoAgendamento.dataSelecionada) { alert('Selecione uma data'); return; }
        if (passo === 4 && !estadoAgendamento.horaSelecionada) { alert('Selecione um horário'); return; }
        if (passo === 5 && !estadoAgendamento.profissionalId) { alert('Selecione um profissional'); return; }
        
        document.querySelectorAll('.passo').forEach(el => el.classList.add('hidden'));
        document.getElementById(`passo-${passo}`).classList.remove('hidden');
        
        for (let i = 1; i <= 5; i++) {
            const ind = document.getElementById(`indicador-${i}`);
            if (i === passo) {
                ind.className = 'flex items-center gap-2 px-4 py-2 rounded-full bg-gradient-to-r from-[#7B19E5] to-[#A855F7] text-white shadow-md';
            } else {
                ind.className = 'flex items-center gap-2 px-4 py-2 rounded-full bg-white/50 border border-[#FFD6F4] text-[#4A00B9]';
            }
        }
        
        if (passo === 2 && !document.querySelector('.calendar-days')) construirCalendario();
        if (passo === 3) carregarHorarios();
        if (passo === 4) carregarProfissionais();
        if (passo === 5) preencherResumo();
    }

    document.querySelectorAll('.servico-card').forEach(card => {
        card.addEventListener('click', function(e) {
            if (e.target.type !== 'checkbox') {
                toggleServico(parseInt(this.dataset.id), this.dataset.nome, parseInt(this.dataset.duracao), parseFloat(this.dataset.preco));
            }
        });
    });

    document.getElementById('servico-search')?.addEventListener('input', function() {
        const termo = this.value
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .toLowerCase();

        document.querySelectorAll('.servico-card').forEach(card => {
            const nome = card.dataset.nome
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')
                .toLowerCase();

            card.classList.toggle('hidden', termo && !nome.includes(termo));
        });
    });

    document.getElementById('profissional-search')?.addEventListener('input', filtrarProfissionais);
    
    irParaPasso(1);
</script>
</x-app-layout>
