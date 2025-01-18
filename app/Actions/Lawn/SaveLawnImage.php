<?php

declare(strict_types=1);

namespace App\Actions\Lawn;

use App\Contracts\Lawn\ImageArchiveInterface;
use App\Contracts\Lawn\SaveLawnImageInterface;
use App\Enums\LawnImageType;
use App\Models\Lawn;
use App\Models\LawnImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use RuntimeException;

final readonly class SaveLawnImage implements SaveLawnImageInterface
{
    public function __construct(
        private ImageArchiveInterface $archiveAction
    ) {}

    /**
     * Save a new lawn image
     *
     * @throws RuntimeException if saving fails
     */
    public function handle(Lawn $lawn, TemporaryUploadedFile $image): LawnImage
    {
        // Validate image
        $this->validateImage($image);

        // Find and handle existing image
        $oldImage = $this->findLatestImage($lawn);
        if ($oldImage instanceof LawnImage) {
            $this->archiveAction->handle($oldImage);
        }

        // Generate new image filename
        $filename = $this->generateImageFilename($lawn, $image);

        // Ensure directory exists
        $this->ensureDirectoryExists($lawn);

        // Process and save new image
        $this->processAndSaveImage($image, $filename);

        // Create and return new image record
        return LawnImage::create([
            'lawn_id' => $lawn->id,
            'image_path' => $filename,
            'type' => LawnImageType::GENERAL->value,
            'imageable_id' => $lawn->id,
            'imageable_type' => Lawn::class,
        ]);
    }

    /**
     * Validate uploaded image against configuration rules
     *
     * @throws RuntimeException
     */
    private function validateImage(UploadedFile $image): void
    {
        $maxSize = Config::get('lawn.images.max_file_size');
        $allowedTypes = Config::get('lawn.images.allowed_types');

        if (! $image->isValid()) {
            throw new RuntimeException('Invalid image file.');
        }

        if ($image->getSize() > $maxSize * 1024) {
            throw new RuntimeException("Image exceeds maximum size of {$maxSize}KB.");
        }

        $extension = $image->getClientOriginalExtension();
        if (! in_array(strtolower($extension), $allowedTypes)) {
            throw new RuntimeException('Invalid image type. Allowed types: '.implode(', ', $allowedTypes));
        }
    }

    /**
     * Find the latest non-archived image for a lawn
     */
    private function findLatestImage(Lawn $lawn): ?LawnImage
    {
        return $lawn->images()
            ->where('type', LawnImageType::GENERAL)
            ->whereNull('archived_at')
            ->latest()
            ->first();
    }

    /**
     * Generate a unique filename for the new image
     */
    private function generateImageFilename(Lawn $lawn, UploadedFile $image): string
    {
        $extension = $image->getClientOriginalExtension();

        return sprintf(
            '%s/%d/images/%s.%s',
            Config::get('lawn.storage.base_path'),
            $lawn->id,
            uniqid(),
            $extension
        );
    }

    /**
     * Ensure the image storage directory exists
     */
    private function ensureDirectoryExists(Lawn $lawn): void
    {
        $directory = sprintf(
            '%s/%d/images',
            Config::get('lawn.storage.base_path'),
            $lawn->id
        );

        if (! Storage::disk('public')->exists($directory)) {
            Storage::disk('public')->makeDirectory($directory);
        }
    }

    /**
     * Process and save the image with resizing
     *
     * @throws RuntimeException
     */
    private function processAndSaveImage(UploadedFile $image, string $filename): void
    {
        $sourcePath = $image->getRealPath();
        $source = imagecreatefromstring(file_get_contents($sourcePath));

        if (! $source) {
            throw new RuntimeException('Could not create image from file');
        }

        $width = imagesx($source);
        $height = imagesy($source);
        $extension = $image->getClientOriginalExtension();

        // Calculate new dimensions (max width from config)
        $maxWidth = Config::get('lawn.images.max_width');
        if ($width > $maxWidth) {
            $newWidth = $maxWidth;
            $newHeight = (int) floor($height * ($maxWidth / $width));
        } else {
            $newWidth = $width;
            $newHeight = $height;
        }

        $new = imagecreatetruecolor($newWidth, $newHeight);

        // Preserve transparency for PNG images
        if ($extension === 'png') {
            imagealphablending($new, false);
            imagesavealpha($new, true);
        }

        imagecopyresampled(
            $new,
            $source,
            0,
            0,
            0,
            0,
            $newWidth,
            $newHeight,
            $width,
            $height
        );

        $tempPath = tempnam(sys_get_temp_dir(), 'img');
        $quality = Config::get('lawn.images.quality');

        // Save processed image
        match ($extension) {
            'jpg', 'jpeg' => imagejpeg($new, $tempPath, $quality),
            'png' => imagepng($new, $tempPath, (int) floor(($quality * 9) / 100)),
            'webp' => imagewebp($new, $tempPath, $quality),
            default => imagejpeg($new, $tempPath, $quality),
        };

        // Clean up GD resources
        imagedestroy($source);
        imagedestroy($new);

        // Move to storage
        Storage::disk('public')->put(
            $filename,
            file_get_contents($tempPath)
        );

        unlink($tempPath);
    }
}
