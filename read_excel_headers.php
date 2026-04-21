<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use PhpOffice\PhpSpreadsheet\IOFactory;

try {
    $filePath = __DIR__ . '/Documentación/polizas.xlsx';
    if (!file_exists($filePath)) {
        die("File not found\n");
    }

    $spreadsheet = IOFactory::load($filePath);
    $worksheet = $spreadsheet->getActiveSheet();
    $highestRow = min($worksheet->getHighestRow(), 3); // Max 3 rows
    $highestColumn = $worksheet->getHighestColumn();
    
    $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);

    for ($row = 1; $row <= $highestRow; ++$row) {
        $rowData = [];
        for ($col = 1; $col <= $highestColumnIndex; ++$col) {
            $value = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
            $rowData[] = $value;
        }
        echo "Row $row: " . implode(" | ", $rowData) . "\n";
    }

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}
