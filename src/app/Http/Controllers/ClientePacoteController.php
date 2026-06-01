<?php

namespace App\Http\Controllers;

use App\Models\ClientePacote;
use App\Models\Pacote;
use App\Models\User;
use App\Services\ClientePacoteService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ClientePacoteController extends Controller
{
    private const FORMAS_PAGAMENTO = ['dinheiro', 'pix', 'cartao_debito', 'cartao_credito'];

    public function indexCliente()
    {
        $pacotesDisponiveis = Pacote::with(['servico', 'servicos'])
            ->where('ativo', true)
            ->orderBy('nome')
            ->get();

        $meusPacotes = auth()->user()
            ->pacotesAtivos()
            ->with('pacote.servicos')
            ->orderBy('data_validade')
            ->get();

        $pacotesPendentes = ClientePacote::with('pacote.servicos')
            ->where('cliente_id', auth()->id())
            ->whereIn('status_pagamento', ['pendente', 'aguardando_confirmacao'])
            ->orderByDesc('created_at')
            ->get();

        return view('cliente.pacotes.index', compact('pacotesDisponiveis', 'meusPacotes', 'pacotesPendentes'));
    }

    public function comprarCliente(Request $request, ClientePacoteService $clientePacoteService)
    {
        $request->validate([
            'pacote_id' => ['required', Rule::exists('pacotes', 'id_pacote')->where('ativo', true)],
        ]);

        $clientePacote = $clientePacoteService->venderPacote((int) auth()->id(), (int) $request->pacote_id, null, 'pendente', 'pix');

        return redirect()
            ->route('cliente.pacotes.index')
            ->with('success', 'Pedido de pacote criado. Pague pelo PIX e clique em "Ja paguei" para a equipe confirmar a entrada.')
            ->with('pix_pacote_id', $clientePacote->id);
    }

    public function informarPagamento(ClientePacote $clientePacote)
    {
        if ((int) $clientePacote->cliente_id !== (int) auth()->id() || $clientePacote->status_pagamento !== 'pendente') {
            return back()->withErrors(['pagamento' => 'Este pagamento nao pode ser sinalizado.']);
        }

        $clientePacote->update(['status_pagamento' => 'aguardando_confirmacao']);

        return back()->with('success', 'Pagamento informado. A equipe vai confirmar se o PIX entrou e liberar o pacote.');
    }

    public function create()
    {
        $clientes = User::where('cargo', 'cliente')->orderBy('name')->get();
        $pacotes = Pacote::where('ativo', true)->orderBy('nome')->get();
        $formasPagamento = self::FORMAS_PAGAMENTO;

        return view('admin.pacotes.venda', compact('clientes', 'pacotes', 'formasPagamento'));
    }

    public function store(Request $request, ClientePacoteService $clientePacoteService)
    {
        $request->validate([
            'cliente_id' => ['required', Rule::exists('users', 'id')->where('cargo', 'cliente')],
            'pacote_id' => ['required', Rule::exists('pacotes', 'id_pacote')->where('ativo', true)],
            'forma_pagamento' => ['nullable', Rule::in(self::FORMAS_PAGAMENTO)],
        ]);

        $vendedorId = auth()->user()->isRecepcionista() ? (int) auth()->id() : null;

        $clientePacoteService->venderPacote(
            (int) $request->cliente_id,
            (int) $request->pacote_id,
            $vendedorId,
            'pago',
            $request->forma_pagamento ?? 'dinheiro',
            auth()->id()
        );

        return redirect()->back()->with('success', 'Pacote vendido com sucesso! O cliente já pode usar as sessões.');
    }

    public function confirmarPagamento(Request $request, ClientePacote $clientePacote)
    {
        $dados = $request->validate([
            'forma_pagamento' => ['required', Rule::in(self::FORMAS_PAGAMENTO)],
        ]);

        if (! in_array($clientePacote->status_pagamento, ['pendente', 'aguardando_confirmacao'], true)) {
            return back()->withErrors(['pagamento' => 'Este pacote ja foi analisado.']);
        }

        $clientePacote->update([
            'status_pagamento' => 'pago',
            'forma_pagamento' => $dados['forma_pagamento'],
            'pago_em' => now(),
            'confirmado_por_id' => auth()->id(),
        ]);

        return back()->with('status', 'Pagamento do pacote confirmado. As sessoes foram liberadas para a cliente.');
    }

    public function cancelarPagamento(ClientePacote $clientePacote)
    {
        if (! in_array($clientePacote->status_pagamento, ['pendente', 'aguardando_confirmacao'], true)) {
            return back()->withErrors(['pagamento' => 'Este pacote ja foi analisado.']);
        }

        $clientePacote->update([
            'status_pagamento' => 'cancelado',
            'status' => 'finalizado',
        ]);

        return back()->with('status', 'Pedido de pacote cancelado.');
    }
}
