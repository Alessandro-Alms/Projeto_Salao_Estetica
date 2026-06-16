<?php

namespace App\Http\Controllers;

use App\Models\ClientePacote;
use App\Models\Produto;
use App\Models\User;
use App\Models\Venda;
use App\Services\PagamentoService;
use App\Services\VendaProdutoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class VendaProdutoController extends Controller
{
    private const FORMAS_PAGAMENTO = ['dinheiro', 'pix', 'cartao_debito', 'cartao_credito'];

    public function indexCliente()
    {
        $produtos = Produto::where('quantidade_estoque', '>', 0)
            ->orderBy('nome')
            ->get();

        $vendasPendentes = Venda::with('produto')
            ->where('profissional_id', auth()->id())
            ->where('status_pagamento', 'pendente')
            ->whereNotNull('produto_id')
            ->orderBy('created_at')
            ->get();

        $pedidosPendentes = $vendasPendentes
            ->groupBy('produto_id')
            ->map(function ($vendas) {
                $primeiraVenda = $vendas->first();

                return (object) [
                    'produto_id' => $primeiraVenda->produto_id,
                    'produto' => $primeiraVenda->produto,
                    'quantidade' => $vendas->sum('quantidade'),
                    'valor_venda' => $vendas->sum('valor_venda'),
                    'ids' => $vendas->pluck('id_venda')->all(),
                ];
            })
            ->values();

        return view('cliente.produtos.index', compact('produtos', 'vendasPendentes', 'pedidosPendentes'));
    }

    public function comprarCliente(Request $request, VendaProdutoService $vendaProdutoService)
    {
        $dados = $request->validate([
            'produto_id' => ['required_without:itens', 'exists:produtos,id_produto'],
            'quantidade' => ['required_without:itens', 'integer', 'min:1'],
            'itens' => ['nullable', 'array', 'min:1'],
            'itens.*.produto_id' => ['required_with:itens', 'exists:produtos,id_produto'],
            'itens.*.quantidade' => ['required_with:itens', 'integer', 'min:1'],
        ]);

        return DB::transaction(function () use ($dados, $vendaProdutoService) {
            try {
                $codigoPedido = (string) Str::uuid();

                foreach ($this->normalizarItensCliente($dados) as $item) {
                    $vendaProdutoService->registrarVenda(
                        (int) auth()->id(),
                        (int) $item['produto_id'],
                        (int) $item['quantidade'],
                        false,
                        'pendente',
                        null,
                        null,
                        null,
                        $codigoPedido
                    );
                }
            } catch (\RuntimeException $exception) {
                return back()->withErrors(['quantidade' => $exception->getMessage()])->withInput();
            }

            return redirect()->route('cliente.produtos.index')
                ->with('success', 'Produto reservado com sucesso! Retire e pague presencialmente na recepcao.');
        });
    }

    public function historicoCliente()
    {
        $agendamentos = \App\Models\Agendamento::with(['servico', 'profissional', 'pagamentos'])
            ->where('cliente_id', auth()->id())
            ->where('status', 'executado')
            ->orderByDesc('updated_at')
            ->get();

        $vendas = Venda::with(['produto', 'pagamentos'])
            ->where('profissional_id', auth()->id())
            ->whereNotNull('produto_id')
            ->orderByDesc('created_at')
            ->get();

        $pacotes = ClientePacote::with('pacote.servicos', 'confirmadoPor', 'pagamentos')
            ->where('cliente_id', auth()->id())
            ->orderByDesc('created_at')
            ->get();

        return view('cliente.compras.index', compact('agendamentos', 'vendas', 'pacotes'));
    }

    public function atualizarCliente(Request $request, Venda $venda)
    {
        $dados = $request->validate([
            'quantidade' => ['required', 'integer', 'min:1'],
        ]);

        if (! $this->clientePodeEditarVenda($venda)) {
            return back()->withErrors(['pedido' => 'Este pedido nao pode mais ser editado.']);
        }

        return DB::transaction(function () use ($venda, $dados) {
            $venda->refresh();
            $novaQuantidade = (int) $dados['quantidade'];
            $diferenca = $novaQuantidade - (int) $venda->quantidade;
            $produto = Produto::lockForUpdate()->findOrFail($venda->produto_id);

            if ($diferenca > 0 && $produto->quantidade_estoque < $diferenca) {
                return back()->withErrors(['quantidade' => 'Estoque insuficiente para aumentar essa quantidade.']);
            }

            if ($diferenca > 0) {
                $produto->decrement('quantidade_estoque', $diferenca);
            } elseif ($diferenca < 0) {
                $produto->increment('quantidade_estoque', abs($diferenca));
            }

            $venda->update([
                'quantidade' => $novaQuantidade,
                'valor_venda' => $produto->valor_unitario * $novaQuantidade,
            ]);

            return back()->with('success', 'Pedido atualizado.');
        });
    }

    public function cancelarCliente(Venda $venda)
    {
        if (! $this->clientePodeEditarVenda($venda)) {
            return back()->withErrors(['pedido' => 'Este pedido nao pode mais ser cancelado.']);
        }

        DB::transaction(function () use ($venda) {
            Produto::where('id_produto', $venda->produto_id)->increment('quantidade_estoque', $venda->quantidade);
            $venda->update(['status_pagamento' => 'cancelado']);
        });

        return back()->with('success', 'Item removido do pedido.');
    }

    public function atualizarProdutoCliente(Request $request, Produto $produto)
    {
        $dados = $request->validate([
            'quantidade' => ['required', 'integer', 'min:1'],
        ]);

        return DB::transaction(function () use ($produto, $dados) {
            $vendas = Venda::where('profissional_id', auth()->id())
                ->where('produto_id', $produto->id_produto)
                ->where('status_pagamento', 'pendente')
                ->lockForUpdate()
                ->get();

            if ($vendas->isEmpty()) {
                return back()->withErrors(['pedido' => 'Este pedido nao pode mais ser editado.']);
            }

            $quantidadeAtual = (int) $vendas->sum('quantidade');
            $novaQuantidade = (int) $dados['quantidade'];
            $diferenca = $novaQuantidade - $quantidadeAtual;
            $produto = Produto::lockForUpdate()->findOrFail($produto->id_produto);

            if ($diferenca > 0 && $produto->quantidade_estoque < $diferenca) {
                return back()->withErrors(['quantidade' => 'Estoque insuficiente para aumentar essa quantidade.']);
            }

            if ($diferenca > 0) {
                $produto->decrement('quantidade_estoque', $diferenca);
            } elseif ($diferenca < 0) {
                $produto->increment('quantidade_estoque', abs($diferenca));
            }

            $principal = $vendas->first();
            $principal->update([
                'quantidade' => $novaQuantidade,
                'valor_venda' => $produto->valor_unitario * $novaQuantidade,
            ]);

            $vendas->slice(1)->each->update([
                'status_pagamento' => 'cancelado',
                'quantidade' => 0,
                'valor_venda' => 0,
            ]);

            return back()->with('success', 'Pedido atualizado.');
        });
    }

    public function cancelarProdutoCliente(Produto $produto)
    {
        $vendas = Venda::where('profissional_id', auth()->id())
            ->where('produto_id', $produto->id_produto)
            ->where('status_pagamento', 'pendente')
            ->get();

        if ($vendas->isEmpty()) {
            return back()->withErrors(['pedido' => 'Este pedido nao pode mais ser cancelado.']);
        }

        DB::transaction(function () use ($vendas, $produto) {
            Produto::where('id_produto', $produto->id_produto)->increment('quantidade_estoque', $vendas->sum('quantidade'));

            foreach ($vendas as $venda) {
                $venda->update(['status_pagamento' => 'cancelado']);
            }
        });

        return back()->with('success', 'Item removido do pedido.');
    }

    public function cancelarComandaCliente()
    {
        $vendas = Venda::where('profissional_id', auth()->id())
            ->where('status_pagamento', 'pendente')
            ->whereNotNull('produto_id')
            ->get();

        DB::transaction(function () use ($vendas) {
            foreach ($vendas as $venda) {
                Produto::where('id_produto', $venda->produto_id)->increment('quantidade_estoque', $venda->quantidade);
                $venda->update(['status_pagamento' => 'cancelado']);
            }
        });

        return back()->with('success', 'Pedido cancelado.');
    }

    public function create()
    {
        $produtos = Produto::orderBy('nome')->get();
        $clientes = User::where('cargo', 'cliente')->orderBy('name')->get();
        $formasPagamento = self::FORMAS_PAGAMENTO;

        return view('admin.vendas.produtos', compact('produtos', 'clientes', 'formasPagamento'));
    }

    public function store(Request $request, VendaProdutoService $vendaProdutoService)
    {
        $dados = $request->validate([
            'cliente_id' => ['required', Rule::exists('users', 'id')->where('cargo', 'cliente')],
            'produto_id' => ['required_without:itens', 'nullable', 'exists:produtos,id_produto'],
            'quantidade' => ['required_without:itens', 'nullable', 'integer', 'min:1'],
            'itens' => ['nullable', 'array', 'min:1'],
            'itens.*.produto_id' => ['required_with:itens', 'exists:produtos,id_produto'],
            'itens.*.quantidade' => ['required_with:itens', 'integer', 'min:1'],
            'forma_pagamento' => ['nullable', Rule::in(self::FORMAS_PAGAMENTO)],
            'pagamentos' => ['nullable', 'array'],
            'pagamentos.*.forma_pagamento' => ['nullable', Rule::in(self::FORMAS_PAGAMENTO)],
            'pagamentos.*.valor' => ['nullable'],
        ]);

        return DB::transaction(function () use ($dados, $vendaProdutoService) {
            $itens = $this->normalizarItensCliente($dados);
            $produtos = Produto::whereIn('id_produto', collect($itens)->pluck('produto_id'))->get()->keyBy('id_produto');
            $valorTotal = collect($itens)->sum(function ($item) use ($produtos) {
                return (float) ($produtos->get($item['produto_id'])->valor_unitario ?? 0) * (int) $item['quantidade'];
            });
            $pagamentoService = app(PagamentoService::class);
            $pagamentos = $pagamentoService->normalizar($dados['pagamentos'] ?? [], $valorTotal, $dados['forma_pagamento'] ?? 'dinheiro');
            $formaResumo = $pagamentoService->formaResumo($pagamentos, $dados['forma_pagamento'] ?? 'dinheiro');
            $codigoPedido = (string) Str::uuid();

            try {
                foreach ($itens as $item) {
                    $produto = $produtos->get($item['produto_id']);
                    $valorItem = (float) ($produto->valor_unitario ?? 0) * (int) $item['quantidade'];

                    $vendaProdutoService->registrarVenda(
                        (int) $dados['cliente_id'],
                        (int) $item['produto_id'],
                        (int) $item['quantidade'],
                        true,
                        'pago',
                        $formaResumo,
                        auth()->id(),
                        $this->distribuirPagamentosParaVenda($pagamentos, $valorItem, (float) $valorTotal),
                        $codigoPedido
                    );
                }
            } catch (\RuntimeException $exception) {
                return back()->withErrors(['quantidade' => $exception->getMessage()])->withInput();
            }

            return redirect()->route('admin.vendas.produtos.create')
                ->with('status', 'Comanda registrada com sucesso!');
        });
    }

    public function pendentes(Request $request)
    {
        $vendasPendentes = Venda::with(['produto', 'vendedor', 'pagamentos'])
            ->where('status_pagamento', 'pendente')
            ->whereNotNull('produto_id')
            ->orderBy('created_at')
            ->get();

        $comandasPendentes = $vendasPendentes
            ->groupBy(fn ($venda) => $venda->profissional_id . '|' . $this->codigoComanda($venda))
            ->map(function ($vendas) {
                return (object) [
                    'codigo_pedido' => $this->codigoComanda($vendas->first()),
                    'cliente' => $vendas->first()->vendedor,
                    'vendas' => $vendas,
                    'quantidade_total' => $vendas->sum('quantidade'),
                    'valor_total' => $vendas->sum('valor_venda'),
                    'criada_em' => $vendas->first()->created_at,
                ];
            })
            ->values();

        $pacotesPendentes = ClientePacote::with(['cliente', 'pacote.servicos', 'pagamentos'])
            ->whereIn('status_pagamento', ['pendente', 'aguardando_confirmacao'])
            ->orderBy('created_at')
            ->get();

        $formasPagamento = self::FORMAS_PAGAMENTO;

        return view('admin.vendas.pendentes', compact('vendasPendentes', 'comandasPendentes', 'pacotesPendentes', 'formasPagamento'));
    }

    public function confirmarComanda(Request $request, User $cliente, string $pedido)
    {
        $dados = $request->validate([
            'forma_pagamento' => ['required', Rule::in(self::FORMAS_PAGAMENTO)],
            'pagamentos' => ['nullable', 'array'],
            'pagamentos.*.forma_pagamento' => ['nullable', Rule::in(self::FORMAS_PAGAMENTO)],
            'pagamentos.*.valor' => ['nullable'],
        ]);

        return DB::transaction(function () use ($dados, $cliente, $pedido) {
            $vendas = $this->queryVendasDaComanda($cliente, $pedido)
                ->where('status_pagamento', 'pendente')
                ->whereNotNull('produto_id')
                ->lockForUpdate()
                ->get();

            if ($vendas->isEmpty()) {
                return back()->withErrors(['pagamento' => 'Esta comanda ja foi analisada.']);
            }

            $valorTotal = (float) $vendas->sum('valor_venda');
            $pagamentoService = app(PagamentoService::class);
            $pagamentos = $pagamentoService->normalizar($dados['pagamentos'] ?? [], $valorTotal, $dados['forma_pagamento']);
            $formaResumo = $pagamentoService->formaResumo($pagamentos, $dados['forma_pagamento']);

            foreach ($vendas as $venda) {
                $venda->update([
                    'status_pagamento' => 'pago',
                    'forma_pagamento' => $formaResumo,
                    'pago_em' => now(),
                    'confirmado_por_id' => auth()->id(),
                ]);

                $pagamentoService->registrar(
                    $venda,
                    $this->distribuirPagamentosParaVenda($pagamentos, (float) $venda->valor_venda, $valorTotal),
                    auth()->id()
                );
            }

            return back()->with('status', 'Comanda de produtos confirmada. O valor entrou no caixa.');
        });
    }

    public function cancelarComanda(User $cliente, string $pedido)
    {
        return DB::transaction(function () use ($cliente, $pedido) {
            $vendas = $this->queryVendasDaComanda($cliente, $pedido)
                ->where('status_pagamento', 'pendente')
                ->whereNotNull('produto_id')
                ->lockForUpdate()
                ->get();

            if ($vendas->isEmpty()) {
                return back()->withErrors(['pagamento' => 'Esta comanda ja foi analisada.']);
            }

            foreach ($vendas as $venda) {
                Produto::where('id_produto', $venda->produto_id)->increment('quantidade_estoque', $venda->quantidade);
                $venda->update(['status_pagamento' => 'cancelado']);
            }

            return back()->with('status', 'Comanda cancelada e estoque devolvido.');
        });
    }

    public function confirmarVenda(Request $request, Venda $venda)
    {
        $dados = $request->validate([
            'forma_pagamento' => ['required', Rule::in(self::FORMAS_PAGAMENTO)],
            'pagamentos' => ['nullable', 'array'],
            'pagamentos.*.forma_pagamento' => ['nullable', Rule::in(self::FORMAS_PAGAMENTO)],
            'pagamentos.*.valor' => ['nullable'],
        ]);

        if ($venda->status_pagamento !== 'pendente') {
            return back()->withErrors(['pagamento' => 'Esta venda ja foi analisada.']);
        }

        $pagamentoService = app(PagamentoService::class);
        $pagamentos = $pagamentoService->normalizar($dados['pagamentos'] ?? [], (float) $venda->valor_venda, $dados['forma_pagamento']);

        $venda->update([
            'status_pagamento' => 'pago',
            'forma_pagamento' => $pagamentoService->formaResumo($pagamentos, $dados['forma_pagamento']),
            'pago_em' => now(),
            'confirmado_por_id' => auth()->id(),
        ]);
        $pagamentoService->registrar($venda, $pagamentos, auth()->id());

        return back()->with('status', 'Pagamento da venda confirmado. O valor entrou no caixa.');
    }

    public function cancelarVenda(Venda $venda)
    {
        if ($venda->status_pagamento !== 'pendente') {
            return back()->withErrors(['pagamento' => 'Esta venda ja foi analisada.']);
        }

        DB::transaction(function () use ($venda) {
            if ($venda->produto_id) {
                Produto::where('id_produto', $venda->produto_id)->increment('quantidade_estoque', $venda->quantidade);
            }

            $venda->update(['status_pagamento' => 'cancelado']);
        });

        return back()->with('status', 'Pedido cancelado e estoque devolvido.');
    }

    private function normalizarItensCliente(array $dados): array
    {
        $itens = $dados['itens'] ?? [[
            'produto_id' => $dados['produto_id'],
            'quantidade' => $dados['quantidade'],
        ]];

        return collect($itens)
            ->groupBy('produto_id')
            ->map(fn ($grupo, $produtoId) => [
                'produto_id' => (int) $produtoId,
                'quantidade' => (int) $grupo->sum('quantidade'),
            ])
            ->values()
            ->all();
    }

    private function clientePodeEditarVenda(Venda $venda): bool
    {
        return (int) $venda->profissional_id === (int) auth()->id()
            && $venda->status_pagamento === 'pendente'
            && $venda->produto_id !== null;
    }

    private function aumentarVendaPendente(Venda $venda, int $quantidade): void
    {
        $produto = Produto::lockForUpdate()->findOrFail($venda->produto_id);

        if ($produto->quantidade_estoque < $quantidade) {
            throw new \RuntimeException('Estoque insuficiente para a quantidade solicitada.');
        }

        $produto->decrement('quantidade_estoque', $quantidade);

        $venda->update([
            'quantidade' => $venda->quantidade + $quantidade,
            'valor_venda' => $produto->valor_unitario * ($venda->quantidade + $quantidade),
        ]);
    }

    private function distribuirPagamentosParaVenda(array $pagamentos, float $valorVenda, float $valorTotal): array
    {
        if ($valorTotal <= 0) {
            return [];
        }

        $restante = (int) round($valorVenda * 100);
        $distribuidos = [];
        $ultimoIndex = count($pagamentos) - 1;

        foreach ($pagamentos as $index => $pagamento) {
            if ($index === $ultimoIndex) {
                $centavos = $restante;
            } else {
                $centavos = (int) round(((float) $pagamento['valor'] * 100) * ($valorVenda / $valorTotal));
                $centavos = min($centavos, $restante);
                $restante -= $centavos;
            }

            if ($centavos <= 0) {
                continue;
            }

            $distribuidos[] = [
                'forma_pagamento' => $pagamento['forma_pagamento'],
                'valor' => round($centavos / 100, 2),
            ];
        }

        return $distribuidos;
    }

    private function queryVendasDaComanda(User $cliente, string $pedido)
    {
        $query = Venda::where('profissional_id', $cliente->id);

        return $query->where('codigo_pedido', $pedido);
    }

    private function codigoComanda(Venda $venda): string
    {
        return $venda->codigo_pedido ?: 'legado-' . $venda->profissional_id . '-' . preg_replace('/\D/', '', (string) $venda->created_at);
    }
}
