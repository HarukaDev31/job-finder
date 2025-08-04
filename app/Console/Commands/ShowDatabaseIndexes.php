<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ShowDatabaseIndexes extends Command
{
    protected $signature = 'db:indexes {table? : Nombre de la tabla específica}';
    protected $description = 'Mostrar información sobre los índices de la base de datos';

    public function handle()
    {
        $table = $this->argument('table');
        
        if ($table) {
            $this->showTableIndexes($table);
        } else {
            $this->showAllIndexes();
        }
    }

    private function showAllIndexes()
    {
        $this->info('📊 ÍNDICES DE LA BASE DE DATOS');
        $this->line('');

        $tables = ['users', 'trabajos', 'postulantes', 'postulaciones'];
        
        foreach ($tables as $table) {
            $this->showTableIndexes($table);
            $this->line('');
        }
    }

    private function showTableIndexes($table)
    {
        $this->info("📋 Tabla: {$table}");
        
        try {
            $indexes = DB::select("
                SELECT 
                    INDEX_NAME as name,
                    COLUMN_NAME as column_name,
                    NON_UNIQUE as non_unique,
                    SEQ_IN_INDEX as sequence
                FROM INFORMATION_SCHEMA.STATISTICS 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = ?
                ORDER BY INDEX_NAME, SEQ_IN_INDEX
            ", [$table]);

            if (empty($indexes)) {
                $this->warn("   No se encontraron índices para la tabla {$table}");
                return;
            }

            $currentIndex = '';
            foreach ($indexes as $index) {
                if ($currentIndex !== $index->name) {
                    $currentIndex = $index->name;
                    $type = $index->non_unique ? 'INDEX' : 'UNIQUE';
                    $this->line("   🔸 {$index->name} ({$type})");
                }
                $this->line("      └─ {$index->column_name}");
            }

        } catch (\Exception $e) {
            $this->error("Error al obtener índices de la tabla {$table}: " . $e->getMessage());
        }
    }
}
