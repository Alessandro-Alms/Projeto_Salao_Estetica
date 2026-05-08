<?php

namespace App\Http\Controllers;

use App\Models\Pacote;
use App\Models\User;
use App\Services\ClientePacoteService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ClientePacoteController extends Controller
{
    public function create()
    {
        $clientes = User::where('cargo', 'cliente')->orderBy('name')->get();
        $pacotes = Pacote::where('ativo', true)->orderBy('nome')->get();

        return view('admin.pacotes.venda', compact('clientes', 'pacotes'));
    }

    public function store(Request $request, ClientePacoteService $clientePacoteService)
    {
        $request->validate([
            'cliente_id' => ['required', Rule::exists('users', 'id')->where('cargo', 'cliente')],
            'pacote_id' => ['required', Rule::exists('pacotes', 'id_pacote')->where('ativo', true)],
        ]);

        $clientePacoteService->venderPacote((int) $request->cliente_id, (int) $request->pacote_id);

        return redirect()->back()->with('success', 'Pacote vendido com sucesso! O cliente já pode usar as sessões.');
    }
}
