<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Exception;

final class DiagnosticSeeder extends Seeder
{
    public function run(): void
    {
        try {
            // Detailed logging of database connection
            $this->logDatabaseDetails();

            // Check table existence and content
            $this->checkTableStatus();

            // Log environment-specific information
            $this->logEnvironmentInfo();

        } catch (Exception $e) {
            Log::channel('stderr')->error('Diagnostic Seeder Error: ' . $e->getMessage());
            Log::channel('stderr')->error($e->getTraceAsString());
        }
    }

    private function logDatabaseDetails(): void
    {
        $connection = config('database.default');
        $database = config('database.connections.' . $connection);

        Log::channel('stderr')->info('Database Connection Details', [
            'connection' => $connection,
            'driver' => $database['driver'],
            'database_path' => $database['database'] ?? 'N/A',
        ]);
    }

    private function checkTableStatus(): void
    {
        try {
            $tables = Schema::getConnection()->getDoctrineSchemaManager()->listTableNames();

            Log::channel('stderr')->info('Existing Tables', [
                'count' => count($tables),
                'tables' => $tables
            ]);

            foreach ($tables as $table) {
                try {
                    $rowCount = DB::table($table)->count();
                    Log::channel('stderr')->info("Table: $table, Row Count: $rowCount");
                } catch (Exception $e) {
                    Log::channel('stderr')->error("Error counting rows in table $table: " . $e->getMessage());
                }
            }
        } catch (Exception $e) {
            Log::channel('stderr')->error('Error listing tables: ' . $e->getMessage());
        }
    }

    private function logEnvironmentInfo(): void
    {
        Log::channel('stderr')->info('Environment Information', [
            'environment' => app()->environment(),
            'debug_mode' => config('app.debug'),
            'php_version' => phpversion(),
            'laravel_version' => app()->version(),
        ]);
    }
}
