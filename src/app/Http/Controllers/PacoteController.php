<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pacote;
use App\Models\Servico;
use Illuminate\Support\Facades\DB;

class PacoteController extends Controller
{
    public function index()
    {
        // Busca todos os pacotes e traz o nome do serviço associado
        $pacotes = Pacote::with(['servico', 'servicos'])->orderBy('nome')->get();
        
        // Busca os serviços para preencher o <select> do formulário de criação
        $servicos = Servico::orderBy('nome')->get();

        return view('admin.pacotes.index', compact('pacotes', 'servicos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'servicos_ids' => ['required', 'array', 'min:1'],
            'servicos_ids.*' => ['integer', 'exists:servicos,id_servico'],
            'quantidade_sessoes' => 'required|integer|min:2',
            'valor_total' => 'required|numeric|min:0',
            'validade_dias' => 'required|integer|min:1',
        ]);

        $servicosIds = array_values(array_unique(array_map('intval', $request->servicos_ids)));

        $pacote = Pacote::create([
            'nome' => $request->nome,
            'servico_id' => $servicosIds[0],
            'quantidade_sessoes' => $request->quantidade_sessoes,
            'valor_total' => $request->valor_total,
            'validade_dias' => $request->validade_dias,
            'ativo' => true,
        ]);

        $pacote->servicos()->sync($servicosIds);

        return redirect()->route('admin.pacotes.index')->with('success', 'Pacote criado com sucesso!');
    }
    public function edit($id)
    {
        // Busca o pacote pelo id (usamos id_pacote no banco)
        $pacote = Pacote::with('servicos')->findOrFail($id);
        $servicos = Servico::orderBy('nome')->get();
        
        return view('admin.pacotes.editar', compact('pacote', 'servicos'));
    }

    public function update(Request $request, $id)
    {
        $pacote = Pacote::findOrFail($id);

        $request->validate([
            'nome' => 'required|string|max:255',
            'servicos_ids' => ['required', 'array', 'min:1'],
            'servicos_ids.*' => ['integer', 'exists:servicos,id_servico'],
            'quantidade_sessoes' => 'required|integer|min:2',
            'valor_total' => 'required|numeric|min:0',
            'validade_dias' => 'required|integer|min:1',
        ]);

        $servicosIds = array_values(array_unique(array_map('intval', $request->servicos_ids)));

        $pacote->update([
            'nome' => $request->nome,
            'servico_id' => $servicosIds[0],
            'quantidade_sessoes' => $request->quantidade_sessoes,
            'valor_total' => $request->valor_total,
            'validade_dias' => $request->validade_dias,
        ]);

        $pacote->servicos()->sync($servicosIds);

        return redirect()->route('admin.pacotes.index')->with('success', 'Pacote atualizado com sucesso!');
    }

    public function destroy($id)
    {
        $pacote = Pacote::findOrFail($id);

        $temVenda = DB::table('cliente_pacotes')->where('pacote_id', $pacote->id_pacote)->exists();

        if ($temVenda) {
            return redirect()->route('admin.pacotes.index')->with('error', 'Não é possível excluir um pacote que já foi vendido. Marque como inativo para preservar o histórico.');
        }

        $pacote->delete();

        return redirect()->route('admin.pacotes.index')->with('success', 'Pacote excluído com sucesso!');
    }
}
