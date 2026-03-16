<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use Inertia\Inertia;

use App\Traits\AuditoriaHelper;

class ClienteController extends Controller
{
    use AuditoriaHelper;
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
            'numero_documento' => [
                'required', 
                'string', 
                'max:50', 
                \Illuminate\Validation\Rule::unique('clientes')->whereNull('deleted_at')
            ],
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

        $cliente = Cliente::create($validated);

        $this->registrarAuditoria('Crear Cliente', 'Cliente', $cliente->id, $validated);

        return redirect()->route('clientes.index')->with('success', 'Cliente registrado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $cliente = Cliente::with([
            'riesgos.polizas.aseguradora', 
            'riesgos.polizas.ramo', 
            'polizas.aseguradora', 
            'polizas.ramo',
            'annaCredentials',
            'paymentCredentials.aseguradora'
        ])->findOrFail($id);

        return Inertia::render('Clientes/Show', [
            'cliente' => $cliente
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $cliente = Cliente::with(['annaCredentials', 'paymentCredentials.aseguradora'])->findOrFail($id);
        $aseguradoras = \App\Models\Aseguradora::orderBy('nombre')->get(['id', 'nombre']);
        
        return Inertia::render('Clientes/Edit', [
            'cliente' => $cliente,
            'aseguradoras' => $aseguradoras
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
            'numero_documento' => [
                'required', 
                'string', 
                'max:50', 
                \Illuminate\Validation\Rule::unique('clientes')->ignore($cliente->id)->whereNull('deleted_at')
            ],
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

        $antes = $cliente->toArray();
        $cliente->update($validated);

        $this->registrarAuditoria('Editar Cliente', 'Cliente', $cliente->id, $validated, $antes);

        // Actualizar Credenciales ANNA
        $cliente->annaCredentials()->delete();
        if ($request->has('anna_credentials')) {
            foreach ($request->input('anna_credentials') as $cred) {
                if (!empty($cred['usuario']) && !empty($cred['password'])) {
                    $cliente->annaCredentials()->create($cred);
                }
            }
        }

        // Actualizar Credenciales de Pagos
        $cliente->paymentCredentials()->delete();
        if ($request->has('payment_credentials')) {
            foreach ($request->input('payment_credentials') as $cred) {
                if (!empty($cred['aseguradora_id']) && !empty($cred['usuario']) && !empty($cred['password'])) {
                    $cliente->paymentCredentials()->create($cred);
                }
            }
        }

        return redirect()->route('clientes.index')->with('success', 'Cliente actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $cliente = Cliente::findOrFail($id);

        if ($cliente->polizas()->exists()) {
            return redirect()->back()->with('error', 'No se puede eliminar el cliente porque tiene pólizas asociadas.');
        }

        if ($cliente->riesgos()->exists()) {
            return redirect()->back()->with('error', 'No se puede eliminar el cliente porque tiene riesgos asociados.');
        }

        $this->registrarAuditoria('Eliminar Cliente', 'Cliente', $cliente->id, $cliente->toArray());

        $cliente->delete();

        return redirect()->route('clientes.index')->with('success', 'Cliente eliminado exitosamente.');
    }
}
