<x-app-layout>
    <x-slot name="header">
        <div class="hidden"></div>
    </x-slot>

    <div class="py-12 relative">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="glass-card rounded-2xl shadow-xl overflow-hidden">
                <div class="p-8 bg-white/70 backdrop-blur-sm border border-white/40">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 bg-gradient-to-br from-[#7B19E5] to-[#FF2EB6] rounded-xl flex items-center justify-center shadow-md">
                            <span class="text-white text-lg">✧</span>
                        </div>
                        <h1 class="text-2xl font-title text-[#4A00B9]">Agendamento</h1>
                    </div>

                    @if(session('status'))
                        <div class="mb-6 p-4 rounded-lg bg-green-50/80 border border-green-200 text-green-700">
                            ✧ {{ session('status') }}
                        </div>
                    @endif

                    <form action="{{ route('admin.agenda.store') }}" method="POST">
                        @csrf
                        
                        @if ($errors->any())
                            <div class="mb-6 p-4 rounded-lg bg-red-50/80 border border-red-200 text-red-700">
                                <ul class="list-disc list-inside">
                                    @foreach ($errors->all() as $error)
                                        <li>✧ {{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="grid grid-cols-1 gap-5">
                            <div>
                                <label class="block text-sm font-medium text-[#4A00B9] mb-2">Cliente</label>
                                <select name="cliente_id" 
                                    class="w-full px-4 py-3 bg-white/50 border border-[#FFD6F4] rounded-lg focus:outline-none focus:border-[#7B19E5] focus:ring-2 focus:ring-[#7B19E5]/20 transition-all">
                                    <option value="">Selecione um cliente</option>
                                    @foreach(\App\Models\User::where('cargo', 'cliente')->get() as $cliente)
                                        <option value="{{ $cliente->id }}">{{ $cliente->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-[#4A00B9] mb-2">Profissional</label>
                                <select name="profissional_id" 
                                    class="w-full px-4 py-3 bg-white/50 border border-[#FFD6F4] rounded-lg focus:outline-none focus:border-[#7B19E5] focus:ring-2 focus:ring-[#7B19E5]/20 transition-all">
                                    <option value="">Selecione um profissional</option>
                                    @foreach(\App\Models\User::where('cargo', 'profissional')->get() as $pro)
                                        <option value="{{ $pro->id }}">{{ $pro->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-[#4A00B9] mb-2">Serviço</label>
                                <div>
                                <label class="block text-sm font-medium text-[#4A00B9] mb-2">Quais serviços você deseja?</label>
                                <div class="space-y-3 max-h-96 overflow-y-auto p-4 bg-white/30 rounded-lg border border-[#FFD6F4]">
                                    
                                    <!-- Cabelo -->
                                    <div>
                                        <p class="text-sm font-medium text-[#7B19E5] mb-2 flex items-center gap-2">
                                            <span>✧</span> Cabelo
                                        </p>
                                        <div class="ml-6 space-y-2">
                                            <label class="flex items-center gap-3 p-2 rounded-lg hover:bg-white/50 cursor-pointer transition group">
                                                <input type="checkbox" name="servicos[]" value="Corte e finalização" class="w-4 h-4 text-[#7B19E5] rounded border-[#FFD6F4] focus:ring-[#7B19E5]">
                                                <span class="text-sm text-[#1A002B] group-hover:text-[#7B19E5] transition">Corte e finalização — R$ 89</span>
                                            </label>
                                            <label class="flex items-center gap-3 p-2 rounded-lg hover:bg-white/50 cursor-pointer transition group">
                                                <input type="checkbox" name="servicos[]" value="Progressiva e botox" class="w-4 h-4 text-[#7B19E5] rounded border-[#FFD6F4] focus:ring-[#7B19E5]">
                                                <span class="text-sm text-[#1A002B] group-hover:text-[#7B19E5] transition">Progressiva e botox — R$ 89</span>
                                            </label>
                                        </div>
                                    </div>

                                    <!-- Unhas -->
                                    <div>
                                        <p class="text-sm font-medium text-[#7B19E5] mb-2 flex items-center gap-2">
                                            <span>✧</span> Unhas
                                        </p>
                                        <div class="ml-6 space-y-2">
                                            <label class="flex items-center gap-3 p-2 rounded-lg hover:bg-white/50 cursor-pointer transition group">
                                                <input type="checkbox" name="servicos[]" value="Manicure e pedicure" class="w-4 h-4 text-[#7B19E5] rounded border-[#FFD6F4]">
                                                <span class="text-sm text-[#1A002B]">Manicure e pedicure — R$ 49</span>
                                            </label>
                                            <label class="flex items-center gap-3 p-2 rounded-lg hover:bg-white/50 cursor-pointer transition group">
                                                <input type="checkbox" name="servicos[]" value="Alongamento de fibra" class="w-4 h-4 text-[#7B19E5] rounded border-[#FFD6F4]">
                                                <span class="text-sm text-[#1A002B]">Alongamento de fibra — R$ 49</span>
                                            </label>
                                            <label class="flex items-center gap-3 p-2 rounded-lg hover:bg-white/50 cursor-pointer transition group">
                                                <input type="checkbox" name="servicos[]" value="Nail art exclusiva" class="w-4 h-4 text-[#7B19E5] rounded border-[#FFD6F4]">
                                                <span class="text-sm text-[#1A002B]">Nail art exclusiva — R$ 49</span>
                                            </label>
                                            <label class="flex items-center gap-3 p-2 rounded-lg hover:bg-white/50 cursor-pointer transition group">
                                                <input type="checkbox" name="servicos[]" value="Esmaltação em gel" class="w-4 h-4 text-[#7B19E5] rounded border-[#FFD6F4]">
                                                <span class="text-sm text-[#1A002B]">Esmaltação em gel — R$ 49</span>
                                            </label>
                                        </div>
                                    </div>

                                    <!-- Maquiagem -->
                                    <div>
                                        <p class="text-sm font-medium text-[#7B19E5] mb-2 flex items-center gap-2">
                                            <span>✧</span> Maquiagem
                                        </p>
                                        <div class="ml-6 space-y-2">
                                            <label class="flex items-center gap-3 p-2 rounded-lg hover:bg-white/50 cursor-pointer transition group">
                                                <input type="checkbox" name="servicos[]" value="Social e noiva" class="w-4 h-4 text-[#7B19E5] rounded border-[#FFD6F4]">
                                                <span class="text-sm text-[#1A002B]">Social e noiva — R$ 79</span>
                                            </label>
                                            <label class="flex items-center gap-3 p-2 rounded-lg hover:bg-white/50 cursor-pointer transition group">
                                                <input type="checkbox" name="servicos[]" value="Make artística" class="w-4 h-4 text-[#7B19E5] rounded border-[#FFD6F4]">
                                                <span class="text-sm text-[#1A002B]">Make artística — R$ 79</span>
                                            </label>
                                        </div>
                                    </div>

                                    <!-- Sobrancelhas -->
                                    <div>
                                        <p class="text-sm font-medium text-[#7B19E5] mb-2 flex items-center gap-2">
                                            <span>✧</span> Sobrancelhas
                                        </p>
                                        <div class="ml-6 space-y-2">
                                            <label class="flex items-center gap-3 p-2 rounded-lg hover:bg-white/50 cursor-pointer transition group">
                                                <input type="checkbox" name="servicos[]" value="Design tradicional" class="w-4 h-4 text-[#7B19E5] rounded border-[#FFD6F4]">
                                                <span class="text-sm text-[#1A002B]">Design tradicional — R$ 39</span>
                                            </label>
                                            <label class="flex items-center gap-3 p-2 rounded-lg hover:bg-white/50 cursor-pointer transition group">
                                                <input type="checkbox" name="servicos[]" value="Henna e fio a fio" class="w-4 h-4 text-[#7B19E5] rounded border-[#FFD6F4]">
                                                <span class="text-sm text-[#1A002B]">Henna e fio a fio — R$ 39</span>
                                            </label>
                                        </div>
                                    </div>

                                    <!-- Tratamentos -->
                                    <div>
                                        <p class="text-sm font-medium text-[#7B19E5] mb-2 flex items-center gap-2">
                                            <span>✧</span> Tratamentos
                                        </p>
                                        <div class="ml-6 space-y-2">
                                            <label class="flex items-center gap-3 p-2 rounded-lg hover:bg-white/50 cursor-pointer transition group">
                                                <input type="checkbox" name="servicos[]" value="Hidratação, nutrição e reconstrução" class="w-4 h-4 text-[#7B19E5] rounded border-[#FFD6F4]">
                                                <span class="text-sm text-[#1A002B]">Hidratação, nutrição e reconstrução — R$ 59</span>
                                            </label>
                                        </div>
                                    </div>

                                    <!-- Coloração -->
                                    <div>
                                        <p class="text-sm font-medium text-[#7B19E5] mb-2 flex items-center gap-2">
                                            <span>✧</span> Coloração
                                        </p>
                                        <div class="ml-6 space-y-2">
                                            <label class="flex items-center gap-3 p-2 rounded-lg hover:bg-white/50 cursor-pointer transition group">
                                                <input type="checkbox" name="servicos[]" value="Mechas, luzes e coloração completa" class="w-4 h-4 text-[#7B19E5] rounded border-[#FFD6F4]">
                                                <span class="text-sm text-[#1A002B]">Mechas, luzes e coloração completa — R$ 129</span>
                                            </label>
                                        </div>
                                    </div>

                                    <!-- Penteados -->
                                    <div>
                                        <p class="text-sm font-medium text-[#7B19E5] mb-2 flex items-center gap-2">
                                            <span>✧</span> Penteados
                                        </p>
                                        <div class="ml-6 space-y-2">
                                            <label class="flex items-center gap-3 p-2 rounded-lg hover:bg-white/50 cursor-pointer transition group">
                                                <input type="checkbox" name="servicos[]" value="Festas, noivas e eventos especiais" class="w-4 h-4 text-[#7B19E5] rounded border-[#FFD6F4]">
                                                <span class="text-sm text-[#1A002B]">Festas, noivas e eventos especiais — R$ 69</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">✧ Marque quantos serviços quiser</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-[#4A00B9] mb-2">Data e Hora do Início</label>
                                <input type="datetime-local" name="data_hora" 
                                    class="w-full px-4 py-3 bg-white/50 border border-[#FFD6F4] rounded-lg focus:outline-none focus:border-[#7B19E5] focus:ring-2 focus:ring-[#7B19E5]/20 transition-all">
                            </div>

                            <button type="submit" class="w-full bg-gradient-to-r from-[#7B19E5] to-[#FF2EB6] text-white py-4 text-sm rounded-full font-medium btn-primary shadow-lg hover:shadow-xl transition-all mt-4">
                                Salvar Agendamento
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

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