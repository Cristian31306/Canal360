<?php
require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

$filePath = 'Documentación/polizas.xlsx';
if (!file_exists($filePath)) {
    die("No existe el archivo\n");
}

$spreadsheet = IOFactory::load($filePath);
$worksheet = $spreadsheet->getActiveSheet();
$highestRow = 15; // Solo las primeras filas
$highestColumn = $worksheet->getHighestColumn();
$highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);

$cabeceras = [];
for ($col = 1; $col <= $highestColumnIndex; ++$col) {
    $val = strtolower(trim($worksheet->getCellByColumnAndRow($col, 1)->getValue() ?? ''));
    $val = str_replace(['ó', 'é', 'í', 'á', 'ú'], ['o', 'e', 'i', 'a', 'u'], $val);
    $cabeceras[$col] = $val;
}

echo "CABECERAS DETECTADAS:\n";
print_r($cabeceras);
echo "\nDATOS DE LAS PRIMERAS FILAS:\n";

echo "BUSCANDO TODAS LAS FILAS DE 'COAL NORTE SAS'...\n";
$highestRow = $worksheet->getHighestRow();
for ($row = 2; $row <= $highestRow; ++$row) {
    $cliente = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
    if (str_contains(strtoupper($cliente ?? ''), 'COAL NORTE SAS')) {
        echo "--- FILA $row ---\n";
        for ($col = 1; $col <= $highestColumnIndex; ++$col) {
            $key = $cabeceras[$col] ?: "COL_$col";
            $val = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
            echo "[$key]: $val\n";
        }
    }
}
