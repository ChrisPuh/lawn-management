<?php

declare(strict_types=1);

namespace App\Contracts\Lawn;

use App\Models\LawnImage;
use RuntimeException;

interface DeleteLawnImageInterface
{
    /**
     * Delete a lawn image
     *
     * @throws RuntimeException if deletion fails
     */
    public function handle(LawnImage $image): void;
}
