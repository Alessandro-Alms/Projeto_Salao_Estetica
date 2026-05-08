<?php

namespace App\Http\Controllers;

use App\Models\Produto;
use App\Services\VendaProdutoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VendaProdutoController extends Controller
{
    public function create()
    {
        $produtos = Produto::orderBy('nome')->get();
        return view('admin.vendas.produtos', compact('produtos'));
    }

    public function store(Request $request, VendaProdutoService $vendaProdutoService)
    {
        $dados = $request->validate([
            'produto_id' => ['required', 'exists:produtos,id_produto'],
            'quantidade' => ['required', 'integer', 'min:1'],
        ]);

        return DB::transaction(function () use ($dados, $vendaProdutoService) {
            $quantidade = (int) $dados['quantidade'];

            try {
                $vendaProdutoService->registrarVenda(auth()->id(), (int) $dados['produto_id'], $quantidade);
            } catch (\RuntimeException $exception) {
                return back()->withErrors(['quantidade' => $exception->getMessage()])->withInput();
            }

            return redirect()->route('admin.vendas.produtos.create')
                ->with('status', 'Venda registrada com sucesso!');
        });
    }
}
