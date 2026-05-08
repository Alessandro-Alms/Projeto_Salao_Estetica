<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Agendamento;
use App\Models\Atendimento;
use App\Models\HorarioTrabalho;
use App\Models\Venda;
use App\Models\Servico;
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'cpf',
        'telefone',
        'cargo',
        'endereco',
        'd_nasc',
        'faltas',
        'obs',
        'ultima_visita',
        'status',
        'contador_fidelidade', 
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    // Relacionamentos profissional - serviços 
    public function servicos()
    {
        return $this->belongsToMany(
            Servico::class,    
            'profissional_servico', 
            'profissional_id', 
            'servico_id', 
        )
        ->withPivot('comissao_percentual', 'duracao_customizada')
        ->withTimestamps();
    }
    public function horariosTrabalho()
    {
        return $this->hasMany(HorarioTrabalho::class, 'profissional_id');
    }
    public function agendamentos(){
        $foreignKey = $this->cargo === 'profissional' ? 'profissional_id' : 'cliente_id';
        return $this->hasMany(Agendamento::class, $foreignKey);
    }
    public function atendimentos(){
        $foreignKey = $this->cargo === 'profissional' ? 'profissional_id' : 'cliente_id';
        return $this->hasMany(Atendimento::class, $foreignKey);
    }
    public function agendamentosComoCliente()
    {
        return $this->hasMany(Agendamento::class, 'cliente_id');
    }
    public function agendamentosComoProfissional()
    {
        return $this->hasMany(Agendamento::class, 'profissional_id');
    }
    public function atendimentosComoCliente()
    {
        return $this->hasMany(Atendimento::class, 'cliente_id');
    }
    public function atendimentosComoProfissional()
    {
        return $this->hasMany(Atendimento::class, 'profissional_id');
    }
    public function vendas()
    {
        return $this->hasMany(Venda::class, 'profissional_id');
    }
    // Busca os pacotes ativos que o cliente comprou e ainda tem sessão
    public function pacotesAtivos()
    {
        return $this->hasMany(ClientePacote::class, 'cliente_id')
                    ->where('status', 'ativo')
                    ->where('sessoes_restantes', '>', 0)
                    ->whereDate('data_validade', '>=', now());
    }
}   
