<?php

declare(strict_types=1);

namespace App\Livewire\Components;

use App\Enums\LawnImageType;
use App\Models\Lawn;
use App\Models\LawnImage;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use RuntimeException;

final class LawnImageUpload extends Component
{
    use WithFileUploads;

    public Lawn $lawn;

    public ?TemporaryUploadedFile $image = null;

    public bool $showConfirmation = false;

    public bool $showSuccessMessage = false;

    /**
     * Validation messages for file upload
     *
     * @var array<string, string>
     */
    private array $validationMessages = [
        'image.required' => 'Bitte wählen Sie ein Bild aus.',
        'image.image' => 'Die Datei muss ein Bild sein.',
        'image.max' => 'Das Bild darf maximal :max KB groß sein.',
        'image.mimes' => 'Das Bild muss eines der folgenden Formate haben: :values.',
    ];

    public function mount(Lawn $lawn): void
    {
        $this->authorize('update', $lawn);
        $this->lawn = $lawn;
    }

    public function getValidationRules(): array
    {
        return [
            'image' => [
                'required',
                'image',
                'max:' . Config::get('lawn.images.max_file_size'),
                'mimes:' . implode(',', Config::get('lawn.images.allowed_types')),
            ],
        ];
    }

    public function updatedImage(): void
    {
        $this->validate($this->getValidationRules(), $this->validationMessages);
        $this->showConfirmation = true;
    }

    public function cancel(): void
    {
        $this->image = null;
        $this->showConfirmation = false;
        $this->showSuccessMessage = false;
    }

    public function save(): void
    {
        $this->validate($this->getValidationRules(), $this->validationMessages);
        $this->authorize('update', $this->lawn);

        if (! $this->image instanceof TemporaryUploadedFile) {
            return;
        }

        // Handle old image archival or deletion
        $oldImage = $this->getLatestImage();
        if ($oldImage !== null) {
            if (Config::get('lawn.storage.archive.enabled')) {
                $this->archiveOldImage($oldImage);
            } else {
                // Direct delete if archiving is disabled
                Storage::disk('public')->delete($oldImage->image_path);
                $oldImage->delete();
            }
        }

        // Generate storage path for new image
        $extension = $this->image->getClientOriginalExtension();
        $filename = sprintf(
            '%s/%d/images/%s.%s',
            Config::get('lawn.storage.base_path'),
            $this->lawn->id,
            uniqid(),
            $extension
        );

        // Ensure directory exists
        $directory = sprintf('%s/%d/images', Config::get('lawn.storage.base_path'), $this->lawn->id);
        if (! Storage::disk('public')->exists($directory)) {
            Storage::disk('public')->makeDirectory($directory);
        }

        // Clean up old temp files if enabled
        if (Config::get('lawn.storage.temp.cleanup_enabled')) {
            $this->cleanupTempFiles();
        }

        // Process and save new image
        $this->processAndSaveImage($filename);

        // Create database record
        LawnImage::create([
            'lawn_id' => $this->lawn->id,
            'image_path' => $filename,
            'type' => LawnImageType::GENERAL->value,
            'imageable_id' => $this->lawn->id,
            'imageable_type' => Lawn::class,
        ]);

        $this->image = null;
        $this->showConfirmation = false;
        $this->showSuccessMessage = true;

        $this->dispatch('hide-success')->self();
        $this->dispatch('image-uploaded');
    }

    private function archiveOldImage(LawnImage $image): void
    {
        // Create archive path with timestamp
        $archivePath = sprintf(
            '%s/%s/%d/%s_%s',
            Config::get('lawn.storage.base_path'),
            Config::get('lawn.storage.archive.path'),
            $this->lawn->id,
            now()->format('Y-m-d_His'),
            basename($image->image_path)
        );

        // Ensure archive directory exists
        $archiveDir = dirname($archivePath);
        if (! Storage::disk('public')->exists($archiveDir)) {
            Storage::disk('public')->makeDirectory($archiveDir);
        }

        // Move file to archive
        $archiveDisk = Config::get('lawn.storage.archive.disk');

        if (Storage::disk('public')->exists($image->image_path)) {
            // If archive disk is different from public, copy then delete
            if ($archiveDisk !== 'public') {
                Storage::disk($archiveDisk)->put(
                    $archivePath,
                    Storage::disk('public')->get($image->image_path)
                );
                Storage::disk('public')->delete($image->image_path);
            } else {
                Storage::disk('public')->move($image->image_path, $archivePath);
            }
        }

        // Update database record
        $image->update([
            'image_path' => $archivePath,
            'archived_at' => now(),
            'delete_after' => now()->addMonths(config('lawn.storage.archive.retention_months')),
        ]);
    }

    /**
     * Process and save the image with resizing
     */
    private function processAndSaveImage(string $filename): void
    {
        if (! $this->image instanceof TemporaryUploadedFile) {
            throw new RuntimeException('No image provided');
        }

        $sourcePath = $this->image->getRealPath();
        $source = imagecreatefromstring(file_get_contents($sourcePath));

        if (! $source) {
            throw new RuntimeException('Could not create image from file');
        }

        $width = imagesx($source);
        $height = imagesy($source);
        $extension = $this->image->getClientOriginalExtension();

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

    public function hideSuccessMessage(): void
    {
        $this->showSuccessMessage = false;
    }

    public function render(): View
    {
        return view('livewire.components.lawn-image-upload', [
            'latestImage' => $this->getLatestImage(),
        ]);
    }

    /**
     * Get the latest image for the lawn
     */
    private function getLatestImage(): ?LawnImage
    {
        return $this->lawn->images()
            ->whereNull('archived_at')
            ->latest()
            ->first();
    }

    /**
     * Clean up old temporary files
     */
    private function cleanupTempFiles(): void
    {
        $tempDirectory = storage_path(Config::get('lawn.storage.temp.path'));
        if (! is_dir($tempDirectory)) {
            return;
        }

        $files = scandir($tempDirectory);
        $now = time();
        $retentionHours = Config::get('lawn.storage.temp.retention_hours');

        foreach ($files as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }

            $filePath = $tempDirectory . '/' . $file;
            if (is_file($filePath) && $now - filemtime($filePath) >= $retentionHours * 3600) {
                unlink($filePath);
            }
        }
    }
}
