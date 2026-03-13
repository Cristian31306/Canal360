<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
 
class AbonoCartera extends Model
{
    use SoftDeletes;
 
    protected $table = 'abono_carteras';
 
    protected $fillable = [
        'cartera_id',
        'monto',
        'fecha_pago',
        'metodo_pago',
        'referencia',
        'observaciones'
    ];
 
    protected $casts = [
        'fecha_pago' => 'date',
        'monto' => 'decimal:2'
    ];
 
    public function cartera()
    {
        return $this->belongsTo(Cartera::class);
    }
}
