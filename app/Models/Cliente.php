<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model implements \Serializable
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

    public function annaCredentials()
    {
        return $this->hasMany(ClienteCredencialAnna::class);
    }

    public function paymentCredentials()
    {
        return $this->hasMany(ClienteCredencialPago::class);
    }

    /**
     * Métodos para la interfaz Serializable
     */
    public function serialize(): string
    {
        return serialize($this->__serialize());
    }

    public function unserialize(string $data): void
    {
        $this->__unserialize(unserialize($data));
    }

    public function __serialize(): array
    {
        return $this->toArray();
    }

    public function __unserialize(array $data): void
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
    }
}

