<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Venda extends Model
{
    protected $primaryKey = 'id_venda';
    protected $fillable = [
    'user_id', 
    'id_produto', 
    'id_servico',   
    'quantidade', 
    'valor_venda'
    ];

    // Quem realizou a venda (gerente/recepcionista)
    public function vendedor() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function produto() {
        return $this->belongsTo(Produto::class, 'id_produto');
    }

    public function servico() {
        return $this->belongsTo(Servico::class, 'id_servico');
    }
}
