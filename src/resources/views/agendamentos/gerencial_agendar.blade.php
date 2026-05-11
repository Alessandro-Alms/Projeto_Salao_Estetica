<x-app-layout>
    <div class="py-12 relative">
        <!-- Fundo -->
        <div class="fixed inset-0 -z-10 overflow-hidden">
            <div class="absolute top-0 -left-20 w-[600px] h-[600px] bg-[#7B19E5]/20 rounded-full blur-3xl"></div>
            <div class="absolute bottom-0 -right-20 w-[700px] h-[700px] bg-[#FF2EB6]/20 rounded-full blur-3xl"></div>
            <div class="absolute top-1/3 left-1/3 w-[400px] h-[400px] bg-[#A955D3]/15 rounded-full blur-3xl"></div>
            <div class="absolute inset-0 bg-gradient-to-br from-[#7B19E5]/5 via-white/30 to-[#FF2EB6]/5"></div>
        </div>

        <div class="max-w-2xl mx-auto px-4">
            <div class="glass-card rounded-2xl shadow-xl overflow-hidden">
                <div class="p-8 bg-white/70 backdrop-blur-sm border border-white/40">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 bg-gradient-to-br from-[#7B19E5] to-[#FF2EB6] rounded-xl flex items-center justify-center shadow-md">
                            <span class="text-white text-lg">✧</span>
                        </div>
                        <h1 class="text-2xl font-title text-[#4A00B9]">Agendar para Cliente</h1>
                    </div>

                    @if ($errors->any())
                        <div class="mb-6 p-4 rounded-lg bg-red-50/80 border border-red-200 text-red-700">
                            <p class="font-medium mb-1">✧ Não foi possível agendar:</p>
                            <ul class="list-disc list-inside text-sm">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('admin.agendar.cliente.salvar') }}" method="POST" id="form-agendar">
                        @csrf
                        
                        <div class="space-y-5">
                            <!-- Cliente - Busca por nome -->
                            <div>
                                <label class="block text-sm font-medium text-[#4A00B9] mb-2">✧ Selecione o Cliente</label>
                                <div class="relative">
                                    <input type="text" id="cliente-search" placeholder="Digite o nome do cliente..." 
                                        class="w-full px-4 py-3 bg-white/50 border border-[#FFD6F4] rounded-lg focus:outline-none focus:border-[#7B19E5] focus:ring-2 focus:ring-[#7B19E5]/20 transition-all">
                                    <div id="cliente-dropdown" class="hidden absolute top-full left-0 right-0 mt-1 bg-white/95 border border-[#FFD6F4] rounded-lg shadow-lg z-50 max-h-60 overflow-y-auto">
                                        @foreach($clientes as $cliente)
                                            <div class="px-4 py-3 hover:bg-[#7B19E5]/10 cursor-pointer cliente-option" data-id="{{ $cliente->id }}" data-name="{{ $cliente->name }}">
                                                <p class="font-medium text-[#4A00B9]">{{ $cliente->name }}</p>
                                                <p class="text-xs text-gray-500">{{ $cliente->email }}</p>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <input type="hidden" name="cliente_id" id="cliente-id" value="{{ old('cliente_id') }}" required>
                                <div id="cliente-selecionado" class="mt-2 p-3 bg-green-50/50 border border-green-200 rounded-lg hidden">
                                    <p class="text-sm text-green-700"><strong>Cliente selecionado:</strong> <span id="cliente-nome"></span></p>
                                </div>
                            </div>

                            <!-- Profissional - Busca por nome -->
                            <div>
                                <label class="block text-sm font-medium text-[#4A00B9] mb-2">✦ Selecione o Profissional</label>
                                <div class="relative">
                                    <input type="text" id="profissional-search" placeholder="Digite o nome do profissional..." 
                                        class="w-full px-4 py-3 bg-white/50 border border-[#FFD6F4] rounded-lg focus:outline-none focus:border-[#7B19E5] focus:ring-2 focus:ring-[#7B19E5]/20 transition-all">
                                    <div id="profissional-dropdown" class="hidden absolute top-full left-0 right-0 mt-1 bg-white/95 border border-[#FFD6F4] rounded-lg shadow-lg z-50 max-h-60 overflow-y-auto">
                                        @foreach($profissionais as $prof)
                                            <div class="px-4 py-3 hover:bg-[#7B19E5]/10 cursor-pointer profissional-option" data-id="{{ $prof->id }}" data-name="{{ $prof->name }}">
                                                <p class="font-medium text-[#4A00B9]">{{ $prof->name }}</p>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <input type="hidden" name="profissional_id" id="profissional-id" value="{{ old('profissional_id') }}" required>
                                <div id="profissional-selecionado" class="mt-2 p-3 bg-green-50/50 border border-green-200 rounded-lg hidden">
                                    <p class="text-sm text-green-700"><strong>Profissional selecionado:</strong> <span id="profissional-nome"></span></p>
                                </div>
                            </div>

                            <!-- Serviço - Single select -->
                            <div>
                                <label class="block text-sm font-medium text-[#4A00B9] mb-2">✧ Selecione o Serviço</label>
                                <select name="servico_id"
                                    data-searchable-select
                                    data-searchable-placeholder="Digite o nome do serviço..."
                                    class="w-full px-4 py-3 bg-white/50 border border-[#FFD6F4] rounded-lg focus:outline-none focus:border-[#7B19E5] focus:ring-2 focus:ring-[#7B19E5]/20 transition-all"
                                    id="servico-select" required>
                                    <option value="">Selecione o serviço</option>
                                    @foreach($servicos as $servico)
                                        <option value="{{ $servico->id_servico }}" data-preco="{{ $servico->preco }}" {{ old('servico_id') == $servico->id_servico ? 'selected' : '' }}>
                                            {{ $servico->nome }} - R$ {{ number_format($servico->preco, 2, ',', '.') }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Data e Hora -->
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-[#4A00B9] mb-2">✧ Data</label>
                                    <input type="date" name="data" id="data-agendamento"
                                        class="w-full px-4 py-3 bg-white/50 border border-[#FFD6F4] rounded-lg focus:outline-none focus:border-[#7B19E5] focus:ring-2 focus:ring-[#7B19E5]/20 transition-all"
                                        min="{{ now()->format('Y-m-d') }}" value="{{ old('data') }}" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-[#4A00B9] mb-2">✦ Hora</label>
                                    <input type="time" name="hora" 
                                        class="w-full px-4 py-3 bg-white/50 border border-[#FFD6F4] rounded-lg focus:outline-none focus:border-[#7B19E5] focus:ring-2 focus:ring-[#7B19E5]/20 transition-all"
                                        value="{{ old('hora') }}" required>
                                </div>
                            </div>

                            <!-- Botões -->
                            <div id="aviso-fim-semana" class="hidden rounded-xl border border-yellow-200 bg-yellow-50 p-4 text-sm text-yellow-800">
                                <strong>✧ Acréscimo de fim de semana:</strong> sábados e domingos funcionam em horário normal, mas o serviço recebe +25%. A comissão do profissional também considera esse valor maior.
                                <p id="preview-fim-semana" class="mt-1 font-bold"></p>
                            </div>

                            <div class="flex gap-4 pt-4">
                                <a href="{{ route('dashboard') }}" class="flex-1 px-4 py-3 bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium rounded-full transition-all text-center">
                                    ← Cancelar
                                </a>
                                <button type="submit" class="flex-1 px-4 py-3 bg-gradient-to-r from-[#7B19E5] to-[#FF2EB6] hover:shadow-lg text-white font-medium rounded-full transition-all">
                                    Confirmar agendamento
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700;800&family=Playfair+Display:wght@700&family=Space+Grotesk:wght@300;400;500;600;700&display=swap');
        
        .font-title {
            font-family: 'Playfair Display', serif;
            font-weight: 700;
            letter-spacing: -0.02em;
        }
        
        .glass-card {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 8px 32px rgba(123, 25, 229, 0.1);
        }
    </style>

    <script>
        // ===== BUSCA DE CLIENTE =====
        const clienteSearch = document.getElementById('cliente-search');
        const clienteDropdown = document.getElementById('cliente-dropdown');
        const clienteId = document.getElementById('cliente-id');
        const clienteSelecionado = document.getElementById('cliente-selecionado');
        const clienteNome = document.getElementById('cliente-nome');

        clienteSearch.addEventListener('focus', () => clienteDropdown.classList.remove('hidden'));
        clienteSearch.addEventListener('input', (e) => {
            const valor = e.target.value.toLowerCase();
            const opcoes = clienteDropdown.querySelectorAll('.cliente-option');
            
            opcoes.forEach(opcao => {
                const nome = opcao.dataset.name.toLowerCase();
                opcao.style.display = nome.includes(valor) ? 'block' : 'none';
            });
        });

        document.addEventListener('click', (e) => {
            if (!e.target.closest('#cliente-search') && !e.target.closest('#cliente-dropdown')) {
                clienteDropdown.classList.add('hidden');
            }
        });

        document.querySelectorAll('.cliente-option').forEach(opcao => {
            opcao.addEventListener('click', () => {
                const id = opcao.dataset.id;
                const nome = opcao.dataset.name;
                clienteId.value = id;
                clienteSearch.value = nome;
                clienteDropdown.classList.add('hidden');
                clienteSelecionado.classList.remove('hidden');
                clienteNome.textContent = nome;
            });
        });

        // ===== BUSCA DE PROFISSIONAL =====
        const profissionalSearch = document.getElementById('profissional-search');
        const profissionalDropdown = document.getElementById('profissional-dropdown');
        const profissionalId = document.getElementById('profissional-id');
        const profissionalSelecionado = document.getElementById('profissional-selecionado');
        const profissionalNome = document.getElementById('profissional-nome');
        const dataAgendamento = document.getElementById('data-agendamento');
        const servicoSelect = document.getElementById('servico-select');
        const avisoFimSemana = document.getElementById('aviso-fim-semana');
        const previewFimSemana = document.getElementById('preview-fim-semana');

        profissionalSearch.addEventListener('focus', () => profissionalDropdown.classList.remove('hidden'));
        profissionalSearch.addEventListener('input', (e) => {
            const valor = e.target.value.toLowerCase();
            const opcoes = profissionalDropdown.querySelectorAll('.profissional-option');
            
            opcoes.forEach(opcao => {
                const nome = opcao.dataset.name.toLowerCase();
                opcao.style.display = nome.includes(valor) ? 'block' : 'none';
            });
        });

        document.addEventListener('click', (e) => {
            if (!e.target.closest('#profissional-search') && !e.target.closest('#profissional-dropdown')) {
                profissionalDropdown.classList.add('hidden');
            }
        });

        document.querySelectorAll('.profissional-option').forEach(opcao => {
            opcao.addEventListener('click', () => {
                const id = opcao.dataset.id;
                const nome = opcao.dataset.name;
                profissionalId.value = id;
                profissionalSearch.value = nome;
                profissionalDropdown.classList.add('hidden');
                profissionalSelecionado.classList.remove('hidden');
                profissionalNome.textContent = nome;
            });
        });

        function formatarMoeda(valor) {
            return Number(valor || 0).toLocaleString('pt-BR', {
                style: 'currency',
                currency: 'BRL'
            });
        }

        function atualizarAvisoFimSemana() {
            const data = dataAgendamento.value;
            const opcaoServico = servicoSelect.selectedOptions[0];
            const preco = Number(opcaoServico?.dataset.preco || 0);

            if (!data) {
                avisoFimSemana.classList.add('hidden');
                return;
            }

            const diaSemana = new Date(`${data}T12:00:00`).getDay();
            const fimDeSemana = diaSemana === 0 || diaSemana === 6;

            if (!fimDeSemana) {
                avisoFimSemana.classList.add('hidden');
                return;
            }

            const acrescimo = preco * 0.25;
            const total = preco + acrescimo;
            previewFimSemana.textContent = preco > 0
                ? `Prévia: ${formatarMoeda(preco)} + ${formatarMoeda(acrescimo)} = ${formatarMoeda(total)}.`
                : 'Selecione o serviço para ver a prévia do valor.';
            avisoFimSemana.classList.remove('hidden');
        }

        dataAgendamento.addEventListener('change', atualizarAvisoFimSemana);
        servicoSelect.addEventListener('change', atualizarAvisoFimSemana);
        atualizarAvisoFimSemana();
    </script>
</x-app-layout>
