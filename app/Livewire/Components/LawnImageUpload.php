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

    public bool $showSuccessMessage = false;

    /**
     * Validation messages for file upload
     *
     * @var array<string, string>
     */
    private array $validationMessages = [
        'image.required' => 'Bitte wählen Sie ein Bild aus.',
        'image.image' => 'Die Datei muss ein Bild sein.',
        'image.max' => 'Das Bild darf maximal 5MB groß sein.',
    ];

    /**
     * Validation rules for file upload
     *
     * @var array<string, array<string>>
     */
    private array $validationRules = [
        'image' => ['required', 'image', 'max:5120'], // 5MB max
    ];

    public function mount(Lawn $lawn): void
    {
        $this->authorize('update', $lawn);
        $this->lawn = $lawn;
    }

    public function updatedImage(): void
    {
        $this->validate($this->validationRules, $this->validationMessages);
        $this->showConfirmation = true;
        $this->showSuccessMessage = false;
    }

    public function cancel(): void
    {
        $this->image = null;
        $this->showConfirmation = false;
        $this->showSuccessMessage = false;
    }

    public function save(): void
    {
        $this->validate($this->validationRules, $this->validationMessages);
        $this->authorize('update', $this->lawn);

        if (! $this->image instanceof TemporaryUploadedFile) {
            return;
        }

        // Handle old image deletion
        $oldImage = $this->getLatestImage();

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

        // Clean up old temp files
        $this->cleanupTempFiles();

        // Process and save new image
        $this->processAndSaveImage($filename);

        // Delete old image after successful upload
        if ($oldImage !== null) {
            Storage::disk('public')->delete($oldImage->image_path);
            $oldImage->delete();
        }

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

        // Hide success message after 3 seconds using JS dispatch
        $this->dispatch('hide-success')->self();
        $this->dispatch('image-uploaded');
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

        // Calculate new dimensions (max width 1200px)
        $maxWidth = 1200;
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

        // Save processed image
        match ($extension) {
            'jpg', 'jpeg' => imagejpeg($new, $tempPath, 80),
            'png' => imagepng($new, $tempPath, 8),
            default => imagejpeg($new, $tempPath, 80),
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

    /**
     * Delete an image
     */
    public function delete(int $imageId): void
    {
        $this->authorize('delete', $this->lawn);

        $image = LawnImage::find($imageId);

        if ($image && $image->lawn_id === $this->lawn->id) {
            Storage::disk('public')->delete($image->image_path);
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
     * Clean up old temporary files
     */
    private function cleanupTempFiles(): void
    {
        $tempDirectory = storage_path('app/livewire-tmp');
        if (! is_dir($tempDirectory)) {
            return;
        }

        $files = scandir($tempDirectory);
        $now = time();

        foreach ($files as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }

            $filePath = $tempDirectory . '/' . $file;
            if (is_file($filePath) && $now - filemtime($filePath) >= 24 * 60 * 60) {
                unlink($filePath);
            }
        }
    }
}
