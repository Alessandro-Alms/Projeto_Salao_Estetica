<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pacote;
use App\Models\User;
use App\Models\ClientePacote;
use Carbon\Carbon;

class ClientePacoteController extends Controller
{
    // Mostra a tela de venda
    public function create()
    {
        // Puxa todos os clientes (Ajuste o 'cargo' se no seu banco for diferente)
        $clientes = User::where('cargo', 'cliente')->orderBy('name')->get(); 
        
        // Puxa apenas os pacotes que estão ativos
        $pacotes = Pacote::where('ativo', true)->orderBy('nome')->get();

        return view('admin.pacotes.venda', compact('clientes', 'pacotes'));
    }

    // Processa a venda e salva no banco
    public function store(Request $request)
    {
        $request->validate([
            'cliente_id' => 'required|exists:users,id',
            'pacote_id' => 'required|exists:pacotes,id_pacote',
        ]);

        // Achamos o pacote para saber quantas sessões ele tem e a validade
        $pacote = Pacote::findOrFail($request->pacote_id);

        // Criamos a "carteirinha" do cliente
        ClientePacote::create([
            'cliente_id' => $request->cliente_id,
            'pacote_id' => $pacote->id_pacote,
            'sessoes_restantes' => $pacote->quantidade_sessoes, // Ex: Entra 5
            'data_compra' => now(),
            'data_validade' => now()->addDays($pacote->validade_dias), // Calcula a validade automaticamente
            'status' => 'ativo',
        ]);

        // Dica: Futuramente você pode colocar um código aqui para salvar esse valor ($pacote->valor_total) no caixa do salão!

        return redirect()->back()->with('success', 'Pacote vendido com sucesso! O cliente já pode usar as sessões.');
    }
}