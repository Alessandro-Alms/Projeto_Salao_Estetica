<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientePacote extends Model
{
    protected $fillable = [
        'cliente_id',
        'pacote_id',   
        'sessoes_restantes', 
        'data_compra', 
        'data_validade', 
        'status'];

    public function pacote()
    {
        return $this->belongsTo(Pacote::class, 'pacote_id', 'id_pacote');
    }

    public function cliente()
    {
        return $this->belongsTo(User::class, 'cliente_id');
    }
}
