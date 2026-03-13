<?php

namespace App\Exports;

use App\Models\Cartera;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\Exportable;

class CarteraExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    protected $filters;

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = Cartera::with(['poliza.clientes', 'poliza.aseguradora', 'poliza.ramo', 'abonos']);

        if (!empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->whereHas('poliza', function($q) use ($search) {
                $q->where('numero_poliza', 'like', "%{$search}%")
                  ->orWhereHas('clientes', function($q) use ($search) {
                      $q->where('nombre_razon_social', 'like', "%{$search}%");
                  });
            });
        }

        if (!empty($this->filters['aseguradora_id'])) {
            $query->whereHas('poliza', function($q) {
                $q->where('aseguradora_id', $this->filters['aseguradora_id']);
            });
        }

        if (!empty($this->filters['ramo_id'])) {
            $query->whereHas('poliza', function($q) {
                $q->where('ramo_id', $this->filters['ramo_id']);
            });
        }

        if (!empty($this->filters['cliente_id'])) {
            $query->whereHas('poliza.clientes', function($q) {
                $q->where('clientes.id', $this->filters['cliente_id']);
            });
        }

        if (!empty($this->filters['anio'])) {
            if (config('database.default') === 'sqlite') {
                $query->whereHas('poliza', function($q) {
                    $q->whereRaw("strftime('%Y', expedicion_fecha) = ?", [$this->filters['anio']]);
                });
            } else {
                $query->whereHas('poliza', function($q) {
                    $q->whereYear('expedicion_fecha', $this->filters['anio']);
                });
            }
        }

        if (!empty($this->filters['estado'])) {
            $query->where('estado', $this->filters['estado']);
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'Cliente(s)',
            'Número de Póliza',
            'Aseguradora',
            'Ramo',
            'Valor Total',
            'Saldo Pendiente',
            'Estado',
            'Fecha Expedición',
            'Fecha Límite',
            'Días en Cartera'
        ];
    }

    public function map($cartera): array
    {
        $saldoPendiente = $cartera->saldo_pendiente;
        $diasCarpeta = $cartera->dias_en_cartera;

        return [
            $cartera->poliza->clientes->pluck('nombre_razon_social')->implode(', '),
            $cartera->poliza->numero_poliza,
            $cartera->poliza->aseguradora->nombre,
            $cartera->poliza->ramo->nombre,
            $cartera->valor_a_pagar,
            $saldoPendiente,
            ucfirst($cartera->estado),
            $cartera->poliza->expedicion_fecha->format('d/m/Y'),
            $cartera->fecha_limite ? $cartera->fecha_limite->format('d/m/Y') : 'N/A',
            (int)$diasCarpeta
        ];
    }
}
