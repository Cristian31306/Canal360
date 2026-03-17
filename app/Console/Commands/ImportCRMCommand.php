<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Imports\CRMImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\File;

class ImportCRMCommand extends Command
{
    protected $signature = 'crm:import {file=DatosCRM.xlsx}';
    protected $description = 'Importar títulos mineros desde un archivo Excel al CRM';

    public function handle()
    {
        $fileName = $this->argument('file');
        $filePath = base_path($fileName);

        if (!File::exists($filePath)) {
            $this->error("El archivo no existe en la ruta: {$filePath}");
            return 1;
        }

        $this->info("Iniciando importación desde {$fileName}...");

        try {
            Excel::import(new CRMImport, $filePath);
            $this->info('¡Importación completada con éxito!');
        } catch (\Exception $e) {
            $this->error('Error durante la importación: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
