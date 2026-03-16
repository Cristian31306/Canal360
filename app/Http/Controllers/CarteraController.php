<?php
 
namespace App\Http\Controllers;
 
use App\Models\Cartera;
use App\Models\AbonoCartera;
use App\Models\Aseguradora;
use App\Models\Ramo;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use App\Exports\CarteraExport;
use Maatwebsite\Excel\Facades\Excel;
 
use App\Traits\AuditoriaHelper;

class CarteraController extends Controller
{
    use AuditoriaHelper;
    public function index(Request $request)
    {
        $query = Cartera::with(['poliza.aseguradora', 'poliza.ramo', 'poliza.clientes']);
 
        // Filtro de búsqueda general
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->whereHas('poliza', function($q) use ($search) {
                $q->where('numero_poliza', 'like', "%{$search}%")
                  ->orWhereHas('aseguradora', function($sq) use ($search) {
                      $sq->where('nombre', 'like', "%{$search}%");
                  })
                  ->orWhereHas('clientes', function($sq) use ($search) {
                      $sq->where('nombre_razon_social', 'like', "%{$search}%");
                  });
            });
        }
 
        // Filtro por Aseguradora
        if ($request->filled('aseguradora_id')) {
            $query->whereHas('poliza', function($q) use ($request) {
                $q->where('aseguradora_id', $request->aseguradora_id);
            });
        }
 
        // Filtro por Ramo
        if ($request->filled('ramo_id')) {
            $query->whereHas('poliza', function($q) use ($request) {
                $q->where('ramo_id', $request->ramo_id);
            });
        }
 
        // Filtro por Cliente
        if ($request->filled('cliente_id')) {
            $query->whereHas('poliza.clientes', function($q) use ($request) {
                $q->where('clientes.id', $request->cliente_id);
            });
        }
 
        // Filtro por Estado
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }
 
        // Filtro por Año
        if ($request->filled('anio')) {
            $query->whereHas('poliza', function($q) use ($request) {
                $q->whereYear('expedicion_fecha', $request->anio);
            });
        }
 
        $carteras = $query->latest()->paginate(10)->withQueryString();
 
        // Obtener años disponibles dinámicamente
        $isSqlite = DB::getDriverName() === 'sqlite';
        $yearSelect = $isSqlite ? 'strftime("%Y", expedicion_fecha)' : 'YEAR(expedicion_fecha)';
        $years = \App\Models\Poliza::selectRaw("$yearSelect as anio")
            ->distinct()
            ->orderBy('anio', 'desc')
            ->pluck('anio');
 
        return Inertia::render('Cartera/Index', [
            'carteras' => $carteras,
            'filters' => $request->all(['search', 'aseguradora_id', 'ramo_id', 'cliente_id', 'estado', 'anio']),
            'aseguradoras' => Aseguradora::orderBy('nombre')->get(['id', 'nombre']),
            'ramos' => Ramo::orderBy('nombre')->get(['id', 'nombre']),
            'clientes' => Cliente::orderBy('nombre_razon_social')->get(['id', 'nombre_razon_social']),
            'estados' => ['pendiente', 'pagado', 'acuerdo_pago', 'vencido'],
            'years' => $years
        ]);
    }
 
    public function show(string $id)
    {
        $cartera = Cartera::with(['poliza.aseguradora', 'poliza.ramo', 'poliza.riesgo', 'poliza.clientes', 'abonos'])
            ->findOrFail($id);
 
        return Inertia::render('Cartera/Show', [
            'cartera' => $cartera
        ]);
    }
 
    public function storeAbono(Request $request, string $id)
    {
        $cartera = Cartera::findOrFail($id);
 
        $validated = $request->validate([
            'monto' => 'required|numeric|min:0.01|max:' . ($cartera->saldo_pendiente + 0.01),
            'fecha_pago' => 'required|date',
            'metodo_pago' => 'required|string',
            'referencia' => 'nullable|string|max:255',
            'observaciones' => 'nullable|string',
        ]);
 
        DB::transaction(function () use ($cartera, $validated) {
            AbonoCartera::create([
                'cartera_id' => $cartera->id,
                'monto' => $validated['monto'],
                'fecha_pago' => $validated['fecha_pago'],
                'metodo_pago' => $validated['metodo_pago'],
                'referencia' => $validated['referencia'] ?? null,
                'observaciones' => $validated['observaciones'] ?? null,
            ]);
 
            // Recalcular estado
            if ($cartera->fresh()->saldo_pendiente <= 0) {
                $cartera->update(['estado' => 'pagado']);
            } elseif ($cartera->estado === 'pendiente' && $cartera->total_abonado > 0) {
                $cartera->update(['estado' => 'acuerdo_pago']);
            }

            $this->registrarAuditoria('Registrar Abono', 'Cartera', $cartera->id, $validated);
        });
 
        return redirect()->back()->with('success', 'Abono registrado exitosamente.');
    }

    public function destroy(string $id)
    {
        $cartera = Cartera::findOrFail($id);

        if ($cartera->abonos()->count() > 0) {
            return redirect()->back()->with('error', 'No se puede eliminar el registro de cartera porque ya tiene abonos realizados.');
        }

        $this->registrarAuditoria('Eliminar Registro Cartera', 'Cartera', $cartera->id, $cartera->toArray());

        $cartera->delete();

        return redirect()->route('cartera.index')->with('success', 'Registro de cartera eliminado exitosamente.');
    }

    public function destroyAbono(string $id)
    {
        $abono = AbonoCartera::findOrFail($id);
        $cartera = $abono->cartera;
        
        $abono->delete();

        // Recalcular estado de la cartera
        if ($cartera->total_abonado <= 0) {
            $cartera->update(['estado' => 'pendiente']);
        } elseif ($cartera->saldo_pendiente > 0) {
            $cartera->update(['estado' => 'acuerdo_pago']);
        }

        $this->registrarAuditoria('Eliminar Abono', 'Cartera', $cartera->id, ['abono_id' => $id]);

        return redirect()->back()->with('success', 'Abono eliminado exitosamente.');
    }

    public function export(Request $request)
    {
        return Excel::download(new CarteraExport($request->all()), 'reporte_cartera_' . now()->format('Ymd_His') . '.xlsx');
    }
}
