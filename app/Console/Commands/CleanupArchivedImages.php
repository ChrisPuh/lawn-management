<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\LawnImage;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

final class CleanupArchivedImages extends Command
{
    protected $signature = 'lawn:cleanup-archived-images
                            {--chunk=100 : Number of records to process in each chunk}
                            {--no-progress : Disable the progress bar}';

    protected $description = 'Cleanup archived lawn images past their retention period';

    public function handle(): int
    {
        if (! Config::get('lawn.storage.archive.enabled')) {
            $this->info('Image archiving is disabled. Skipping cleanup.');

            return self::SUCCESS;
        }

        $query = LawnImage::query()
            ->whereNotNull('archived_at')
            ->where('delete_after', '<', now());

        $total = $query->count();

        if ($total === 0) {
            $this->info('No images to cleanup.');

            return self::SUCCESS;
        }

        $this->info("Found {$total} images to cleanup");

        $chunkSize = $this->option('chunk') ?? 100;
        $count = 0;
        $failed = 0;

        $progressBar = ! $this->option('no-progress') && $this->output->getVerbosity() !== OutputInterface::VERBOSITY_QUIET
            ? $this->output->createProgressBar($total)
            : null;

        $progressBar?->start();

        $query->orderBy('id')
            ->chunk($chunkSize, function ($images) use (&$count, &$failed, $progressBar): void {
                foreach ($images as $image) {
                    try {
                        // Validate image path before deletion
                        if (! $image->image_path) {
                            throw new Exception('Invalid image path');
                        }

                        if (Storage::disk('public')->exists($image->image_path)) {
                            Storage::disk('public')->delete($image->image_path);
                        }
                        $image->delete();
                        $count++;
                        $progressBar?->advance();
                    } catch (Throwable $e) {
                        Log::error('Failed to cleanup image', [
                            'image_id' => $image->id,
                            'error' => $e->getMessage(),
                        ]);
                        $failed++;
                    }
                }
            });

        $progressBar?->finish();
        $this->newLine();

        $this->info("Cleaned up {$count} archived images.");

        if ($failed > 0) {
            $this->warn("{$failed} images failed to cleanup.");

            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
