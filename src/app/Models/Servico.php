<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Servico extends Model
{
    // Define a chave primária personalizada
    protected $primaryKey = 'id_servico';

    // Campos que podem ser preenchidos em massa
    protected $fillable = [
        'nome',
        'descricao',
        'preco',
        'duracao',
    ];
}