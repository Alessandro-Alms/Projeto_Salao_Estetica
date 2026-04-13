<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BloqueioHorario extends Model
{
    protected $table = 'bloqueios_horarios';
    protected $primaryKey = 'id_bloqueio';
    protected $fillable = [
        'profissional_id',
        'data_hora_inicio', 
        'data_hora_fim', 
        'motivo'
    ];
    public function profissional()
    {
        return $this->belongsTo(User::class, 'profissional_id');
    }
}
