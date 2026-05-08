<?php

namespace App\Http\Controllers;

use App\Models\Servico;
use Illuminate\Http\Request;
use App\Models\Agendamento;
use Illuminate\Support\Facades\DB;

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
        $validado = $request->validate([
            'nome' => ['required', 'string', 'max:100'],
            'preco' => ['required', 'numeric', 'min:0'],
            'duracao' => ['required', 'integer', 'min:1'],
        ]);
        Servico::create($validado);

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
    
    public function destroy($id)
    {
        $servico = Servico::findOrFail($id);

        // 1. Verifica se existe no histórico de agendamentos
        $temAgendamento = Agendamento::where('servico_id', $id)->exists()
            || DB::table('agendamento_servico')->where('servico_id', $id)->exists();

        // 2. Verifica se está vinculado a algum pacote
        $temPacote = DB::table('pacotes')->where('servico_id', $id)->exists();

        // 3. Verifica se algum profissional faz este serviço (tabela pivô)
        $temProfissional = DB::table('profissional_servico')->where('servico_id', $id)->exists();

        // Se qualquer um dos três for verdadeiro, nós barramos a exclusão na hora!
        if ($temAgendamento || $temPacote || $temProfissional) {
            return redirect()->back()->withErrors([
                'error' => 'Ação bloqueada! Este serviço não pode ser excluído porque possui histórico de agendamentos, está vinculado a um profissional ou pertence a um pacote. Recomendamos apenas editar o nome para "(Inativo)".'
            ]);
        }

        // Se passou, deleta.
        $servico->delete();

        // AQUI ESTÁ A CORREÇÃO DO ERRO DE ROTA:
        return redirect()->back()->with('status', 'Serviço excluído com sucesso!');
    }
}
