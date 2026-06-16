<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Pagamento extends Model
{
    protected $fillable = [
        'forma_pagamento',
        'valor',
        'recebido_por_id',
        'pago_em',
    ];

    protected $casts = [
        'valor' => 'decimal:2',
        'pago_em' => 'datetime',
    ];

    public function pagavel(): MorphTo
    {
        return $this->morphTo();
    }

    public function recebidoPor()
    {
        return $this->belongsTo(User::class, 'recebido_por_id');
    }
}
