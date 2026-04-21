<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$aseguradoras = \App\Models\Aseguradora::pluck('nombre')->toArray();
$ramos = \App\Models\Ramo::pluck('nombre')->toArray();

echo "--- ASEGURADORAS EN BD ---\n";
print_r($aseguradoras);

echo "\n--- RAMOS EN BD ---\n";
print_r($ramos);
