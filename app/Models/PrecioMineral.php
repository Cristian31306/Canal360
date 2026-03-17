<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrecioMineral extends Model
{
    protected $table = 'precio_minerales';

    protected $fillable = [
        'mes',
        'anio',
    ];

    public function valores()
    {
        return $this->hasMany(PrecioMineralValor::class);
    }

    /**
     * Obtiene el valor de un mineral específico por su slug
     */
    public function getValor($slug)
    {
        $valor = $this->valores()->whereHas('mineral', function($q) use ($slug) {
            $q->where('slug', $slug);
        })->first();

        return $valor ? $valor->precio : 0;
    }

    /**
     * Obtiene el registro del mes anterior
     */
    public function mesAnterior()
    {
        $mes = $this->mes - 1;
        $anio = $this->anio;

        if ($mes == 0) {
            $mes = 12;
            $anio--;
        }

        return self::where('mes', $mes)
            ->where('anio', $anio)
            ->first();
    }

    /**
     * Calcula la variación porcentual con respecto al mes anterior por slug
     */
    public function calcularVariacion($slug)
    {
        $anterior = $this->mesAnterior();
        $valorActual = $this->getValor($slug);
        
        if (!$anterior || ($valorAnterior = $anterior->getValor($slug)) == 0) {
            return 0;
        }

        $diferencia = $valorActual - $valorAnterior;
        return ($diferencia / $valorAnterior) * 100;
    }

    /**
     * Calcula la diferencia en pesos con respecto al mes anterior por slug
     */
    public function calcularDiferencia($slug)
    {
        $anterior = $this->mesAnterior();
        $valorActual = $this->getValor($slug);
        
        if (!$anterior) {
            return 0;
        }

        return $valorActual - $anterior->getValor($slug);
    }
}
