<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Poliza;
use App\Models\Riesgo;
use App\Models\Cartera;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            [
                'name' => 'Total Clientes',
                'stat' => (string) Cliente::count(),
                'icon' => 'M17 20h5V4H2v16h5m8 0v-5a2 2 0 00-2-2H9a2 2 0 00-2 2v5m8 0H7',
                'background' => 'bg-indigo-500'
            ],
            [
                'name' => 'Pólizas Vigentes',
                'stat' => (string) Poliza::where('estado', 'vigente')->count(),
                'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                'background' => 'bg-emerald-500'
            ],
            [
                'name' => 'Riesgos Registrados',
                'stat' => (string) Riesgo::count(),
                'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
                'background' => 'bg-orange-500'
            ],
            [
                'name' => 'Cartera por Cobrar',
                'stat' => '$' . number_format(
                    Cartera::where('estado', '!=', 'pagado')->sum('valor_a_pagar') - 
                    \App\Models\AbonoCartera::whereHas('cartera', function($q) {
                        $q->where('estado', '!=', 'pagado');
                    })->sum('monto'), 0, ',', '.'),
                'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                'background' => 'bg-rose-500'
            ],
        ];

        return Inertia::render('Dashboard', [
            'stats' => $stats
        ]);
    }
}
