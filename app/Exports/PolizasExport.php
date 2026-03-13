<?php

namespace App\Exports;

use App\Models\Poliza;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\Exportable;

class PolizasExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    protected $filters;

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = Poliza::with(['aseguradora', 'ramo', 'clientes', 'riesgo']);

        if (!empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('numero_poliza', 'like', "%{$search}%")
                  ->orWhereHas('clientes', function($q) use ($search) {
                      $q->where('nombre_razon_social', 'like', "%{$search}%")
                        ->orWhere('nit_cedula', 'like', "%{$search}%");
                  })
                  ->orWhereHas('aseguradora', function($q) use ($search) {
                      $q->where('nombre', 'like', "%{$search}%");
                  });
            });
        }

        if (!empty($this->filters['aseguradora_id'])) {
            $query->where('aseguradora_id', $this->filters['aseguradora_id']);
        }

        if (!empty($this->filters['ramo_id'])) {
            $query->where('ramo_id', $this->filters['ramo_id']);
        }

        if (!empty($this->filters['cliente_id'])) {
            $query->whereHas('clientes', function($q) {
                $q->where('clientes.id', $this->filters['cliente_id']);
            });
        }

        if (!empty($this->filters['anio'])) {
            $fechaCampo = $this->filters['fecha_tipo'] ?? 'inicio_vigencia';
            // Compatibilidad SQLite/MySQL
            if (config('database.default') === 'sqlite') {
                $query->whereRaw("strftime('%Y', $fechaCampo) = ?", [$this->filters['anio']]);
            } else {
                $query->whereYear($fechaCampo, $this->filters['anio']);
            }
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'Número de Póliza',
            'Cliente(s)',
            'Riesgo/Identificador',
            'Aseguradora',
            'Ramo',
            'Inicio Vigencia',
            'Fin Vigencia',
            'Valor Asegurado',
            'Prima Neta',
            'Tasa (%)',
            'Estado',
            'Fecha Expedición'
        ];
    }

    public function map($poliza): array
    {
        return [
            $poliza->numero_poliza,
            $poliza->clientes->pluck('nombre_razon_social')->implode(', '),
            $poliza->riesgo?->identificador ?? 'S/I',
            $poliza->aseguradora->nombre,
            $poliza->ramo->nombre,
            $poliza->inicio_vigencia->format('d/m/Y'),
            $poliza->fin_vigencia->format('d/m/Y'),
            $poliza->valor_asegurado,
            $poliza->prima_antes_iva,
            number_format($poliza->tasa, 1),
            ucfirst($poliza->estado),
            $poliza->expedicion_fecha->format('d/m/Y')
        ];
    }
}
