<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CatMineral extends Model
{
    protected $table = 'cat_minerales';

    protected $fillable = [
        'nombre',
        'slug',
        'activo',
    ];

    public function valores()
    {
        return $this->hasMany(PrecioMineralValor::class, 'cat_mineral_id');
    }
}
