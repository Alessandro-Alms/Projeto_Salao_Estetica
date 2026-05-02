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
        'obs',
        'valor_comissao',
        'comissao_paga_percentual'
    ];

    // Relacionamento: O agendamento pertence a um cliente
    public function cliente() {
        return $this->belongsTo(User::class, 'cliente_id');
    }

    // Relacionamento: O agendamento pertence a um profissional
    public function profissional() {
        return $this->belongsTo(User::class, 'profissional_id');
    }

    // Relacionamento: O agendamento é de um serviço específico (compatibilidade com código antigo)
    public function servico() {
        return $this->belongsTo(Servico::class, 'servico_id', 'id_servico');
    }

    // Relacionamento: Múltiplos serviços por agendamento
    public function servicos() {
        return $this->belongsToMany(
            Servico::class,
            'agendamento_servico',
            'agendamento_id',
            'servico_id',
            'id_agendamento',
            'id_servico'
        );
    }

    // app/Models/Agendamento.php

    public function avaliacao()
    {
        // Relaciona o ID do agendamento com a tabela de avaliações
        return $this->hasOne(Avaliacao::class, 'agendamento_id', 'id_agendamento');
    }
}
