<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ramo extends Model
{
    protected $fillable = ['nombre'];

    public function aseguradoras()
    {
        return $this->belongsToMany(Aseguradora::class, 'aseguradora_ramo')->withTimestamps();
    }

    public function polizas()
    {
        return $this->hasMany(Poliza::class);
    }
}
