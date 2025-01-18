<?php

declare(strict_types=1);

namespace App\Actions\Lawn;

use App\Contracts\Lawn\DeleteLawnImageInterface;
use App\Contracts\Lawn\ImageArchiveInterface;
use App\Models\LawnImage;
use Illuminate\Support\Facades\Auth;
use RuntimeException;

final readonly class DeleteLawnImage implements DeleteLawnImageInterface
{
    public function __construct(
        private ImageArchiveInterface $archiveAction
    ) {}

    /**
     * Delete a lawn image
     *
     * @throws RuntimeException if deletion or authorization fails
     */
    public function handle(LawnImage $image): void
    {
        // Ensure the user is authorized to delete the image
        if (! $image->lawn->user->is(Auth::user())) {
            throw new RuntimeException('Sie sind nicht berechtigt, dieses Bild zu löschen.');
        }

        // Archive the existing image instead of deleting
        $this->archiveAction->handle($image);
    }
}
