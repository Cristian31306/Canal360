<?php

namespace App\Http\Controllers;

use App\Remote\ServiceRegistry;
use App\Remote\Marshaller;
use App\Models\Poliza;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RemoteMonitorController extends Controller
{
    public function index()
    {
        $registry = new ServiceRegistry();
        
        return Inertia::render('Admin/RemoteMonitor', [
            'services' => $registry->listServices(),
        ]);
    }

    public function simulate()
    {
        // 1. Objeto Original
        $poliza = new Poliza([
            'numero_poliza' => 'PLZ-SIM-' . rand(1000, 9999),
            'valor_asegurado' => rand(1000000, 9000000),
            'estado' => 'vigente'
        ]);

        // 2. Proceso de Marshaling (Guía 5)
        $marshaled = Marshaller::marshal($poliza);

        // 3. Proceso de Unmarshaling (Guía 5)
        $reconstructed = Marshaller::unmarshal($marshaled);

        return response()->json([
            'steps' => [
                [
                    'title' => 'Objeto de Negocio (Original)',
                    'desc' => 'Objeto vivo en la memoria del cliente antes de ser enviado.',
                    'data' => $poliza->toArray()
                ],
                [
                    'title' => 'Marshaling (Empaquetado)',
                    'desc' => 'Conversión del objeto a una trama JSON serializada para el transporte por socket.',
                    'data' => json_decode($marshaled, true),
                    'raw' => $marshaled
                ],
                [
                    'title' => 'Unmarshaling (Reconstrucción)',
                    'desc' => 'El servidor recibe los bytes y reconstruye el objeto original garantizando la integridad.',
                    'data' => $reconstructed->toArray()
                ]
            ]
        ]);
    }
}
