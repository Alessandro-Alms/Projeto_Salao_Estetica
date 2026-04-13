<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pacote extends Model
{
    protected $primaryKey = 'id_pacote';
    protected $fillable = [
        'nome',
        'servico_id', 
        'quantidade_sessoes', 
        'valor_total', 
        'validade_dias', 
        'ativo'
        ];

    public function servico()
    {
        return $this->belongsTo(Servico::class, 'servico_id', 'id_servico');
    }
}
