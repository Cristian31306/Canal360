<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Models\Cliente;
use App\Models\Aseguradora;
use App\Models\Ramo;
use App\Models\Riesgo;
use App\Models\Poliza;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Illuminate\Support\Str;

try {
    echo "Cargando archivo...\n";
    $spreadsheet = IOFactory::load(__DIR__ . '/Documentación/polizas.xlsx');
    $worksheet = $spreadsheet->getActiveSheet();
    $highestRow = min($worksheet->getHighestRow(), 15); // Procesar hasta la fila 15
    $highestColumn = $worksheet->getHighestColumn();
    $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);

    $cabeceras = [];
    for ($col = 1; $col <= $highestColumnIndex; ++$col) {
        $cabeceras[$col] = strtolower(trim($worksheet->getCellByColumnAndRow($col, 1)->getValue() ?? ''));
    }

    echo "Procesando filas...\n";
    for ($row = 2; $row <= $highestRow; ++$row) {
        $rowData = [];
        for ($col = 1; $col <= $highestColumnIndex; ++$col) {
            $celda = $worksheet->getCellByColumnAndRow($col, $row);
            // Obtener valor calculado si hay formula
            try {
                $val = $celda->getCalculatedValue();
            } catch (\Exception $e) {
                $val = $celda->getValue();
            }
            $rowData[$cabeceras[$col]] = $val;
        }

        $clienteNombre = trim($rowData['cliente'] ?? '');
        if (empty($clienteNombre)) {
            echo "Fila $row ignorada: Sin cliente\n";
            continue;
        }

        echo "Fila $row - Cliente: $clienteNombre\n";
        
        $cliente = Cliente::firstOrCreate(
            ['nombre_razon_social' => $clienteNombre],
            [
                'tipo_persona' => 'juridica',
                'email' => $rowData['correo'] ?? 'importado@algorah.bond',
                'telefono' => $rowData['telefono'] ?? '0000000',
                'tipo_documento' => 'NIT',
                'numero_documento' => 'IMP-' . mt_rand(100000, 999999) . '-' . mt_rand(1000, 9999)
            ]
        );

        $aseguradoraNombre = trim($rowData['aseguradora'] ?? '');
        if (empty($aseguradoraNombre)) $aseguradoraNombre = 'No Definida';
        $aseguradora = Aseguradora::firstOrCreate(
            ['nombre' => $aseguradoraNombre],
            ['nit' => 'IMP-' . mt_rand(100000, 999999)]
        );

        $ramoNombre = trim($rowData['ramo'] ?? '');
        if (empty($ramoNombre)) $ramoNombre = 'No Definido';
        $ramo = Ramo::firstOrCreate(['nombre' => $ramoNombre]);

        $riesgoRef = trim($rowData['referencia'] ?? '');
        $riesgoId = null;
        if (!empty($riesgoRef)) {
            $riesgo = Riesgo::firstOrCreate(['identificador' => $riesgoRef], ['tipo_riesgo' => 'Importado']);
            $riesgoId = $riesgo->id;
        }

        $numeroPoliza = trim($rowData['numero póliza'] ?? '');
        if (empty($numeroPoliza)) $numeroPoliza = 'PENDIENTE-' . Str::random(8);

        // Parse Date
        $f_exp = $rowData['f expedición'] ?? null;
        $expedicion = is_numeric($f_exp) ? Date::excelToDateTimeObject($f_exp)->format('Y-m-d') : now()->format('Y-m-d');
        
        $vig_desde = $rowData['vigencia desde'] ?? null;
        $inicioVigencia = is_numeric($vig_desde) ? Date::excelToDateTimeObject($vig_desde)->format('Y-m-d') : now()->format('Y-m-d');
        
        $vig_hasta = $rowData['vigencia hasta'] ?? null;
        $finVigencia = is_numeric($vig_hasta) ? Date::excelToDateTimeObject($vig_hasta)->format('Y-m-d') : now()->addYear()->format('Y-m-d');

        // Valores
        $valAseg = is_numeric($rowData['valor asegurado'] ?? null) ? (float)$rowData['valor asegurado'] : 0;
        $prima = is_numeric($rowData['prima'] ?? null) ? (float)$rowData['prima'] : 0;
        $primaT = is_numeric($rowData['valor iva incluido'] ?? null) ? (float)$rowData['valor iva incluido'] : 0;
        $tasa = is_numeric($rowData['tasa'] ?? null) ? (float)$rowData['tasa'] : 0;

        try {
            $poliza = Poliza::create([
                'numero_poliza' => $numeroPoliza,
                'aseguradora_id' => $aseguradora->id,
                'ramo_id' => $ramo->id,
                'riesgo_id' => $riesgoId,
                'expedicion_fecha' => $expedicion,
                'inicio_vigencia' => $inicioVigencia,
                'fin_vigencia' => $finVigencia,
                'valor_asegurado' => $valAseg,
                'prima_antes_iva' => $prima,
                'iva' => max(0, $primaT - $prima),
                'prima_total' => $primaT,
                'tasa' => $tasa,
                'estado' => 'vigente'
            ]);
            echo "   -> Póliza creada con éxito: " . $poliza->id . "\n";
        } catch (\Exception $ex) {
            echo "   -> Error creando póliza: " . $ex->getMessage() . "\n";
        }
    }
} catch (\Exception $e) {
    echo "Error General: " . $e->getMessage() . "\n" . $e->getTraceAsString();
}
