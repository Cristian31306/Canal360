<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Auditoria extends Model
{
    protected $fillable = [
        'usuario_id', 'accion', 'entidad_afectada', 'entidad_id', 'detalles_json'
    ];

    protected $casts = [
        'detalles_json' => 'array',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
