<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Auditoria;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AuditoriaController extends Controller
{
    /**
     * Check if the authenticated user is an admin.
     */
    protected function authorizeAdmin()
    {
        if (!auth()->user()->is_admin) {
            abort(403, 'No tienes permisos para acceder a esta sección.');
        }
    }

    /**
     * Display a listing of the audit logs.
     */
    public function index(Request $request)
    {
        $this->authorizeAdmin();

        $query = Auditoria::with('usuario:id,name,email');

        // Filtros
        if ($request->filled('usuario_id')) {
            $query->where('usuario_id', $request->usuario_id);
        }

        if ($request->filled('accion')) {
            $query->where('accion', 'like', '%' . $request->accion . '%');
        }

        if ($request->filled('entidad')) {
            $query->where('entidad_afectada', 'like', '%' . $request->entidad . '%');
        }

        if ($request->filled('fecha_inicio')) {
            $query->whereDate('created_at', '>=', $request->fecha_inicio);
        }

        if ($request->filled('fecha_fin')) {
            $query->whereDate('created_at', '<=', $request->fecha_fin);
        }

        return Inertia::render('Admin/Auditoria/Index', [
            'auditorias' => $query->latest()->paginate(20)->withQueryString(),
            'usuarios' => User::select('id', 'name')->orderBy('name')->get(),
            'filters' => $request->only(['usuario_id', 'accion', 'entidad', 'fecha_inicio', 'fecha_fin']),
        ]);
    }
}
