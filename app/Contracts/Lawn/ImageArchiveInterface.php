<?php

declare(strict_types=1);

namespace App\Contracts\Lawn;

use App\Models\LawnImage;
use RuntimeException;

interface ImageArchiveInterface
{
    /**
     * Archive the given image
     *
     * @throws RuntimeException if archiving fails
     */
    public function handle(LawnImage $image): void;
}
