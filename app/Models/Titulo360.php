<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Titulo360 extends Model
{
    use SoftDeletes;

    protected $table = 'titulos_360';

    protected $fillable = [
        'par',
        'titulo',
        'nombre',
        'minerales',
        'departamento',
        'municipio',
        'etapa',
        'fecha_inicio',
        'fecha_fin',
        'aseguradora_id',
        'aseguradora_nombre',
        'valor_asegurado',
        'correo',
        'celular',
        'cliente_canal',
        'asesores',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'valor_asegurado' => 'decimal:2',
        'cliente_canal' => 'boolean',
    ];

    public function aseguradora(): BelongsTo
    {
        return $this->belongsTo(Aseguradora::class);
    }

    /**
     * Boot the model.
     */
    protected static function booted()
    {
        static::saving(function ($titulo) {
            // Lógica inteligente: Si el título coincide con algún identificador en la tabla de Riesgos, marcar como Cliente Canal
            $existeEnRiesgos = Riesgo::where('identificador', $titulo->titulo)->exists();
            if ($existeEnRiesgos) {
                $titulo->cliente_canal = true;
            }
        });
    }
}
