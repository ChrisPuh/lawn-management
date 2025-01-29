<?php

declare(strict_types=1);

namespace App\Livewire\Components;

use App\Contracts\Lawn\DeleteLawnImageInterface;
use App\Contracts\Lawn\SaveLawnImageInterface;
use App\Enums\LawnImageType;
use App\Models\Lawn;
use App\Models\LawnImage;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Config;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use RuntimeException;

final class LawnImageUpload extends Component
{
    use AuthorizesRequests;
    use WithFileUploads;

    public Lawn $lawn;

    public ?TemporaryUploadedFile $image = null;

    public bool $showConfirmation = false;

    public bool $showSuccessMessage = false;

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
                'max:'.Config::get('lawn.images.max_file_size'),
                'mimes:'.implode(',', Config::get('lawn.images.allowed_types')),
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

    public function save(SaveLawnImageInterface $saveAction): void
    {
        $this->validate($this->getValidationRules(), $this->validationMessages);
        $this->authorize('update', $this->lawn);

        if (! $this->image instanceof TemporaryUploadedFile) {
            return;
        }

        try {
            $saveAction->handle($this->lawn, $this->image);

            $this->image = null;
            $this->showConfirmation = false;
            $this->showSuccessMessage = true;

            $this->dispatch('hide-success')->self();
            $this->dispatch('image-uploaded');
        } catch (Exception $e) {
            $this->addError('image', $e->getMessage());
        }
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

    public function delete(DeleteLawnImageInterface $deleteAction, int $imageId): void
    {
        // Find the image
        $image = LawnImage::findOrFail($imageId);

        try {
            $deleteAction->handle($image);

            // Dispatch global success message with explicit payload
            $this->dispatch('show-success-message', [
                'message' => 'Bild erfolgreich gelöscht',
                'duration' => 3000,
            ]);
            $this->dispatch('image-deleted');
        } catch (RuntimeException $e) {
            $this->addError('image', $e->getMessage());
        }
    }

    private function getLatestImage(): ?LawnImage
    {

        return $this->lawn->images()
            ->where('imageable_type', Lawn::class)
            ->where('type', LawnImageType::GENERAL)
            ->whereNull('archived_at')
            ->latest()
            ->first();
    }
}
