<?php

namespace App\Http\Controllers;

use App\Models\CatMineral;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;
use App\Traits\AuditoriaHelper;

class CatMineralController extends Controller
{
    use AuditoriaHelper;

    public function index()
    {
        return Inertia::render('Minerales/Config', [
            'minerales' => CatMineral::orderBy('nombre')->get()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255|unique:cat_minerales,nombre',
            'activo' => 'required|boolean',
        ]);

        $validated['slug'] = Str::slug($validated['nombre']);

        $mineral = CatMineral::create($validated);

        $this->registrarAuditoria('Crear Tipo Mineral', 'Configuración', $mineral->id, $validated);

        return redirect()->back()->with('success', 'Mineral agregado correctamente.');
    }

    public function update(Request $request, CatMineral $cat_minerale)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255|unique:cat_minerales,nombre,' . $cat_minerale->id,
            'activo' => 'required|boolean',
        ]);

        $validated['slug'] = Str::slug($validated['nombre']);
        
        $antes = $cat_minerale->toArray();
        $cat_minerale->update($validated);

        $this->registrarAuditoria('Editar Tipo Mineral', 'Configuración', $cat_minerale->id, $validated, $antes);

        return redirect()->back()->with('success', 'Mineral actualizado correctamente.');
    }

    public function destroy(CatMineral $cat_minerale)
    {
        // Verificar si tiene precios asociados
        if ($cat_minerale->valores()->exists()) {
            return redirect()->back()->withErrors(['error' => 'No se puede eliminar un mineral que ya tiene registros de precios.']);
        }

        $cat_minerale->delete();
        
        $this->registrarAuditoria('Eliminar Tipo Mineral', 'Configuración', $cat_minerale->id, ['id' => $cat_minerale->id]);

        return redirect()->back()->with('success', 'Mineral eliminado correctamente.');
    }
}
