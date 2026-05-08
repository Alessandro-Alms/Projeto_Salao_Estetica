<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Agendamento;

class Servico extends Model
{
    // Define a chave primária personalizada    
    protected $table = 'servicos'; 
    protected $primaryKey = 'id_servico';
    // Campos que podem ser preenchidos em massa
    protected $fillable = [
        'nome',
        'descricao',
        'preco',
        'duracao',
    ];


    public function profissionais()
    {
        return $this->belongsToMany(
            User::class,    
            'profissional_servico', 
            'servico_id', 
            'profissional_id',
            'id_servico',
            'id'
        )
        ->withPivot('comissao_percentual', 'duracao_customizada')
        ->withTimestamps();
    }
}
