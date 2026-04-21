<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    $tables = [];
    $dbTables = DB::select('SHOW TABLES');
    $dbNameKey = null;

    foreach ($dbTables as $tableInfo) {
        if ($dbNameKey === null) {
            $keys = array_keys((array)$tableInfo);
            $dbNameKey = $keys[0];
        }
        $tableName = $tableInfo->{$dbNameKey};
        $columns = DB::select('SHOW COLUMNS FROM ' . $tableName);
        $tables[$tableName] = $columns;
    }

    echo json_encode($tables, JSON_PRETTY_PRINT);
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}
