<?php

declare(strict_types=1);

namespace App\Livewire\Components;

use App\Enums\LawnImageType;
use App\Models\Lawn;
use App\Models\LawnImage;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Renderless;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

final class LawnImageUpload extends Component
{
    use WithFileUploads;

    public Lawn $lawn;

    public ?TemporaryUploadedFile $image = null;

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

    /**
     * Save the uploaded image
     */
    public function updatedImage(): void
    {
        $this->save();
    }

    public function save(): void
    {
        $this->authorize('update', $this->lawn);

        $this->validate();

        if (!$this->image instanceof TemporaryUploadedFile) {
            return;
        }

        // Store the image in the storage/app/public/lawn-images directory
        $path = $this->image->store('lawn-images', 'public');

        if (!is_string($path)) {
            return;
        }

        // Create a new LawnImage record
        LawnImage::create([
            'lawn_id' => $this->lawn->id,
            'image_path' => $path,
            'type' => LawnImageType::GENERAL,
            'imageable_id' => $this->lawn->id,
            'imageable_type' => Lawn::class,
        ]);

        $this->image = null;

        $this->dispatch('image-uploaded');
    }

    /**
     * Delete an image
     */
    public function delete(LawnImage $image): void
    {
        $this->authorize('delete', $this->lawn);

        // Delete the physical file
        Storage::disk('public')->delete($image->image_path);

        // Delete the database record
        $image->delete();

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
}
