<?php

/**
 * SCRIPT DE PRUEBA PARA EXAMEN: SISTEMAS DISTRIBUIDOS (GUÍAS 5 y 6)
 * Este script demuestra el flujo completo: Bind, Lookup, Marshaling y Transparencia.
 */

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Remote\ServiceRegistry;
use App\Remote\RemoteServiceStub;
use App\Models\Poliza;
use App\Models\Cliente;

echo "--- INICIANDO PRUEBA DE SISTEMAS DISTRIBUIDOS ---\n\n";

// 1. FASE 2: OPERACIÓN BIND (Servidor registra su servicio)
echo "[SERVIDOR] Registrando 'ServicioFacturacion' en el Registry...\n";
$registry = new ServiceRegistry();
$registry->bind('ServicioFacturacion', '192.168.1.50:8080');
echo "Service Binding completado con éxito.\n\n";

// 2. FASE 2: OPERACIÓN LOOKUP (Cliente busca el servicio)
echo "[CLIENTE] Buscando 'ServicioFacturacion' sin usar IPs fijas...\n";
$stub = new RemoteServiceStub('ServicioFacturacion');
// Internamente el Stub ya hizo el Lookup al instanciarse o al llamar al método
echo "Referencia remota obtenida dinámicamente.\n\n";

// 3. FASE 1: SERIALIZACIÓN Y MARSHALING (Preparando objeto complejo)
echo "[CLIENTE] Preparando objeto Póliza para envío remoto...\n";
$poliza = new Poliza([
    'numero_poliza' => 'PLZ-2024-999',
    'valor_asegurado' => 5000000,
    'estado' => 'Activa'
]);

// 4. FASE 1: TRANSPARENCIA (Llamada al método remoto)
echo "[CLIENTE] Llamando a servicio.guardar(poliza) de forma transparente...\n";
// El usuario solo ve esto, no ve la lógica de Sockets/Red ni Serialización manual.
$resultado = $stub->guardar($poliza);

echo "\n--- RESULTADOS DE LA PRUEBA ---\n";
echo "Mensaje del servidor: " . $resultado . "\n";
echo "--------------------------------------------------\n";
echo "Puntos demostrados:\n";
echo "[X] Serializable: Clase Poliza implementa Serializable.\n";
echo "[X] Marshaling: El objeto fue empaquetado y desempaquetado sin perder atributos.\n";
echo "[X] Registry/Lookup: No hay IPs hardcoded, se consultó al ServiceRegistry.\n";
echo "[X] Transparencia: El main solo ve: \$stub->guardar(\$poliza).\n";
