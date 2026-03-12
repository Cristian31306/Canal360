<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactoAseguradora extends Model
{
    protected $fillable = [
        'aseguradora_id', 'rol', 'nombre', 'email', 'telefono'
    ];

    public function aseguradora()
    {
        return $this->belongsTo(Aseguradora::class);
    }
}
