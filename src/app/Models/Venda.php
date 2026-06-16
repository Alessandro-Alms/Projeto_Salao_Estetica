<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Venda extends Model
{
    protected $primaryKey = 'id_venda';
    protected $fillable = [
        'profissional_id',
        'produto_id',
        'servico_id',
        'codigo_pedido',
        'quantidade',
        'valor_venda',
        'valor_comissao',
        'comissao_paga_percentual',
        'status_pagamento',
        'forma_pagamento',
        'pago_em',
        'confirmado_por_id',
    ];

    public function getRouteKeyName(): string
    {
        return 'id_venda';
    }

    // Quem realizou a venda.
    public function vendedor() {
        return $this->belongsTo(User::class, 'profissional_id');
    }

    public function profissional() {
        return $this->belongsTo(User::class, 'profissional_id');
    }

    public function produto() {
        return $this->belongsTo(Produto::class, 'produto_id', 'id_produto');
    }

    public function confirmadoPor() {
        return $this->belongsTo(User::class, 'confirmado_por_id');
    }

    public function pagamentos() {
        return $this->morphMany(Pagamento::class, 'pagavel');
    }

    public function servico() {
        return $this->belongsTo(Servico::class, 'servico_id', 'id_servico');
    }
}
