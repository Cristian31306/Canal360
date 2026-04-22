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
        'seg estado' => 'Seguros del Estado S.A.',
        'global' => 'Global Seguros de Vida S.A.',
        'hdi' => 'HDI Seguros Colombia S.A.',
        'mundial' => 'Compañía Mundial de Seguros S.A.',
        'previsora' => 'La Previsora S.A. Compañía de Seguros',
        'solidaria' => 'Aseguradora Solidaria de Colombia',
        'berkley' => 'Berkley International Seguros Colombia S.A.',
        'axacolpatria' => 'AXA Colpatria Seguros S.A.',
        'mapfre' => 'Mapfre Seguros Generales S.A.',
        'liberty' => 'Liberty Seguros S.A.',
    ];

    private $cacheClientes = [];
    private $cacheAseguradoras = [];
    private $cacheRamos = [];
    private $cacheRiesgos = [];

    private function mapAseguradoraName($name)
    {
        $lowerName = strtolower(trim($name));
        if (empty($lowerName)) return 'No Definida';

        foreach ($this->mapeoAseguradoras as $key => $officialName) {
            if (str_contains($lowerName, $key)) {
                return $officialName;
            }
        }
        return trim($name);
    }

    private function parseDate($value, $default = null)
    {
        if (empty($value)) return $default ?? now()->format('Y-m-d');
        
        if (is_numeric($value)) {
            try {
                return Date::excelToDateTimeObject($value)->format('Y-m-d');
            } catch (\Exception $e) {
                return $default ?? now()->format('Y-m-d');
            }
        }

        try {
            return \Carbon\Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception $e) {
            return $default ?? now()->format('Y-m-d');
        }
    }

    public function handle()
    {
        if ($this->option('clear')) {
            $this->warn('Limpiando datos previos de clientes, pólizas, carteras y riesgos...');
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            AbonoCartera::truncate();
            Cartera::truncate();
            DB::table('cliente_poliza')->truncate();
            Poliza::truncate();
            DB::table('cliente_riesgo')->truncate();
            Riesgo::truncate();
            Cliente::truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            $this->info('Tablas de seguros limpias (Clientes, Pólizas, Riesgos, Carteras).');
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
                // Limpiar cabeceras de acentos y espacios para un mapeo robusto
                $val = strtolower(trim($worksheet->getCellByColumnAndRow($col, 1)->getValue() ?? ''));
                $val = str_replace(['ó', 'é', 'í', 'á', 'ú'], ['o', 'e', 'i', 'a', 'u'], $val);
                $cabeceras[$col] = $val;
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
                            'email' => 'importado@canal360.com.co',
                            'telefono' => '0000000',
                            'tipo_documento' => 'NIT',
                            'numero_documento' => 'IMP-' . strtoupper(Str::random(12))
                        ]
                    );
                    $clienteId = $cliente->id;
                    $this->cacheClientes[$clienteKey] = $clienteId;
                }

                $aseguradoraNombreRaw = trim($rowData['aseguradora'] ?? '');
                $aseguradoraNombre = $this->mapAseguradoraName($aseguradoraNombreRaw);
                $asegKey = strtolower($aseguradoraNombre);

                if (isset($this->cacheAseguradoras[$asegKey])) {
                    $aseguradoraId = $this->cacheAseguradoras[$asegKey];
                } else {
                    $aseguradora = Aseguradora::firstOrCreate(
                        ['nombre' => $aseguradoraNombre],
                        ['nit' => 'NIT-' . strtoupper(Str::random(10))]
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
                        $riesgo = Riesgo::firstOrCreate(['identificador' => $riesgoRef], ['tipo_riesgo' => 'General']);
                        $riesgoId = $riesgo->id;
                        $this->cacheRiesgos[$riesgoKey] = $riesgoId;
                    }
                    // Vincular riesgo al cliente si no existe
                    if (!$riesgo->clientes()->where('cliente_id', $clienteId)->exists()) {
                        $riesgo->clientes()->attach($clienteId);
                    }
                }

                $numeroPoliza = trim($rowData['numero poliza'] ?? '');
                if (empty($numeroPoliza)) {
                    $bar->advance();
                    continue;
                }

                // Procesamiento robusto de fechas
                $expedicion = $this->parseDate($rowData['f expedicion'] ?? null);
                $inicioVigencia = $this->parseDate($rowData['vigencia desde'] ?? null);
                $finVigencia = $this->parseDate($rowData['vigencia hasta'] ?? null, \Carbon\Carbon::parse($inicioVigencia)->addYear()->format('Y-m-d'));

                $valAseg = is_numeric($rowData['valor asegurado'] ?? null) ? (float)$rowData['valor asegurado'] : 0;
                $prima = is_numeric($rowData['prima'] ?? null) ? (float)$rowData['prima'] : 0;
                $primaT = is_numeric($rowData['valor iva incluido'] ?? null) ? (float)$rowData['valor iva incluido'] : 0;
                $tasa = is_numeric($rowData['tasa'] ?? null) ? (float)$rowData['tasa'] : 0;
                $abono = is_numeric($rowData['abono'] ?? null) ? (float)$rowData['abono'] : 0;

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

                    // Vincular SIEMPRE al cliente, tenga o no tenga riesgo
                    $poliza->clientes()->attach($clienteId, ['rol' => 'tomador']);

                    // Crear cartera
                    $cartera = Cartera::create([
                        'poliza_id' => $poliza->id,
                        'valor_a_pagar' => $primaT,
                        'fecha_limite' => $inicioVigencia,
                        'estado' => $abono >= $primaT ? 'pagado' : 'pendiente'
                    ]);

                    // Registrar abono si existe
                    if ($abono > 0) {
                        AbonoCartera::create([
                            'cartera_id' => $cartera->id,
                            'monto' => $abono,
                            'fecha_pago' => $expedicion,
                            'metodo_pago' => 'Transferencia',
                            'referencia' => 'Abono Importado Excel',
                            'observaciones' => 'Migración Inicial'
                        ]);
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
