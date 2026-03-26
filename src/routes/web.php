    <?php

    use App\Http\Controllers\ProfileController;
    use Illuminate\Support\Facades\Route;
    use App\Http\Controllers\Admin\UserController;
    use App\Http\Controllers\ServicoController;
    use App\Http\Controllers\ProdutoController;

    Route::get('/', function () {
        return view('welcome');
    });

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->middleware(['auth', 'verified'])->name('dashboard');

    Route::middleware('auth')->group(function () {
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
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


    require __DIR__.'/auth.php';
    