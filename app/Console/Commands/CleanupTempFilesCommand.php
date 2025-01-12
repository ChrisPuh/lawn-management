<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;

final class CleanupTempFilesCommand extends Command
{
    protected $signature = 'app:cleanup-temp-files {--force : Force cleanup regardless of configuration}';

    protected $description = 'Cleanup old temporary files from uploads';

    public function handle(): void
    {
        if (! config('lawn.storage.temp.cleanup_enabled') && ! $this->option('force')) {
            $this->info('Temp file cleanup is disabled in configuration.');
            $this->info('Use --force to run anyway.');

            return;
        }

        $tempDirectory = storage_path('app/'.config('lawn.storage.temp.path'));
        if (! is_dir($tempDirectory)) {
            $this->info('No temp directory found at: '.$tempDirectory);

            return;
        }

        $count = 0;
        $errors = 0;
        $files = scandir($tempDirectory);
        $now = time();
        $retention = config('lawn.storage.temp.retention_hours', 24) * 60 * 60;

        $this->output->progressStart(count($files));

        foreach ($files as $file) {
            $this->output->progressAdvance();

            if ($file === '.' || $file === '..') {
                continue;
            }

            $filePath = $tempDirectory.'/'.$file;
            if (is_file($filePath)) {
                try {
                    // Delete if older than retention period
                    if ($now - filemtime($filePath) >= $retention) {
                        unlink($filePath);
                        $count++;
                    }
                } catch (Exception $e) {
                    $this->error("Could not delete file: {$file}");
                    $errors++;
                }
            }
        }

        $this->output->progressFinish();

        $this->info('Cleanup completed:');
        $this->line(" - Deleted files: {$count}");
        if ($errors > 0) {
            $this->warn(" - Failed deletions: {$errors}");
        }
        $this->line(" - Retention period: {$this->formatHours($retention / 3600)}");
    }

    private function formatHours(int $hours): string
    {
        if ($hours < 24) {
            return "{$hours} hours";
        }

        $days = floor($hours / 24);
        $remainingHours = $hours % 24;

        return $remainingHours > 0
            ? "{$days} days, {$remainingHours} hours"
            : "{$days} days";
    }
}
