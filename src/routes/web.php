<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ServicoController;
use App\Http\Controllers\ProdutoController;
use App\Http\Controllers\AgendamentoController;
use App\Http\Controllers\FinanceiroController;
use App\Http\Controllers\BloqueioController;
use App\Http\Controllers\PacoteController;
use App\Http\Controllers\ClientePacoteController;
use App\Http\Controllers\RelatorioController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// ✅ Página de agendamento (Adicionado pelo Front-end)
Route::get('/agendar', function () {
    return view('agendar');
})->name('agendar');

Route::middleware('auth')->group(function () {
    Route::get('/meu-agendamento', [AgendamentoController::class, 'clienteAgendar'])->name('cliente.agendar.novo'); 
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/agendar-servico', [AgendamentoController::class, 'clienteAgendar'])->name('cliente.agendar');
    Route::post('/agendar-servico', [AgendamentoController::class, 'storeCliente'])->name('cliente.agendar.salvar');
    Route::get('/meus-agendamentos', [AgendamentoController::class, 'indexCliente'])->name('cliente.index');
    Route::post('/agendamento/{id}/cancelar', [AgendamentoController::class, 'cancelarCliente'])->name('cliente.agendamento.cancelar');
    Route::post('/agendamentos/{id}/presenca', [AgendamentoController::class, 'confirmarPresenca'])->name('agendamento.presenca');
    Route::patch('/agendamentos/{id}/falta', [AgendamentoController::class, 'marcarFalta'])->name('agendamentos.falta');
    Route::get('/admin/pacotes/venda', [ClientePacoteController::class, 'create'])->name('admin.venda.create');
    Route::post('/admin/pacotes/venda', [ClientePacoteController::class, 'store'])->name('admin.venda.store');
    Route::post('/avaliar-agendamento', [AgendamentoController::class, 'salvarAvaliacao'])->name('cliente.avaliar.salvar');
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

// Agrupei todas as rotas do Gerente num lugar só para ficar mais limpo
Route::middleware(['auth', 'gerente'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('servicos', ServicoController::class)->names([
        'index'   => 'servicos.index',
        'create'  => 'servicos.criar',
        'store'   => 'servicos.salvar',
        'edit'    => 'servicos.editar',
        'update'  => 'servicos.atualizar',
        'destroy' => 'servicos.deletar',
    ])->parameters(['servicos' => 'servico'])->except(['show']);

    Route::resource('produtos', ProdutoController::class)->names([
        'index'   => 'produtos.index',
        'create'  => 'produtos.criar',
        'store'   => 'produtos.salvar',
        'edit'    => 'produtos.editar',
        'update'  => 'produtos.atualizar',
        'destroy' => 'produtos.deletar',
    ])->parameters(['produtos' => 'produto'])->except(['show']);
    
    Route::resource('bloqueios', BloqueioController::class)->except(['show', 'edit', 'update']);
    Route::patch('/usuarios/{id}/status', [UserController::class, 'alterarStatus'])->name('usuarios.status');
    
    Route::get('/financeiro/fechamento', [FinanceiroController::class, 'fechamento'])->name('financeiro.fechamento');
    Route::get('/financeiro/comissoes', [FinanceiroController::class, 'comissoes'])->name('financeiro.comissoes');
    Route::get('/pacotes', [PacoteController::class, 'index'])->name('pacotes.index');
    Route::post('/pacotes', [PacoteController::class, 'store'])->name('pacotes.store');
    Route::get('/pacotes/{id}/editar', [PacoteController::class, 'edit'])->name('pacotes.edit');
    Route::put('/pacotes/{id}', [PacoteController::class, 'update'])->name('pacotes.update');
    Route::delete('/pacotes/{id}', [PacoteController::class, 'destroy'])->name('pacotes.destroy');
    Route::get('/relatorios', [RelatorioController::class, 'index'])->name('relatorios.index');
    Route::get('/relatorios/exportar-pdf', [RelatorioController::class, 'exportarPdf'])->name('relatorios.exportarPdf');
    Route::get('/relatorios/exportar-excel', [RelatorioController::class, 'exportarExcel'])->name('relatorios.exportarExcel');
    Route::get('/relatorios/faturamento', [RelatorioController::class, 'faturamento'])->name('relatorios.faturamento');
    Route::get('/relatorios/ocupacao', [RelatorioController::class, 'ocupacao'])->name('relatorios.ocupacao');
    Route::get('/relatorios/desempenho', [RelatorioController::class, 'desempenho'])->name('relatorios.desempenho');
    Route::get('/relatorios/produtos', [RelatorioController::class, 'produtos'])->name('relatorios.produtos');
    Route::get('/relatorios/fidelizacao', [RelatorioController::class, 'fidelizacao'])->name('relatorios.fidelizacao');
    Route::get('/relatorios/cancelamentos', [RelatorioController::class, 'cancelamentos'])->name('relatorios.cancelamentos');
    Route::get('/relatorios/financeiro', [RelatorioController::class, 'financeiro'])->name('relatorios.financeiro');
    Route::get('/relatorios/comissoes', [RelatorioController::class, 'comissoes'])->name('relatorios.comissoes');
    Route::get('/relatorios/estoque', [RelatorioController::class, 'estoque'])->name('relatorios.estoque');
    Route::get('/relatorios/sazonalidade', [RelatorioController::class, 'sazonalidade'])->name('relatorios.sazonalidade');
    Route::get('/relatorios/avaliacoes', [RelatorioController::class, 'avaliacoes'])->name('relatorios.avaliacoes');
    Route::get('/relatorios/previsao', [RelatorioController::class, 'previsao'])->name('relatorios.previsao');
});

Route::middleware(['auth', 'profissional'])->prefix('profissional')->name('profissional.')->group(function () {
    Route::get('/configuracoes', [UserController::class, 'configuracoesservicos'])->name('servicos.editar');
    Route::put('/configuracoes', [UserController::class, 'atualizarconfiguracoesservicos'])->name('servicos.atualizar');
    Route::post('/agendamento/{id_agendamento}/executado', [AgendamentoController::class, 'marcarComoExecutado'])->name('agendamento.executado');
    Route::get('/minha-agenda', [AgendamentoController::class, 'agendaProfissional'])->name('agenda');
    Route::get('/extrato', [UserController::class, 'extrato'])->name('extrato');
});

// rota para listar os agendamentos em formato JSON (para o FullCalendar) 
Route::get('api/agendamentos', [AgendamentoController::class, 'listarJson'])->name('admin.agenda.json');

require __DIR__.'/auth.php';