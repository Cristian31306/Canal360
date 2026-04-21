<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Polizas: " . \App\Models\Poliza::count() . "\n";
echo "Clientes: " . \App\Models\Cliente::count() . "\n";
