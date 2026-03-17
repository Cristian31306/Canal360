<?php

namespace App\Http\Controllers;

use App\Models\Titulo360;
use App\Models\Aseguradora;
use App\Models\Riesgo;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Traits\AuditoriaHelper;

class Titulo360Controller extends Controller
{
    use AuditoriaHelper;

    public function index(Request $request)
    {
        $query = Titulo360::with('aseguradora');

        // Filtro de búsqueda general
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('par', 'like', "%{$search}%")
                    ->orWhere('titulo', 'like', "%{$search}%")
                    ->orWhere('nombre', 'like', "%{$search}%")
                    ->orWhere('departamento', 'like', "%{$search}%")
                    ->orWhere('municipio', 'like', "%{$search}%")
                    ->orWhere('asesores', 'like', "%{$search}%");
            });
        }

        // Filtros específicos
        if ($request->filled('aseguradora_id')) {
            $query->where('aseguradora_id', $request->aseguradora_id);
        }

        if ($request->filled('cliente_canal')) {
            $query->where('cliente_canal', $request->cliente_canal === 'si');
        }

        // Filtros por rango de fechas (Inicio)
        if ($request->filled('inicio_desde')) {
            $query->whereDate('fecha_inicio', '>=', $request->inicio_desde);
        }
        if ($request->filled('inicio_hasta')) {
            $query->whereDate('fecha_inicio', '<=', $request->inicio_hasta);
        }

        // Filtros por rango de fechas (Fin)
        if ($request->filled('fin_desde')) {
            $query->whereDate('fecha_fin', '>=', $request->fin_desde);
        }
        if ($request->filled('fin_hasta')) {
            $query->whereDate('fecha_fin', '<=', $request->fin_hasta);
        }

        // Ordenamiento
        $sortColumn = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        
        // Validar columnas para evitar inyección si se exponen campos directos
        $validSorts = ['titulo', 'nombre', 'valor_asegurado', 'fecha_inicio', 'fecha_fin', 'created_at'];
        if (in_array($sortColumn, $validSorts)) {
            $query->orderBy($sortColumn, $sortDirection);
        } else {
            $query->latest();
        }

        $titulos = $query->paginate(20)->withQueryString();

        return Inertia::render('Titulos360/Index', [
            'titulos' => $titulos,
            'filters' => $request->all(['search', 'aseguradora_id', 'cliente_canal', 'inicio_desde', 'inicio_hasta', 'fin_desde', 'fin_hasta', 'sort', 'direction']),
            'aseguradoras' => Aseguradora::orderBy('nombre')->get(['id', 'nombre']),
        ]);
    }

    public function create()
    {
        return Inertia::render('Titulos360/Create', [
            'aseguradoras' => Aseguradora::orderBy('nombre')->get(['id', 'nombre']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'par' => 'nullable|string|max:255',
            'titulo' => 'required|string|max:255|unique:titulos_360,titulo',
            'nombre' => 'required|string|max:255',
            'minerales' => 'nullable|string',
            'departamento' => 'nullable|string|max:255',
            'municipio' => 'nullable|string|max:255',
            'etapa' => 'nullable|string|max:255',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date',
            'aseguradora_id' => 'nullable|exists:aseguradoras,id',
            'aseguradora_nombre' => 'nullable|string|max:255',
            'valor_asegurado' => 'nullable|numeric|min:0',
            'correo' => 'nullable|string',
            'celular' => 'nullable|string',
            'asesores' => 'nullable|string',
        ]);

        $titulo = Titulo360::create($validated);

        $this->registrarAuditoria('Crear Título 360', 'Módulo 360', $titulo->id, $validated);

        return redirect()->route('titulos-360.index')->with('success', 'Título minero registrado correctamente.');
    }

    public function edit(Titulo360 $titulos_360)
    {
        return Inertia::render('Titulos360/Edit', [
            'titulo' => $titulos_360,
            'aseguradoras' => Aseguradora::orderBy('nombre')->get(['id', 'nombre']),
        ]);
    }

    public function update(Request $request, Titulo360 $titulos_360)
    {
        $validated = $request->validate([
            'par' => 'nullable|string|max:255',
            'titulo' => 'required|string|max:255|unique:titulos_360,titulo,' . $titulos_360->id,
            'nombre' => 'required|string|max:255',
            'minerales' => 'nullable|string',
            'departamento' => 'nullable|string|max:255',
            'municipio' => 'nullable|string|max:255',
            'etapa' => 'nullable|string|max:255',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date',
            'aseguradora_id' => 'nullable|exists:aseguradoras,id',
            'aseguradora_nombre' => 'nullable|string|max:255',
            'valor_asegurado' => 'nullable|numeric|min:0',
            'correo' => 'nullable|string',
            'celular' => 'nullable|string',
            'asesores' => 'nullable|string',
        ]);

        $antes = $titulos_360->toArray();
        $titulos_360->update($validated);

        $this->registrarAuditoria('Editar Título 360', 'Módulo 360', $titulos_360->id, $validated, $antes);

        return redirect()->route('titulos-360.index')->with('success', 'Título minero actualizado correctamente.');
    }

    public function destroy(Titulo360 $titulos_360)
    {
        $titulos_360->delete();
        $this->registrarAuditoria('Eliminar Título 360', 'Módulo 360', $titulos_360->id, ['id' => $titulos_360->id]);

        return redirect()->back()->with('success', 'Título eliminado correctamente.');
    }
}
