<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cartera extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $fillable = [
        'poliza_id', 'valor_a_pagar', 'fecha_limite', 'estado'
    ];

    public function poliza()
    {
        return $this->belongsTo(Poliza::class);
    }
}
