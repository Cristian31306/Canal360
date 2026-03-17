<#
Script para migrar los datos locales (SQLite) a la base de datos de producción (MySQL) en el VPS.
#>

Write-Host "Iniciando exportación de base de datos local..." -ForegroundColor Cyan

# Creamos un script PHP temporal para exportar los datos mediante Laravel
$exportScript = @"
<?php
require __DIR__.'/vendor/autoload.php';
`$app = require_once __DIR__.'/bootstrap/app.php';
`$kernel = `$app->make(Illuminate\Contracts\Console\Kernel::class);
`$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

`$tables = DB::connection('sqlite')->select('SELECT name FROM sqlite_master WHERE type="table" AND name NOT LIKE "sqlite_%"');

`$exportData = [];
foreach(`$tables as `$tableObj) {
    `$table = `$tableObj->name;
    if (`$table === 'migrations') continue; // No exportar migraciones
    `$exportData[`$table] = DB::table(`$table)->get()->toArray();
}

file_put_contents('database_export.json', json_encode(`$exportData));
echo "Exportación completada. \n";
"@

Set-Content -Path .\temp_export.php -Value $exportScript
php temp_export.php
Remove-Item .\temp_export.php

if (-Not (Test-Path .\database_export.json)) {
    Write-Host "Error: No se pudo generar el archivo de exportación." -ForegroundColor Red
    exit
}

Write-Host "Subiendo datos al VPS..." -ForegroundColor Cyan
scp .\database_export.json cristian@167.86.72.200:/home/cristian/apps/Canal360/database_export.json

Write-Host "Preparando importación en el VPS..." -ForegroundColor Cyan

# Script para importar los datos en el VPS
$importScript = @"
<?php
require __DIR__.'/vendor/autoload.php';
`$app = require_once __DIR__.'/bootstrap/app.php';
`$kernel = `$app->make(Illuminate\Contracts\Console\Kernel::class);
`$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

`$file = __DIR__.'/database_export.json';
if (!file_exists(`$file)) {
    die("Archivo json no encontrado.\n");
}

`$data = json_decode(file_get_contents(`$file), true);

Schema::disableForeignKeyConstraints();

foreach(`$data as `$table => `$rows) {
    echo "Importando tabla: `$table (".count(`$rows)." filas)...\n";
    DB::table(`$table)->truncate();
    
    // Chunk insert para evitar errores de memoria o "Packet too large" en MySQL
    `$chunks = array_chunk(`$rows, 100);
    foreach(`$chunks as `$chunk) {
        // Convertir objetos stdClass a array si es necesario
        `$insertData = array_map(function(`$row) { return (array)`$row; }, `$chunk);
        
        try {
            DB::table(`$table)->insert(`$insertData);
        } catch (\Exception `$e) {
            echo "Error en tabla `$table: " . `$e->getMessage() . "\n";
        }
    }
}

Schema::enableForeignKeyConstraints();
echo "Importación finalizada con éxito.\n";
"@

Set-Content -Path .\temp_import.php -Value $importScript
scp .\temp_import.php cristian@167.86.72.200:/home/cristian/apps/Canal360/temp_import.php
Remove-Item .\temp_import.php

Write-Host "Ejecutando importación en el servidor remoto (esto vaciará y sobreescribirá la BD del VPS)..." -ForegroundColor Yellow
ssh cristian@167.86.72.200 "cd /home/cristian/apps/Canal360 && php temp_import.php && rm temp_import.php && rm database_export.json"

Write-Host "Sincronización finalizada correctamente ✅" -ForegroundColor Green
