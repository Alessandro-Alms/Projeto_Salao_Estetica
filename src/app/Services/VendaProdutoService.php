<?php

namespace App\Services;

use App\Models\Produto;
use App\Models\Venda;
use Illuminate\Support\Facades\Schema;

class VendaProdutoService
{
    public function validarEstoque(array $itens): ?string
    {
        foreach ($itens as $item) {
            if (empty($item['id'])) {
                continue;
            }

            $produto = Produto::find($item['id']);
            $quantidade = (int) ($item['quantidade'] ?? 1);

            if (!$produto || $produto->quantidade_estoque < $quantidade) {
                return 'Estoque insuficiente para o produto: ' . ($produto->nome ?? 'produto não encontrado');
            }
        }

        return null;
    }

    public function registrarVenda(
        int $vendedorId,
        int $produtoId,
        int $quantidade,
        bool $geraComissao = true,
        string $statusPagamento = 'pago',
        ?string $formaPagamento = null,
        ?int $confirmadoPorId = null,
        ?array $pagamentos = null,
        ?string $codigoPedido = null,
        ?int $agendamentoId = null,
        float $percentualValor = 1.0
    ): Venda
    {
        $produto = Produto::lockForUpdate()->findOrFail($produtoId);

        if ($produto->quantidade_estoque < $quantidade) {
            throw new \RuntimeException('Estoque insuficiente para a quantidade solicitada.');
        }

        $produto->decrement('quantidade_estoque', $quantidade);
        $valorVenda = round(($produto->valor_unitario * $quantidade) * $percentualValor, 2);
        $financeiroService = app(FinanceiroService::class);

        $dadosVenda = [
            // A coluna se chama profissional_id, mas representa o usuario que realizou a venda/compra.
            'profissional_id' => $vendedorId,
            'produto_id' => $produto->id_produto,
            'quantidade' => $quantidade,
            'valor_venda' => $valorVenda,
        ];

        if (Schema::hasColumn('vendas', 'valor_comissao')) {
            $dadosVenda['valor_comissao'] = $geraComissao ? $financeiroService->calcularComissaoProduto((float) $valorVenda) : 0;
        }

        if (Schema::hasColumn('vendas', 'comissao_paga_percentual')) {
            $dadosVenda['comissao_paga_percentual'] = $geraComissao ? FinanceiroService::COMISSAO_PRODUTO_PERCENTUAL : 0;
        }

        if (Schema::hasColumn('vendas', 'status_pagamento')) {
            $dadosVenda['status_pagamento'] = $statusPagamento;
        }

        if (Schema::hasColumn('vendas', 'forma_pagamento')) {
            $dadosVenda['forma_pagamento'] = $formaPagamento;
        }

        if (Schema::hasColumn('vendas', 'pago_em')) {
            $dadosVenda['pago_em'] = $statusPagamento === 'pago' ? now() : null;
        }

        if (Schema::hasColumn('vendas', 'confirmado_por_id')) {
            $dadosVenda['confirmado_por_id'] = $statusPagamento === 'pago' ? $confirmadoPorId : null;
        }

        if (Schema::hasColumn('vendas', 'codigo_pedido')) {
            $dadosVenda['codigo_pedido'] = $codigoPedido;
        }

        if (Schema::hasColumn('vendas', 'agendamento_id')) {
            $dadosVenda['agendamento_id'] = $agendamentoId;
        }

        $venda = Venda::create($dadosVenda);

        if ($statusPagamento === 'pago') {
            $pagamentoService = app(PagamentoService::class);
            $pagamentoService->registrar(
                $venda,
                $pagamentos ?: $pagamentoService->normalizar([], (float) $valorVenda, $formaPagamento ?? 'dinheiro'),
                $confirmadoPorId
            );
        }

        return $venda;
    }

    public function registrarVendas(
        int $vendedorId,
        array $itens,
        bool $geraComissao = true,
        string $statusPagamento = 'pago',
        ?string $formaPagamento = null,
        ?int $confirmadoPorId = null,
        ?int $agendamentoId = null,
        float $percentualValor = 1.0
    ): void
    {
        foreach ($itens as $item) {
            if (empty($item['id'])) {
                continue;
            }

            $this->registrarVenda(
                $vendedorId,
                (int) $item['id'],
                (int) ($item['quantidade'] ?? 1),
                $geraComissao,
                $statusPagamento,
                $formaPagamento,
                $confirmadoPorId,
                null,
                $agendamentoId ? 'ATEND-' . $agendamentoId : null,
                $agendamentoId,
                $percentualValor
            );
        }
    }
}
