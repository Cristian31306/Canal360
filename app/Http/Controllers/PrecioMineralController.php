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
        $precios = $query->orderBy('anio', 'desc')
                        ->orderBy('mes', 'desc')
                        ->paginate(10)
                        ->through(function ($precio) {
                            return [
                                'id' => $precio->id,
                                'mes' => $precio->mes,
                                'anio' => $precio->anio,
                                'oro' => $precio->oro,
                                'plata' => $precio->plata,
                                'platino' => $precio->platino,
                                'variaciones' => [
                                    'oro' => [
                                        'porcentaje' => $precio->calcularVariacion('oro'),
                                        'diferencia' => $precio->calcularDiferencia('oro'),
                                    ],
                                    'plata' => [
                                        'porcentaje' => $precio->calcularVariacion('plata'),
                                        'diferencia' => $precio->calcularDiferencia('plata'),
                                    ],
                                    'platino' => [
                                        'porcentaje' => $precio->calcularVariacion('platino'),
                                        'diferencia' => $precio->calcularDiferencia('platino'),
                                    ],
                                ]
                            ];
                        });

        return Inertia::render('Minerales/Index', [
            'precios' => $precios,
            'filters' => $request->only('anio')
        ]);
    }

    public function create()
    {
        return Inertia::render('Minerales/Create', [
            'meses' => [
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
            ],
            'anio_actual' => Carbon::now()->year
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'mes' => 'required|integer|between:1,12',
            'anio' => 'required|integer|min:2000',
            'oro' => 'required|numeric|min:0',
            'plata' => 'required|numeric|min:0',
            'platino' => 'required|numeric|min:0',
        ]);

        // Evitar duplicados para mes/año
        $exists = PrecioMineral::where('mes', $validated['mes'])
                             ->where('anio', $validated['anio'])
                             ->exists();

        if ($exists) {
            return redirect()->back()->withErrors(['mes' => 'Ya existe un registro para este mes y año.']);
        }

        $precio = PrecioMineral::create($validated);

        $this->registrarAuditoria('Crear Precio Mineral', 'Mineral', $precio->id, $validated);

        return redirect()->route('minerales.index')->with('success', 'Precio de minerales registrado exitosamente.');
    }

    public function edit(string $id)
    {
        $precio = PrecioMineral::findOrFail($id);
        
        return Inertia::render('Minerales/Edit', [
            'precio' => $precio,
            'meses' => [
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
            ]
        ]);
    }

    public function update(Request $request, string $id)
    {
        $precio = PrecioMineral::findOrFail($id);

        $validated = $request->validate([
            'mes' => 'required|integer|between:1,12',
            'anio' => 'required|integer|min:2000',
            'oro' => 'required|numeric|min:0',
            'plata' => 'required|numeric|min:0',
            'platino' => 'required|numeric|min:0',
        ]);

        // Verificar unicidad si cambió mes o año
        if ($precio->mes != $validated['mes'] || $precio->anio != $validated['anio']) {
            $exists = PrecioMineral::where('mes', $validated['mes'])
                                 ->where('anio', $validated['anio'])
                                 ->exists();

            if ($exists) {
                return redirect()->back()->withErrors(['mes' => 'Ya existe un registro para este mes y año.']);
            }
        }

        $antes = $precio->toArray();
        $precio->update($validated);

        $this->registrarAuditoria('Editar Precio Mineral', 'Mineral', $precio->id, $validated, $antes);

        return redirect()->route('minerales.index')->with('success', 'Precio de minerales actualizado exitosamente.');
    }

    public function destroy(string $id)
    {
        $precio = PrecioMineral::findOrFail($id);
        $this->registrarAuditoria('Eliminar Precio Mineral', 'Mineral', $precio->id, $precio->toArray());

        $precio->delete();

        return redirect()->route('minerales.index')->with('success', 'Registro eliminado exitosamente.');
    }
}
