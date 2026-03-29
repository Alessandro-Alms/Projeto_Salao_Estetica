<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="bg-white p-6 rounded-xl shadow-sm border border-pink-100 flex items-center justify-between">
        <div>
            <h3 class="text-lg font-bold text-gray-800">Meus Serviços</h3>
            <p class="text-sm text-gray-500">Configure o que você faz e suas comissões.</p>
        </div>
        <a href="{{ route('profissional.servicos.editar') }}" class="bg-pink-600 text-white px-4 py-2 rounded-lg font-bold text-sm hover:bg-pink-700 transition">
            Configurar
        </a>
    </div>

    <div class="bg-white p-6 rounded-xl shadow-sm border border-blue-100 flex items-center justify-between">
        <div>
            <h3 class="text-lg font-bold text-gray-800">Minha Agenda</h3>
            <p class="text-sm text-gray-500">Defina seus horários de trabalho semanais.</p>
        </div>
        <a href="{{ route('profissional.servicos.editar') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg font-bold text-sm hover:bg-blue-700 transition">
            Ajustar Horários
        </a>
    </div>
</div>
<div class="bg-white p-6 mt-6 rounded-xl shadow-sm border border-gray-200">
    <h2 class="text-xl font-bold text-gray-800 mb-4">Próximos Atendimentos</h2>
    <p class="text-sm text-gray-500">Veja os próximos agendamentos que você tem.</p>

    <div class="mt-4">
        <a href="{{ route('profissional.agenda') }}" class="text-pink-600 font-bold hover:underline">
            Ver Minha Agenda Completa
        </a>
    </div>
</div>