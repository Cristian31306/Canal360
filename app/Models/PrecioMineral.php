<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrecioMineral extends Model
{
    protected $table = 'precio_minerales';

    protected $fillable = [
        'mes',
        'anio',
        'oro',
        'plata',
        'platino',
    ];

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
     * Calcula la variación porcentual con respecto al mes anterior
     */
    public function calcularVariacion($mineral)
    {
        $anterior = $this->mesAnterior();
        
        if (!$anterior || $anterior->$mineral == 0) {
            return 0;
        }

        $diferencia = $this->$mineral - $anterior->$mineral;
        return ($diferencia / $anterior->$mineral) * 100;
    }

    /**
     * Calcula la diferencia en pesos con respecto al mes anterior
     */
    public function calcularDiferencia($mineral)
    {
        $anterior = $this->mesAnterior();
        
        if (!$anterior) {
            return 0;
        }

        return $this->$mineral - $anterior->$mineral;
    }
}
