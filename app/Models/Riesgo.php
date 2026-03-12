<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Riesgo extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $fillable = [
        'tipo_riesgo', 'identificador', 'descripcion', 'es_nad', 'numero_nad'
    ];

    public function clientes()
    {
        return $this->belongsToMany(Cliente::class, 'cliente_riesgo')->withTimestamps();
    }

    public function polizas()
    {
        return $this->hasMany(Poliza::class);
    }
}
