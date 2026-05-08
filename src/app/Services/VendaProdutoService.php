<?php

namespace App\Services;

use App\Models\Produto;
use App\Models\Venda;

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
                return 'Estoque insuficiente para o produto: ' . ($produto->nome ?? 'Desconhecido');
            }
        }

        return null;
    }

    public function registrarVenda(int $vendedorId, int $produtoId, int $quantidade): Venda
    {
        $produto = Produto::lockForUpdate()->findOrFail($produtoId);

        if ($produto->quantidade_estoque < $quantidade) {
            throw new \RuntimeException('Estoque insuficiente para a quantidade solicitada.');
        }

        $produto->decrement('quantidade_estoque', $quantidade);

        return Venda::create([
            // A coluna se chama profissional_id, mas representa o usuario que realizou a venda.
            'profissional_id' => $vendedorId,
            'produto_id' => $produto->id_produto,
            'quantidade' => $quantidade,
            'valor_venda' => $produto->valor_unitario * $quantidade,
        ]);
    }

    public function registrarVendas(int $vendedorId, array $itens): void
    {
        foreach ($itens as $item) {
            if (empty($item['id'])) {
                continue;
            }

            $this->registrarVenda(
                $vendedorId,
                (int) $item['id'],
                (int) ($item['quantidade'] ?? 1)
            );
        }
    }
}
