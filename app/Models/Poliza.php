<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Poliza extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $fillable = [
        'numero_poliza', 'aseguradora_id', 'ramo_id', 'riesgo_id',
        'expedicion_fecha', 'inicio_vigencia', 'fin_vigencia',
        'valor_asegurado', 'prima_antes_iva', 'iva', 'prima_total', 'tasa', 'estado'
    ];

    protected $casts = [
        'expedicion_fecha' => 'date',
        'inicio_vigencia' => 'date',
        'fin_vigencia' => 'date',
        'valor_asegurado' => 'decimal:2',
        'prima_antes_iva' => 'decimal:2',
        'iva' => 'decimal:2',
        'prima_total' => 'decimal:2',
        'tasa' => 'decimal:6',
    ];

    public function aseguradora()
    {
        return $this->belongsTo(Aseguradora::class);
    }

    public function ramo()
    {
        return $this->belongsTo(Ramo::class);
    }

    public function riesgo()
    {
        return $this->belongsTo(Riesgo::class);
    }

    public function cartera()
    {
        return $this->hasMany(Cartera::class);
    }

    public function clientes()
    {
        return $this->belongsToMany(Cliente::class, 'cliente_poliza')
                    ->withPivot('rol')
                    ->withTimestamps();
    }
}
