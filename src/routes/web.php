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
use App\Http\Controllers\VendaProdutoController;
use App\Http\Controllers\RelatorioController;
use App\Models\Produto;
use App\Models\Servico;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;

Route::get('/', function () {
    $servicosDestaque = Schema::hasTable('servicos')
        ? Servico::orderBy('nome')->limit(4)->get()
        : collect();
    $produtosDestaque = Schema::hasTable('produtos')
        ? Produto::where('quantidade_estoque', '>', 0)->orderBy('nome')->limit(4)->get()
        : collect();
    $depoimentosDestaque = Schema::hasTable('avaliacoes') && Schema::hasTable('users')
        ? DB::table('avaliacoes')
        ->join('users as clientes', 'avaliacoes.cliente_id', '=', 'clientes.id')
        ->leftJoin('users as profissionais', 'avaliacoes.profissional_id', '=', 'profissionais.id')
        ->whereNotNull('avaliacoes.comentario')
        ->where('avaliacoes.comentario', '!=', '')
        ->orderByDesc('avaliacoes.nota')
        ->orderByDesc('avaliacoes.created_at')
        ->limit(3)
        ->select(
            'avaliacoes.nota',
            'avaliacoes.comentario',
            'clientes.name as cliente_nome',
            'profissionais.name as profissional_nome'
        )
        ->get()
        : collect();

    return view('welcome', compact('servicosDestaque', 'produtosDestaque', 'depoimentosDestaque'));
})->name('public.home');

Route::get('/servicos', function () {
    $titulo = 'Todos os servicos';
    $subtitulo = 'Confira todos os servicos disponiveis no salao.';
    $tipo = 'servicos';
    $itens = Schema::hasTable('servicos') ? Servico::orderBy('nome')->get() : collect();

    return view('public.catalogo', compact('titulo', 'subtitulo', 'tipo', 'itens'));
})->name('public.servicos');

Route::get('/produtos', function () {
    $titulo = 'Todos os produtos';
    $subtitulo = 'Produtos profissionais disponiveis para compra no salao.';
    $tipo = 'produtos';
    $itens = Schema::hasTable('produtos') ? Produto::where('quantidade_estoque', '>', 0)->orderBy('nome')->get() : collect();

    return view('public.catalogo', compact('titulo', 'subtitulo', 'tipo', 'itens'));
})->name('public.produtos');

Route::get('/depoimentos', function () {
    $titulo = 'Depoimentos';
    $subtitulo = 'Avaliacoes reais deixadas pelas nossas clientes.';
    $tipo = 'depoimentos';
    $itens = Schema::hasTable('avaliacoes') && Schema::hasTable('users')
        ? DB::table('avaliacoes')
        ->join('users as clientes', 'avaliacoes.cliente_id', '=', 'clientes.id')
        ->leftJoin('users as profissionais', 'avaliacoes.profissional_id', '=', 'profissionais.id')
        ->whereNotNull('avaliacoes.comentario')
        ->where('avaliacoes.comentario', '!=', '')
        ->orderByDesc('avaliacoes.created_at')
        ->select(
            'avaliacoes.nota',
            'avaliacoes.comentario',
            'clientes.name as cliente_nome',
            'profissionais.name as profissional_nome',
            'avaliacoes.created_at'
        )
        ->get()
        : collect();

    return view('public.catalogo', compact('titulo', 'subtitulo', 'tipo', 'itens'));
})->name('public.depoimentos');

Route::post('/contato/enviar', function (Request $request) {
    $dados = $request->validate([
        'nome' => ['required', 'string', 'max:120'],
        'email' => ['required', 'email', 'max:255'],
        'assunto' => ['required', 'string', 'max:160'],
        'mensagem' => ['required', 'string', 'max:2000'],
    ]);

    $destinatario = env('CONTACT_TO_ADDRESS', config('mail.from.address'));

    $conteudo = implode(PHP_EOL, [
        'Nova mensagem enviada pelo site Cheias de Charme',
        '',
        'Nome: ' . $dados['nome'],
        'E-mail: ' . $dados['email'],
        'Assunto: ' . $dados['assunto'],
        '',
        'Mensagem:',
        $dados['mensagem'],
    ]);

    Mail::raw($conteudo, function ($message) use ($dados, $destinatario) {
        $message->to($destinatario)
            ->replyTo($dados['email'], $dados['nome'])
            ->subject('Contato pelo site: ' . $dados['assunto']);
    });

    if (Schema::hasTable('contato_mensagens')) {
        DB::table('contato_mensagens')->insert([
            'nome' => $dados['nome'],
            'email' => $dados['email'],
            'assunto' => $dados['assunto'],
            'mensagem' => $dados['mensagem'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    Log::info('Mensagem de contato recebida pelo site.', [
        'nome' => $dados['nome'],
        'email' => $dados['email'],
        'assunto' => $dados['assunto'],
    ]);

    return redirect()
        ->route('public.home')
        ->withFragment('contato')
        ->with('contato_sucesso', 'Mensagem enviada com sucesso! Em breve entraremos em contato.');
})->name('public.contato.enviar');

Route::get('/dashboard', [UserController::class, 'dashboard'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// ✅ Redirecionamento para o formulário de agendamento do cliente
Route::get('/agendar', function () {
    return redirect()->route('cliente.agendar.novo');
})->name('agendar');

// ==========================================
// ROTAS DO CLIENTE (Exige Login)
// ==========================================
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // ✅ Mantido intacto para não quebrar a view atual
    Route::get('/agendar-servico', [AgendamentoController::class, 'clienteAgendar'])
        ->middleware('cliente_redirect:agendar')
        ->name('cliente.agendar');
    Route::post('/agendar-servico', [AgendamentoController::class, 'storeCliente'])
        ->middleware('cliente_redirect:agendar')
        ->name('cliente.agendar.salvar');
    
    // ======== NOVAS ROTAS DO WIZARD ========
    Route::get('/agendar-novo', [AgendamentoController::class, 'novoAgendamento'])
        ->middleware('cliente_redirect:agendar')
        ->name('cliente.agendar.novo');
    
    // ✅ CORREÇÃO 1: Nomes adicionados às rotas AJAX
    Route::get('/api/profissionais-por-servico', [AgendamentoController::class, 'getProfissionaisAjax'])->name('api.profissionais');
    Route::get('/api/horarios-disponiveis', [AgendamentoController::class, 'getHorariosAjax'])->name('api.horarios');
    // =======================================

    Route::get('/meus-agendamentos', [AgendamentoController::class, 'indexCliente'])
        ->middleware('cliente_redirect:agendamentos')
        ->name('cliente.index');
    Route::get('/meus-produtos', [VendaProdutoController::class, 'indexCliente'])
        ->middleware('cliente_redirect:produtos')
        ->name('cliente.produtos.index');
    Route::get('/minhas-compras', [VendaProdutoController::class, 'historicoCliente'])
        ->middleware('cliente_redirect:produtos')
        ->name('cliente.compras.index');
    Route::post('/meus-produtos/comprar', [VendaProdutoController::class, 'comprarCliente'])
        ->middleware('cliente_redirect:produtos')
        ->name('cliente.produtos.comprar');
    Route::patch('/meus-produtos/comanda/cancelar-tudo', [VendaProdutoController::class, 'cancelarComandaCliente'])
        ->middleware('cliente_redirect:produtos')
        ->name('cliente.produtos.comanda.cancelar-tudo');
    Route::patch('/meus-produtos/pedido/produto/{produto:id_produto}', [VendaProdutoController::class, 'atualizarProdutoCliente'])
        ->middleware('cliente_redirect:produtos')
        ->name('cliente.produtos.pedido.produto.atualizar');
    Route::patch('/meus-produtos/pedido/produto/{produto:id_produto}/cancelar', [VendaProdutoController::class, 'cancelarProdutoCliente'])
        ->middleware('cliente_redirect:produtos')
        ->name('cliente.produtos.pedido.produto.cancelar');
    Route::patch('/meus-produtos/comanda/{venda}', [VendaProdutoController::class, 'atualizarCliente'])
        ->middleware('cliente_redirect:produtos')
        ->name('cliente.produtos.comanda.atualizar');
    Route::patch('/meus-produtos/comanda/{venda}/cancelar', [VendaProdutoController::class, 'cancelarCliente'])
        ->middleware('cliente_redirect:produtos')
        ->name('cliente.produtos.comanda.cancelar');
    Route::get('/meus-pacotes', [ClientePacoteController::class, 'indexCliente'])
        ->middleware('cliente_redirect:pacotes')
        ->name('cliente.pacotes.index');
    Route::post('/meus-pacotes/comprar', [ClientePacoteController::class, 'comprarCliente'])
        ->middleware('cliente_redirect:pacotes')
        ->name('cliente.pacotes.comprar');
    Route::patch('/meus-pacotes/{clientePacote}/informar-pagamento', [ClientePacoteController::class, 'informarPagamento'])
        ->middleware('cliente_redirect:pacotes')
        ->name('cliente.pacotes.informar-pagamento');
    Route::post('/agendamento/{id}/cancelar', [AgendamentoController::class, 'cancelarCliente'])
        ->middleware('cliente_redirect:agendamentos')
        ->name('cliente.agendamento.cancelar');
    Route::post('/agendamentos/{id}/presenca', [AgendamentoController::class, 'confirmarPresenca'])->name('agendamento.presenca');
    Route::patch('/agendamentos/{id}/falta', [AgendamentoController::class, 'marcarFalta'])->name('agendamentos.falta');
    Route::post('/avaliar-agendamento', [AgendamentoController::class, 'salvarAvaliacao'])
        ->middleware('cliente_redirect:agendamentos')
        ->name('cliente.avaliar.salvar');
});

// ==========================================
// ROTAS ADMIN (Gerente ou Recepcionista)
// ==========================================
Route::middleware(['auth', 'role:gerente,recepcionista'])->prefix('admin')->name('admin.')->group(function () {
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
    Route::get('/agendar-cliente', [AgendamentoController::class, 'agendarGerencial'])->name('agendar.cliente');
    Route::post('/agendar-cliente', [AgendamentoController::class, 'salvarAgendamentoGerencial'])->name('agendar.cliente.salvar');

    // ✅ CORREÇÃO 2: Rotas de Venda de Pacotes movidas para cá (Segurança total)
    // O prefixo 'admin' e o nome 'admin.' já são aplicados automaticamente por este grupo
    Route::get('/pacotes/venda', [ClientePacoteController::class, 'create'])->name('venda.create');
    Route::post('/pacotes/venda', [ClientePacoteController::class, 'store'])->name('venda.store');

    // Vendas de produtos (recepcao)
    Route::get('/vendas/produtos', [VendaProdutoController::class, 'create'])->name('vendas.produtos.create');
    Route::post('/vendas/produtos', [VendaProdutoController::class, 'store'])->name('vendas.produtos.store');
    Route::get('/vendas/pendentes', [VendaProdutoController::class, 'pendentes'])->name('vendas.pendentes');
    Route::patch('/vendas/{venda}/confirmar-pagamento', [VendaProdutoController::class, 'confirmarVenda'])->name('vendas.confirmar-pagamento');
    Route::patch('/vendas/{venda}/cancelar-pendente', [VendaProdutoController::class, 'cancelarVenda'])->name('vendas.cancelar-pendente');
    Route::patch('/cliente-pacotes/{clientePacote}/confirmar-pagamento', [ClientePacoteController::class, 'confirmarPagamento'])->name('cliente-pacotes.confirmar-pagamento');
    Route::patch('/cliente-pacotes/{clientePacote}/cancelar-pendente', [ClientePacoteController::class, 'cancelarPagamento'])->name('cliente-pacotes.cancelar-pendente');
    
    // Rotas de Financeiro (Gerente ou Recepcionista)
    Route::get('/financeiro/fechamento', [FinanceiroController::class, 'fechamento'])->name('financeiro.fechamento');
    Route::get('/financeiro/fechamento/pdf', [FinanceiroController::class, 'exportarFechamentoPdf'])->name('financeiro.fechamento.pdf');
});

// ==========================================
// ROTAS ADMIN (Apenas Gerente)
// ==========================================
Route::middleware(['auth', 'role:gerente'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/contatos', function () {
        $mensagens = Schema::hasTable('contato_mensagens')
            ? DB::table('contato_mensagens')->orderByDesc('created_at')->paginate(12)
            : collect();

        return view('admin.contatos.index', compact('mensagens'));
    })->name('contatos.index');

    Route::patch('/contatos/{id}/lida', function (int $id) {
        if (Schema::hasTable('contato_mensagens')) {
            $dados = [
                'lida_at' => now(),
                'updated_at' => now(),
            ];

            if (Schema::hasColumn('contato_mensagens', 'cliente_notificado_at')) {
                $dados['cliente_notificado_at'] = null;
            }

            DB::table('contato_mensagens')->where('id', $id)->update($dados);
        }

        return back()->with('status', 'Mensagem marcada como lida.');
    })->name('contatos.lida');

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
    
    // Rotas de Financeiro (Apenas Gerente)
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
    
    // Downloads individuais por relatório
    Route::get('/relatorios/faturamento/download-excel', [RelatorioController::class, 'downloadFaturamentoExcel'])->name('relatorios.faturamento.download-excel');
    Route::get('/relatorios/comissoes/download-excel', [RelatorioController::class, 'downloadComissoesExcel'])->name('relatorios.comissoes.download-excel');
    Route::get('/relatorios/comissoes/download-pdf', [RelatorioController::class, 'downloadComissoesPdf'])->name('relatorios.comissoes.download-pdf');
    Route::get('/relatorios/produtos/download-excel', [RelatorioController::class, 'downloadProdutosExcel'])->name('relatorios.produtos.download-excel');
    Route::get('/relatorios/estoque/download-excel', [RelatorioController::class, 'downloadEstoqueExcel'])->name('relatorios.estoque.download-excel');
    Route::get('/relatorios/desempenho/download-excel', [RelatorioController::class, 'downloadDesempenhoExcel'])->name('relatorios.desempenho.download-excel');
    Route::get('/relatorios/fidelizacao/download-excel', [RelatorioController::class, 'downloadFidelizacaoExcel'])->name('relatorios.fidelizacao.download-excel');
    Route::get('/relatorios/cancelamentos/download-excel', [RelatorioController::class, 'downloadCancelamentosExcel'])->name('relatorios.cancelamentos.download-excel');
    Route::get('/relatorios/financeiro/download-excel', [RelatorioController::class, 'downloadFinanceiroExcel'])->name('relatorios.financeiro.download-excel');
    Route::get('/relatorios/sazonalidade/download-excel', [RelatorioController::class, 'downloadSazonalideExcel'])->name('relatorios.sazonalidade.download-excel');
    Route::get('/relatorios/avaliacoes/download-excel', [RelatorioController::class, 'downloadAvaliacoesExcel'])->name('relatorios.avaliacoes.download-excel');
    Route::get('/relatorios/ocupacao/download-excel', [RelatorioController::class, 'downloadOcupacaoExcel'])->name('relatorios.ocupacao.download-excel');
});

// ==========================================
// ROTAS PROFISSIONAL
// ==========================================
Route::middleware(['auth', 'role:profissional'])->prefix('profissional')->name('profissional.')->group(function () {
    Route::get('/configuracoes', [UserController::class, 'configuracoesservicos'])->name('servicos.editar');
    Route::put('/configuracoes', [UserController::class, 'atualizarconfiguracoesservicos'])->name('servicos.atualizar');
    Route::post('/configuracoes/bloqueios', [UserController::class, 'bloquearDiaDisponibilidade'])->name('servicos.bloqueios.store');
    Route::delete('/configuracoes/bloqueios/{bloqueio}', [UserController::class, 'removerBloqueioDisponibilidade'])->name('servicos.bloqueios.destroy');
    Route::patch('/configuracoes/feriados/{feriado}', [UserController::class, 'atualizarFeriadoDisponibilidade'])->name('servicos.feriados.status');
    Route::post('/agendamento/{id_agendamento}/executado', [AgendamentoController::class, 'marcarComoExecutado'])->name('agendamento.executado');
    Route::get('/minha-agenda', [AgendamentoController::class, 'agendaProfissional'])->name('agenda');
    Route::get('/extrato', [UserController::class, 'extrato'])->name('extrato');
});

require __DIR__.'/auth.php';
