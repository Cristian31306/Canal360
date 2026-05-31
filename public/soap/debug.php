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

    // Prueba de llamada SOAP local
    echo "\n=== SOAP CLIENT TEST CALL ===\n";
    $wsdlPath = __DIR__ . '/service.wsdl';
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)) ? "https" : "http";
    $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';
    $location = "$protocol://$host/soap/server.php";

    echo "Local WSDL file exists: " . (file_exists($wsdlPath) ? 'YES' : 'NO') . "\n";
    echo "Target Location URL: $location\n";

    $client = new \SoapClient($wsdlPath, [
        'location' => $location,
        'trace' => true,
        'exceptions' => true,
        'cache_wsdl' => WSDL_CACHE_NONE
    ]);

    $documento = 'IMP-ARIAHNMSNMBS';
    $valorAsegurado = 500000;
    
    echo "Calling validatePolicy('$documento', $valorAsegurado)...\n";
    $response = $client->validatePolicy($documento, $valorAsegurado);
    echo "SOAP Response type: " . gettype($response) . "\n";
    echo "SOAP Response data:\n";
    print_r($response);

} catch (\Throwable $e) {
    echo "\nError during diagnostics: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
