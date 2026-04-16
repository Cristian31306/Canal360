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
                'icon' => 'M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z',
                'background' => 'bg-indigo-500'
            ],
            [
                'name' => 'Pólizas Vigentes',
                'stat' => (string) Poliza::where('estado', 'vigente')->count(),
                'icon' => 'M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z',
                'background' => 'bg-emerald-500'
            ],
            [
                'name' => 'Riesgos Registrados',
                'stat' => (string) Riesgo::count(),
                'icon' => 'M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.74c0 5.592 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z',
                'background' => 'bg-orange-500'
            ],
            [
                'name' => 'Cartera por Cobrar',
                'stat' => '$' . number_format(
                    Cartera::where('estado', '!=', 'pagado')->sum('valor_a_pagar') - 
                    \App\Models\AbonoCartera::whereHas('cartera', function($q) {
                        $q->where('estado', '!=', 'pagado');
                    })->sum('monto'), 0, ',', '.'),
                'icon' => 'M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                'background' => 'bg-rose-500'
            ],
        ];

        return Inertia::render('Dashboard', [
            'stats' => $stats
        ]);
    }
}
