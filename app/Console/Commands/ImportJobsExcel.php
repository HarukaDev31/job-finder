<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ImportService;

class ImportJobsExcel extends Command
{
    protected $signature = 'import:jobs-excel {file : Ruta al archivo Excel}';
    protected $description = 'Importar datos de trabajos, postulantes y postulaciones desde archivo Excel';

    protected $importService;

    public function __construct(ImportService $importService)
    {
        parent::__construct();
        $this->importService = $importService;
    }

    public function handle()
    {
        $filePath = $this->argument('file');

        if (!file_exists($filePath)) {
            $this->error("El archivo no existe: {$filePath}");
            return 1;
        }

        $this->info("Iniciando importación desde: {$filePath}");
        $this->info("Procesando archivo...");

        try {
            $result = $this->importService->importFromExcel($filePath);

            if ($result['success']) {
                $this->info("✅ " . $result['message']);
                $this->line($result['output']);
                $this->info("Importación completada exitosamente!");
                return 0;
            } else {
                $this->error("❌ " . $result['message']);
                return 1;
            }

        } catch (\Exception $e) {
            $this->error("❌ Error durante la importación: " . $e->getMessage());
            return 1;
        }
    }
}
