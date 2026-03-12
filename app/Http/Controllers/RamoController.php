<?php

namespace App\Http\Controllers;

use App\Models\Ramo;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RamoController extends Controller
{
    public function index(Request $request)
    {
        $query = Ramo::query();
        
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where('nombre', 'like', "%{$search}%");
        }
        
        $ramos = $query->latest()->paginate(10);

        return Inertia::render('Ramos/Index', [
            'ramos' => $ramos,
            'filters' => $request->only('search')
        ]);
    }

    public function create()
    {
        return Inertia::render('Ramos/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255|unique:ramos,nombre',
        ]);

        Ramo::create($validated);

        return redirect()->route('ramos.index')->with('success', 'Ramo creado exitosamente.');
    }

    public function edit(string $id)
    {
        $ramo = Ramo::findOrFail($id);
        return Inertia::render('Ramos/Edit', [
            'ramo' => $ramo
        ]);
    }

    public function update(Request $request, string $id)
    {
        $ramo = Ramo::findOrFail($id);

        $validated = $request->validate([
            'nombre' => 'required|string|max:255|unique:ramos,nombre,' . $ramo->id,
        ]);

        $ramo->update($validated);

        return redirect()->route('ramos.index')->with('success', 'Ramo actualizado exitosamente.');
    }

    public function destroy(string $id)
    {
        $ramo = Ramo::findOrFail($id);
        $ramo->delete();

        return redirect()->route('ramos.index')->with('success', 'Ramo eliminado exitosamente.');
    }
}
