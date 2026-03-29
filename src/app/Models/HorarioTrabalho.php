<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HorarioTrabalho extends Model
{
    protected $table = 'horarios_trabalho';
    protected $fillable = [
        'usuario_id',
        'dia_semana',
        'hora_inicio',
        'hora_fim',
        'trabalha'
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
