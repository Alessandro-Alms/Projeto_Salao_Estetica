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

    public function servicos()
    {
        return $this->belongsToMany(
            Servico::class,
            'pacote_servico',
            'pacote_id',
            'servico_id',
            'id_pacote',
            'id_servico'
        )->withTimestamps();
    }

    public function aceitaServico(int $servicoId): bool
    {
        if ($this->relationLoaded('servicos')) {
            return $this->servicos->contains('id_servico', $servicoId);
        }

        return $this->servicos()->where('servicos.id_servico', $servicoId)->exists()
            || (int) $this->servico_id === $servicoId;
    }
}
