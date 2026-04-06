    <?php

    use App\Http\Controllers\ProfileController;
    use Illuminate\Support\Facades\Route;
    use App\Http\Controllers\Admin\UserController;
    use App\Http\Controllers\ServicoController;
    use App\Http\Controllers\ProdutoController;
    use App\Http\Controllers\AgendamentoController;
    use App\Http\Controllers\FinanceiroController;

    Route::get('/', function () {
        return view('welcome');
    });

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->middleware(['auth', 'verified'])->name('dashboard');

    Route::middleware('auth')->group(function () {
        Route::get('/meu-agendamento', [AgendamentoController::class, 'clienteAgendar'])->name('cliente.agendar'); 
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
        Route::get('/agendar-servico', [AgendamentoController::class, 'criarAgendamentoCliente'])->name('cliente.agendar');
        Route::post('/agendar-servico', [AgendamentoController::class, 'storeCliente'])->name('cliente.agendar.salvar');
        Route::get('/meus-agendamentos', [AgendamentoController::class, 'indexCliente'])->name('cliente.index');
        Route::post('/agendamento/{id}/cancelar', [AgendamentoController::class, 'cancelarCliente'])->name('cliente.agendamento.cancelar');
        Route::post('/agendamentos/{id}/presenca', [AgendamentoController::class, 'confirmarPresenca'])->name('agendamento.presenca');
    });

    Route::middleware(['auth', 'gerente_ou_recepcionista'])->prefix('admin')->name('admin.')->group(function () {
        
        Route::resource('usuarios', UserController::class)->names([
            'index'   => 'usuarios.index',
            'create'  => 'usuarios.criar',
            'store'   => 'usuarios.salvar',
            'edit'    => 'usuarios.editar',
            'update'  => 'usuarios.atualizar',
            'destroy' => 'usuarios.deletar',
        ])->except(['show']);
        Route::get('clientes', [UserController::class, 'index'])->name('clientes.index');
        Route::get('profissionais', [UserController::class, 'index'])->name('profissionais.index');
        Route::get('agenda', [AgendamentoController::class, 'index'])->name('agenda.index');
        Route::post('agenda', [AgendamentoController::class, 'store'])->name('agenda.store');

    });
    Route::middleware(['auth', 'gerente'])->prefix('admin')->name('admin.')->group(function () {
        Route::resource('servicos', ServicoController::class)->names([
            'index'   => 'servicos.index',
            'create'  => 'servicos.criar',
            'store'   => 'servicos.salvar',
            'edit'    => 'servicos.editar',
            'update'  => 'servicos.atualizar',
            'destroy' => 'servicos.deletar',
        ])->parameters([
            'servicos' => 'servico'
        ])->except(['show']);
    });

    Route::middleware(['auth', 'gerente'])->prefix('admin')->name('admin.')->group(function () {
        Route::resource('produtos', ProdutoController::class)->names([
            'index'   => 'produtos.index',
            'create'  => 'produtos.criar',
            'store'   => 'produtos.salvar',
            'edit'    => 'produtos.editar',
            'update'  => 'produtos.atualizar',
            'destroy' => 'produtos.deletar',
        ])->parameters([
            'produtos' => 'produto'
        ])->except(['show']);
    });
        Route::middleware(['auth', 'profissional'])->prefix('profissional')->name('profissional.')->group(function () {
        Route::get('/configuracoes', [UserController::class, 'configuracoesservicos'])->name('servicos.editar');
        Route::put('/configuracoes', [UserController::class, 'atualizarconfiguracoesservicos'])->name('servicos.atualizar');
        Route::post('/agendamento/{id_agendamento}/executado', [AgendamentoController::class, 'marcarComoExecutado'])->name('agendamento.executado');
        Route::get('/minha-agenda', [AgendamentoController::class, 'agendaProfissional'])->name('agenda');
    });
    //rota para listar os agendamentos em formato JSON (para o FullCalendar) 
    Route::get('api/agendamentos', [AgendamentoController::class, 'listarJson'])->name('admin.agenda.json');
    // Coloque esta linha LOGO ANTES do final do arquivo, ou dentro do grupo 'gerente'
    Route::middleware(['auth', 'gerente'])->get('/admin/financeiro/fechamento', [FinanceiroController::class, 'fechamento'])->name('admin.financeiro.fechamento');
    Route::middleware(['auth', 'gerente'])->get('/admin/financeiro/comissoes', [FinanceiroController::class, 'comissoes'])->name('admin.financeiro.comissoes');
    require __DIR__.'/auth.php';
    