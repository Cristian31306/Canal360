<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PortalAgencia extends Model
{
    protected $table = 'portales_agencia';

    protected $fillable = [
        'aseguradora_id',
        'nombre',
        'usuario',
        'password',
        'link',
        'notas',
    ];

    public function aseguradora(): BelongsTo
    {
        return $this->belongsTo(Aseguradora::class);
    }
}
