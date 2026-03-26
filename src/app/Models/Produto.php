<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Produto extends Model
{
    protected $primaryKey = 'id_produto'; // Chave primária customizada

    protected $fillable = [
        'nome',
        'descricao',
        'tipo',
        'valor_unitario',
        'quantidade_estoque',
    ];

    // Opcional: Um acessório para formatar o preço na exibição
    public function getPrecoFormatadoAttribute()
    {
        return 'R$ ' . number_format($this->valor_unitario, 2, ',', '.');
    }
    public function vendas()
    {
        return $this->hasMany(Venda::class, 'id_produto');
    }
}
