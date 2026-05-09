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
        'quantidade',
        'valor_venda',
        'valor_comissao',
        'comissao_paga_percentual',
    ];

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

    public function servico() {
        return $this->belongsTo(Servico::class, 'servico_id', 'id_servico');
    }
}
