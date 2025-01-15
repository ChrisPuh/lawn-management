<?php

declare(strict_types=1);

namespace App\Actions\Lawn;

use App\Contracts\Lawn\ImageArchiveInterface;
use App\Models\LawnImage;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;

final class ArchiveLawnImage implements ImageArchiveInterface
{
    /**
     * Archive an existing lawn image
     */
    public function handle(LawnImage $image): void
    {
        // Skip if already archived
        if ($image->archived_at !== null) {
            return;
        }

        // Check if archiving is enabled in config
        if (! Config::get('lawn.storage.archive.enabled')) {
            // If archiving is disabled, just delete the image
            $this->deleteImage($image);

            return;
        }

        $this->archiveImageFile($image);
        $this->updateImageRecord($image);
    }

    /**
     * Move or copy the image file to archive location
     */
    private function archiveImageFile(LawnImage $image): void
    {
        $archivePath = $this->generateArchivePath($image);
        $archiveDisk = Config::get('lawn.storage.archive.disk');

        // Ensure archive directory exists
        $archiveDir = dirname($archivePath);
        if (! Storage::disk('public')->exists($archiveDir)) {
            Storage::disk('public')->makeDirectory($archiveDir);
        }

        // Move or copy file
        if (Storage::disk('public')->exists($image->image_path)) {
            if ($archiveDisk !== 'public') {
                // Different disk: copy then delete
                Storage::disk($archiveDisk)->put(
                    $archivePath,
                    Storage::disk('public')->get($image->image_path)
                );
                Storage::disk('public')->delete($image->image_path);
            } else {
                // Same disk: move directly
                Storage::disk('public')->move($image->image_path, $archivePath);
            }
        }
    }

    /**
     * Update the image record with archive metadata
     */
    private function updateImageRecord(LawnImage $image): void
    {
        $archivePath = $this->generateArchivePath($image);

        $image->update([
            'image_path' => $archivePath,
            'archived_at' => now(),
            'delete_after' => now()->addMonths(
                config('lawn.storage.archive.retention_months')
            ),
        ]);
    }

    /**
     * Generate a unique archive path for the image
     */
    private function generateArchivePath(LawnImage $image): string
    {
        return sprintf(
            '%s/%s/%d/%s_%s',
            Config::get('lawn.storage.base_path'),
            Config::get('lawn.storage.archive.path'),
            $image->lawn_id,
            now()->format('Y-m-d_His'),
            basename($image->image_path)
        );
    }

    /**
     * Delete the image if archiving is disabled
     */
    private function deleteImage(LawnImage $image): void
    {
        if (Storage::disk('public')->exists($image->image_path)) {
            Storage::disk('public')->delete($image->image_path);
        }
        $image->delete();
    }
}
