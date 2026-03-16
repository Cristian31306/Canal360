<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClienteCredencialAnna extends Model
{
    protected $table = 'cliente_credenciales_anna';

    protected $fillable = [
        'cliente_id',
        'usuario',
        'password',
        'observaciones'
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }
}
