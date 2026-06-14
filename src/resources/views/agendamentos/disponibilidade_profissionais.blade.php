<x-app-layout>
<div class="py-12 relative">
    <div class="fixed inset-0 -z-10 overflow-hidden">
        <div class="absolute top-0 -left-20 w-[600px] h-[600px] bg-[#7B19E5]/20 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 -right-20 w-[700px] h-[700px] bg-[#FF2EB6]/20 rounded-full blur-3xl"></div>
        <div class="absolute inset-0 bg-gradient-to-br from-[#7B19E5]/5 via-white/30 to-[#FF2EB6]/5"></div>
    </div>

    <div class="max-w-6xl mx-auto px-4">
        <div class="glass-card rounded-3xl shadow-xl overflow-hidden">
            <div class="p-6 sm:p-8 bg-white/70 backdrop-blur-sm">
                <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between mb-8 pb-4 border-b border-[#FFD6F4]">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-gradient-to-br from-[#7B19E5] to-[#FF2EB6] rounded-2xl flex items-center justify-center shadow-md">
                            <span class="text-white text-xl">+</span>
                        </div>
                        <div>
                            <h2 class="text-2xl font-title text-[#4A00B9]">Disponibilidade dos Profissionais</h2>
                            <p class="text-sm text-gray-500 mt-0.5">Escolha uma data e um horario para ver quem esta livre.</p>
                        </div>
                    </div>

                    <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center px-5 py-2.5 rounded-full bg-white/80 border border-[#FFD6F4] text-[#7B19E5] font-bold hover:bg-[#7B19E5] hover:text-white transition-all">
                        Voltar
                    </a>
                </div>

                <form id="form-disponibilidade" class="grid grid-cols-1 md:grid-cols-[1fr_1fr_auto] gap-4 items-end">
                    <div>
                        <label for="data-disponibilidade" class="block text-sm font-medium text-[#4A00B9] mb-2">Data</label>
                        <input type="date" id="data-disponibilidade" required
                            class="w-full px-4 py-3 bg-white/60 border border-[#FFD6F4] rounded-lg focus:outline-none focus:border-[#7B19E5] focus:ring-2 focus:ring-[#7B19E5]/20 transition-all">
                    </div>

                    <div>
                        <label for="hora-disponibilidade" class="block text-sm font-medium text-[#4A00B9] mb-2">Horario</label>
                        <input type="time" id="hora-disponibilidade" required step="900"
                            class="w-full px-4 py-3 bg-white/60 border border-[#FFD6F4] rounded-lg focus:outline-none focus:border-[#7B19E5] focus:ring-2 focus:ring-[#7B19E5]/20 transition-all">
                    </div>

                    <input type="hidden" id="duracao-disponibilidade" value="30">

                    <button type="submit" class="bg-gradient-to-r from-[#7B19E5] to-[#FF2EB6] text-white px-8 py-3 rounded-xl font-bold shadow-md hover:shadow-lg transition-all">
                        Consultar
                    </button>
                </form>

                <div id="resumo-consulta" class="hidden mt-6 p-4 rounded-xl bg-[#7B19E5]/5 border border-[#FFD6F4] text-sm text-[#1A002B]"></div>

                <form id="filtro-profissionais" data-local-filter class="hidden mt-6">
                    <label for="busca-profissional" class="block text-sm font-medium text-[#4A00B9] mb-2">Pesquisar profissional</label>
                    <input type="text" id="busca-profissional" data-filter-search placeholder="Digite o nome do profissional..."
                        class="w-full px-4 py-3 bg-white/60 border border-[#FFD6F4] rounded-lg focus:outline-none focus:border-[#7B19E5] focus:ring-2 focus:ring-[#7B19E5]/20 transition-all">
                </form>

                <div id="estado-disponibilidade" class="mt-8 text-center text-gray-500 py-10">
                    Informe data e horario para consultar.
                </div>

                <div id="lista-profissionais" class="hidden mt-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4"></div>

                <div id="sem-resultados-filtro" class="hidden mt-8 text-center text-gray-500 py-10">
                    Nenhum profissional encontrado nessa pesquisa.
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&display=swap');

    .font-title { font-family: 'Playfair Display', serif; font-weight: 700; letter-spacing: -0.02em; }
    .glass-card { background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.3); box-shadow: 0 8px 32px rgba(123, 25, 229, 0.1); }
    .profissional-card { border: 2px solid #FFD6F4; border-radius: 14px; padding: 16px; background: rgba(255, 255, 255, 0.85); transition: all 0.2s ease; }
    .profissional-card:hover { border-color: #7B19E5; transform: translateY(-2px); box-shadow: 0 8px 20px rgba(123, 25, 229, 0.1); }
</style>

<script>
    const urlProfissionaisDisponiveis = "{{ route('api.profissionais-disponiveis') }}";
    const formDisponibilidade = document.getElementById('form-disponibilidade');
    const dataInput = document.getElementById('data-disponibilidade');
    const horaInput = document.getElementById('hora-disponibilidade');
    const duracaoInput = document.getElementById('duracao-disponibilidade');
    const resumoConsulta = document.getElementById('resumo-consulta');
    const estado = document.getElementById('estado-disponibilidade');
    const lista = document.getElementById('lista-profissionais');
    const filtro = document.getElementById('filtro-profissionais');
    const busca = document.getElementById('busca-profissional');
    const semResultadosFiltro = document.getElementById('sem-resultados-filtro');

    function formatarDataLocal(data) {
        const ano = data.getFullYear();
        const mes = String(data.getMonth() + 1).padStart(2, '0');
        const dia = String(data.getDate()).padStart(2, '0');

        return `${ano}-${mes}-${dia}`;
    }

    function normalizarTexto(texto) {
        return (texto || '')
            .toString()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .toLowerCase();
    }

    function formatarDataBR(dataIso) {
        const [ano, mes, dia] = dataIso.split('-');
        return `${dia}/${mes}/${ano}`;
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

    function mostrarEstado(mensagem) {
        estado.textContent = mensagem;
        estado.classList.remove('hidden');
        lista.classList.add('hidden');
        filtro.classList.add('hidden');
        semResultadosFiltro.classList.add('hidden');
    }

    function renderizarProfissionais(profissionais) {
        lista.innerHTML = '';
        busca.value = '';

        if (!profissionais.length) {
            mostrarEstado('Nenhum profissional disponivel nesse dia e horario.');
            return;
        }

        estado.classList.add('hidden');
        lista.classList.remove('hidden');
        filtro.classList.remove('hidden');

        profissionais.forEach((profissional) => {
            const servicos = profissional.servicos?.length ? profissional.servicos.join(', ') : 'Sem servicos vinculados';
            const especial = profissional.atendimento_especial
                ? `<span class="inline-flex mt-3 rounded-full bg-yellow-100 px-3 py-1 text-xs font-bold text-yellow-700">+${profissional.percentual_acrescimo}% especial</span>`
                : '';

            const card = document.createElement('article');
            card.className = 'profissional-card';
            card.dataset.profissionalCard = 'true';
            card.dataset.filterText = `${profissional.name} ${profissional.email || ''} ${profissional.telefone || ''} ${servicos}`;
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
                ${especial}
            `;

            lista.appendChild(card);
        });

        aplicarFiltroProfissionais();
    }

    formDisponibilidade.addEventListener('submit', async (event) => {
        event.preventDefault();

        if (!dataInput.value || !horaInput.value) {
            mostrarEstado('Informe data e horario para consultar.');
            return;
        }

        resumoConsulta.textContent = `Consulta para ${formatarDataBR(dataInput.value)} as ${horaInput.value}.`;
        resumoConsulta.classList.remove('hidden');
        mostrarEstado('Buscando profissionais disponiveis...');

        const params = new URLSearchParams({
            data: dataInput.value,
            hora: horaInput.value,
            duracao: duracaoInput.value,
        });

        try {
            const resposta = await fetch(`${urlProfissionaisDisponiveis}?${params.toString()}`, {
                headers: { Accept: 'application/json' },
            });

            if (!resposta.ok) {
                throw new Error('Nao foi possivel consultar agora.');
            }

            const dados = await resposta.json();
            renderizarProfissionais(dados.profissionais || []);
        } catch (erro) {
            mostrarEstado(erro.message || 'Nao foi possivel consultar agora.');
        }
    });

    busca.addEventListener('input', aplicarFiltroProfissionais);

    const hoje = new Date();
    const limite = new Date();
    limite.setMonth(limite.getMonth() + 3);
    dataInput.min = formatarDataLocal(hoje);
    dataInput.max = formatarDataLocal(limite);
    dataInput.value = formatarDataLocal(hoje);
</script>
</x-app-layout>
