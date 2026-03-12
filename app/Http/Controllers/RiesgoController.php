<?php

namespace App\Http\Controllers;

use App\Models\Riesgo;
use App\Models\Cliente;
use App\Models\Ramo;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RiesgoController extends Controller
{
    public function index(Request $request)
    {
        $query = Riesgo::with('clientes');
        
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where('identificador', 'like', "%{$search}%")
                  ->orWhere('tipo_riesgo', 'like', "%{$search}%")
                  ->orWhereHas('clientes', function($q) use ($search) {
                      $q->where('nombre_razon_social', 'like', "%{$search}%")
                        ->orWhere('numero_documento', 'like', "%{$search}%");
                  });
        }
        
        $riesgos = $query->latest()->paginate(10);

        return Inertia::render('Riesgos/Index', [
            'riesgos' => $riesgos,
            'filters' => $request->only('search')
        ]);
    }

    public function create()
    {
        $clientes = Cliente::orderBy('nombre_razon_social')->select('id', 'nombre_razon_social', 'numero_documento')->get();
        $ramos = Ramo::orderBy('nombre')->get();
        
        return Inertia::render('Riesgos/Create', [
            'clientes' => $clientes,
            'ramos' => $ramos
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'cliente_ids' => 'required|array|min:1',
            'cliente_ids.*' => 'exists:clientes,id',
            'tipo_riesgo' => 'required|string|max:100',
            'identificador' => 'nullable|string|max:150',
            'descripcion' => 'nullable|string',
            'es_nad' => 'boolean',
            'numero_nad' => 'nullable|required_if:es_nad,true|string|max:100',
        ]);

        $riesgo = Riesgo::create([
            'tipo_riesgo' => $validated['tipo_riesgo'],
            'identificador' => $validated['identificador'],
            'descripcion' => $validated['descripcion'],
            'es_nad' => $validated['es_nad'] ?? false,
            'numero_nad' => $validated['numero_nad'] ?? null,
        ]);

        $riesgo->clientes()->attach($validated['cliente_ids']);

        return redirect()->route('riesgos.index')->with('success', 'Riesgo registrado exitosamente.');
    }

    public function show(string $id)
    {
        $riesgo = Riesgo::with(['clientes', 'polizas.aseguradora', 'polizas.ramo'])->findOrFail($id);

        return Inertia::render('Riesgos/Show', [
            'riesgo' => $riesgo
        ]);
    }

    public function edit(string $id)
    {
        $riesgo = Riesgo::with('clientes:id')->findOrFail($id);
        $clientes = Cliente::orderBy('nombre_razon_social')->select('id', 'nombre_razon_social', 'numero_documento')->get();
        $ramos = Ramo::orderBy('nombre')->get();
        
        // Transformar clientes para que el frontend maneje solo IDs
        $riesgo->cliente_ids = $riesgo->clientes->pluck('id');
        unset($riesgo->clientes);

        return Inertia::render('Riesgos/Edit', [
            'riesgo' => $riesgo,
            'clientes' => $clientes,
            'ramos' => $ramos
        ]);
    }

    public function update(Request $request, string $id)
    {
        $riesgo = Riesgo::findOrFail($id);

        $validated = $request->validate([
            'cliente_ids' => 'required|array|min:1',
            'cliente_ids.*' => 'exists:clientes,id',
            'tipo_riesgo' => 'required|string|max:100',
            'identificador' => 'nullable|string|max:150',
            'descripcion' => 'nullable|string',
            'es_nad' => 'boolean',
            'numero_nad' => 'nullable|required_if:es_nad,true|string|max:100',
        ]);

        $riesgo->update([
            'tipo_riesgo' => $validated['tipo_riesgo'],
            'identificador' => $validated['identificador'],
            'descripcion' => $validated['descripcion'],
            'es_nad' => $validated['es_nad'] ?? false,
            'numero_nad' => $validated['numero_nad'] ?? null,
        ]);

        $riesgo->clientes()->sync($validated['cliente_ids']);

        return redirect()->route('riesgos.index')->with('success', 'Riesgo actualizado exitosamente.');
    }

    public function destroy(string $id)
    {
        $riesgo = Riesgo::findOrFail($id);
        $riesgo->delete();

        return redirect()->route('riesgos.index')->with('success', 'Riesgo eliminado exitosamente.');
    }
}
