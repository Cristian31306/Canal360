<?php

namespace App\Http\Controllers;

use App\Models\PrecioMineral;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Carbon;

use App\Traits\AuditoriaHelper;

class PrecioMineralController extends Controller
{
    use AuditoriaHelper;
    public function index(Request $request)
    {
        $query = PrecioMineral::query();

        if ($request->has('anio') && $request->anio) {
            $query->where('anio', $request->anio);
        }

        $mineralesActivos = \App\Models\CatMineral::where('activo', true)->get();

        $precios = $query->with('valores.mineral')
                        ->orderBy('anio', 'desc')
                        ->orderBy('mes', 'desc')
                        ->paginate(10)
                        ->through(function ($p) use ($mineralesActivos) {
                            $data = [
                                'id' => $p->id,
                                'mes' => $p->mes,
                                'anio' => $p->anio,
                                'variaciones' => []
                            ];

                            foreach ($mineralesActivos as $m) {
                                $slug = $m->slug;
                                $data[$slug] = $p->getValor($slug);
                                $data['variaciones'][$slug] = [
                                    'porcentaje' => $p->calcularVariacion($slug),
                                    'diferencia' => $p->calcularDiferencia($slug),
                                ];
                            }
                            
                            return $data;
                        });

        return Inertia::render('Minerales/Index', [
            'precios' => $precios,
            'minerales' => $mineralesActivos,
            'filters' => $request->only('anio')
        ]);
    }

    public function create()
    {
        return Inertia::render('Minerales/Create', [
            'meses' => $this->getMeses(),
            'anio_actual' => Carbon::now()->year,
            'minerales' => \App\Models\CatMineral::where('activo', true)->get()
        ]);
    }

    public function store(Request $request)
    {
        $rules = [
            'mes' => 'required|integer|between:1,12',
            'anio' => 'required|integer|min:2000',
            'precios' => 'required|array',
        ];

        $mineralesActivos = \App\Models\CatMineral::where('activo', true)->get();
        foreach ($mineralesActivos as $m) {
            $rules["precios.{$m->id}"] = 'required|numeric|min:0';
        }

        $validated = $request->validate($rules);

        // Evitar duplicados para mes/año
        $exists = PrecioMineral::where('mes', $validated['mes'])
                             ->where('anio', $validated['anio'])
                             ->exists();

        if ($exists) {
            return redirect()->back()->withErrors(['mes' => 'Ya existe un registro para este mes y año.']);
        }

        $pMineral = PrecioMineral::create([
            'mes' => $validated['mes'],
            'anio' => $validated['anio'],
        ]);

        foreach ($validated['precios'] as $catId => $precio) {
            \App\Models\PrecioMineralValor::create([
                'precio_mineral_id' => $pMineral->id,
                'cat_mineral_id' => $catId,
                'precio' => $precio,
            ]);
        }

        $this->registrarAuditoria('Crear Precio Mineral', 'Mineral', $pMineral->id, $validated);

        return redirect()->route('minerales.index')->with('success', 'Precio de minerales registrado exitosamente.');
    }

    public function edit(string $id)
    {
        $precio = PrecioMineral::with('valores')->findOrFail($id);
        
        $preciosMap = [];
        foreach ($precio->valores as $v) {
            $preciosMap[$v->cat_mineral_id] = $v->precio;
        }

        return Inertia::render('Minerales/Edit', [
            'precio' => $precio,
            'precios_existentes' => $preciosMap,
            'meses' => $this->getMeses(),
            'minerales' => \App\Models\CatMineral::where('activo', true)->get()
        ]);
    }

    public function update(Request $request, string $id)
    {
        $pMineral = PrecioMineral::findOrFail($id);

        $rules = [
            'mes' => 'required|integer|between:1,12',
            'anio' => 'required|integer|min:2000',
            'precios' => 'required|array',
        ];

        $mineralesActivos = \App\Models\CatMineral::where('activo', true)->get();
        foreach ($mineralesActivos as $m) {
            $rules["precios.{$m->id}"] = 'required|numeric|min:0';
        }

        $validated = $request->validate($rules);

        // Verificar unicidad si cambió mes o año
        if ($pMineral->mes != $validated['mes'] || $pMineral->anio != $validated['anio']) {
            $exists = PrecioMineral::where('mes', $validated['mes'])
                                 ->where('anio', $validated['anio'])
                                 ->exists();

            if ($exists) {
                return redirect()->back()->withErrors(['mes' => 'Ya existe un registro para este mes y año.']);
            }
        }

        $antes = $pMineral->load('valores')->toArray();
        $pMineral->update([
            'mes' => $validated['mes'],
            'anio' => $validated['anio'],
        ]);

        foreach ($validated['precios'] as $catId => $precio) {
            \App\Models\PrecioMineralValor::updateOrCreate(
                ['precio_mineral_id' => $pMineral->id, 'cat_mineral_id' => $catId],
                ['precio' => $precio]
            );
        }

        $this->registrarAuditoria('Editar Precio Mineral', 'Mineral', $pMineral->id, $validated, $antes);

        return redirect()->route('minerales.index')->with('success', 'Precio de minerales actualizado exitosamente.');
    }

    private function getMeses()
    {
        return [
            ['id' => 1, 'nombre' => 'Enero'],
            ['id' => 2, 'nombre' => 'Febrero'],
            ['id' => 3, 'nombre' => 'Marzo'],
            ['id' => 4, 'nombre' => 'Abril'],
            ['id' => 5, 'nombre' => 'Mayo'],
            ['id' => 6, 'nombre' => 'Junio'],
            ['id' => 7, 'nombre' => 'Julio'],
            ['id' => 8, 'nombre' => 'Agosto'],
            ['id' => 9, 'nombre' => 'Septiembre'],
            ['id' => 10, 'nombre' => 'Octubre'],
            ['id' => 11, 'nombre' => 'Noviembre'],
            ['id' => 12, 'nombre' => 'Diciembre'],
        ];
    }

    public function destroy(string $id)
    {
        $precio = PrecioMineral::findOrFail($id);
        $this->registrarAuditoria('Eliminar Precio Mineral', 'Mineral', $precio->id, $precio->toArray());

        $precio->delete();

        return redirect()->route('minerales.index')->with('success', 'Registro eliminado exitosamente.');
    }
}
