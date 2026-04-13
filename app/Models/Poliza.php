<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Poliza extends Model implements \Serializable
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $fillable = [
        'numero_poliza', 'anexo', 'aseguradora_id', 'ramo_id', 'riesgo_id',
        'expedicion_fecha', 'inicio_vigencia', 'fin_vigencia',
        'valor_asegurado', 'prima_antes_iva', 'iva', 'prima_total', 'tasa', 
        'estado', 'liquidacion', 'poliza_anterior_id'
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
        'anexo' => 'integer',
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

    public function polizaAnterior()
    {
        return $this->belongsTo(Poliza::class, 'poliza_anterior_id');
    }

    public function polizaSiguiente()
    {
        return $this->hasOne(Poliza::class, 'poliza_anterior_id');
    }

    /**
     * Métodos para la interfaz Serializable
     */
    public function serialize(): string
    {
        return serialize($this->__serialize());
    }

    public function unserialize(string $data): void
    {
        $this->__unserialize(unserialize($data));
    }

    public function __serialize(): array
    {
        return $this->toArray();
    }

    public function __unserialize(array $data): void
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
    }
}

