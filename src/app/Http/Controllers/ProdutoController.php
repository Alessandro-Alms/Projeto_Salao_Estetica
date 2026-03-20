<?php

namespace App\Http\Controllers;

use App\Models\Produto;
use Illuminate\Http\Request;

class ProdutoController extends Controller
{
    public function index(Request $request)
    {
        $query = Produto::query();

        if ($request->filled('search')) {
            $query->where('nome', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        $produtos = $query->orderBy('nome', 'asc')->paginate(10)->withQueryString();

        return view('admin.produtos.index', compact('produtos'));
    }
    public function create()
    {
        return view('admin.produtos.criar');
    }
    public function store(Request $request)
    {
        $dados = $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'descricao' => ['nullable', 'string'],
            'tipo' => ['required', 'in:acessorios,kits,cosmeticos,cabelo'],
            'valor_unitario' => ['required', 'numeric', 'min:1'],
            'quantidade_estoque' => ['required', 'integer', 'min:0'],
        ]);

        Produto::create($dados);

        return redirect()->route('admin.produtos.index')->with('status', 'Produto cadastrado com sucesso!');
    }
    /**
     * Display the specified resource.
     */
    public function show(Produto $produto)
    {
        //
    }

    public function edit(Produto $produto)
    {
        return view('admin.produtos.editar', compact('produto'));
    }

    public function update(Request $request, Produto $produto)
    {
        $dados = $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'descricao' => ['nullable', 'string'],
            'tipo' => ['required', 'in:acessorios,kits,cosmeticos,cabelo'],
            'valor_unitario' => ['required', 'numeric', 'min:1'],
            'quantidade_estoque' => ['required', 'integer', 'min:0'],
        ]);

        $produto->update($dados);

        return redirect()->route('admin.produtos.index')->with('status', 'Produto atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Produto $produto)
    {
        if (auth()->user()->cargo !== 'gerente') {
            return redirect()->route('admin.produtos.index')->with('error', 'Sem permissão.');
        }
        if ($produto->quantidade_estoque > 0) {
            return redirect()->route('admin.produtos.index')->with('error', 'Não é possível deletar um produto que ainda possui estoque!');
        }
        $produto->delete();

        return redirect()->route('admin.produtos.index')->with('status', 'Produto deletado com sucesso!');
    }
}
