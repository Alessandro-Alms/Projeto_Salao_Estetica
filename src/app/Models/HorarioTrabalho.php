<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HorarioTrabalho extends Model
{
    protected $table = 'horarios_trabalho';
    protected $fillable = [
        'profissional_id',
        'dia_semana',
        'hora_inicio',
        'hora_fim',
        'almoco_inicio',
        'almoco_fim',
        'trabalha'
    ];

    public function profissional()
    {
        return $this->belongsTo(User::class, 'profissional_id');
    }
}
