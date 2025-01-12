<?php

declare(strict_types=1);

namespace App\Livewire\Components;

use App\Enums\LawnImageType;
use App\Models\Lawn;
use App\Models\LawnImage;
use Illuminate\Contracts\View\View;
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

    /** @var array<string, string> */
    protected $messages = [
        'image.required' => 'Bitte wählen Sie ein Bild aus.',
        'image.image' => 'Die Datei muss ein Bild sein.',
        'image.max' => 'Das Bild darf maximal 5MB groß sein.',
    ];

    /** @var array<string, string|array<string>> */
    protected $rules = [
        'image' => ['required', 'image', 'max:5120'], // 5MB max
    ];

    public function mount(Lawn $lawn): void
    {
        $this->authorize('update', $lawn);
        $this->lawn = $lawn;
    }

    public function updatedImage(): void
    {
        $this->validate();
        $this->showConfirmation = true;
    }

    public function cancelUpload(): void
    {
        $this->image = null;
        $this->showConfirmation = false;
    }

    /**
     * Save the uploaded image
     */
    public function save(): void
    {
        $this->authorize('update', $this->lawn);

        if (! $this->image instanceof TemporaryUploadedFile) {
            return;
        }

        // Generate storage path
        $extension = $this->image->getClientOriginalExtension();
        $filename = sprintf(
            'lawns/%d/images/%s.%s',
            $this->lawn->id,
            uniqid(),
            $extension
        );

        // Ensure directory exists
        $directory = sprintf('lawns/%d/images', $this->lawn->id);
        if (! Storage::disk('public')->exists($directory)) {
            Storage::disk('public')->makeDirectory($directory);
        }

        // Clean up old temp files (älter als 24 Stunden)
        $this->cleanupTempFiles();

        // Get the source image
        $sourcePath = $this->image->getRealPath();

        // Create GD image from source
        $source = imagecreatefromstring(file_get_contents($sourcePath));
        if (! $source) {
            throw new RuntimeException('Could not create image from file');
        }

        // Get original dimensions
        $width = (int) imagesx($source);
        $height = (int) imagesy($source);

        // Calculate new dimensions (max width 1200px)
        $maxWidth = 1200;
        if ($width > $maxWidth) {
            $newWidth = $maxWidth;
            $newHeight = (int) floor($height * ($maxWidth / $width));
        } else {
            $newWidth = $width;
            $newHeight = $height;
        }

        // Create new image
        $new = imagecreatetruecolor($newWidth, $newHeight);

        // Preserve transparency for PNG images
        if ($extension === 'png') {
            imagealphablending($new, false);
            imagesavealpha($new, true);
        }

        // Resize image
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

        // Create temp file for processed image
        $tempPath = tempnam(sys_get_temp_dir(), 'img');

        // Save processed image
        if ($extension === 'jpg' || $extension === 'jpeg') {
            imagejpeg($new, $tempPath, 80);
        } elseif ($extension === 'png') {
            imagepng($new, $tempPath, 8);
        } else {
            imagejpeg($new, $tempPath, 80);
        }

        // Clean up GD resources
        imagedestroy($source);
        imagedestroy($new);

        // Move to storage
        Storage::disk('public')->put(
            $filename,
            file_get_contents($tempPath)
        );

        // Clean up temp file
        unlink($tempPath);

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
        $this->dispatch('image-uploaded');
    }

    /**
     * Delete an image
     */
    public function delete(int $imageId): void
    {
        $this->authorize('delete', $this->lawn);

        $image = LawnImage::find($imageId);

        if ($image && $image->lawn_id === $this->lawn->id) {
            // Delete the physical file
            Storage::disk('public')->delete($image->image_path);

            // Delete the database record
            $image->delete();
        }

        $this->dispatch('image-deleted');
    }

    /**
     * Get the latest image for the lawn
     */
    public function getLatestImage(): ?LawnImage
    {
        return $this->lawn->images()
            ->latest()
            ->first();
    }

    public function render(): View
    {
        return view('livewire.components.lawn-image-upload', [
            'latestImage' => $this->getLatestImage(),
        ]);
    }

    /**
     * Clean up old temporary files
     */
    private function cleanupTempFiles(): void
    {
        $tempDirectory = storage_path('app/livewire-tmp');
        if (! is_dir($tempDirectory)) {
            return;
        }

        // Cleanup files older than 24 hours
        $files = scandir($tempDirectory);
        $now = time();

        foreach ($files as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }

            $filePath = $tempDirectory.'/'.$file;
            if (is_file($filePath)) {
                // Delete if older than 24 hours
                if ($now - filemtime($filePath) >= 24 * 60 * 60) {
                    unlink($filePath);
                }
            }
        }
    }
}
