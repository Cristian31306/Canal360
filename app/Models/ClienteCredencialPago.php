<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClienteCredencialPago extends Model
{
    protected $table = 'cliente_credenciales_pagos';

    protected $fillable = [
        'cliente_id',
        'aseguradora_id',
        'usuario',
        'password',
        'observaciones'
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function aseguradora()
    {
        return $this->belongsTo(Aseguradora::class);
    }
}
