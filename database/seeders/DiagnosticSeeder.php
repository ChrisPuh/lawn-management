<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Console\OutputStyle;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Helper\Table;
use Exception;

final class DiagnosticSeeder extends Seeder
{
    private OutputStyle $output;

    public function run(): void
    {
        $this->output = $this->command->getOutput();

        try {
            $this->printHeader('Database Diagnostic Report');
            $this->logDatabaseDetails();
            $this->checkTableStatus();
            $this->logEnvironmentInfo();
            $this->printFooter();
        } catch (Exception $e) {
            $this->output->error("Diagnostic Error: {$e->getMessage()}");
            $this->output->error($e->getTraceAsString());
        }
    }

    private function printHeader(string $title): void
    {
        $this->output->newLine();
        $this->output->title($title);
    }

    private function printFooter(): void
    {
        $this->output->newLine();
        $this->output->success('Diagnostic completed successfully');
        $this->output->newLine();
    }

    private function logDatabaseDetails(): void
    {
        $connection = config('database.default');
        $database = config('database.connections.' . $connection);

        $this->output->section('Database Connection');

        $table = new Table($this->output);
        $table->setHeaders(['Setting', 'Value']);
        $table->addRows([
            ['Connection', $connection],
            ['Driver', $database['driver']],
            ['Database', $database['database'] ?? 'N/A'],
            ['Version', DB::select('SELECT VERSION() as version')[0]->version ?? 'unknown'],
        ]);
        $table->render();
    }

    private function checkTableStatus(): void
    {
        try {
            $tables = Schema::getTableListing();
            $this->output->section('Database Tables Overview');

            // Summary
            $this->output->text(sprintf(
                'Found %d tables in database "%s"',
                count($tables),
                config('database.connections.'.config('database.default').'.database')
            ));
            $this->output->newLine();

            // Detailed table information
            foreach ($tables as $table) {
                $rowCount = DB::table($table)->count();
                $columns = Schema::getColumnListing($table);
                $indexes = Schema::getIndexListing($table);

                $this->output->text("<fg=blue>📊 Table:</> <options=bold>$table</>");

                $detailsTable = new Table($this->output);
                $detailsTable->setHeaders(['Property', 'Value']);
                $detailsTable->addRows([
                    ['Row Count', $rowCount],
                    ['Columns', implode(', ', $columns)],
                    ['Indexes', implode(', ', $indexes)],
                ]);
                $detailsTable->render();
                $this->output->newLine();
            }
        } catch (Exception $e) {
            $this->output->error('Error listing tables: ' . $e->getMessage());
        }
    }

    private function logEnvironmentInfo(): void
    {
        $this->output->section('Environment Information');

        $table = new Table($this->output);
        $table->setHeaders(['Component', 'Details']);
        $table->addRows([
            ['Environment', app()->environment()],
            ['Debug Mode', config('app.debug') ? 'Enabled ⚠️' : 'Disabled ✅'],
            ['PHP Version', phpversion()],
            ['Laravel Version', app()->version()],
            ['Memory Usage', $this->formatBytes(memory_get_usage(true))],
        ]);
        $table->render();
    }

    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        return round($bytes / (1024 ** $pow), 2) . ' ' . $units[$pow];
    }
}
