<?php

declare(strict_types=1);

namespace App\Contracts\LawnCare;

use App\Models\LawnCare;
use App\Models\LawnCareLog;

interface LogLawnCareActionContract
{
    /**
     * @param  array<string, mixed>  $additional_data
     */
    public function execute(LawnCare $lawn_care, string $action, int $user_id, array $additional_data = []): LawnCareLog;
}
