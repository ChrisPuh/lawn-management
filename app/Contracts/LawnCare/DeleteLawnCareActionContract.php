<?php

declare(strict_types=1);

namespace App\Contracts\LawnCare;

use App\Models\LawnCare;

interface DeleteLawnCareActionContract
{
    /**
     * Delete a lawn care record
     */
    public function execute(LawnCare $lawnCare, int $userId): bool;
}
