<?php

namespace App\Http\Controllers;

use App\Models\Aseguradora;
use Illuminate\Http\Request;
use Inertia\Inertia;

use App\Traits\AuditoriaHelper;

class AseguradoraController extends Controller
{
    use AuditoriaHelper;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Aseguradora::query();
        
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where('nombre', 'like', "%{$search}%")
                  ->orWhere('nit', 'like', "%{$search}%")
                  ->orWhereHas('contactos', function($q) use ($search) {
                      $q->where('nombre', 'like', "%{$search}%");
                  });
        }
        
        $aseguradoras = $query->with('contactos')->latest()->paginate(10);
        return Inertia::render('Aseguradoras/Index', [
            'aseguradoras' => $aseguradoras,
            'filters' => $request->only('search')
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Aseguradoras/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'nit' => [
                'required', 
                'string', 
                'max:50', 
                \Illuminate\Validation\Rule::unique('aseguradoras')->whereNull('deleted_at')
            ],
            'contactos' => 'nullable|array',
            'contactos.*.rol' => 'required|string|max:255',
            'contactos.*.nombre' => 'required|string|max:255',
            'contactos.*.telefono' => 'nullable|string|max:50',
            'contactos.*.email' => 'nullable|email|max:255',
            'logo' => 'nullable|image|max:2048',
        ]);

        $data = [
            'nombre' => $validated['nombre'],
            'nit' => $validated['nit']
        ];

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('logos', 'public');
        }

        $aseguradora = Aseguradora::create($data);

        if (!empty($validated['contactos'])) {
            $aseguradora->contactos()->createMany($validated['contactos']);
        }

        $this->registrarAuditoria('Crear Aseguradora', 'Aseguradora', $aseguradora->id, $validated);

        return redirect()->route('aseguradoras.index')->with('success', 'Aseguradora registrada exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $aseguradora = Aseguradora::with(['contactos', 'ramos'])->findOrFail($id);
        
        // Ramos únicos que tienen pólizas con esta aseguradora
        $ramosCount = \App\Models\Poliza::where('aseguradora_id', $aseguradora->id)
            ->join('ramos', 'polizas.ramo_id', '=', 'ramos.id')
            ->select('ramos.nombre', \DB::raw('count(*) as total'))
            ->groupBy('ramos.nombre')
            ->get();

        $driver = \DB::getDriverName();
        $yearExpr = $driver === 'sqlite' ? "strftime('%Y', inicio_vigencia)" : "YEAR(inicio_vigencia)";

        // Estadísticas por año (Año de inicio de vigencia)
        $statsAnuales = \App\Models\Poliza::where('aseguradora_id', $aseguradora->id)
            ->select(\DB::raw("$yearExpr as anio"), \DB::raw('count(*) as total'))
            ->groupBy('anio')
            ->orderBy('anio', 'desc')
            ->get();

        return Inertia::render('Aseguradoras/Show', [
            'aseguradora' => $aseguradora,
            'ramosCount' => $ramosCount,
            'statsAnuales' => $statsAnuales
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $aseguradora = Aseguradora::with('contactos')->findOrFail($id);
        return Inertia::render('Aseguradoras/Edit', [
            'aseguradora' => $aseguradora
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $aseguradora = Aseguradora::findOrFail($id);

        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'nit' => [
                'required', 
                'string', 
                'max:50', 
                \Illuminate\Validation\Rule::unique('aseguradoras')->ignore($aseguradora->id)->whereNull('deleted_at')
            ],
            'contactos' => 'nullable|array',
            'contactos.*.id' => 'nullable|integer|exists:contacto_aseguradoras,id',
            'contactos.*.rol' => 'required|string|max:255',
            'contactos.*.nombre' => 'required|string|max:255',
            'contactos.*.telefono' => 'nullable|string|max:50',
            'contactos.*.email' => 'nullable|email|max:255',
            'logo' => 'nullable|image|max:2048',
        ]);

        $data = [
            'nombre' => $validated['nombre'],
            'nit' => $validated['nit']
        ];

        if ($request->hasFile('logo')) {
            if ($aseguradora->logo) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($aseguradora->logo);
            }
            $data['logo'] = $request->file('logo')->store('logos', 'public');
        } elseif ($request->boolean('remove_logo')) {
            if ($aseguradora->logo) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($aseguradora->logo);
            }
            $data['logo'] = null;
        }

        $antes = $aseguradora->toArray();
        $aseguradora->update($data);

        if (isset($validated['contactos']) && is_array($validated['contactos'])) {
            $contactosExistentesIds = [];

            foreach ($validated['contactos'] as $contactoData) {
                if (!empty($contactoData['id'])) {
                    $contacto = $aseguradora->contactos()->find($contactoData['id']);
                    if ($contacto) {
                        $contacto->update($contactoData);
                        $contactosExistentesIds[] = $contacto->id;
                    }
                } else {
                    $nuevoContacto = $aseguradora->contactos()->create($contactoData);
                    $contactosExistentesIds[] = $nuevoContacto->id;
                }
            }
            // Eliminar contactos que ya no estén en la lista
            $aseguradora->contactos()->whereNotIn('id', $contactosExistentesIds)->delete();
        } else {
            $aseguradora->contactos()->delete();
        }

        $this->registrarAuditoria('Editar Aseguradora', 'Aseguradora', $aseguradora->id, $validated, $antes);

        return redirect()->route('aseguradoras.index')->with('success', 'Aseguradora actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $aseguradora = Aseguradora::findOrFail($id);

        if ($aseguradora->polizas()->exists()) {
            return redirect()->back()->with('error', 'No se puede eliminar la aseguradora porque tiene pólizas asociadas.');
        }

        if ($aseguradora->contactos()->exists()) {
            return redirect()->back()->with('error', 'No se puede eliminar la aseguradora porque tiene contactos registrados.');
        }

        $this->registrarAuditoria('Eliminar Aseguradora', 'Aseguradora', $aseguradora->id, $aseguradora->toArray());

        $aseguradora->delete();

        return redirect()->route('aseguradoras.index')->with('success', 'Aseguradora eliminada exitosamente.');
    }
}
