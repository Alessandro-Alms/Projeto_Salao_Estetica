<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Atendimento extends Model
{
    public function cliente() { return $this->belongsTo(User::class, 'id_cliente'); }
    public function profissional() { return $this->belongsTo(User::class, 'user_id'); }
}
