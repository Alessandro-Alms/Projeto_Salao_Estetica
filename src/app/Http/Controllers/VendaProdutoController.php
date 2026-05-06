<?php

namespace App\Http\Controllers;

use App\Models\Produto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VendaProdutoController extends Controller
{
    public function create()
    {
        $produtos = Produto::orderBy('nome')->get();
        return view('admin.vendas.produtos', compact('produtos'));
    }

    public function store(Request $request)
    {
        $dados = $request->validate([
            'produto_id' => ['required', 'exists:produtos,id_produto'],
            'quantidade' => ['required', 'integer', 'min:1'],
        ]);

        return DB::transaction(function () use ($dados) {
            $produto = Produto::lockForUpdate()->findOrFail($dados['produto_id']);
            $quantidade = (int) $dados['quantidade'];

            // Garante estoque antes de registrar a venda.
            if ($produto->quantidade_estoque < $quantidade) {
                return back()->withErrors([
                    'quantidade' => 'Estoque insuficiente para a quantidade solicitada.'
                ])->withInput();
            }

            $produto->decrement('quantidade_estoque', $quantidade);

            DB::table('vendas')->insert([
                'profissional_id' => auth()->id(),
                'produto_id' => $produto->id_produto,
                'quantidade' => $quantidade,
                'valor_venda' => $produto->valor_unitario * $quantidade,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return redirect()->route('admin.vendas.produtos.create')
                ->with('status', 'Venda registrada com sucesso!');
        });
    }
}
