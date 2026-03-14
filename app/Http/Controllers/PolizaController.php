<?php

namespace App\Http\Controllers;

use App\Models\Poliza;
use App\Models\Aseguradora;
use App\Models\Ramo;
use App\Models\Riesgo;
use App\Models\Cliente;
use App\Models\Cartera;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use App\Exports\PolizasExport;
use Maatwebsite\Excel\Facades\Excel;

class PolizaController extends Controller
{
    public function index(Request $request)
    {
        $query = Poliza::with(['aseguradora', 'ramo', 'riesgo', 'clientes']);

        // Filtro de estado para la vista general
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        } else {
            // Por defecto ocultamos las que están en proceso o liquidadas de la vista general si se prefiere
            // O las mostramos todas. El usuario pidió secciones aparte, así que quizás filtrar.
        }

        // Filtro de búsqueda general
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('numero_poliza', 'like', "%{$search}%")
                  ->orWhereHas('aseguradora', function($sq) use ($search) {
                      $sq->where('nombre', 'like', "%{$search}%");
                  })
                  ->orWhereHas('clientes', function($sq) use ($search) {
                      $sq->where('nombre_razon_social', 'like', "%{$search}%")
                        ->orWhere('numero_documento', 'like', "%{$search}%");
                  });
            });
        }

        // Filtro por Aseguradora
        if ($request->filled('aseguradora_id')) {
            $query->where('aseguradora_id', $request->aseguradora_id);
        }

        // Filtro por Ramo
        if ($request->filled('ramo_id')) {
            $query->where('ramo_id', $request->ramo_id);
        }

        // Filtro por Cliente
        if ($request->filled('cliente_id')) {
            $query->whereHas('clientes', function($q) use ($request) {
                $q->where('clientes.id', $request->cliente_id);
            });
        }

        // Filtro por Año (Vigencia o Expedición)
        if ($request->filled('anio')) {
            $column = $request->get('fecha_tipo', 'inicio_vigencia'); // Default a inicio_vigencia si no se especifica
            $query->whereYear($column, $request->anio);
        }

        $polizas = $query->latest()->paginate(10)->withQueryString();

        return Inertia::render('Polizas/Index', [
            'polizas' => $polizas,
            'filters' => $request->all(['search', 'aseguradora_id', 'ramo_id', 'cliente_id', 'anio', 'fecha_tipo', 'estado']),
            'aseguradoras' => Aseguradora::orderBy('nombre')->get(['id', 'nombre']),
            'ramos' => Ramo::orderBy('nombre')->get(['id', 'nombre']),
            'clientes' => Cliente::orderBy('nombre_razon_social')->get(['id', 'nombre_razon_social']),
        ]);
    }

    /**
     * Vista de Renovaciones y Trámites
     */
    public function renewals(Request $request)
    {
        $tab = $request->get('tab', 'upcoming');
        $query = Poliza::with(['aseguradora', 'ramo', 'riesgo', 'clientes', 'polizaAnterior:id,liquidacion']);

        if ($tab === 'upcoming') {
            // Pólizas vigentes o vencidas que vencen en los próximos 91 días o vencieron hace máximo 30 días
            // Y que NO tienen ya un trámite de renovación iniciado
            $query->whereIn('estado', ['vigente', 'vencida'])
                  ->whereBetween('fin_vigencia', [now()->subDays(30), now()->addDays(91)])
                  ->whereDoesntHave('polizaSiguiente');
        } elseif ($tab === 'liquidated') {
            // Pólizas clonadas esperando ser enviadas
            $query->where('estado', 'liquidada');
        } elseif ($tab === 'processing') {
            // Pólizas en la aseguradora
            $query->where('estado', 'en_proceso');
        } elseif ($tab === 'lost') {
            // Pólizas vencidas hace más de 30 días y que no han sido marcadas como renovadas
            $query->where('estado', 'vencida')
                  ->where('fin_vigencia', '<', now()->subDays(30));
        }

        // Aplicar los mismos filtros que en index si se desea
        if ($request->filled('aseguradora_id')) $query->where('aseguradora_id', $request->aseguradora_id);
        if ($request->filled('ramo_id')) $query->where('ramo_id', $request->ramo_id);

        return Inertia::render('Polizas/Renewals/Index', [
            'polizas' => $query->latest()->paginate(10)->withQueryString(),
            'filters' => $request->all(['tab', 'aseguradora_id', 'ramo_id']),
            'aseguradoras' => Aseguradora::orderBy('nombre')->get(['id', 'nombre']),
            'ramos' => Ramo::orderBy('nombre')->get(['id', 'nombre']),
        ]);
    }

    /**
     * Iniciar liquidación (Clonar póliza)
     */
    public function liquidate(Request $request, Poliza $poliza)
    {
        // Evitar doble liquidación si ya tiene un trámite iniciado
        if ($poliza->polizaSiguiente()->exists()) {
            return redirect()->back()->with('error', 'Esta póliza ya tiene un trámite de renovación iniciado.');
        }

        $request->validate([
            'liquidacion' => 'required|string',
        ]);

        return DB::transaction(function () use ($poliza, $request) {
            // Aseguramos que la póliza y sus relaciones estén bien cargadas
            $poliza->loadMissing('clientes');

            // Clonamos la póliza actual
            $newPoliza = $poliza->replicate();
            
            // Ajustamos los datos para la nueva versión (trámite)
            $newPoliza->fill([
                'estado' => 'liquidada',
                'numero_poliza' => $poliza->numero_poliza . '-RENO-' . now()->year,
                'liquidacion' => $request->liquidacion,
                'poliza_anterior_id' => $poliza->id,
                'expedicion_fecha' => now(),
            ]);
            
            // Fechas tentativas (Asegurando instancias de Carbon)
            if ($poliza->fin_vigencia) {
                $newPoliza->inicio_vigencia = $poliza->fin_vigencia;
                $newPoliza->fin_vigencia = \Illuminate\Support\Carbon::parse($poliza->fin_vigencia)->addYear();
            }
            
            $newPoliza->save();

            // Clonar la relación de clientes con sus roles originales
            foreach ($poliza->clientes as $cliente) {
                $newPoliza->clientes()->attach($cliente->id, [
                    'rol' => $cliente->pivot->rol
                ]);
            }

            return redirect()->back()->with('success', 'Póliza liquidada y preparada para trámite.');
        });
    }

    /**
     * Enviar a la aseguradora (En Proceso)
     */
    public function sendToInsurance(Poliza $poliza)
    {
        $poliza->update(['estado' => 'en_proceso']);
        return redirect()->back()->with('success', 'Póliza marcada "En Proceso" (Enviada a Aseguradora).');
    }

    /**
     * Finalizar Renovación (Vigente)
     */
    public function finalizeRenewal(Request $request, Poliza $poliza)
    {
        $validated = $request->validate([
            'numero_poliza' => 'required|string',
            'anexo' => 'required|integer|min:0',
            'expedicion_fecha' => 'required|date',
            'inicio_vigencia' => 'required|date',
            'fin_vigencia' => 'required|date|after:inicio_vigencia',
            'valor_asegurado' => 'required|numeric|min:0',
            'prima_antes_iva' => 'required|numeric|min:0',
            'prima_total' => 'required|numeric|min:0',
            'tasa' => 'nullable|numeric|min:0',
        ]);

        return DB::transaction(function () use ($poliza, $validated) {
            // Aseguramos que IVA sea 0 si no se usa
            $data = array_merge($validated, [
                'estado' => 'vigente',
                'iva' => 0
            ]);

            // Actualizar la nueva póliza a vigente con todos los datos finales
            $poliza->update($data);

            // Si tiene una póliza anterior vinculada, marcarla como renovada
            if ($poliza->poliza_anterior_id) {
                Poliza::where('id', $poliza->poliza_anterior_id)->update(['estado' => 'renovada']);
            }

            // Crear registro en Cartera automáticamente
            Cartera::updateOrCreate(
                ['poliza_id' => $poliza->id],
                [
                    'valor_a_pagar' => $poliza->prima_total,
                    'fecha_limite' => $poliza->expedicion_fecha->copy()->addDays(30),
                    'estado' => 'pendiente'
                ]
            );

            return redirect()->back()->with('success', 'Renovación finalizada exitosamente. La póliza ahora está vigente.');
        });
    }

    /**
     * Cancelar un trámite de renovación (Eliminar el registro clonado)
     */
    public function cancelRenewal(Poliza $poliza)
    {
        // Solo permitir borrar si está en trámite (no si ya es vigente/terminada)
        if (!in_array($poliza->estado, ['liquidada', 'en_proceso', 'vencida'])) {
             return redirect()->back()->with('error', 'No se puede cancelar una póliza en este estado.');
        }

        $poliza->delete();
        return redirect()->back()->with('success', 'Trámite de renovación cancelado. La póliza original volverá a aparecer para ser liquidada.');
    }

    public function create()
    {
        return Inertia::render('Polizas/Create', [
            'aseguradoras' => Aseguradora::orderBy('nombre')->get(['id', 'nombre']),
            'ramos' => Ramo::orderBy('nombre')->get(['id', 'nombre']),
            'riesgos' => Riesgo::with('clientes:id,nombre_razon_social,numero_documento')->orderBy('tipo_riesgo')->get(['id', 'tipo_riesgo', 'identificador']),
            'clientes' => Cliente::orderBy('nombre_razon_social')->get(['id', 'nombre_razon_social', 'numero_documento']),
            'roles' => ['tomador', 'asegurado', 'beneficiario']
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'numero_poliza' => 'required|string|unique:polizas,numero_poliza',
            'aseguradora_id' => 'required|exists:aseguradoras,id',
            'ramo_id' => 'required|exists:ramos,id',
            'riesgo_id' => 'required|exists:riesgos,id',
            'expedicion_fecha' => 'required|date',
            'inicio_vigencia' => 'required|date',
            'fin_vigencia' => 'required|date|after:inicio_vigencia',
            'valor_asegurado' => 'required|numeric|min:0',
            'prima_antes_iva' => 'required|numeric|min:0',
            'iva' => 'required|numeric|min:0',
            'prima_total' => 'required|numeric|min:0',
            'tasa' => 'nullable|numeric|min:0',
            'estado' => 'required|in:vigente,vencida,renovada,cancelada,liquidada,en_proceso',
            'liquidacion' => 'nullable|string',
            'poliza_anterior_id' => 'nullable|exists:polizas,id',
            'clientes' => 'required|array|min:1',
            'clientes.*.id' => 'required|exists:clientes,id',
            'clientes.*.rol' => 'required|in:tomador,asegurado,beneficiario',
        ]);

        DB::transaction(function () use ($validated) {
            $validated['iva'] = 0; // Se establece en 0 por defecto
            $poliza = Poliza::create($validated);
 
            foreach ($validated['clientes'] as $clienteData) {
                $poliza->clientes()->attach($clienteData['id'], ['rol' => $clienteData['rol']]);
            }

            // Crear registro en Cartera automáticamente
            Cartera::create([
                'poliza_id' => $poliza->id,
                'valor_a_pagar' => $poliza->prima_total,
                'fecha_limite' => $poliza->expedicion_fecha->addDays(30), // Por defecto 30 días
                'estado' => 'pendiente'
            ]);
        });

        return redirect()->route('polizas.index')->with('success', 'Póliza registrada exitosamente.');
    }

    public function show(string $id)
    {
        $poliza = Poliza::with(['aseguradora', 'ramo', 'riesgo', 'clientes', 'cartera'])->findOrFail($id);

        return Inertia::render('Polizas/Show', [
            'poliza' => $poliza
        ]);
    }

    public function edit(string $id)
    {
        $poliza = Poliza::with('clientes')->findOrFail($id);
        
        return Inertia::render('Polizas/Edit', [
            'poliza' => $poliza,
            'aseguradoras' => Aseguradora::orderBy('nombre')->get(['id', 'nombre']),
            'ramos' => Ramo::orderBy('nombre')->get(['id', 'nombre']),
            'riesgos' => Riesgo::with('clientes:id,nombre_razon_social,numero_documento')->orderBy('tipo_riesgo')->get(['id', 'tipo_riesgo', 'identificador']),
            'clientes' => Cliente::orderBy('nombre_razon_social')->get(['id', 'nombre_razon_social', 'numero_documento']),
            'roles' => ['tomador', 'asegurado', 'beneficiario']
        ]);
    }

    public function update(Request $request, string $id)
    {
        $poliza = Poliza::findOrFail($id);

        $validated = $request->validate([
            'numero_poliza' => 'required|string|unique:polizas,numero_poliza,' . $poliza->id,
            'aseguradora_id' => 'required|exists:aseguradoras,id',
            'ramo_id' => 'required|exists:ramos,id',
            'riesgo_id' => 'required|exists:riesgos,id',
            'expedicion_fecha' => 'required|date',
            'inicio_vigencia' => 'required|date',
            'fin_vigencia' => 'required|date|after:inicio_vigencia',
            'valor_asegurado' => 'required|numeric|min:0',
            'prima_antes_iva' => 'required|numeric|min:0',
            'iva' => 'required|numeric|min:0',
            'prima_total' => 'required|numeric|min:0',
            'tasa' => 'nullable|numeric|min:0',
            'estado' => 'required|in:vigente,vencida,renovada,cancelada,liquidada,en_proceso',
            'liquidacion' => 'nullable|string',
            'poliza_anterior_id' => 'nullable|exists:polizas,id',
            'clientes' => 'required|array|min:1',
            'clientes.*.id' => 'required|exists:clientes,id',
            'clientes.*.rol' => 'required|in:tomador,asegurado,beneficiario',
        ]);

        DB::transaction(function () use ($poliza, $validated) {
            $poliza->update($validated);

            $syncData = [];
            foreach ($validated['clientes'] as $clienteData) {
                $syncData[$clienteData['id']] = ['rol' => $clienteData['rol']];
            }
            $poliza->clientes()->sync($syncData);
        });

        return redirect()->route('polizas.index')->with('success', 'Póliza actualizada exitosamente.');
    }

    public function destroy(string $id)
    {
        $poliza = Poliza::findOrFail($id);
        $poliza->delete();

        return redirect()->route('polizas.index')->with('success', 'Póliza eliminada exitosamente.');
    }

    public function export(Request $request)
    {
        return Excel::download(new PolizasExport($request->all()), 'reporte_polizas_' . now()->format('Ymd_His') . '.xlsx');
    }
}
