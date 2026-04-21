<?php

namespace App\Imports;

use App\Models\Aseguradora;
use App\Models\Cliente;
use App\Models\Poliza;
use App\Models\Ramo;
use App\Models\Riesgo;
use App\Models\Cartera;
use App\Models\AbonoCartera;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithLimit;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class PolizasImport implements ToModel, WithHeadingRow, WithCalculatedFormulas, WithChunkReading
{
    private $rowCount = 0;
    
    // Cachés locales para evitar consultas a BD
    private $cacheAseguradoras = [];
    private $cacheClientes = [];
    private $cacheRamos = [];
    private $cacheRiesgos = [];

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
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $this->rowCount++;
        if ($this->rowCount % 50 === 0) {
            Log::info("Importando fila " . $this->rowCount . "...");
        }
        
        // 1. Cliente
        $clienteNombre = trim($row['cliente'] ?? '');
        $clienteId = null;
        if (!empty($clienteNombre)) {
            $cacheKey = strtolower($clienteNombre);
            if (isset($this->cacheClientes[$cacheKey])) {
                $clienteId = $this->cacheClientes[$cacheKey];
            } else {
                $cliente = Cliente::firstOrCreate(
                    ['nombre_razon_social' => $clienteNombre],
                    [
                        'tipo_persona' => 'juridica',
                        'email' => $row['correo'] ?? 'importado@algorah.bond',
                        'telefono' => $row['telefono'] ?? '0000000',
                        'tipo_documento' => 'NIT',
                        'numero_documento' => 'IMP-' . mt_rand(100000, 999999) . '-' . mt_rand(1000, 9999)
                    ]
                );
                $clienteId = $cliente->id;
                $this->cacheClientes[$cacheKey] = $clienteId;
            }
        }

        // 2. Aseguradora
        $aseguradoraNombreRaw = trim($row['aseguradora'] ?? '');
        if (empty($aseguradoraNombreRaw)) {
            $aseguradoraNombreRaw = 'No Definida';
        }
        $aseguradoraNombre = $this->mapAseguradoraName($aseguradoraNombreRaw);
        $cacheKeyAseg = strtolower($aseguradoraNombre);
        
        if (isset($this->cacheAseguradoras[$cacheKeyAseg])) {
            $aseguradoraId = $this->cacheAseguradoras[$cacheKeyAseg];
        } else {
            $aseguradora = Aseguradora::firstOrCreate(
                ['nombre' => $aseguradoraNombre],
                [
                    'nit' => 'IMP-' . mt_rand(100000, 999999),
                    'contacto_nombre' => 'Importado',
                    'telefono' => '0000000',
                    'email' => 'importado@aseguradora.com',
                ]
            );
            $aseguradoraId = $aseguradora->id;
            $this->cacheAseguradoras[$cacheKeyAseg] = $aseguradoraId;
        }

        // 3. Ramo
        $ramoNombre = trim($row['ramo'] ?? '');
        if (empty($ramoNombre)) {
            $ramoNombre = 'No Definido';
        }
        $cacheKeyRamo = strtolower($ramoNombre);
        if (isset($this->cacheRamos[$cacheKeyRamo])) {
            $ramoId = $this->cacheRamos[$cacheKeyRamo];
        } else {
            $ramo = Ramo::firstOrCreate(['nombre' => $ramoNombre]);
            $ramoId = $ramo->id;
            $this->cacheRamos[$cacheKeyRamo] = $ramoId;
        }

        // 4. Riesgo
        $riesgoReferencia = trim($row['referencia'] ?? '');
        $riesgoId = null;
        if (!empty($riesgoReferencia) && $clienteId) {
            $cacheKeyRiesgo = strtolower($riesgoReferencia);
            if (isset($this->cacheRiesgos[$cacheKeyRiesgo])) {
                $riesgoId = $this->cacheRiesgos[$cacheKeyRiesgo];
                // Relación la asumo ya creada si está en cache
            } else {
                $riesgo = Riesgo::firstOrCreate(
                    [
                        'identificador' => $riesgoReferencia,
                    ],
                    [
                        'tipo_riesgo' => 'Importado',
                        'descripcion' => 'Importado desde Excel'
                    ]
                );
                $riesgoId = $riesgo->id;
                $this->cacheRiesgos[$cacheKeyRiesgo] = $riesgoId;
                
                // Relacionar riesgo con el cliente si no están relacionados
                if (!$riesgo->clientes()->where('cliente_id', $clienteId)->exists()) {
                    $riesgo->clientes()->attach($clienteId);
                }
            }
        }

        // Si faltan datos clave, omitir (o podríamos insertar con nulls si es permitido)
        if (!$aseguradoraId || !$ramoId) {
            // Se requiere aseguradora y ramo según la base de datos
            return null; 
        }

        // Parsear fechas
        $expedicionFecha = $this->parseDate($row['f_expedicion'] ?? null);
        $inicioVigencia = $this->parseDate($row['vigencia_desde'] ?? null);
        $finVigencia = $this->parseDate($row['vigencia_hasta'] ?? null);

        // Parsear Valores
        $prima = $this->parseNumber($row['prima'] ?? 0);
        $primaTotal = $this->parseNumber($row['valor_iva_incluido'] ?? 0);
        $iva = $primaTotal - $prima;
        $valorAsegurado = $this->parseNumber($row['valor_asegurado'] ?? 0);
        $tasa = $this->parseNumber($row['tasa'] ?? null);
        $numeroPoliza = trim($row['numero_poliza'] ?? '');
        if (empty($numeroPoliza)) {
            $numeroPoliza = 'PENDIENTE-' . Str::random(8); // Número temporal único
        }

        // 5. Crear Poliza
        $poliza = Poliza::create([
            'numero_poliza' => $numeroPoliza,
            'aseguradora_id' => $aseguradoraId,
            'ramo_id' => $ramoId,
            'riesgo_id' => $riesgoId,
            'expedicion_fecha' => $expedicionFecha ?? now()->format('Y-m-d'),
            'inicio_vigencia' => $inicioVigencia ?? now()->format('Y-m-d'),
            'fin_vigencia' => $finVigencia ?? now()->addYear()->format('Y-m-d'),
            'valor_asegurado' => $valorAsegurado,
            'prima_antes_iva' => $prima,
            'iva' => $iva > 0 ? $iva : 0,
            'prima_total' => $primaTotal,
            'tasa' => $tasa,
            'estado' => 'vigente', // Default
        ]);

        // Crear relación con cliente si existe
        if ($clienteId) {
            $poliza->clientes()->attach($clienteId, ['rol' => 'tomador']); // Asumiendo que la relacion es muchos a muchos a traves de cliente_poliza
        }

        // 6. Cartera y Abonos
        $saldoPendiente = $this->parseNumber($row['saldo_pendiente'] ?? 0);
        $cartera = Cartera::create([
            'poliza_id' => $poliza->id,
            'valor_a_pagar' => $primaTotal,
            'fecha_limite' => $inicioVigencia ?? now()->format('Y-m-d'),
            'estado' => $saldoPendiente > 0 ? 'pendiente' : 'pagado'
        ]);

        // Registrar abonos si los hay
        $pagos = [
            ['monto' => $row['pago_1'] ?? null, 'fecha' => $row['f_1_cuota'] ?? null],
            ['monto' => $row['pago_2'] ?? null, 'fecha' => $row['f_2_cuota'] ?? null],
            ['monto' => $row['pago_3'] ?? null, 'fecha' => $row['f_3_cuota'] ?? null],
            ['monto' => $row['pago_4'] ?? null, 'fecha' => $row['f_4_cuota'] ?? null],
        ];

        foreach ($pagos as $p) {
            $monto = $this->parseNumber($p['monto']);
            if ($monto > 0) {
                AbonoCartera::create([
                    'cartera_id' => $cartera->id,
                    'monto' => $monto,
                    'fecha_pago' => $this->parseDate($p['fecha']) ?? now()->format('Y-m-d'),
                    'metodo_pago' => 'Transferencia',
                    'referencia' => 'Importado',
                    'observaciones' => 'Abono desde Excel'
                ]);
            }
        }

        return $poliza;
    }

    private function parseDate($value)
    {
        if (empty($value)) return null;

        if (is_numeric($value)) {
            try {
                return Date::excelToDateTimeObject($value)->format('Y-m-d');
            } catch (\Exception $e) {
                return null;
            }
        }

        try {
            return Carbon::createFromFormat('d/m/Y', $value)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    private function parseNumber($value)
    {
        if (empty($value) || $value === '-' || $value === ' ') return 0;
        // Limpiar símbolos de moneda y separadores
        $clean = preg_replace('/[^0-9.\-]/', '', $value);
        return is_numeric($clean) ? (float) $clean : 0;
    }

    public function chunkSize(): int
    {
        return 500;
    }
}
