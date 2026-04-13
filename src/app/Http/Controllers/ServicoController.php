<?php

namespace App\Http\Controllers;

use App\Models\Servico;
use Illuminate\Http\Request;

class ServicoController extends Controller
{
    public function index()
    {
        $servicos = Servico::all();
        return view('admin.servicos.index', compact('servicos'));
    }
    public function create()
    {
        if (auth()->user()->cargo !== 'gerente') {
            abort(403, 'Apenas gerentes podem cadastrar novos serviços.');
        }
        return view('admin.servicos.criar');
    }
    public function store(Request $request)
    {
        $request->validate([
            'nome' => ['required', 'string', 'max:100'],
            'preco' => ['required', 'numeric', 'min:0'],
            'duracao' => ['required', 'integer', 'min:1'],
        ]);
        Servico::create($request->all());

        return redirect()->route('admin.servicos.index')->with('success', 'Serviço cadastrado!');
    }
    /**
     * Display the specified resource.
     */
    public function show(Servico $servico)
    {
        //
    }

    public function edit(Servico $servico)
    {
        return view('admin.servicos.editar', compact('servico'));
    }

    public function update(Request $request, Servico $servico)
    {
        if (auth()->user()->cargo !== 'gerente') {
            return redirect()->route('admin.servicos.index')->with('error', 'Sem permissão.');
        }

        $validado = $request->validate([
            'nome' => ['required', 'string', 'max:100'],
            'preco' => ['required', 'numeric', 'min:0'],
            'duracao' => ['required', 'integer', 'min:1'],
        ]);

        $servico->update($validado);

        return redirect()->route('admin.servicos.index')->with('success', 'Serviço atualizado!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Servico $servico)
    {
        if (auth()->user()->cargo !== 'gerente') {
            return redirect()->route('admin.servicos.index')->with('error', 'Sem permissão.');
        }

        $servico->delete();

        return redirect()->route('admin.servicos.index')->with('success', 'Serviço removido!');
    }
}
