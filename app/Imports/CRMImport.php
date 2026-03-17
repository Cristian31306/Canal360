<?php

namespace App\Imports;

use App\Models\Titulo360;
use App\Models\Aseguradora;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;

class CRMImport implements OnEachRow, WithHeadingRow
{
    private $aseguradoras;

    public function __construct()
    {
        // Cachear aseguradoras
        $this->aseguradoras = Aseguradora::all()->pluck('id', 'nombre')->toArray();
    }

    public function onRow(Row $row)
    {
        $data = array_change_key_case($row->toArray(), CASE_LOWER);
        
        $tituloCode = trim($data['titulo'] ?? '');
        if (empty($tituloCode)) return;

        $aseguradoraNombre = $data['aseguradora'] ?? null;
        $aseguradoraId = null;

        if ($aseguradoraNombre) {
            $aseguradoraId = $this->aseguradoras[$aseguradoraNombre] ?? null;
        }

        Titulo360::updateOrCreate(
            ['titulo' => $tituloCode],
            [
                'par'                => $data['par'] ?? null,
                'nombre'             => !empty($data['nombre']) ? $data['nombre'] : 'Sin Titular',
                'minerales'          => $data['minerales'] ?? null,
                'departamento'       => $data['departamento'] ?? null,
                'municipio'          => $data['municipio'] ?? null,
                'etapa'              => $data['etapa'] ?? null,
                'fecha_inicio'       => $this->transformDate($data['fecha_inicio'] ?? null),
                'fecha_fin'          => $this->transformDate($data['fecha_fin'] ?? null),
                'aseguradora_id'     => $aseguradoraId,
                'aseguradora_nombre' => $aseguradoraId ? null : $aseguradoraNombre,
                'valor_asegurado'    => $this->transformNumber($data['valor_asegurado'] ?? 0),
                'correo'             => $data['correo'] ?? null,
                'celular'            => $data['celular'] ?? null,
                'asesores'           => $data['asesores'] ?? null,
            ]
        );
    }

    private function transformDate($value)
    {
        if (empty($value)) return null;
        
        try {
            // Intentar procesar formato Excel serial o string Y-m-d
            if (is_numeric($value)) {
                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value);
            }
            return Carbon::parse($value);
        } catch (\Exception $e) {
            return null;
        }
    }

    private function transformNumber($value)
    {
        if (empty($value)) return 0;
        // Quitar cualquier carácter que no sea número o punto decimal
        $clean = preg_replace('/[^0-9.]/', '', $value);
        return (float) $clean;
    }
}
