<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Agendamento extends Model
{
    protected $primaryKey = 'id_agendamento';
    protected $fillable = [
        'cliente_id',
        'profissional_id',
        'servico_id', 
        'data_hora_inicio',
        'data_hora_fim', 
        'valor_total', 
        'status', 
        'obs'
    ];

    // Relacionamento: O agendamento pertence a um cliente
    public function cliente() {
        return $this->belongsTo(User::class, 'cliente_id');
    }

    // Relacionamento: O agendamento pertence a um profissional
    public function profissional() {
        return $this->belongsTo(User::class, 'profissional_id');
    }

    // Relacionamento: O agendamento é de um serviço específico
    public function servico() {
        return $this->belongsTo(Servico::class, 'servico_id', 'id_servico');
    }
}
