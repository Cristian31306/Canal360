<?php
header('Content-Type: text/plain');
echo "=== CANAL360 SOAP DIAGNOSTICS ===\n\n";
echo "PHP Version: " . PHP_VERSION . "\n";
echo "SoapClient Class Exists: " . (class_exists('SoapClient') ? 'YES' : 'NO') . "\n";
echo "SoapServer Class Exists: " . (class_exists('SoapServer') ? 'YES' : 'NO') . "\n";

try {
    // Intentar bootstrap de Laravel para inspeccionar la base de datos
    require __DIR__ . '/../../vendor/autoload.php';
    $app = require_once __DIR__ . '/../../bootstrap/app.php';
    $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
    
    echo "\n=== CLIENT SEARCH (HECTOR) ===\n";
    $clients = App\Models\Cliente::where('nombre_razon_social', 'like', '%HECTOR%')->get();
    if ($clients->isEmpty()) {
        echo "No clients found matching HECTOR.\n";
    } else {
        foreach ($clients as $client) {
            echo "ID: {$client->id} | Name: {$client->nombre_razon_social} | Document: '{$client->numero_documento}'\n";
        }
    }
} catch (\Throwable $e) {
    echo "\nError bootstrapping Laravel database: " . $e->getMessage() . "\n";
}
