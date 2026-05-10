<section class="space-y-6">
    <header>
        <h2 class="text-lg font-title text-[#FF2EB6]">
            {{ __('Excluir conta') }}
        </h2>

        <p class="mt-1 text-sm text-gray-500">
            {{ __('Uma vez que sua conta for excluída, todos os seus dados serão permanentemente removidos. Antes de excluir, baixe qualquer informação que deseja manter.') }}
        </p>
    </header>

    <button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        class="bg-gradient-to-r from-[#FF2EB6] to-[#FF69B4] text-white px-6 py-2.5 text-sm rounded-full font-medium btn-primary shadow-md hover:shadow-lg transition-all"
    >
        {{ __('EXCLUIR CONTA') }}
    </button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6 bg-white/95 backdrop-blur-sm rounded-2xl">
            @csrf
            @method('delete')

            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 bg-gradient-to-br from-[#FF2EB6] to-[#FF69B4] rounded-xl flex items-center justify-center shadow-md">
                    <span class="text-white text-lg">✧</span>
                </div>
                <h2 class="text-lg font-title text-[#FF2EB6]">
                    {{ __('Tem certeza que deseja excluir sua conta?') }}
                </h2>
            </div>

            <p class="mt-1 text-sm text-gray-500">
                {{ __('Uma vez que sua conta for excluída, todos os seus dados serão permanentemente removidos. Digite sua senha para confirmar.') }}
            </p>

            <div class="mt-6">
                <label for="password" class="block text-sm font-medium text-[#4A00B9] mb-2">{{ __('Senha') }}</label>
                <input
                    id="password"
                    name="password"
                    type="password"
                    class="w-full px-4 py-3 bg-white/50 border border-[#FFD6F4] rounded-lg focus:outline-none focus:border-[#FF2EB6] focus:ring-2 focus:ring-[#FF2EB6]/20 transition-all"
                    placeholder="{{ __('Digite sua senha') }}"
                />
                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2 text-red-500 text-xs" />
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <button type="button" x-on:click="$dispatch('close')" class="px-6 py-2 text-sm text-gray-500 hover:text-[#7B19E5] transition-colors">
                    {{ __('Cancelar') }}
                </button>

                <button type="submit" class="bg-gradient-to-r from-[#FF2EB6] to-[#FF69B4] text-white px-6 py-2 text-sm rounded-full font-medium btn-primary shadow-md hover:shadow-lg transition-all">
                    {{ __('EXCLUIR CONTA') }}
                </button>
            </div>
        </form>
    </x-modal>
</section>

<style>
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
