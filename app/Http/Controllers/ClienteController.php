<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ClienteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Cliente::query();
        
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where('nombre_razon_social', 'like', "%{$search}%")
                  ->orWhere('numero_documento', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
        }
        
        $clientes = $query->latest()->paginate(10);
        return Inertia::render('Clientes/Index', [
            'clientes' => $clientes,
            'filters' => $request->only('search')
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Clientes/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tipo_persona' => 'required|in:natural,juridica',
            'tipo_documento' => 'required|string|max:10',
            'numero_documento' => 'required|string|max:50|unique:clientes,numero_documento',
            'nombre_razon_social' => 'required|string|max:255',
            'telefono' => 'required|string|max:50',
            'email' => 'required|email|max:255',
            'direccion' => 'required|string|max:255',
            'ciudad' => 'required|string|max:100',
            'fecha_nacimiento' => 'nullable|date',
            'fecha_contacto' => 'nullable|date',
            'observaciones' => 'nullable|string',
            'rep_legal_nombre' => 'nullable|string|max:255',
            'rep_legal_documento' => 'nullable|string|max:50',
            'rep_legal_telefono' => 'nullable|string|max:50',
            'rep_legal_email' => 'nullable|email|max:255',
        ]);

        Cliente::create($validated);

        return redirect()->route('clientes.index')->with('success', 'Cliente registrado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $cliente = Cliente::findOrFail($id);
        
        // Cargar las relaciones cuando existan:
        // $cliente->load(['riesgos', 'polizas']);

        return Inertia::render('Clientes/Show', [
            'cliente' => $cliente
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $cliente = Cliente::findOrFail($id);
        return Inertia::render('Clientes/Edit', [
            'cliente' => $cliente
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $cliente = Cliente::findOrFail($id);

        $validated = $request->validate([
            'tipo_persona' => 'required|in:natural,juridica',
            'tipo_documento' => 'required|string|max:10',
            'numero_documento' => 'required|string|max:50|unique:clientes,numero_documento,'.$cliente->id,
            'nombre_razon_social' => 'required|string|max:255',
            'telefono' => 'required|string|max:50',
            'email' => 'required|email|max:255',
            'direccion' => 'required|string|max:255',
            'ciudad' => 'required|string|max:100',
            'fecha_nacimiento' => 'nullable|date',
            'fecha_contacto' => 'nullable|date',
            'observaciones' => 'nullable|string',
            'rep_legal_nombre' => 'nullable|string|max:255',
            'rep_legal_documento' => 'nullable|string|max:50',
            'rep_legal_telefono' => 'nullable|string|max:50',
            'rep_legal_email' => 'nullable|email|max:255',
        ]);

        $cliente->update($validated);

        return redirect()->route('clientes.index')->with('success', 'Cliente actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $cliente = Cliente::findOrFail($id);
        $cliente->delete();

        return redirect()->route('clientes.index')->with('success', 'Cliente eliminado exitosamente.');
    }
}
