<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cartera extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $fillable = [
        'poliza_id', 'valor_a_pagar', 'fecha_limite', 'estado'
    ];

    protected $appends = ['total_abonado', 'saldo_pendiente', 'dias_en_cartera'];

    protected $casts = [
        'fecha_limite' => 'date',
        'valor_a_pagar' => 'decimal:2',
    ];

    public function poliza()
    {
        return $this->belongsTo(Poliza::class);
    }

    public function abonos()
    {
        return $this->hasMany(AbonoCartera::class);
    }

    public function getTotalAbonadoAttribute()
    {
        return $this->abonos()->sum('monto');
    }

    public function getSaldoPendienteAttribute()
    {
        return max(0, $this->valor_a_pagar - $this->total_abonado);
    }

    public function getDiasEnCarteraAttribute()
    {
        $fechaReferencia = $this->poliza->expedicion_fecha ?? $this->created_at;
        
        if ($this->estado === 'pagado') {
            $ultimoAbono = $this->abonos()->latest('fecha_pago')->first();
            $fechaFin = $ultimoAbono ? $ultimoAbono->fecha_pago : now();
        } else {
            $fechaFin = now();
        }

        return (int) $fechaReferencia->diffInDays($fechaFin);
    }
}
