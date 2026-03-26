<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

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
    public function servicos()
    {
        return $this->belongsToMany(
            Servico::class,    
            'profissional_servico', 
            'usuario_id', 
            'servico_id', 
        )
        ->withPivot('comissao_percentual', 'duracao_customizada')
        ->withTimestamps();
    }
    public function agendamentos(){
        $foreignKey = $this->cargo === 'profissional' ? 'cliente_id' : 'profissional_id';
        return $this->hasMany(Agendamento::class, $foreignKey);
    }
    public function atendimentos(){
        $foreignKey = $this->cargo === 'profissional' ? 'cliente_id' : 'profissional_id';
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
        return $this->hasMany(Venda::class, 'user_id');
    }
}   