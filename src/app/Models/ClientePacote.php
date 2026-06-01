<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientePacote extends Model
{
    protected $fillable = [
        'cliente_id',
        'pacote_id',   
        'vendedor_id',
        'sessoes_restantes', 
        'data_compra', 
        'data_validade', 
        'valor_comissao',
        'comissao_paga_percentual',
        'status',
        'status_pagamento',
        'forma_pagamento',
        'pago_em',
        'confirmado_por_id',
    ];

    public function pacote()
    {
        return $this->belongsTo(Pacote::class, 'pacote_id', 'id_pacote');
    }

    public function cliente()
    {
        return $this->belongsTo(User::class, 'cliente_id');
    }

    public function vendedor()
    {
        return $this->belongsTo(User::class, 'vendedor_id');
    }

    public function confirmadoPor()
    {
        return $this->belongsTo(User::class, 'confirmado_por_id');
    }
}
