<?php

declare(strict_types=1);

namespace App\Contracts\Lawn;

use App\Models\Lawn;
use App\Models\LawnImage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use RuntimeException;

interface SaveLawnImageInterface
{
    /**
     * Save a new lawn image
     *
     * @throws RuntimeException if saving fails
     */
    public function handle(Lawn $lawn, TemporaryUploadedFile $image): LawnImage;
}
