<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $fillable = [
        'tipo_persona', 'tipo_documento', 'numero_documento', 'nombre_razon_social',
        'telefono', 'email', 'direccion', 'ciudad', 'fecha_nacimiento', 'fecha_contacto',
        'observaciones', 'rep_legal_nombre', 'rep_legal_documento', 'rep_legal_telefono', 'rep_legal_email'
    ];

    public function riesgos()
    {
        return $this->belongsToMany(Riesgo::class, 'cliente_riesgo')->withTimestamps();
    }

    public function polizas()
    {
        return $this->belongsToMany(Poliza::class, 'cliente_poliza')
                    ->withPivot('rol')
                    ->withTimestamps();
    }
}
