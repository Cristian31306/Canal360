<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrecioMineralValor extends Model
{
    protected $table = 'precio_mineral_valores';

    protected $fillable = [
        'precio_mineral_id',
        'cat_mineral_id',
        'precio',
    ];

    public function precioMineral()
    {
        return $this->belongsTo(PrecioMineral::class);
    }

    public function mineral()
    {
        return $this->belongsTo(CatMineral::class, 'cat_mineral_id');
    }
}
