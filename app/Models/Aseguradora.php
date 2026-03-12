<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Aseguradora extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $fillable = [
        'nombre', 'nit', 'logo'
    ];

    protected $appends = ['logo_url'];

    public function getLogoUrlAttribute()
    {
        return $this->logo ? asset('storage/' . $this->logo) : null;
    }

    public function ramos()
    {
        return $this->belongsToMany(Ramo::class, 'aseguradora_ramo')->withTimestamps();
    }

    public function polizas()
    {
        return $this->hasMany(Poliza::class);
    }

    public function contactos()
    {
        return $this->hasMany(ContactoAseguradora::class);
    }
}
