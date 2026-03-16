<?php

namespace App\Http\Controllers;

use App\Models\PortalAgencia;
use App\Models\Aseguradora;
use Illuminate\Http\Request;
use Inertia\Inertia;

use App\Traits\AuditoriaHelper;

class PortalAgenciaController extends Controller
{
    use AuditoriaHelper;
    public function index(Request $request)
    {
        $query = PortalAgencia::with('aseguradora');

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where('nombre', 'like', "%{$search}%")
                  ->orWhere('usuario', 'like', "%{$search}%")
                  ->orWhereHas('aseguradora', function($q) use ($search) {
                      $q->where('nombre', 'like', "%{$search}%");
                  });
        }

        $portales = $query->latest()->paginate(10);

        return Inertia::render('Portales/Index', [
            'portales' => $portales,
            'filters' => $request->only('search')
        ]);
    }

    public function create()
    {
        return Inertia::render('Portales/Create', [
            'aseguradoras' => Aseguradora::orderBy('nombre')->get(['id', 'nombre'])
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'usuario' => 'required|string|max:255',
            'password' => 'required|string|max:255',
            'aseguradora_id' => 'nullable|exists:aseguradoras,id',
            'link' => 'nullable|url|max:255',
            'notas' => 'nullable|string',
        ]);

        $portal = PortalAgencia::create($validated);

        $this->registrarAuditoria('Crear Portal Agencia', 'Portal', $portal->id, $validated);

        return redirect()->route('portales.index')->with('success', 'Portal registrado exitosamente.');
    }

    public function edit(string $id)
    {
        $portal = PortalAgencia::findOrFail($id);
        
        return Inertia::render('Portales/Edit', [
            'portal' => $portal,
            'aseguradoras' => Aseguradora::orderBy('nombre')->get(['id', 'nombre'])
        ]);
    }

    public function update(Request $request, string $id)
    {
        $portal = PortalAgencia::findOrFail($id);

        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'usuario' => 'required|string|max:255',
            'password' => 'required|string|max:255',
            'aseguradora_id' => 'nullable|exists:aseguradoras,id',
            'link' => 'nullable|url|max:255',
            'notas' => 'nullable|string',
        ]);

        $antes = $portal->toArray();
        $portal->update($validated);

        $this->registrarAuditoria('Editar Portal Agencia', 'Portal', $portal->id, $validated, $antes);

        return redirect()->route('portales.index')->with('success', 'Portal actualizado exitosamente.');
    }

    public function destroy(string $id)
    {
        $portal = PortalAgencia::findOrFail($id);
        $this->registrarAuditoria('Eliminar Portal Agencia', 'Portal', $portal->id, $portal->toArray());

        $portal->delete();

        return redirect()->route('portales.index')->with('success', 'Portal eliminado exitosamente.');
    }
}
