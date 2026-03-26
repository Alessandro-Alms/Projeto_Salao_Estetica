<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Atendimento extends Model
{
    protected $primaryKey = 'id_atendimento';
    protected $fillable = [
        'cliente_id', 
        'profissional_id', 
        'agendamento_id', 
        'valor_total', 
        'avaliacao', 
        'descricao_detalhada'
    ];

    public function cliente() {
        return $this->belongsTo(User::class, 'cliente_id');
    }

    public function profissional() {
        return $this->belongsTo(User::class, 'profissional_id');
    }

    public function agendamento() {
        return $this->belongsTo(Agendamento::class, 'agendamento_id');
    }
}
