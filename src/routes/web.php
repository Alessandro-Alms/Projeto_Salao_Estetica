<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ServicoController;

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
    ])->parameters([
        'usuarios' => 'user'
    ])->except(['show']);

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


require __DIR__.'/auth.php';
