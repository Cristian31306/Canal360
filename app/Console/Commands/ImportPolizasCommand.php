<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Models\Cliente;
use App\Models\Aseguradora;
use App\Models\Ramo;
use App\Models\Riesgo;
use App\Models\Poliza;
use App\Models\Cartera;
use App\Models\AbonoCartera;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ImportPolizasCommand extends Command
{
    protected $signature = 'canal360:import-polizas {--clear : Limpia las tablas de pólizas antes de importar}';
    protected $description = 'Importa las pólizas de forma optimizada desde Excel';

    private $mapeoAseguradoras = [
        'sura' => 'Seguros Generales Suramericana S.A.',
        'suramericana' => 'Seguros Generales Suramericana S.A.',
        'allianz' => 'Allianz Seguros S.A.',
        'bolivar' => 'Compañía de Seguros Bolívar S.A.',
        'estado' => 'Seguros del Estado S.A.',
        'global' => 'Global Seguros de Vida S.A.',
        'hdi' => 'HDI Seguros Colombia S.A.',
        'mundial' => 'Compañía Mundial de Seguros S.A.',
        'previsora' => 'La Previsora S.A. Compañía de Seguros',
        'solidaria' => 'Aseguradora Solidaria de Colombia',
        'berkley' => 'Berkley International Seguros Colombia S.A.',
    ];

    private $cacheClientes = [];
    private $cacheAseguradoras = [];
    private $cacheRamos = [];
    private $cacheRiesgos = [];

    private function mapAseguradoraName($name)
    {
        $lowerName = strtolower(trim($name));
        foreach ($this->mapeoAseguradoras as $key => $officialName) {
            if (str_contains($lowerName, $key)) {
                return $officialName;
            }
        }
        return trim($name);
    }

    public function handle()
    {
        if ($this->option('clear')) {
            $this->warn('Limpiando datos previos de pólizas, carteras y riesgos...');
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            AbonoCartera::truncate();
            Cartera::truncate();
            DB::table('cliente_poliza')->truncate();
            Poliza::truncate();
            DB::table('cliente_riesgo')->truncate();
            Riesgo::truncate();
            // No truncamos Clientes, Aseguradoras o Ramos para mantener la base de datos limpia pero funcional
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            $this->info('Tablas de pólizas limpias.');
        }

        $this->info('Iniciando lectura de Excel...');
        $filePath = base_path('Documentación/polizas.xlsx');
        
        if (!file_exists($filePath)) {
            $this->error('El archivo polizas.xlsx no existe en Documentación/');
            return;
        }

        try {
            $spreadsheet = IOFactory::load($filePath);
            $worksheet = $spreadsheet->getActiveSheet();
            $highestRow = $worksheet->getHighestRow();
            $highestColumn = $worksheet->getHighestColumn();
            $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);

            $cabeceras = [];
            for ($col = 1; $col <= $highestColumnIndex; ++$col) {
                $cabeceras[$col] = strtolower(trim($worksheet->getCellByColumnAndRow($col, 1)->getValue() ?? ''));
            }

            $this->info("Archivo cargado. Procesando " . ($highestRow - 1) . " registros...");
            $bar = $this->output->createProgressBar($highestRow - 1);
            $bar->start();

            for ($row = 2; $row <= $highestRow; ++$row) {
                $rowData = [];
                for ($col = 1; $col <= $highestColumnIndex; ++$col) {
                    $celda = $worksheet->getCellByColumnAndRow($col, $row);
                    try {
                        $val = $celda->getCalculatedValue();
                    } catch (\Exception $e) {
                        $val = $celda->getValue();
                    }
                    $rowData[$cabeceras[$col]] = $val;
                }

                $clienteNombre = trim($rowData['cliente'] ?? '');
                if (empty($clienteNombre)) {
                    $bar->advance();
                    continue;
                }

                $clienteKey = strtolower($clienteNombre);
                if (isset($this->cacheClientes[$clienteKey])) {
                    $clienteId = $this->cacheClientes[$clienteKey];
                } else {
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
                    $clienteId = $cliente->id;
                    $this->cacheClientes[$clienteKey] = $clienteId;
                }

                $aseguradoraNombreRaw = trim($rowData['aseguradora'] ?? '');
                if (empty($aseguradoraNombreRaw)) $aseguradoraNombreRaw = 'No Definida';
                $aseguradoraNombre = $this->mapAseguradoraName($aseguradoraNombreRaw);
                $asegKey = strtolower($aseguradoraNombre);

                if (isset($this->cacheAseguradoras[$asegKey])) {
                    $aseguradoraId = $this->cacheAseguradoras[$asegKey];
                } else {
                    $aseguradora = Aseguradora::firstOrCreate(
                        ['nombre' => $aseguradoraNombre],
                        ['nit' => 'IMP-' . mt_rand(100000, 999999)]
                    );
                    $aseguradoraId = $aseguradora->id;
                    $this->cacheAseguradoras[$asegKey] = $aseguradoraId;
                }

                $ramoNombre = trim($rowData['ramo'] ?? '');
                if (empty($ramoNombre)) $ramoNombre = 'No Definido';
                $ramoKey = strtolower($ramoNombre);

                if (isset($this->cacheRamos[$ramoKey])) {
                    $ramoId = $this->cacheRamos[$ramoKey];
                } else {
                    $ramo = Ramo::firstOrCreate(['nombre' => $ramoNombre]);
                    $ramoId = $ramo->id;
                    $this->cacheRamos[$ramoKey] = $ramoId;
                }

                $riesgoRef = trim($rowData['referencia'] ?? '');
                $riesgoId = null;
                if (!empty($riesgoRef)) {
                    $riesgoKey = strtolower($riesgoRef);
                    if (isset($this->cacheRiesgos[$riesgoKey])) {
                        $riesgoId = $this->cacheRiesgos[$riesgoKey];
                    } else {
                        $riesgo = Riesgo::firstOrCreate(['identificador' => $riesgoRef], ['tipo_riesgo' => 'Importado']);
                        $riesgoId = $riesgo->id;
                        $this->cacheRiesgos[$riesgoKey] = $riesgoId;
                        if (!$riesgo->clientes()->where('cliente_id', $clienteId)->exists()) {
                            $riesgo->clientes()->attach($clienteId);
                        }
                    }
                }

                $numeroPoliza = trim($rowData['numero póliza'] ?? '');
                if (empty($numeroPoliza)) $numeroPoliza = 'PENDIENTE-' . Str::random(8);

                $f_exp = $rowData['f expedición'] ?? null;
                $expedicion = is_numeric($f_exp) ? Date::excelToDateTimeObject($f_exp)->format('Y-m-d') : now()->format('Y-m-d');
                $vig_desde = $rowData['vigencia desde'] ?? null;
                $inicioVigencia = is_numeric($vig_desde) ? Date::excelToDateTimeObject($vig_desde)->format('Y-m-d') : now()->format('Y-m-d');
                $vig_hasta = $rowData['vigencia hasta'] ?? null;
                $finVigencia = is_numeric($vig_hasta) ? Date::excelToDateTimeObject($vig_hasta)->format('Y-m-d') : now()->addYear()->format('Y-m-d');

                $valAseg = is_numeric($rowData['valor asegurado'] ?? null) ? (float)$rowData['valor asegurado'] : 0;
                $prima = is_numeric($rowData['prima'] ?? null) ? (float)$rowData['prima'] : 0;
                $primaT = is_numeric($rowData['valor iva incluido'] ?? null) ? (float)$rowData['valor iva incluido'] : 0;
                $tasa = is_numeric($rowData['tasa'] ?? null) ? (float)$rowData['tasa'] : 0;

                try {
                    $poliza = Poliza::create([
                        'numero_poliza' => $numeroPoliza,
                        'aseguradora_id' => $aseguradoraId,
                        'ramo_id' => $ramoId,
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

                    if ($clienteId) {
                        $poliza->clientes()->attach($clienteId, ['rol' => 'tomador']);
                    }

                    $saldoPendiente = is_numeric($rowData['saldo pendiente'] ?? null) ? (float)$rowData['saldo pendiente'] : 0;
                    $cartera = Cartera::create([
                        'poliza_id' => $poliza->id,
                        'valor_a_pagar' => $primaT,
                        'fecha_limite' => $inicioVigencia,
                        'estado' => $saldoPendiente > 0 ? 'pendiente' : 'pagado'
                    ]);

                    $pagosKeys = [
                        ['monto' => 'pago 1', 'fecha' => 'f. 1 cuota'],
                        ['monto' => 'pago 2', 'fecha' => 'f. 2 cuota'],
                        ['monto' => 'pago 3', 'fecha' => 'f. 3 cuota'],
                        ['monto' => 'pago 4', 'fecha' => 'f. 4 cuota'],
                    ];

                    foreach ($pagosKeys as $p) {
                        $monto = is_numeric($rowData[$p['monto']] ?? null) ? (float)$rowData[$p['monto']] : 0;
                        if ($monto > 0) {
                            $f_pago = $rowData[$p['fecha']] ?? null;
                            $fechaPago = is_numeric($f_pago) ? Date::excelToDateTimeObject($f_pago)->format('Y-m-d') : now()->format('Y-m-d');
                            AbonoCartera::create([
                                'cartera_id' => $cartera->id,
                                'monto' => $monto,
                                'fecha_pago' => $fechaPago,
                                'metodo_pago' => 'Transferencia',
                                'referencia' => 'Importado',
                                'observaciones' => 'Abono Excel'
                            ]);
                        }
                    }

                } catch (\Exception $ex) {
                    \Illuminate\Support\Facades\Log::error("Fila $row error: " . $ex->getMessage());
                }

                $bar->advance();
            }

            $bar->finish();
            $this->info("\n¡Importación completada exitosamente!");

        } catch (\Exception $e) {
            $this->error('Ocurrió un error: ' . $e->getMessage());
        }
    }
}
