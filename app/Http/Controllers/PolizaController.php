<?php

namespace App\Http\Controllers;

use App\Models\Poliza;
use App\Models\Aseguradora;
use App\Models\Ramo;
use App\Models\Riesgo;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class PolizaController extends Controller
{
    public function index(Request $request)
    {
        $query = Poliza::with(['aseguradora', 'ramo', 'riesgo', 'clientes']);

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where('numero_poliza', 'like', "%{$search}%")
                  ->orWhereHas('aseguradora', function($q) use ($search) {
                      $q->where('nombre', 'like', "%{$search}%");
                  })
                  ->orWhereHas('clientes', function($q) use ($search) {
                      $q->where('nombre_razon_social', 'like', "%{$search}%")
                        ->orWhere('numero_documento', 'like', "%{$search}%");
                  });
        }

        $polizas = $query->latest()->paginate(10);

        return Inertia::render('Polizas/Index', [
            'polizas' => $polizas,
            'filters' => $request->only('search')
        ]);
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
            'estado' => 'required|in:vigente,vencida,renovada,cancelada',
            'clientes' => 'required|array|min:1',
            'clientes.*.id' => 'required|exists:clientes,id',
            'clientes.*.rol' => 'required|in:tomador,asegurado,beneficiario',
        ]);

        DB::transaction(function () use ($validated) {
            $poliza = Poliza::create($validated);

            foreach ($validated['clientes'] as $clienteData) {
                $poliza->clientes()->attach($clienteData['id'], ['rol' => $clienteData['rol']]);
            }
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
            'estado' => 'required|in:vigente,vencida,renovada,cancelada',
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
}
