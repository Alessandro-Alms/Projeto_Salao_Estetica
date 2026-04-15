<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Avaliacao extends Model
{
    protected $table = 'avaliacoes';
    protected $fillable = [
        'agendamento_id', 
        'cliente_id', 
        'profissional_id', 
        'nota', 
        'comentario'];

    public function agendamento() {
        return $this->belongsTo(Agendamento::class, 'agendamento_id');
    }
}